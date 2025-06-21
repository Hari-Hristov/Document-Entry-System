<?php

class RequestStep
{
    private PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    // Add department_category_id to support department-to-department routing
    public function create($requestId, $stepOrder, $requiredDocument)
    {
        // Determine department_category_id for special routing
        $stmt = $this->db->prepare("SELECT category_id, document_type FROM document_requests WHERE id = ?");
        $stmt->execute([$requestId]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);
        $departmentCategoryId = $request['category_id'];
        // If this is 'Заявление за студентски права' requested from 'Сесия', route to 'Отдел Студенти' (category_id = 1)
        if ($request['document_type'] === 'Заявление за поправка' && $requiredDocument === 'Заявление за студентски права') {
            $departmentCategoryId = 1; // Отдел Студенти
        }
        $stmt = $this->db->prepare("INSERT INTO request_steps (request_id, step_order, required_document, department_category_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$requestId, $stepOrder, $requiredDocument, $departmentCategoryId]);
    }

    public function getPendingStepsForUser($userId)
    {
        $stmt = $this->db->prepare("
            SELECT rs.*, dr.category_id, c.name AS category_name
            FROM request_steps rs
            JOIN document_requests dr ON rs.request_id = dr.id
            JOIN categories c ON dr.category_id = c.id
            WHERE dr.uploaded_by_user_id = ? AND rs.status = 'waiting_user'
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStepFile($stepId, $fileName)
    {
        $stmt = $this->db->prepare("UPDATE request_steps SET uploaded_file = ?, status = 'waiting_responsible', updated_at = NOW() WHERE id = ?");
        $stmt->execute([$fileName, $stepId]);
    }

    public function rejectStep($stepId)
    {
        $stmt = $this->db->prepare("UPDATE request_steps SET status = 'rejected', updated_at = NOW() WHERE id = ?");
        $stmt->execute([$stepId]);
    }

    public function getStepsForResponsible($userId)
    {
        // Only show steps for the responsible's department
        $stmt = $this->db->prepare("
            SELECT rs.*, dr.category_id, c.name AS category_name, dr.uploaded_by_user_id, u.username, dr.filename AS initial_filename
            FROM request_steps rs
            JOIN document_requests dr ON rs.request_id = dr.id
            JOIN categories c ON dr.category_id = c.id
            JOIN users u ON dr.uploaded_by_user_id = u.id
            JOIN categories rc ON rs.department_category_id = rc.id
            WHERE rc.responsible_user_id = ? AND rs.status = 'waiting_responsible'
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function approveStepAndMove($stepId)
    {
        // Get step and related request
        $stmt = $this->db->prepare("
            SELECT rs.*, dr.filename AS initial_filename, dr.uploaded_by_user_id, dr.category_id, dr.id AS request_id, dr.access_code, dr.document_type
            FROM request_steps rs
            JOIN document_requests dr ON rs.request_id = dr.id
            WHERE rs.id = ?
        ");
        $stmt->execute([$stepId]);
        $step = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$step) return false;

        // Special case: if this is 'Заявление за студентски права' step, after approval, create payment step for 'Сесия'
        if ($step['required_document'] === 'Заявление за студентски права' && $step['document_type'] === 'Заявление за поправка') {
            // Mark this step as approved
            $this->db->prepare("UPDATE request_steps SET status = 'approved', updated_at = NOW() WHERE id = ?")
                ->execute([$stepId]);
            // Create payment step for 'Сесия' (category_id = 4)
            $nextOrder = $step['step_order'] + 1;
            $this->create($step['request_id'], $nextOrder, 'Платежно за такса');
            $this->db->prepare("UPDATE request_steps SET status = 'waiting_user' WHERE request_id = ? AND step_order = ?")
                ->execute([$step['request_id'], $nextOrder]);
            return true;
        }

        // 1. Move the answer file to documents with a NEW access_code
        $newAccessCode = bin2hex(random_bytes(8));
        $stmt = $this->db->prepare("
            INSERT INTO documents (user_id, filename, category_id, access_code, created_at, status)
            VALUES (?, ?, ?, ?, NOW(), 'new')
        ");
        $stmt->execute([
            $step['uploaded_by_user_id'],
            $step['uploaded_file'],
            $step['category_id'],
            $newAccessCode
        ]);
        $answerDocId = $this->db->lastInsertId();

        // Log: answer file added
        $this->logAction($step['uploaded_by_user_id'], $answerDocId, 'answer_uploaded_and_approved');

        // 2. Move the initial document to documents with the ORIGINAL access_code, only if not already present
        $stmt = $this->db->prepare("SELECT id FROM documents WHERE access_code = ?");
        $stmt->execute([$step['access_code']]);
        $existingDocId = $stmt->fetchColumn();

        if (!$existingDocId) {
            $stmt = $this->db->prepare("
                INSERT INTO documents (user_id, filename, category_id, access_code, created_at, status)
                VALUES (?, ?, ?, ?, NOW(), 'new')
            ");
            $stmt->execute([
                $step['uploaded_by_user_id'],
                $step['initial_filename'],
                $step['category_id'],
                $step['access_code']
            ]);
            $initialDocId = $this->db->lastInsertId();

            // Log: initial file added
            $this->logAction($step['uploaded_by_user_id'], $initialDocId, 'initial_uploaded_and_approved');
        } else {
            $initialDocId = $existingDocId;
        }

        $stmt = $this->db->prepare("DELETE FROM request_steps WHERE id = ?");
        $stmt->execute([$stepId]);
        
        $this->logAction($step['uploaded_by_user_id'], $answerDocId, 'request_step_deleted');
        
        $this->db->prepare("DELETE FROM request_steps WHERE request_id = ?")->execute([$step['request_id']]);
        
        $stmt = $this->db->prepare("DELETE FROM document_requests WHERE id = ?");
        $stmt->execute([$step['request_id']]);
        
        $this->logAction($step['uploaded_by_user_id'], $initialDocId, 'document_request_deleted');

        return true;
    }

    // Add this helper method to the class:
    private function logAction($userId, $documentId, $action)
    {
        $stmt = $this->db->prepare("
            INSERT INTO access_logs (document_id, user_id, action, accessed_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$documentId, $userId, $action]);
    }
}
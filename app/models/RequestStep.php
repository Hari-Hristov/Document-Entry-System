<?php

class RequestStep
{
    private PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function create($requestId, $stepOrder, $requiredDocument)
    {
        $stmt = $this->db->prepare("INSERT INTO request_steps (request_id, step_order, required_document) VALUES (?, ?, ?)");
        $stmt->execute([$requestId, $stepOrder, $requiredDocument]);
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
        $stmt = $this->db->prepare("
            SELECT rs.*, dr.category_id, c.name AS category_name, dr.uploaded_by_user_id, u.username, dr.filename AS initial_filename
            FROM request_steps rs
            JOIN document_requests dr ON rs.request_id = dr.id
            JOIN categories c ON dr.category_id = c.id
            JOIN users u ON dr.uploaded_by_user_id = u.id
            WHERE c.responsible_user_id = ? AND rs.status = 'waiting_responsible'
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function approveStepAndMove($stepId)
    {
        // Get step and related request
        $stmt = $this->db->prepare("
            SELECT rs.*, dr.filename AS initial_filename, dr.uploaded_by_user_id, dr.category_id, dr.id AS request_id, dr.access_code
            FROM request_steps rs
            JOIN document_requests dr ON rs.request_id = dr.id
            WHERE rs.id = ?
        ");
        $stmt->execute([$stepId]);
        $step = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$step) return false;

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

        // 3. Delete the step
        $stmt = $this->db->prepare("DELETE FROM request_steps WHERE id = ?");
        $stmt->execute([$stepId]);
        // Log: step deleted
        $this->logAction($step['uploaded_by_user_id'], $answerDocId, 'request_step_deleted');

        // 4. Delete the document_request
        $stmt = $this->db->prepare("DELETE FROM document_requests WHERE id = ?");
        $stmt->execute([$step['request_id']]);
        // Log: request deleted
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
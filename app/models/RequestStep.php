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
            SELECT rs.*, dr.category_id
            FROM request_steps rs
            JOIN document_requests dr ON rs.request_id = dr.id
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

    public function approveStep($stepId)
    {
        $stmt = $this->db->prepare("UPDATE request_steps SET status = 'approved', updated_at = NOW() WHERE id = ?");
        $stmt->execute([$stepId]);
    }

    public function rejectStep($stepId)
    {
        $stmt = $this->db->prepare("UPDATE request_steps SET status = 'rejected', updated_at = NOW() WHERE id = ?");
        $stmt->execute([$stepId]);
    }
}
<?php

class RequiredDocument
{
    private PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function getRequiredByCategoryAndType($categoryId, $documentType)
    {
        $stmt = $this->db->prepare("SELECT * FROM required_documents WHERE category_id = ? AND document_type = ?");
        $stmt->execute([$categoryId, $documentType]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getAllByCategoryAndType($categoryId, $documentType)
    {
        $stmt = $this->db->prepare("SELECT required_document FROM required_documents WHERE category_id = ? AND document_type = ?");
        $stmt->execute([$categoryId, $documentType]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
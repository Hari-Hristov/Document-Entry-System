<?php

require_once 'Model.php';

class Document extends Model {
    public function create($filename, $category_id, $access_code) {
        $sql = "INSERT INTO documents (filename, category_id, access_code, created_at, status)
                VALUES (:filename, :category_id, :access_code, NOW(), 'new')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':filename' => $filename,
            ':category_id' => $category_id,
            ':access_code' => $access_code
        ]);
        return $this->db->lastInsertId();
    }

    public function getById($id) {
        $sql = "SELECT * FROM documents WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getByAccessCode($code) {
        $sql = "SELECT * FROM documents WHERE access_code = :code";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':code' => $code]);
        return $stmt->fetch();
    }
}
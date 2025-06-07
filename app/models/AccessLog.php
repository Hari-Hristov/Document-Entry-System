<?php

require_once 'Model.php';

class AccessLog extends Model {

    public function log($document_id, $action, $user_id = null) {
        $stmt = $this->db->prepare("
            INSERT INTO access_logs (document_id, action, user_id, accessed_at)
            VALUES (:doc, :action, :uid, NOW())
        ");
        $stmt->execute([
            ':doc' => $document_id,
            ':action' => $action,
            ':uid' => $user_id
        ]);
    }

    public function getByDocument($document_id) {
        $stmt = $this->db->prepare("SELECT * FROM access_logs WHERE document_id = :doc ORDER BY accessed_at DESC");
        $stmt->execute([':doc' => $document_id]);
        return $stmt->fetchAll();
    }
}
<?php
class Document
{
    protected $db;

    public function __construct($db = null)
    {
        // Allow dependency injection or use global $pdo
        $this->db = $db ?: (isset($GLOBALS['pdo']) ? $GLOBALS['pdo'] : null);
    }

    public function create($filename, $category_id, $access_code, $user_id)
    {
        $sql = "INSERT INTO documents (user_id, filename, category_id, access_code, created_at, status)
                VALUES (:user_id, :filename, :category_id, :access_code, NOW(), 'new')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':filename' => $filename,
            ':category_id' => $category_id,
            ':access_code' => $access_code
        ]);
        return $this->db->lastInsertId();
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM documents WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getByAccessCode($code)
    {
        $sql = "SELECT * FROM documents WHERE access_code = :code";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':code' => $code]);
        return $stmt->fetch();
    }

    public static function allWithCategories(): array
    {
        global $pdo;
        $stmt = $pdo->query("
        SELECT d.*, c.name as category_name 
        FROM documents d 
        LEFT JOIN categories c ON d.category_id = c.id
        ORDER BY d.created_at DESC
    ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function updateStatus(int $id, string $newStatus): void
    {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE documents SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $id]);
    }

    public static function toggleFlag(int $id, string $flag): void
    {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE documents SET $flag = NOT $flag WHERE id = ?");
        $stmt->execute([$id]);
    }
}
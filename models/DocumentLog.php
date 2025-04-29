<?php
/**
 * DocumentLog model for handling document action logs
 */
class DocumentLog {
    private $conn;
    private $table = 'document_logs';
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Get all logs for a specific document
     * 
     * @param int $documentId Document ID
     * @return array Array of logs
     */
    public function getDocumentLogs($documentId) {
        $query = "SELECT dl.*, u.name as user_name
                 FROM " . $this->table . " dl
                 LEFT JOIN users u ON dl.user_id = u.id
                 WHERE dl.document_id = :document_id
                 ORDER BY dl.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':document_id', $documentId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Log a document action
     * 
     * @param int $documentId Document ID
     * @param int $userId User ID who performed the action
     * @param string $action Action type (from constants)
     * @param string $comments Comments about the action
     * @return int|false ID of the new log entry or false on failure
     */
    public function logDocumentAction($documentId, $userId, $action, $comments = '') {
        $query = "INSERT INTO " . $this->table . " 
                 (document_id, user_id, action, comments, created_at) 
                 VALUES 
                 (:document_id, :user_id, :action, :comments, NOW())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':document_id', $documentId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':comments', $comments);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Get a log entry by ID
     * 
     * @param int $id Log ID
     * @return array|false Log data or false if not found
     */
    public function getLogById($id) {
        $query = "SELECT dl.*, u.name as user_name
                 FROM " . $this->table . " dl
                 LEFT JOIN users u ON dl.user_id = u.id
                 WHERE dl.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get recent logs across all documents
     * 
     * @param int $limit Number of logs to return
     * @return array Array of logs
     */
    public function getRecentLogs($limit = 10) {
        $query = "SELECT dl.*, u.name as user_name, d.title as document_title
                 FROM " . $this->table . " dl
                 LEFT JOIN users u ON dl.user_id = u.id
                 LEFT JOIN documents d ON dl.document_id = d.id
                 ORDER BY dl.created_at DESC
                 LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get logs by user
     * 
     * @param int $userId User ID
     * @param int $limit Number of logs to return
     * @return array Array of logs
     */
    public function getUserLogs($userId, $limit = 10) {
        $query = "SELECT dl.*, u.name as user_name, d.title as document_title
                 FROM " . $this->table . " dl
                 LEFT JOIN users u ON dl.user_id = u.id
                 LEFT JOIN documents d ON dl.document_id = d.id
                 WHERE dl.user_id = :user_id
                 ORDER BY dl.created_at DESC
                 LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get logs for documents in a specific department
     * 
     * @param int $departmentId Department ID
     * @param int $limit Number of logs to return
     * @return array Array of logs
     */
    public function getDepartmentLogs($departmentId, $limit = 10) {
        $query = "SELECT dl.*, u.name as user_name, d.title as document_title
                 FROM " . $this->table . " dl
                 LEFT JOIN users u ON dl.user_id = u.id
                 LEFT JOIN documents d ON dl.document_id = d.id
                 WHERE d.current_department_id = :department_id
                 ORDER BY dl.created_at DESC
                 LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':department_id', $departmentId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Delete logs for a specific document
     * 
     * @param int $documentId Document ID
     * @return bool Success or failure
     */
    public function deleteDocumentLogs($documentId) {
        $query = "DELETE FROM " . $this->table . " WHERE document_id = :document_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':document_id', $documentId);
        
        return $stmt->execute();
    }
}
?>

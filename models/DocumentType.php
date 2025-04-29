<?php
/**
 * DocumentType model for handling document type data
 */
class DocumentType {
    private $conn;
    private $table = 'document_types';
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Get all document types
     * 
     * @return array Array of document types
     */
    public function getAllDocumentTypes() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get a document type by ID
     * 
     * @param int $id Document type ID
     * @return array|false Document type data or false if not found
     */
    public function getDocumentTypeById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get a document type by code
     * 
     * @param string $code Document type code
     * @return array|false Document type data or false if not found
     */
    public function getDocumentTypeByCode($code) {
        $query = "SELECT * FROM " . $this->table . " WHERE code = :code";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Create a new document type
     * 
     * @param string $name Document type name
     * @param string $code Document type code
     * @param string $description Document type description
     * @return int|false ID of the new document type or false on failure
     */
    public function createDocumentType($name, $code, $description = '') {
        $query = "INSERT INTO " . $this->table . " 
                 (name, code, description, created_at) 
                 VALUES 
                 (:name, :code, :description, NOW())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':description', $description);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update an existing document type
     * 
     * @param int $id Document type ID
     * @param string $name Document type name
     * @param string $code Document type code
     * @param string $description Document type description
     * @return bool Success or failure
     */
    public function updateDocumentType($id, $name, $code, $description = '') {
        $query = "UPDATE " . $this->table . " 
                 SET name = :name, code = :code, description = :description, updated_at = NOW() 
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':description', $description);
        
        return $stmt->execute();
    }
    
    /**
     * Delete a document type
     * 
     * @param int $id Document type ID
     * @return bool Success or failure
     */
    public function deleteDocumentType($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Get total number of document types
     * 
     * @return int Total number of document types
     */
    public function getTotalDocumentTypes() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result['total'];
    }
}
?>

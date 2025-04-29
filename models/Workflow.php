<?php
/**
 * Workflow model for handling workflow data
 */
class Workflow {
    private $conn;
    private $table = 'workflows';
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Get all workflows with document type information
     * 
     * @return array Array of workflows
     */
    public function getAllWorkflows() {
        $query = "SELECT w.*, dt.name as document_type_name 
                 FROM " . $this->table . " w
                 LEFT JOIN document_types dt ON w.document_type_id = dt.id
                 ORDER BY w.name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get a workflow by ID
     * 
     * @param int $id Workflow ID
     * @return array|false Workflow data or false if not found
     */
    public function getWorkflowById($id) {
        $query = "SELECT w.*, dt.name as document_type_name 
                 FROM " . $this->table . " w
                 LEFT JOIN document_types dt ON w.document_type_id = dt.id
                 WHERE w.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get workflows by document type
     * 
     * @param int $documentTypeId Document type ID
     * @return array Array of workflows
     */
    public function getWorkflowsByDocumentType($documentTypeId) {
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE document_type_id = :document_type_id
                 ORDER BY name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':document_type_id', $documentTypeId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Create a new workflow
     * 
     * @param string $name Workflow name
     * @param string $description Workflow description
     * @param int $documentTypeId Document type ID
     * @return int|false ID of the new workflow or false on failure
     */
    public function createWorkflow($name, $description, $documentTypeId) {
        $query = "INSERT INTO " . $this->table . " 
                 (name, description, document_type_id, created_at) 
                 VALUES 
                 (:name, :description, :document_type_id, NOW())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':document_type_id', $documentTypeId);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update an existing workflow
     * 
     * @param int $id Workflow ID
     * @param string $name Workflow name
     * @param string $description Workflow description
     * @param int $documentTypeId Document type ID
     * @return bool Success or failure
     */
    public function updateWorkflow($id, $name, $description, $documentTypeId) {
        $query = "UPDATE " . $this->table . " 
                 SET name = :name, description = :description, document_type_id = :document_type_id, 
                 updated_at = NOW() 
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':document_type_id', $documentTypeId);
        
        return $stmt->execute();
    }
    
    /**
     * Delete a workflow
     * 
     * @param int $id Workflow ID
     * @return bool Success or failure
     */
    public function deleteWorkflow($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Get documents using a specific workflow
     * 
     * @param int $workflowId Workflow ID
     * @return array Array of documents
     */
    public function getDocumentsUsingWorkflow($workflowId) {
        $query = "SELECT d.* FROM documents d
                 WHERE d.workflow_id = :workflow_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':workflow_id', $workflowId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get total number of workflows
     * 
     * @return int Total number of workflows
     */
    public function getTotalWorkflows() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result['total'];
    }
}
?>

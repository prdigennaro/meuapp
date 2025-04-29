<?php
/**
 * Template model for handling document template data
 */
class Template {
    private $conn;
    private $table = 'templates';
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Get all templates with document type information
     * 
     * @return array Array of templates
     */
    public function getAllTemplates() {
        $query = "SELECT t.*, dt.name as document_type_name 
                 FROM " . $this->table . " t
                 LEFT JOIN document_types dt ON t.document_type_id = dt.id
                 ORDER BY t.name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get a template by ID
     * 
     * @param int $id Template ID
     * @return array|false Template data or false if not found
     */
    public function getTemplateById($id) {
        $query = "SELECT t.*, dt.name as document_type_name 
                 FROM " . $this->table . " t
                 LEFT JOIN document_types dt ON t.document_type_id = dt.id
                 WHERE t.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get templates grouped by document types
     * 
     * @return array Array of templates grouped by document type
     */
    public function getTemplatesByDocumentTypes() {
        $query = "SELECT t.*, dt.name as document_type_name, dt.id as document_type_id 
                 FROM " . $this->table . " t
                 LEFT JOIN document_types dt ON t.document_type_id = dt.id
                 ORDER BY dt.name ASC, t.name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $templates = $stmt->fetchAll();
        $grouped = [];
        
        foreach ($templates as $template) {
            $docTypeId = $template['document_type_id'];
            $docTypeName = $template['document_type_name'];
            
            if (!isset($grouped[$docTypeId])) {
                $grouped[$docTypeId] = [
                    'name' => $docTypeName,
                    'templates' => []
                ];
            }
            
            $grouped[$docTypeId]['templates'][] = $template;
        }
        
        return $grouped;
    }
    
    /**
     * Get templates by document type
     * 
     * @param int $documentTypeId Document type ID
     * @return array Array of templates
     */
    public function getTemplatesByDocumentType($documentTypeId) {
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE document_type_id = :document_type_id
                 ORDER BY name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':document_type_id', $documentTypeId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Create a new template
     * 
     * @param string $name Template name
     * @param string $description Template description
     * @param int $documentTypeId Document type ID
     * @param string $content Template content
     * @param string $variables JSON string of variables
     * @return int|false ID of the new template or false on failure
     */
    public function createTemplate($name, $description, $documentTypeId, $content, $variables) {
        $query = "INSERT INTO " . $this->table . " 
                 (name, description, document_type_id, content, variables, created_at) 
                 VALUES 
                 (:name, :description, :document_type_id, :content, :variables, NOW())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':document_type_id', $documentTypeId);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':variables', $variables);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update an existing template
     * 
     * @param int $id Template ID
     * @param string $name Template name
     * @param string $description Template description
     * @param int $documentTypeId Document type ID
     * @param string $content Template content
     * @param string $variables JSON string of variables
     * @return bool Success or failure
     */
    public function updateTemplate($id, $name, $description, $documentTypeId, $content, $variables) {
        $query = "UPDATE " . $this->table . " 
                 SET name = :name, description = :description, document_type_id = :document_type_id, 
                 content = :content, variables = :variables, updated_at = NOW() 
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':document_type_id', $documentTypeId);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':variables', $variables);
        
        return $stmt->execute();
    }
    
    /**
     * Delete a template
     * 
     * @param int $id Template ID
     * @return bool Success or failure
     */
    public function deleteTemplate($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Get documents using a specific template
     * 
     * @param int $templateId Template ID
     * @return array Array of documents
     */
    public function getDocumentsUsingTemplate($templateId) {
        $query = "SELECT d.* FROM documents d
                 WHERE d.template_id = :template_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':template_id', $templateId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get total number of templates
     * 
     * @return int Total number of templates
     */
    public function getTotalTemplates() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result['total'];
    }
}
?>

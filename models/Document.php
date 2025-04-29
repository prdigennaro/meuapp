<?php
/**
 * Document model for handling document data
 */
class Document {
    private $conn;
    private $table = 'documents';
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Get all documents with related information
     * 
     * @return array Array of documents
     */
    public function getAllDocuments() {
        $query = "SELECT d.*, 
                 dt.name as document_type_name, 
                 t.name as template_name,
                 u.name as created_by_name,
                 dep.name as current_department_name
                 FROM " . $this->table . " d
                 LEFT JOIN document_types dt ON d.document_type_id = dt.id
                 LEFT JOIN templates t ON d.template_id = t.id
                 LEFT JOIN users u ON d.created_by = u.id
                 LEFT JOIN departments dep ON d.current_department_id = dep.id
                 ORDER BY d.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get a document by ID with related information
     * 
     * @param int $id Document ID
     * @return array|false Document data or false if not found
     */
    public function getDocumentById($id) {
        $query = "SELECT d.*, 
                 dt.name as document_type_name, 
                 t.name as template_name,
                 u.name as created_by_name,
                 dep.name as current_department_name,
                 w.name as workflow_name
                 FROM " . $this->table . " d
                 LEFT JOIN document_types dt ON d.document_type_id = dt.id
                 LEFT JOIN templates t ON d.template_id = t.id
                 LEFT JOIN users u ON d.created_by = u.id
                 LEFT JOIN departments dep ON d.current_department_id = dep.id
                 LEFT JOIN workflows w ON d.workflow_id = w.id
                 WHERE d.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get documents by type
     * 
     * @param int $typeId Document type ID
     * @return array Array of documents
     */
    public function getDocumentsByType($typeId) {
        $query = "SELECT d.*, 
                 dt.name as document_type_name, 
                 u.name as created_by_name,
                 dep.name as current_department_name
                 FROM " . $this->table . " d
                 LEFT JOIN document_types dt ON d.document_type_id = dt.id
                 LEFT JOIN users u ON d.created_by = u.id
                 LEFT JOIN departments dep ON d.current_department_id = dep.id
                 WHERE d.document_type_id = :type_id
                 ORDER BY d.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':type_id', $typeId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get documents created by a specific user
     * 
     * @param int $userId User ID
     * @return array Array of documents
     */
    public function getDocumentsByUser($userId) {
        $query = "SELECT d.*, 
                 dt.name as document_type_name, 
                 u.name as created_by_name,
                 dep.name as current_department_name
                 FROM " . $this->table . " d
                 LEFT JOIN document_types dt ON d.document_type_id = dt.id
                 LEFT JOIN users u ON d.created_by = u.id
                 LEFT JOIN departments dep ON d.current_department_id = dep.id
                 WHERE d.created_by = :user_id
                 ORDER BY d.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get documents assigned to a specific department
     * 
     * @param int $departmentId Department ID
     * @return array Array of documents
     */
    public function getDocumentsByDepartment($departmentId) {
        $query = "SELECT d.*, 
                 dt.name as document_type_name, 
                 u.name as created_by_name,
                 dep.name as current_department_name
                 FROM " . $this->table . " d
                 LEFT JOIN document_types dt ON d.document_type_id = dt.id
                 LEFT JOIN users u ON d.created_by = u.id
                 LEFT JOIN departments dep ON d.current_department_id = dep.id
                 WHERE d.current_department_id = :department_id
                 ORDER BY d.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':department_id', $departmentId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Create a new document
     * 
     * @param array $data Document data (title, description, document_type_id, template_id, etc.)
     * @return int|false ID of the new document or false on failure
     */
    public function createDocument($data) {
        // Prepare data
        $title = $data['title'];
        $description = $data['description'] ?? '';
        $documentTypeId = $data['document_type_id'];
        $templateId = $data['template_id'] ?? null;
        $content = $data['content'] ?? '';
        $protocolNumber = $data['protocol_number'];
        $status = $data['status'] ?? DOC_STATUS_DRAFT;
        $createdBy = $data['created_by'];
        $departmentId = $data['current_department_id'];
        $workflowId = $data['workflow_id'] ?? null;
        $currentStepId = $data['current_step_id'] ?? null;
        
        // Convert variables to JSON if not already
        if (isset($data['variables']) && !is_string($data['variables'])) {
            $variables = json_encode($data['variables']);
        } else {
            $variables = $data['variables'] ?? '{}';
        }
        
        $query = "INSERT INTO " . $this->table . " 
                 (title, description, document_type_id, template_id, content, 
                 protocol_number, status, variables, created_by, current_department_id, 
                 workflow_id, current_step_id, created_at) 
                 VALUES 
                 (:title, :description, :document_type_id, :template_id, :content, 
                 :protocol_number, :status, :variables, :created_by, :current_department_id, 
                 :workflow_id, :current_step_id, NOW())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':document_type_id', $documentTypeId);
        $stmt->bindParam(':template_id', $templateId);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':protocol_number', $protocolNumber);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':variables', $variables);
        $stmt->bindParam(':created_by', $createdBy);
        $stmt->bindParam(':current_department_id', $departmentId);
        $stmt->bindParam(':workflow_id', $workflowId);
        $stmt->bindParam(':current_step_id', $currentStepId);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update document content
     * 
     * @param int $id Document ID
     * @param string $content Document content
     * @return bool Success or failure
     */
    public function updateDocumentContent($id, $content) {
        $query = "UPDATE " . $this->table . " 
                 SET content = :content, updated_at = NOW() 
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':content', $content);
        
        return $stmt->execute();
    }
    
    /**
     * Update document status
     * 
     * @param int $id Document ID
     * @param string $status New status
     * @return bool Success or failure
     */
    public function updateDocumentStatus($id, $status) {
        $query = "UPDATE " . $this->table . " 
                 SET status = :status, updated_at = NOW() 
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':status', $status);
        
        return $stmt->execute();
    }
    
    /**
     * Update document department
     * 
     * @param int $id Document ID
     * @param int $departmentId New department ID
     * @return bool Success or failure
     */
    public function updateDocumentDepartment($id, $departmentId) {
        $query = "UPDATE " . $this->table . " 
                 SET current_department_id = :department_id, updated_at = NOW() 
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':department_id', $departmentId);
        
        return $stmt->execute();
    }
    
    /**
     * Start a workflow for a document
     * 
     * @param int $id Document ID
     * @param int $workflowId Workflow ID
     * @param int $departmentId Department ID for the first step
     * @return bool Success or failure
     */
    public function startWorkflow($id, $workflowId, $departmentId) {
        $query = "UPDATE " . $this->table . " 
                 SET workflow_id = :workflow_id, current_department_id = :department_id, 
                 status = :status, updated_at = NOW() 
                 WHERE id = :id";
        
        $status = DOC_STATUS_IN_REVIEW;
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':workflow_id', $workflowId);
        $stmt->bindParam(':department_id', $departmentId);
        $stmt->bindParam(':status', $status);
        
        return $stmt->execute();
    }
    
    /**
     * Get document count for a specific year (for protocol numbers)
     * 
     * @param string $year Year in format specified by PROTOCOL_DATE_FORMAT
     * @return int Count of documents for that year
     */
    public function getDocumentCountForYear($year) {
        $query = "SELECT COUNT(*) as count 
                 FROM " . $this->table . " 
                 WHERE protocol_number LIKE :year_pattern";
        
        $yearPattern = PROTOCOL_PREFIX . $year . '-%';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year_pattern', $yearPattern);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    /**
     * Get total number of documents
     * 
     * @return int Total number of documents
     */
    public function getTotalDocuments() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result['total'];
    }
    
    /**
     * Get recent documents with related information
     * 
     * @param int $limit Number of documents to return
     * @return array Array of documents
     */
    public function getRecentDocuments($limit = 10) {
        $query = "SELECT d.*, 
                 dt.name as document_type_name, 
                 u.name as created_by_name,
                 dep.name as current_department_name
                 FROM " . $this->table . " d
                 LEFT JOIN document_types dt ON d.document_type_id = dt.id
                 LEFT JOIN users u ON d.created_by = u.id
                 LEFT JOIN departments dep ON d.current_department_id = dep.id
                 ORDER BY d.created_at DESC
                 LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get document counts by status
     * 
     * @return array Array of status counts
     */
    public function getDocumentCountsByStatus() {
        $query = "SELECT status, COUNT(*) as count 
                 FROM " . $this->table . " 
                 GROUP BY status";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $results = $stmt->fetchAll();
        $counts = [];
        
        foreach ($results as $row) {
            $counts[$row['status']] = $row['count'];
        }
        
        return $counts;
    }
    
    /**
     * Get document counts by type
     * 
     * @return array Array of type counts
     */
    public function getDocumentCountsByType() {
        $query = "SELECT dt.name as type_name, COUNT(*) as count 
                 FROM " . $this->table . " d
                 LEFT JOIN document_types dt ON d.document_type_id = dt.id
                 GROUP BY d.document_type_id, dt.name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $results = $stmt->fetchAll();
        $counts = [];
        
        foreach ($results as $row) {
            $counts[$row['type_name']] = $row['count'];
        }
        
        return $counts;
    }
    
    /**
     * Get count of documents created by a specific user
     * 
     * @param int $userId User ID
     * @return int Count of documents
     */
    public function getUserDocumentCount($userId) {
        $query = "SELECT COUNT(*) as count 
                 FROM " . $this->table . " 
                 WHERE created_by = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    /**
     * Get count of pending documents for a specific user
     * 
     * @param int $userId User ID
     * @return int Count of pending documents
     */
    public function getPendingDocumentCount($userId) {
        $query = "SELECT COUNT(*) as count 
                 FROM " . $this->table . " 
                 WHERE created_by = :user_id 
                 AND status IN (:status1, :status2)";
        
        $status1 = DOC_STATUS_DRAFT;
        $status2 = DOC_STATUS_IN_REVIEW;
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':status1', $status1);
        $stmt->bindParam(':status2', $status2);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    /**
     * Get count of documents for a specific department
     * 
     * @param int $departmentId Department ID
     * @return int Count of documents
     */
    public function getDepartmentDocumentCount($departmentId) {
        $query = "SELECT COUNT(*) as count 
                 FROM " . $this->table . " 
                 WHERE current_department_id = :department_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':department_id', $departmentId);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    /**
     * Get recent documents created by a specific user
     * 
     * @param int $userId User ID
     * @param int $limit Number of documents to return
     * @return array Array of documents
     */
    public function getRecentUserDocuments($userId, $limit = 5) {
        $query = "SELECT d.*, 
                 dt.name as document_type_name, 
                 u.name as created_by_name,
                 dep.name as current_department_name
                 FROM " . $this->table . " d
                 LEFT JOIN document_types dt ON d.document_type_id = dt.id
                 LEFT JOIN users u ON d.created_by = u.id
                 LEFT JOIN departments dep ON d.current_department_id = dep.id
                 WHERE d.created_by = :user_id
                 ORDER BY d.created_at DESC
                 LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get recent documents for a specific department
     * 
     * @param int $departmentId Department ID
     * @param int $limit Number of documents to return
     * @return array Array of documents
     */
    public function getRecentDepartmentDocuments($departmentId, $limit = 5) {
        $query = "SELECT d.*, 
                 dt.name as document_type_name, 
                 u.name as created_by_name,
                 dep.name as current_department_name
                 FROM " . $this->table . " d
                 LEFT JOIN document_types dt ON d.document_type_id = dt.id
                 LEFT JOIN users u ON d.created_by = u.id
                 LEFT JOIN departments dep ON d.current_department_id = dep.id
                 WHERE d.current_department_id = :department_id
                 ORDER BY d.created_at DESC
                 LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':department_id', $departmentId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get document counts by status for a specific user
     * 
     * @param int $userId User ID
     * @return array Array of status counts
     */
    public function getUserDocumentCountsByStatus($userId) {
        $query = "SELECT status, COUNT(*) as count 
                 FROM " . $this->table . " 
                 WHERE created_by = :user_id
                 GROUP BY status";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $results = $stmt->fetchAll();
        $counts = [];
        
        foreach ($results as $row) {
            $counts[$row['status']] = $row['count'];
        }
        
        return $counts;
    }
    
    /**
     * Get document counts by type for a specific user
     * 
     * @param int $userId User ID
     * @return array Array of type counts
     */
    public function getUserDocumentCountsByType($userId) {
        $query = "SELECT dt.name as type_name, COUNT(*) as count 
                 FROM " . $this->table . " d
                 LEFT JOIN document_types dt ON d.document_type_id = dt.id
                 WHERE d.created_by = :user_id
                 GROUP BY d.document_type_id, dt.name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $results = $stmt->fetchAll();
        $counts = [];
        
        foreach ($results as $row) {
            $counts[$row['type_name']] = $row['count'];
        }
        
        return $counts;
    }
}
?>

<?php
/**
 * Department model for handling department data
 */
class Department {
    private $conn;
    private $table = 'departments';
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Get all departments
     * 
     * @return array Array of departments
     */
    public function getAllDepartments() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get a department by ID
     * 
     * @param int $id Department ID
     * @return array|false Department data or false if not found
     */
    public function getDepartmentById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Create a new department
     * 
     * @param string $name Department name
     * @param string $description Department description
     * @return int|false ID of the new department or false on failure
     */
    public function createDepartment($name, $description = '') {
        $query = "INSERT INTO " . $this->table . " 
                 (name, description, created_at) 
                 VALUES 
                 (:name, :description, NOW())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update an existing department
     * 
     * @param int $id Department ID
     * @param string $name Department name
     * @param string $description Department description
     * @return bool Success or failure
     */
    public function updateDepartment($id, $name, $description = '') {
        $query = "UPDATE " . $this->table . " 
                 SET name = :name, description = :description, updated_at = NOW() 
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        
        return $stmt->execute();
    }
    
    /**
     * Delete a department
     * 
     * @param int $id Department ID
     * @return bool Success or failure
     */
    public function deleteDepartment($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Get total number of departments
     * 
     * @return int Total number of departments
     */
    public function getTotalDepartments() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result['total'];
    }
    
    /**
     * Get users from a specific department
     * 
     * @param int $departmentId Department ID
     * @return array Array of users
     */
    public function getDepartmentUsers($departmentId) {
        $query = "SELECT u.* FROM users u
                 WHERE u.department_id = :department_id
                 ORDER BY u.name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':department_id', $departmentId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Check if a department has any users
     * 
     * @param int $departmentId Department ID
     * @return bool True if department has users, false otherwise
     */
    public function hasDepartmentUsers($departmentId) {
        $query = "SELECT COUNT(*) as count FROM users
                 WHERE department_id = :department_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':department_id', $departmentId);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    /**
     * Check if a department has any workflow steps
     * 
     * @param int $departmentId Department ID
     * @return bool True if department has workflow steps, false otherwise
     */
    public function hasDepartmentWorkflowSteps($departmentId) {
        $query = "SELECT COUNT(*) as count FROM workflow_steps
                 WHERE department_id = :department_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':department_id', $departmentId);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    /**
     * Check if a department has any documents
     * 
     * @param int $departmentId Department ID
     * @return bool True if department has documents, false otherwise
     */
    public function hasDepartmentDocuments($departmentId) {
        $query = "SELECT COUNT(*) as count FROM documents
                 WHERE current_department_id = :department_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':department_id', $departmentId);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
}
?>

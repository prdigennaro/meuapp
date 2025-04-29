<?php
/**
 * User model for handling user data
 */
class User {
    private $conn;
    private $table = 'users';
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Get all users with department information
     * 
     * @return array Array of users
     */
    public function getAllUsers() {
        $query = "SELECT u.*, d.name as department_name 
                 FROM " . $this->table . " u
                 LEFT JOIN departments d ON u.department_id = d.id
                 ORDER BY u.name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get a user by ID
     * 
     * @param int $id User ID
     * @return array|false User data or false if not found
     */
    public function getUserById($id) {
        $query = "SELECT u.*, d.name as department_name 
                 FROM " . $this->table . " u
                 LEFT JOIN departments d ON u.department_id = d.id
                 WHERE u.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get a user by email
     * 
     * @param string $email User email
     * @return array|false User data or false if not found
     */
    public function getUserByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Create a new user
     * 
     * @param string $name User name
     * @param string $email User email
     * @param string $password User password (will be hashed)
     * @param string $role User role (admin or user)
     * @param int $departmentId Department ID
     * @return int|false ID of the new user or false on failure
     */
    public function createUser($name, $email, $password, $role, $departmentId) {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO " . $this->table . " 
                 (name, email, password, role, department_id, created_at) 
                 VALUES 
                 (:name, :email, :password, :role, :department_id, NOW())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':department_id', $departmentId);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update an existing user
     * 
     * @param int $id User ID
     * @param string $name User name
     * @param string $email User email
     * @param string|null $password User password (if provided, will be hashed)
     * @param string $role User role
     * @param int $departmentId Department ID
     * @return bool Success or failure
     */
    public function updateUser($id, $name, $email, $password, $role, $departmentId) {
        // If password is provided, update it as well
        if ($password) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $query = "UPDATE " . $this->table . " 
                     SET name = :name, email = :email, password = :password, 
                     role = :role, department_id = :department_id, updated_at = NOW() 
                     WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':password', $hashedPassword);
        } else {
            $query = "UPDATE " . $this->table . " 
                     SET name = :name, email = :email, role = :role, 
                     department_id = :department_id, updated_at = NOW() 
                     WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
        }
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':department_id', $departmentId);
        
        return $stmt->execute();
    }
    
    /**
     * Delete a user
     * 
     * @param int $id User ID
     * @return bool Success or failure
     */
    public function deleteUser($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Get users by department
     * 
     * @param int $departmentId Department ID
     * @return array Array of users
     */
    public function getUsersByDepartment($departmentId) {
        $query = "SELECT * FROM " . $this->table . " WHERE department_id = :department_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':department_id', $departmentId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get total number of users
     * 
     * @return int Total number of users
     */
    public function getTotalUsers() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result['total'];
    }
    
    /**
     * Verify user credentials
     * 
     * @param string $email User email
     * @param string $password Password to verify
     * @return array|false User data if credentials are valid, false otherwise
     */
    public function verifyCredentials($email, $password) {
        $user = $this->getUserByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
}
?>

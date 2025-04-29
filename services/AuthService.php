<?php
/**
 * AuthService - Handles user authentication
 */
class AuthService {
    private $conn;
    private $userModel;
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->userModel = new User($conn);
    }
    
    /**
     * Authenticate a user by email and password
     * 
     * @param string $email User email
     * @param string $password User password
     * @return array|false User data if authentication successful, false otherwise
     */
    public function login($email, $password) {
        return $this->userModel->verifyCredentials($email, $password);
    }
    
    /**
     * Log out a user by destroying the session
     * 
     * @return void
     */
    public function logout() {
        // Unset all session variables
        $_SESSION = [];
        
        // If it's desired to kill the session, also delete the session cookie.
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Finally, destroy the session.
        session_destroy();
    }
    
    /**
     * Check if user is logged in
     * 
     * @return bool True if user is logged in, false otherwise
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Check if user is an admin
     * 
     * @return bool True if user is an admin, false otherwise
     */
    public function isAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin';
    }
    
    /**
     * Get current user ID from session
     * 
     * @return int|null User ID if logged in, null otherwise
     */
    public function getCurrentUserId() {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }
    
    /**
     * Get current user data
     * 
     * @return array|null User data if logged in, null otherwise
     */
    public function getCurrentUser() {
        $userId = $this->getCurrentUserId();
        
        if ($userId) {
            return $this->userModel->getUserById($userId);
        }
        
        return null;
    }
    
    /**
     * Check if user has access to a specific department
     * 
     * @param int $departmentId Department ID
     * @return bool True if user belongs to department or is admin, false otherwise
     */
    public function hasAccessToDepartment($departmentId) {
        // Admins have access to all departments
        if ($this->isAdmin()) {
            return true;
        }
        
        // Check if user belongs to department
        return isset($_SESSION['user_department']) && $_SESSION['user_department'] == $departmentId;
    }
    
    /**
     * Register CSRF token in session
     * 
     * @return string Generated CSRF token
     */
    public function generateCsrfToken() {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }
    
    /**
     * Validate CSRF token
     * 
     * @param string $token Token to validate
     * @return bool True if token is valid, false otherwise
     */
    public function validateCsrfToken($token) {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        $valid = hash_equals($_SESSION['csrf_token'], $token);
        
        // Consume token after validation (one-time use)
        unset($_SESSION['csrf_token']);
        
        return $valid;
    }
}
?>

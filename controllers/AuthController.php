<?php
/**
 * AuthController - Handles user authentication and authorization
 */
class AuthController {
    private $conn;
    private $authService;
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->authService = new AuthService($conn);
    }
    
    /**
     * Display login form
     */
    public function showLogin() {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            $this->redirectToDashboard();
            return;
        }
        
        // Check for error or success message
        $error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : null;
        $success = isset($_SESSION['login_success']) ? $_SESSION['login_success'] : null;
        
        // Clear session messages
        unset($_SESSION['login_error']);
        unset($_SESSION['login_success']);
        
        include 'views/auth/login.php';
    }
    
    /**
     * Process login form submission
     */
    public function login() {
        // Validate form data
        if (empty($_POST['email']) || empty($_POST['password'])) {
            $_SESSION['login_error'] = 'E-mail e senha são obrigatórios.';
            header('Location: /login');
            exit;
        }
        
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        
        // Attempt to login
        $user = $this->authService->login($email, $password);
        
        if ($user) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_department'] = $user['department_id'];
            
            // Redirect to appropriate dashboard
            $this->redirectToDashboard();
        } else {
            $_SESSION['login_error'] = 'E-mail ou senha inválidos.';
            header('Location: /login');
            exit;
        }
    }
    
    /**
     * Log out the current user
     */
    public function logout() {
        // Clear all session variables
        $_SESSION = array();
        
        // Destroy the session
        session_destroy();
        
        // Redirect to login page
        $_SESSION['login_success'] = 'Você foi desconectado com sucesso.';
        header('Location: /login');
        exit;
    }
    
    /**
     * Show registration form (admin only functionality)
     */
    public function showRegister() {
        // Only admins can register new users
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
            header('Location: /login');
            exit;
        }
        
        $departmentModel = new Department($this->conn);
        $departments = $departmentModel->getAllDepartments();
        
        include 'views/auth/register.php';
    }
    
    /**
     * Process registration form submission
     */
    public function register() {
        // Only admins can register new users
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
            header('Location: /login');
            exit;
        }
        
        // Validate form data
        if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['department_id'])) {
            $_SESSION['register_error'] = 'Todos os campos são obrigatórios.';
            header('Location: /admin/users/add');
            exit;
        }
        
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $departmentId = filter_var($_POST['department_id'], FILTER_SANITIZE_NUMBER_INT);
        $role = isset($_POST['role']) && $_POST['role'] == 'admin' ? 'admin' : 'user';
        
        // Check if email already exists
        $userModel = new User($this->conn);
        if ($userModel->getUserByEmail($email)) {
            $_SESSION['register_error'] = 'Este e-mail já está em uso.';
            header('Location: /admin/users/add');
            exit;
        }
        
        // Create new user
        $userId = $userModel->createUser($name, $email, $password, $role, $departmentId);
        
        if ($userId) {
            $_SESSION['register_success'] = 'Usuário cadastrado com sucesso.';
            header('Location: /admin/users');
            exit;
        } else {
            $_SESSION['register_error'] = 'Erro ao cadastrar usuário. Tente novamente.';
            header('Location: /admin/users/add');
            exit;
        }
    }
    
    /**
     * Redirect to appropriate dashboard based on user role
     */
    private function redirectToDashboard() {
        if ($_SESSION['user_role'] == 'admin') {
            header('Location: /admin/dashboard');
        } else {
            header('Location: /user/dashboard');
        }
        exit;
    }
}
?>

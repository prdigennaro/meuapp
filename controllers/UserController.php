<?php
/**
 * UserController - Handles user management
 */
class UserController {
    private $conn;
    private $userModel;
    private $departmentModel;
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->userModel = new User($conn);
        $this->departmentModel = new Department($conn);
    }
    
    /**
     * Display all users (admin only)
     */
    public function showUsers() {
        // Get all users
        $users = $this->userModel->getAllUsers();
        
        // Get all departments for filter/add new user
        $departments = $this->departmentModel->getAllDepartments();
        
        // Process alert messages
        $success = isset($_SESSION['user_success']) ? $_SESSION['user_success'] : null;
        $error = isset($_SESSION['user_error']) ? $_SESSION['user_error'] : null;
        
        // Clear session messages
        unset($_SESSION['user_success']);
        unset($_SESSION['user_error']);
        
        include 'views/admin/users.php';
    }
    
    /**
     * Display form to add a new user
     */
    public function addUserForm() {
        // Get all departments for select box
        $departments = $this->departmentModel->getAllDepartments();
        
        include 'views/admin/users_add.php';
    }
    
    /**
     * Process user add form submission
     */
    public function addUser() {
        // Validate form data
        if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['department_id'])) {
            $_SESSION['user_error'] = 'Todos os campos são obrigatórios.';
            header('Location: /admin/users/add');
            exit;
        }
        
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $departmentId = filter_var($_POST['department_id'], FILTER_SANITIZE_NUMBER_INT);
        $role = isset($_POST['role']) && $_POST['role'] == 'admin' ? 'admin' : 'user';
        
        // Check if email already exists
        if ($this->userModel->getUserByEmail($email)) {
            $_SESSION['user_error'] = 'Este e-mail já está em uso.';
            header('Location: /admin/users/add');
            exit;
        }
        
        // Create new user
        $userId = $this->userModel->createUser($name, $email, $password, $role, $departmentId);
        
        if ($userId) {
            $_SESSION['user_success'] = 'Usuário cadastrado com sucesso.';
            header('Location: /admin/users');
            exit;
        } else {
            $_SESSION['user_error'] = 'Erro ao cadastrar usuário. Tente novamente.';
            header('Location: /admin/users/add');
            exit;
        }
    }
    
    /**
     * Display form to edit an existing user
     */
    public function editUserForm($userId) {
        // Get user details
        $user = $this->userModel->getUserById($userId);
        
        if (!$user) {
            $_SESSION['user_error'] = 'Usuário não encontrado.';
            header('Location: /admin/users');
            exit;
        }
        
        // Get all departments for select box
        $departments = $this->departmentModel->getAllDepartments();
        
        include 'views/admin/users_edit.php';
    }
    
    /**
     * Process user edit form submission
     */
    public function updateUser() {
        // Validate form data
        if (empty($_POST['id']) || empty($_POST['name']) || empty($_POST['email']) || empty($_POST['department_id'])) {
            $_SESSION['user_error'] = 'Todos os campos são obrigatórios.';
            header('Location: /admin/users');
            exit;
        }
        
        $userId = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $departmentId = filter_var($_POST['department_id'], FILTER_SANITIZE_NUMBER_INT);
        $role = isset($_POST['role']) && $_POST['role'] == 'admin' ? 'admin' : 'user';
        
        // Check if updating password
        $password = null;
        if (!empty($_POST['password'])) {
            $password = $_POST['password'];
        }
        
        // Check if email already exists (excluding current user)
        $existingUser = $this->userModel->getUserByEmail($email);
        if ($existingUser && $existingUser['id'] != $userId) {
            $_SESSION['user_error'] = 'Este e-mail já está em uso.';
            header('Location: /admin/users/edit/' . $userId);
            exit;
        }
        
        // Update user
        $result = $this->userModel->updateUser($userId, $name, $email, $password, $role, $departmentId);
        
        if ($result) {
            $_SESSION['user_success'] = 'Usuário atualizado com sucesso.';
            header('Location: /admin/users');
            exit;
        } else {
            $_SESSION['user_error'] = 'Erro ao atualizar usuário. Tente novamente.';
            header('Location: /admin/users/edit/' . $userId);
            exit;
        }
    }
    
    /**
     * Delete a user
     */
    public function deleteUser($userId) {
        // Cannot delete yourself
        if ($userId == $_SESSION['user_id']) {
            $_SESSION['user_error'] = 'Você não pode excluir seu próprio usuário.';
            header('Location: /admin/users');
            exit;
        }
        
        // Delete user
        $result = $this->userModel->deleteUser($userId);
        
        if ($result) {
            $_SESSION['user_success'] = 'Usuário excluído com sucesso.';
        } else {
            $_SESSION['user_error'] = 'Erro ao excluir usuário. Tente novamente.';
        }
        
        header('Location: /admin/users');
        exit;
    }
    
    /**
     * Display all departments
     */
    public function showDepartments() {
        // Get all departments
        $departments = $this->departmentModel->getAllDepartments();
        
        // Get all users and organize by department
        $allUsers = $this->userModel->getAllUsers();
        $usersByDepartment = [];
        
        foreach ($allUsers as $user) {
            if (!isset($usersByDepartment[$user['department_id']])) {
                $usersByDepartment[$user['department_id']] = [];
            }
            $usersByDepartment[$user['department_id']][] = $user;
        }
        
        // Process alert messages
        $success = isset($_SESSION['department_success']) ? $_SESSION['department_success'] : null;
        $error = isset($_SESSION['department_error']) ? $_SESSION['department_error'] : null;
        
        // Clear session messages
        unset($_SESSION['department_success']);
        unset($_SESSION['department_error']);
        
        include 'views/admin/departments.php';
    }
    
    /**
     * Process department add form submission
     */
    public function addDepartment() {
        // Validate form data
        if (empty($_POST['name'])) {
            $_SESSION['department_error'] = 'O nome do departamento é obrigatório.';
            header('Location: /admin/departments');
            exit;
        }
        
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $description = filter_var($_POST['description'] ?? '', FILTER_SANITIZE_STRING);
        
        // Create new department
        $departmentId = $this->departmentModel->createDepartment($name, $description);
        
        if ($departmentId) {
            $_SESSION['department_success'] = 'Departamento cadastrado com sucesso.';
        } else {
            $_SESSION['department_error'] = 'Erro ao cadastrar departamento. Tente novamente.';
        }
        
        header('Location: /admin/departments');
        exit;
    }
    
    /**
     * Process department edit form submission
     */
    public function updateDepartment() {
        // Validate form data
        if (empty($_POST['id']) || empty($_POST['name'])) {
            $_SESSION['department_error'] = 'Todos os campos são obrigatórios.';
            header('Location: /admin/departments');
            exit;
        }
        
        $departmentId = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $description = filter_var($_POST['description'] ?? '', FILTER_SANITIZE_STRING);
        
        // Update department
        $result = $this->departmentModel->updateDepartment($departmentId, $name, $description);
        
        if ($result) {
            $_SESSION['department_success'] = 'Departamento atualizado com sucesso.';
        } else {
            $_SESSION['department_error'] = 'Erro ao atualizar departamento. Tente novamente.';
        }
        
        header('Location: /admin/departments');
        exit;
    }
    
    /**
     * Delete a department
     */
    public function deleteDepartment($departmentId) {
        // Check if department has users
        $usersInDepartment = $this->userModel->getUsersByDepartment($departmentId);
        
        if (count($usersInDepartment) > 0) {
            $_SESSION['department_error'] = 'Não é possível excluir o departamento pois existem usuários vinculados a ele.';
            header('Location: /admin/departments');
            exit;
        }
        
        // Delete department
        $result = $this->departmentModel->deleteDepartment($departmentId);
        
        if ($result) {
            $_SESSION['department_success'] = 'Departamento excluído com sucesso.';
        } else {
            $_SESSION['department_error'] = 'Erro ao excluir departamento. Tente novamente.';
        }
        
        header('Location: /admin/departments');
        exit;
    }
}
?>

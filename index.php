<?php
// Session settings before session_start
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 in production with HTTPS
ini_set('session.gc_maxlifetime', 3600); // 1 hour

session_start();
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'config/constants.php';
require_once 'includes/functions.php';

// Load models
$modelFiles = glob('models/*.php');
foreach ($modelFiles as $modelFile) {
    require_once $modelFile;
}

// Load services
$serviceFiles = glob('services/*.php');
foreach ($serviceFiles as $serviceFile) {
    require_once $serviceFile;
}

// Initialize database connection
$db = new DatabaseService();
$conn = $db->getConnection();

// Check if database tables exist and create them if not
if (!$db->checkTablesExist()) {
    $db->initializeDatabase();
}

// Simple router
$request = $_SERVER['REQUEST_URI'];
$basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
$route = str_replace($basePath, '', $request);

// Extract the path and query string
$routeParts = explode('?', $route);
$path = $routeParts[0];
$queryString = isset($routeParts[1]) ? $routeParts[1] : '';

// Parse query string
parse_str($queryString, $queryParams);

// Define routes
if ($path == '/' || $path == '') {
    if (isset($_SESSION['user_id'])) {
        if ($_SESSION['user_role'] == 'admin') {
            include 'controllers/DashboardController.php';
            $controller = new DashboardController($conn);
            $controller->adminDashboard();
        } else {
            include 'controllers/DashboardController.php';
            $controller = new DashboardController($conn);
            $controller->userDashboard();
        }
    } else {
        include 'controllers/AuthController.php';
        $controller = new AuthController($conn);
        $controller->showLogin();
    }
} elseif ($path == '/login') {
    include 'controllers/AuthController.php';
    $controller = new AuthController($conn);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->login();
    } else {
        $controller->showLogin();
    }
} elseif ($path == '/logout') {
    include 'controllers/AuthController.php';
    $controller = new AuthController($conn);
    $controller->logout();
} elseif ($path == '/admin/dashboard') {
    requireAdmin();
    include 'controllers/DashboardController.php';
    $controller = new DashboardController($conn);
    $controller->adminDashboard();
} elseif ($path == '/admin/users') {
    requireAdmin();
    include 'controllers/UserController.php';
    $controller = new UserController($conn);
    $controller->showUsers();
} elseif ($path == '/admin/document-types') {
    requireAdmin();
    include 'controllers/DocumentController.php';
    $controller = new DocumentController($conn);
    $controller->showDocumentTypes();
} elseif ($path == '/admin/templates') {
    requireAdmin();
    include 'controllers/TemplateController.php';
    $controller = new TemplateController($conn);
    $controller->showTemplates();
} elseif ($path == '/admin/workflows') {
    requireAdmin();
    include 'controllers/WorkflowController.php';
    $controller = new WorkflowController($conn);
    $controller->showWorkflows();
} elseif ($path == '/admin/departments') {
    requireAdmin();
    include 'controllers/UserController.php';
    $controller = new UserController($conn);
    $controller->showDepartments();
} elseif ($path == '/user/dashboard') {
    requireAuth();
    include 'controllers/DashboardController.php';
    $controller = new DashboardController($conn);
    $controller->userDashboard();
} elseif ($path == '/user/new-document') {
    requireAuth();
    include 'controllers/DocumentController.php';
    $controller = new DocumentController($conn);
    $controller->newDocument();
} elseif ($path == '/user/my-documents') {
    requireAuth();
    include 'controllers/DocumentController.php';
    $controller = new DocumentController($conn);
    $controller->myDocuments();
} elseif ($path == '/user/incoming-documents') {
    requireAuth();
    include 'controllers/DocumentController.php';
    $controller = new DocumentController($conn);
    $controller->incomingDocuments();
} elseif ($path == '/user/document') {
    requireAuth();
    include 'controllers/DocumentController.php';
    $controller = new DocumentController($conn);
    if (isset($queryParams['id'])) {
        $controller->viewDocument($queryParams['id']);
    } else {
        redirectTo('/user/my-documents');
    }
} elseif ($path == '/api/generate-document') {
    requireAuth();
    include_once 'api/ai.php';
    handleGenerateDocument($conn);
} else {
    // 404 Not Found
    header('HTTP/1.0 404 Not Found');
    echo '404 - Page not found';
}

function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        redirectTo('/login');
        exit;
    }
}

function requireAdmin() {
    requireAuth();
    if ($_SESSION['user_role'] != 'admin') {
        redirectTo('/user/dashboard');
        exit;
    }
}

function redirectTo($path) {
    $basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
    header('Location: ' . $basePath . $path);
    exit;
}
?>

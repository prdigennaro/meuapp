<?php
/**
 * DashboardController - Handles dashboard displays for both admin and regular users
 */
class DashboardController {
    private $conn;
    private $documentModel;
    private $documentTypeModel;
    private $userModel;
    private $departmentModel;
    private $workflowModel;
    private $templateModel;
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->documentModel = new Document($conn);
        $this->documentTypeModel = new DocumentType($conn);
        $this->userModel = new User($conn);
        $this->departmentModel = new Department($conn);
        $this->workflowModel = new Workflow($conn);
        $this->templateModel = new Template($conn);
    }
    
    /**
     * Display admin dashboard
     */
    public function adminDashboard() {
        // Check if user is admin
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
            header('Location: /login');
            exit;
        }
        
        // Get statistics for admin dashboard
        $stats = [
            'totalDocuments' => $this->documentModel->getTotalDocuments(),
            'totalUsers' => $this->userModel->getTotalUsers(),
            'totalDepartments' => $this->departmentModel->getTotalDepartments(),
            'totalDocumentTypes' => $this->documentTypeModel->getTotalDocumentTypes(),
            'totalWorkflows' => $this->workflowModel->getTotalWorkflows(),
            'totalTemplates' => $this->templateModel->getTotalTemplates(),
            'recentDocuments' => $this->documentModel->getRecentDocuments(5)
        ];
        
        // Get document counts by status
        $documentsByStatus = $this->documentModel->getDocumentCountsByStatus();
        
        // Get document counts by type
        $documentsByType = $this->documentModel->getDocumentCountsByType();
        
        include 'views/admin/dashboard.php';
    }
    
    /**
     * Display user dashboard
     */
    public function userDashboard() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        // If user is admin, redirect to admin dashboard
        if ($_SESSION['user_role'] == 'admin') {
            header('Location: /admin/dashboard');
            exit;
        }
        
        // Get user-specific statistics
        $userId = $_SESSION['user_id'];
        $departmentId = $_SESSION['user_department'];
        
        $stats = [
            'myDocuments' => $this->documentModel->getUserDocumentCount($userId),
            'pendingDocuments' => $this->documentModel->getPendingDocumentCount($userId),
            'departmentDocuments' => $this->documentModel->getDepartmentDocumentCount($departmentId),
            'recentCreatedDocuments' => $this->documentModel->getRecentUserDocuments($userId, 5),
            'recentDepartmentDocuments' => $this->documentModel->getRecentDepartmentDocuments($departmentId, 5)
        ];
        
        // Get document counts by status for this user
        $myDocumentsByStatus = $this->documentModel->getUserDocumentCountsByStatus($userId);
        
        // Get document counts by type for this user
        $myDocumentsByType = $this->documentModel->getUserDocumentCountsByType($userId);
        
        include 'views/user/dashboard.php';
    }
}
?>

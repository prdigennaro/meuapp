<?php
/**
 * DocumentController - Handles document management
 */
class DocumentController {
    private $conn;
    private $documentModel;
    private $documentTypeModel;
    private $templateModel;
    private $workflowModel;
    private $workflowStepModel;
    private $documentLogModel;
    private $userModel;
    private $departmentModel;
    private $aiService;
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->documentModel = new Document($conn);
        $this->documentTypeModel = new DocumentType($conn);
        $this->templateModel = new Template($conn);
        $this->workflowModel = new Workflow($conn);
        $this->workflowStepModel = new WorkflowStep($conn);
        $this->documentLogModel = new DocumentLog($conn);
        $this->userModel = new User($conn);
        $this->departmentModel = new Department($conn);
        $this->aiService = new AIService($conn);
    }
    
    /**
     * Display document types (admin only)
     */
    public function showDocumentTypes() {
        // Get all document types
        $documentTypes = $this->documentTypeModel->getAllDocumentTypes();
        
        // Process alert messages
        $success = isset($_SESSION['doc_type_success']) ? $_SESSION['doc_type_success'] : null;
        $error = isset($_SESSION['doc_type_error']) ? $_SESSION['doc_type_error'] : null;
        
        // Clear session messages
        unset($_SESSION['doc_type_success']);
        unset($_SESSION['doc_type_error']);
        
        include 'views/admin/document_types.php';
    }
    
    /**
     * Process document type add form submission
     */
    public function addDocumentType() {
        // Validate form data
        if (empty($_POST['name']) || empty($_POST['code'])) {
            $_SESSION['doc_type_error'] = 'Nome e código são obrigatórios.';
            header('Location: /admin/document-types');
            exit;
        }
        
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $code = filter_var($_POST['code'], FILTER_SANITIZE_STRING);
        $description = filter_var($_POST['description'] ?? '', FILTER_SANITIZE_STRING);
        
        // Check if code already exists
        if ($this->documentTypeModel->getDocumentTypeByCode($code)) {
            $_SESSION['doc_type_error'] = 'Este código já está em uso.';
            header('Location: /admin/document-types');
            exit;
        }
        
        // Create new document type
        $typeId = $this->documentTypeModel->createDocumentType($name, $code, $description);
        
        if ($typeId) {
            $_SESSION['doc_type_success'] = 'Tipo de documento cadastrado com sucesso.';
        } else {
            $_SESSION['doc_type_error'] = 'Erro ao cadastrar tipo de documento. Tente novamente.';
        }
        
        header('Location: /admin/document-types');
        exit;
    }
    
    /**
     * Process document type edit form submission
     */
    public function updateDocumentType() {
        // Validate form data
        if (empty($_POST['id']) || empty($_POST['name']) || empty($_POST['code'])) {
            $_SESSION['doc_type_error'] = 'Todos os campos são obrigatórios.';
            header('Location: /admin/document-types');
            exit;
        }
        
        $typeId = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $code = filter_var($_POST['code'], FILTER_SANITIZE_STRING);
        $description = filter_var($_POST['description'] ?? '', FILTER_SANITIZE_STRING);
        
        // Check if code already exists (excluding current type)
        $existingType = $this->documentTypeModel->getDocumentTypeByCode($code);
        if ($existingType && $existingType['id'] != $typeId) {
            $_SESSION['doc_type_error'] = 'Este código já está em uso.';
            header('Location: /admin/document-types');
            exit;
        }
        
        // Update document type
        $result = $this->documentTypeModel->updateDocumentType($typeId, $name, $code, $description);
        
        if ($result) {
            $_SESSION['doc_type_success'] = 'Tipo de documento atualizado com sucesso.';
        } else {
            $_SESSION['doc_type_error'] = 'Erro ao atualizar tipo de documento. Tente novamente.';
        }
        
        header('Location: /admin/document-types');
        exit;
    }
    
    /**
     * Delete a document type
     */
    public function deleteDocumentType($typeId) {
        // Check if type is in use
        $documentsWithType = $this->documentModel->getDocumentsByType($typeId);
        
        if (count($documentsWithType) > 0) {
            $_SESSION['doc_type_error'] = 'Não é possível excluir o tipo pois existem documentos vinculados a ele.';
            header('Location: /admin/document-types');
            exit;
        }
        
        // Delete document type
        $result = $this->documentTypeModel->deleteDocumentType($typeId);
        
        if ($result) {
            $_SESSION['doc_type_success'] = 'Tipo de documento excluído com sucesso.';
        } else {
            $_SESSION['doc_type_error'] = 'Erro ao excluir tipo de documento. Tente novamente.';
        }
        
        header('Location: /admin/document-types');
        exit;
    }
    
    /**
     * Display form to create a new document
     */
    public function newDocument() {
        // Get all document types
        $documentTypes = $this->documentTypeModel->getAllDocumentTypes();
        
        // Get all templates
        $templates = $this->templateModel->getTemplatesByDocumentTypes();
        
        include 'views/user/new_document.php';
    }
    
    /**
     * Process new document form submission
     */
    public function createDocument() {
        // Validate form data
        if (empty($_POST['title']) || empty($_POST['template_id']) || empty($_POST['document_type_id'])) {
            $_SESSION['document_error'] = 'Todos os campos são obrigatórios.';
            header('Location: /user/new-document');
            exit;
        }
        
        $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
        $templateId = filter_var($_POST['template_id'], FILTER_SANITIZE_NUMBER_INT);
        $documentTypeId = filter_var($_POST['document_type_id'], FILTER_SANITIZE_NUMBER_INT);
        $description = filter_var($_POST['description'] ?? '', FILTER_SANITIZE_STRING);
        
        // Get template
        $template = $this->templateModel->getTemplateById($templateId);
        
        if (!$template) {
            $_SESSION['document_error'] = 'Modelo não encontrado.';
            header('Location: /user/new-document');
            exit;
        }
        
        // Get form data for variables
        $variables = [];
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'var_') === 0) {
                $varName = substr($key, 4);
                $variables[$varName] = $value;
            }
        }
        
        // Generate protocol number
        $protocolNumber = $this->generateProtocolNumber();
        
        // Create document
        $documentId = $this->documentModel->createDocument(
            $title,
            $description,
            $documentTypeId,
            $templateId,
            $_SESSION['user_id'],
            $_SESSION['user_department'],
            $protocolNumber,
            DOC_STATUS_DRAFT,
            json_encode($variables)
        );
        
        if (!$documentId) {
            $_SESSION['document_error'] = 'Erro ao criar documento. Tente novamente.';
            header('Location: /user/new-document');
            exit;
        }
        
        // Generate document content using AI
        $documentContent = $this->aiService->generateDocumentContent($template['content'], $variables);
        
        // Update document with generated content
        $this->documentModel->updateDocumentContent($documentId, $documentContent);
        
        // Log document creation
        $this->documentLogModel->logDocumentAction(
            $documentId,
            $_SESSION['user_id'],
            ACTION_CREATE,
            'Documento criado'
        );
        
        $_SESSION['document_success'] = 'Documento criado com sucesso.';
        header('Location: /user/document?id=' . $documentId);
        exit;
    }
    
    /**
     * Display user's documents
     */
    public function myDocuments() {
        // Get documents created by the current user
        $documents = $this->documentModel->getDocumentsByUser($_SESSION['user_id']);
        
        // Get document types for filter
        $documentTypes = $this->documentTypeModel->getAllDocumentTypes();
        
        // Process alert messages
        $success = isset($_SESSION['document_success']) ? $_SESSION['document_success'] : null;
        $error = isset($_SESSION['document_error']) ? $_SESSION['document_error'] : null;
        
        // Clear session messages
        unset($_SESSION['document_success']);
        unset($_SESSION['document_error']);
        
        include 'views/user/my_documents.php';
    }
    
    /**
     * Display incoming documents for the user's department
     */
    public function incomingDocuments() {
        // Get documents assigned to the user's department
        $documents = $this->documentModel->getDocumentsByDepartment($_SESSION['user_department']);
        
        // Get document types for filter
        $documentTypes = $this->documentTypeModel->getAllDocumentTypes();
        
        // Process alert messages
        $success = isset($_SESSION['document_success']) ? $_SESSION['document_success'] : null;
        $error = isset($_SESSION['document_error']) ? $_SESSION['document_error'] : null;
        
        // Clear session messages
        unset($_SESSION['document_success']);
        unset($_SESSION['document_error']);
        
        include 'views/user/incoming_documents.php';
    }
    
    /**
     * View a single document
     */
    public function viewDocument($documentId) {
        // Get document details
        $document = $this->documentModel->getDocumentById($documentId);
        
        if (!$document) {
            $_SESSION['document_error'] = 'Documento não encontrado.';
            header('Location: /user/my-documents');
            exit;
        }
        
        // Check if user has access to this document (creator or in the assigned department)
        if ($document['created_by'] != $_SESSION['user_id'] && $document['current_department_id'] != $_SESSION['user_department']) {
            $_SESSION['document_error'] = 'Você não tem permissão para acessar este documento.';
            header('Location: /user/my-documents');
            exit;
        }
        
        // Get document type
        $documentType = $this->documentTypeModel->getDocumentTypeById($document['document_type_id']);
        
        // Get document logs
        $logs = $this->documentLogModel->getDocumentLogs($documentId);
        
        // Get departments for routing
        $departments = $this->departmentModel->getAllDepartments();
        
        // Get workflow steps if this document is in a workflow
        $workflowSteps = [];
        if ($document['workflow_id']) {
            $workflowSteps = $this->workflowStepModel->getWorkflowStepsByWorkflow($document['workflow_id']);
        }
        
        // Get available workflows for this document type
        $availableWorkflows = $this->workflowModel->getWorkflowsByDocumentType($document['document_type_id']);
        
        // Process alert messages
        $success = isset($_SESSION['document_success']) ? $_SESSION['document_success'] : null;
        $error = isset($_SESSION['document_error']) ? $_SESSION['document_error'] : null;
        
        // Clear session messages
        unset($_SESSION['document_success']);
        unset($_SESSION['document_error']);
        
        include 'views/user/document.php';
    }
    
    /**
     * Process document approval/rejection
     */
    public function processDocument() {
        // Validate form data
        if (empty($_POST['document_id']) || empty($_POST['action'])) {
            $_SESSION['document_error'] = 'Dados inválidos.';
            header('Location: /user/my-documents');
            exit;
        }
        
        $documentId = filter_var($_POST['document_id'], FILTER_SANITIZE_NUMBER_INT);
        $action = filter_var($_POST['action'], FILTER_SANITIZE_STRING);
        $comments = filter_var($_POST['comments'] ?? '', FILTER_SANITIZE_STRING);
        $nextDepartmentId = filter_var($_POST['next_department_id'] ?? null, FILTER_SANITIZE_NUMBER_INT);
        
        // Get document details
        $document = $this->documentModel->getDocumentById($documentId);
        
        if (!$document) {
            $_SESSION['document_error'] = 'Documento não encontrado.';
            header('Location: /user/my-documents');
            exit;
        }
        
        // Check if user has access to process this document
        if ($document['current_department_id'] != $_SESSION['user_department']) {
            $_SESSION['document_error'] = 'Você não tem permissão para processar este documento.';
            header('Location: /user/my-documents');
            exit;
        }
        
        $newStatus = $document['status'];
        $actionMessage = '';
        
        // Process based on action
        if ($action == ACTION_APPROVE) {
            $newStatus = DOC_STATUS_APPROVED;
            $actionMessage = 'Documento aprovado';
        } elseif ($action == ACTION_REJECT) {
            $newStatus = DOC_STATUS_REJECTED;
            $actionMessage = 'Documento rejeitado';
        } elseif ($action == ACTION_FORWARD) {
            if (empty($nextDepartmentId)) {
                $_SESSION['document_error'] = 'Selecione o próximo departamento.';
                header('Location: /user/document?id=' . $documentId);
                exit;
            }
            $newStatus = DOC_STATUS_IN_REVIEW;
            $actionMessage = 'Documento encaminhado para novo departamento';
            
            // Update document department
            $this->documentModel->updateDocumentDepartment($documentId, $nextDepartmentId);
        } elseif ($action == ACTION_COMPLETE) {
            $newStatus = DOC_STATUS_COMPLETED;
            $actionMessage = 'Documento finalizado';
        } elseif ($action == ACTION_ARCHIVE) {
            $newStatus = DOC_STATUS_ARCHIVED;
            $actionMessage = 'Documento arquivado';
        }
        
        // Update document status
        $this->documentModel->updateDocumentStatus($documentId, $newStatus);
        
        // Log action
        if (!empty($comments)) {
            $actionMessage .= ": $comments";
        }
        $this->documentLogModel->logDocumentAction($documentId, $_SESSION['user_id'], $action, $actionMessage);
        
        $_SESSION['document_success'] = 'Documento processado com sucesso.';
        header('Location: /user/document?id=' . $documentId);
        exit;
    }
    
    /**
     * Start a document workflow
     */
    public function startWorkflow() {
        // Validate form data
        if (empty($_POST['document_id']) || empty($_POST['workflow_id'])) {
            $_SESSION['document_error'] = 'Dados inválidos.';
            header('Location: /user/my-documents');
            exit;
        }
        
        $documentId = filter_var($_POST['document_id'], FILTER_SANITIZE_NUMBER_INT);
        $workflowId = filter_var($_POST['workflow_id'], FILTER_SANITIZE_NUMBER_INT);
        
        // Get document details
        $document = $this->documentModel->getDocumentById($documentId);
        
        if (!$document) {
            $_SESSION['document_error'] = 'Documento não encontrado.';
            header('Location: /user/my-documents');
            exit;
        }
        
        // Check if user is the document creator
        if ($document['created_by'] != $_SESSION['user_id']) {
            $_SESSION['document_error'] = 'Apenas o criador do documento pode iniciar um fluxo de trabalho.';
            header('Location: /user/document?id=' . $documentId);
            exit;
        }
        
        // Get workflow
        $workflow = $this->workflowModel->getWorkflowById($workflowId);
        
        if (!$workflow) {
            $_SESSION['document_error'] = 'Fluxo de trabalho não encontrado.';
            header('Location: /user/document?id=' . $documentId);
            exit;
        }
        
        // Get first step of the workflow
        $firstStep = $this->workflowStepModel->getFirstWorkflowStep($workflowId);
        
        if (!$firstStep) {
            $_SESSION['document_error'] = 'Este fluxo de trabalho não possui etapas definidas.';
            header('Location: /user/document?id=' . $documentId);
            exit;
        }
        
        // Update document with workflow and next department
        $this->documentModel->startWorkflow($documentId, $workflowId, $firstStep['department_id']);
        
        // Log workflow start
        $this->documentLogModel->logDocumentAction(
            $documentId,
            $_SESSION['user_id'],
            ACTION_FORWARD,
            'Fluxo de trabalho iniciado: ' . $workflow['name']
        );
        
        $_SESSION['document_success'] = 'Fluxo de trabalho iniciado com sucesso.';
        header('Location: /user/document?id=' . $documentId);
        exit;
    }
    
    /**
     * Generate a unique protocol number
     */
    private function generateProtocolNumber() {
        $year = date(PROTOCOL_DATE_FORMAT);
        $count = $this->documentModel->getDocumentCountForYear($year);
        $count++;
        
        return PROTOCOL_PREFIX . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }
}
?>

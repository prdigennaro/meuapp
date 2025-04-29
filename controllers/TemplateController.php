<?php
/**
 * TemplateController - Handles document template management
 */
class TemplateController {
    private $conn;
    private $templateModel;
    private $documentTypeModel;
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->templateModel = new Template($conn);
        $this->documentTypeModel = new DocumentType($conn);
    }
    
    /**
     * Display all templates
     */
    public function showTemplates() {
        // Get all templates
        $templates = $this->templateModel->getAllTemplates();
        
        // Get all document types for filter and new template form
        $documentTypes = $this->documentTypeModel->getAllDocumentTypes();
        
        // Process alert messages
        $success = isset($_SESSION['template_success']) ? $_SESSION['template_success'] : null;
        $error = isset($_SESSION['template_error']) ? $_SESSION['template_error'] : null;
        
        // Clear session messages
        unset($_SESSION['template_success']);
        unset($_SESSION['template_error']);
        
        include 'views/admin/templates.php';
    }
    
    /**
     * Display form to add a new template
     */
    public function addTemplateForm() {
        // Get all document types for select box
        $documentTypes = $this->documentTypeModel->getAllDocumentTypes();
        
        include 'views/admin/templates_add.php';
    }
    
    /**
     * Process template add form submission
     */
    public function addTemplate() {
        // Validate form data
        if (empty($_POST['name']) || empty($_POST['document_type_id']) || empty($_POST['content'])) {
            $_SESSION['template_error'] = 'Todos os campos são obrigatórios.';
            header('Location: /admin/templates/add');
            exit;
        }
        
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $documentTypeId = filter_var($_POST['document_type_id'], FILTER_SANITIZE_NUMBER_INT);
        $content = $_POST['content']; // HTML content should not be heavily sanitized
        $description = filter_var($_POST['description'] ?? '', FILTER_SANITIZE_STRING);
        
        // Extract variables from content (patterns like {{variable_name}})
        preg_match_all('/{{(.*?)}}/', $content, $matches);
        $variables = [];
        
        if (!empty($matches[1])) {
            foreach ($matches[1] as $variable) {
                $variable = trim($variable);
                if (!empty($variable) && !in_array($variable, $variables)) {
                    $variables[] = $variable;
                }
            }
        }
        
        // Create new template
        $templateId = $this->templateModel->createTemplate(
            $name,
            $description,
            $documentTypeId,
            $content,
            json_encode($variables)
        );
        
        if ($templateId) {
            $_SESSION['template_success'] = 'Modelo cadastrado com sucesso.';
            header('Location: /admin/templates');
            exit;
        } else {
            $_SESSION['template_error'] = 'Erro ao cadastrar modelo. Tente novamente.';
            header('Location: /admin/templates/add');
            exit;
        }
    }
    
    /**
     * Display form to edit an existing template
     */
    public function editTemplateForm($templateId) {
        // Get template details
        $template = $this->templateModel->getTemplateById($templateId);
        
        if (!$template) {
            $_SESSION['template_error'] = 'Modelo não encontrado.';
            header('Location: /admin/templates');
            exit;
        }
        
        // Get all document types for select box
        $documentTypes = $this->documentTypeModel->getAllDocumentTypes();
        
        include 'views/admin/templates_edit.php';
    }
    
    /**
     * Process template edit form submission
     */
    public function updateTemplate() {
        // Validate form data
        if (empty($_POST['id']) || empty($_POST['name']) || empty($_POST['document_type_id']) || empty($_POST['content'])) {
            $_SESSION['template_error'] = 'Todos os campos são obrigatórios.';
            header('Location: /admin/templates');
            exit;
        }
        
        $templateId = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $documentTypeId = filter_var($_POST['document_type_id'], FILTER_SANITIZE_NUMBER_INT);
        $content = $_POST['content']; // HTML content should not be heavily sanitized
        $description = filter_var($_POST['description'] ?? '', FILTER_SANITIZE_STRING);
        
        // Extract variables from content (patterns like {{variable_name}})
        preg_match_all('/{{(.*?)}}/', $content, $matches);
        $variables = [];
        
        if (!empty($matches[1])) {
            foreach ($matches[1] as $variable) {
                $variable = trim($variable);
                if (!empty($variable) && !in_array($variable, $variables)) {
                    $variables[] = $variable;
                }
            }
        }
        
        // Update template
        $result = $this->templateModel->updateTemplate(
            $templateId,
            $name,
            $description,
            $documentTypeId,
            $content,
            json_encode($variables)
        );
        
        if ($result) {
            $_SESSION['template_success'] = 'Modelo atualizado com sucesso.';
            header('Location: /admin/templates');
            exit;
        } else {
            $_SESSION['template_error'] = 'Erro ao atualizar modelo. Tente novamente.';
            header('Location: /admin/templates/edit/' . $templateId);
            exit;
        }
    }
    
    /**
     * Delete a template
     */
    public function deleteTemplate($templateId) {
        // Check if template is in use
        $documentsWithTemplate = $this->templateModel->getDocumentsUsingTemplate($templateId);
        
        if (count($documentsWithTemplate) > 0) {
            $_SESSION['template_error'] = 'Não é possível excluir o modelo pois existem documentos vinculados a ele.';
            header('Location: /admin/templates');
            exit;
        }
        
        // Delete template
        $result = $this->templateModel->deleteTemplate($templateId);
        
        if ($result) {
            $_SESSION['template_success'] = 'Modelo excluído com sucesso.';
        } else {
            $_SESSION['template_error'] = 'Erro ao excluir modelo. Tente novamente.';
        }
        
        header('Location: /admin/templates');
        exit;
    }
    
    /**
     * View a template
     */
    public function viewTemplate($templateId) {
        // Get template details
        $template = $this->templateModel->getTemplateById($templateId);
        
        if (!$template) {
            $_SESSION['template_error'] = 'Modelo não encontrado.';
            header('Location: /admin/templates');
            exit;
        }
        
        // Get document type
        $documentType = $this->documentTypeModel->getDocumentTypeById($template['document_type_id']);
        
        // Get template variables
        $variables = json_decode($template['variables'], true);
        
        include 'views/admin/templates_view.php';
    }
    
    /**
     * Clone an existing template
     */
    public function cloneTemplate($templateId) {
        // Get template details
        $template = $this->templateModel->getTemplateById($templateId);
        
        if (!$template) {
            $_SESSION['template_error'] = 'Modelo não encontrado.';
            header('Location: /admin/templates');
            exit;
        }
        
        // Create new template based on existing one
        $newName = $template['name'] . ' (Cópia)';
        $newTemplateId = $this->templateModel->createTemplate(
            $newName,
            $template['description'],
            $template['document_type_id'],
            $template['content'],
            $template['variables']
        );
        
        if ($newTemplateId) {
            $_SESSION['template_success'] = 'Modelo clonado com sucesso.';
        } else {
            $_SESSION['template_error'] = 'Erro ao clonar modelo. Tente novamente.';
        }
        
        header('Location: /admin/templates');
        exit;
    }
    
    /**
     * Get template variables
     */
    public function getTemplateVariables($templateId) {
        // Get template details
        $template = $this->templateModel->getTemplateById($templateId);
        
        if (!$template) {
            http_response_code(404);
            echo json_encode(['error' => 'Template not found']);
            exit;
        }
        
        // Return variables as JSON
        header('Content-Type: application/json');
        echo $template['variables'];
        exit;
    }
}
?>

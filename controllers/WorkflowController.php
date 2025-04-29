<?php
/**
 * WorkflowController - Handles workflow management
 */
class WorkflowController {
    private $conn;
    private $workflowModel;
    private $workflowStepModel;
    private $documentTypeModel;
    private $departmentModel;
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->workflowModel = new Workflow($conn);
        $this->workflowStepModel = new WorkflowStep($conn);
        $this->documentTypeModel = new DocumentType($conn);
        $this->departmentModel = new Department($conn);
    }
    
    /**
     * Display all workflows
     */
    public function showWorkflows() {
        // Get all workflows
        $workflows = $this->workflowModel->getAllWorkflows();
        
        // Get all document types for filter and new workflow form
        $documentTypes = $this->documentTypeModel->getAllDocumentTypes();
        
        // Process alert messages
        $success = isset($_SESSION['workflow_success']) ? $_SESSION['workflow_success'] : null;
        $error = isset($_SESSION['workflow_error']) ? $_SESSION['workflow_error'] : null;
        
        // Clear session messages
        unset($_SESSION['workflow_success']);
        unset($_SESSION['workflow_error']);
        
        include 'views/admin/workflows.php';
    }
    
    /**
     * Display form to add a new workflow
     */
    public function addWorkflowForm() {
        // Get all document types for select box
        $documentTypes = $this->documentTypeModel->getAllDocumentTypes();
        
        include 'views/admin/workflows_add.php';
    }
    
    /**
     * Process workflow add form submission
     */
    public function addWorkflow() {
        // Validate form data
        if (empty($_POST['name']) || empty($_POST['document_type_id'])) {
            $_SESSION['workflow_error'] = 'Nome e tipo de documento são obrigatórios.';
            header('Location: /admin/workflows/add');
            exit;
        }
        
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $documentTypeId = filter_var($_POST['document_type_id'], FILTER_SANITIZE_NUMBER_INT);
        $description = filter_var($_POST['description'] ?? '', FILTER_SANITIZE_STRING);
        
        // Create new workflow
        $workflowId = $this->workflowModel->createWorkflow($name, $description, $documentTypeId);
        
        if ($workflowId) {
            $_SESSION['workflow_success'] = 'Fluxo de trabalho cadastrado com sucesso. Agora adicione as etapas.';
            header('Location: /admin/workflows/steps/' . $workflowId);
            exit;
        } else {
            $_SESSION['workflow_error'] = 'Erro ao cadastrar fluxo de trabalho. Tente novamente.';
            header('Location: /admin/workflows/add');
            exit;
        }
    }
    
    /**
     * Display form to edit an existing workflow
     */
    public function editWorkflowForm($workflowId) {
        // Get workflow details
        $workflow = $this->workflowModel->getWorkflowById($workflowId);
        
        if (!$workflow) {
            $_SESSION['workflow_error'] = 'Fluxo de trabalho não encontrado.';
            header('Location: /admin/workflows');
            exit;
        }
        
        // Get all document types for select box
        $documentTypes = $this->documentTypeModel->getAllDocumentTypes();
        
        include 'views/admin/workflows_edit.php';
    }
    
    /**
     * Process workflow edit form submission
     */
    public function updateWorkflow() {
        // Validate form data
        if (empty($_POST['id']) || empty($_POST['name']) || empty($_POST['document_type_id'])) {
            $_SESSION['workflow_error'] = 'Todos os campos são obrigatórios.';
            header('Location: /admin/workflows');
            exit;
        }
        
        $workflowId = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $documentTypeId = filter_var($_POST['document_type_id'], FILTER_SANITIZE_NUMBER_INT);
        $description = filter_var($_POST['description'] ?? '', FILTER_SANITIZE_STRING);
        
        // Update workflow
        $result = $this->workflowModel->updateWorkflow($workflowId, $name, $description, $documentTypeId);
        
        if ($result) {
            $_SESSION['workflow_success'] = 'Fluxo de trabalho atualizado com sucesso.';
            header('Location: /admin/workflows');
            exit;
        } else {
            $_SESSION['workflow_error'] = 'Erro ao atualizar fluxo de trabalho. Tente novamente.';
            header('Location: /admin/workflows/edit/' . $workflowId);
            exit;
        }
    }
    
    /**
     * Delete a workflow
     */
    public function deleteWorkflow($workflowId) {
        // Check if workflow is in use
        $documentsWithWorkflow = $this->workflowModel->getDocumentsUsingWorkflow($workflowId);
        
        if (count($documentsWithWorkflow) > 0) {
            $_SESSION['workflow_error'] = 'Não é possível excluir o fluxo de trabalho pois existem documentos vinculados a ele.';
            header('Location: /admin/workflows');
            exit;
        }
        
        // Delete workflow steps first
        $this->workflowStepModel->deleteWorkflowSteps($workflowId);
        
        // Delete workflow
        $result = $this->workflowModel->deleteWorkflow($workflowId);
        
        if ($result) {
            $_SESSION['workflow_success'] = 'Fluxo de trabalho excluído com sucesso.';
        } else {
            $_SESSION['workflow_error'] = 'Erro ao excluir fluxo de trabalho. Tente novamente.';
        }
        
        header('Location: /admin/workflows');
        exit;
    }
    
    /**
     * Display workflow steps
     */
    public function showWorkflowSteps($workflowId) {
        // Get workflow details
        $workflow = $this->workflowModel->getWorkflowById($workflowId);
        
        if (!$workflow) {
            $_SESSION['workflow_error'] = 'Fluxo de trabalho não encontrado.';
            header('Location: /admin/workflows');
            exit;
        }
        
        // Get workflow steps
        $steps = $this->workflowStepModel->getWorkflowStepsByWorkflow($workflowId);
        
        // Get all departments for select box
        $departments = $this->departmentModel->getAllDepartments();
        
        // Process alert messages
        $success = isset($_SESSION['workflow_step_success']) ? $_SESSION['workflow_step_success'] : null;
        $error = isset($_SESSION['workflow_step_error']) ? $_SESSION['workflow_step_error'] : null;
        
        // Clear session messages
        unset($_SESSION['workflow_step_success']);
        unset($_SESSION['workflow_step_error']);
        
        include 'views/admin/workflow_steps.php';
    }
    
    /**
     * Process workflow step add form submission
     */
    public function addWorkflowStep() {
        // Validate form data
        if (empty($_POST['workflow_id']) || empty($_POST['name']) || 
            empty($_POST['department_id']) || !isset($_POST['order']) || $_POST['order'] === '') {
            $_SESSION['workflow_step_error'] = 'Todos os campos são obrigatórios.';
            header('Location: /admin/workflows/steps/' . $_POST['workflow_id']);
            exit;
        }
        
        $workflowId = filter_var($_POST['workflow_id'], FILTER_SANITIZE_NUMBER_INT);
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $departmentId = filter_var($_POST['department_id'], FILTER_SANITIZE_NUMBER_INT);
        $order = filter_var($_POST['order'], FILTER_SANITIZE_NUMBER_INT);
        $description = filter_var($_POST['description'] ?? '', FILTER_SANITIZE_STRING);
        $isFinale = isset($_POST['is_finale']) ? 1 : 0;
        
        // Check if workflow exists
        $workflow = $this->workflowModel->getWorkflowById($workflowId);
        
        if (!$workflow) {
            $_SESSION['workflow_step_error'] = 'Fluxo de trabalho não encontrado.';
            header('Location: /admin/workflows');
            exit;
        }
        
        // Check if order is already in use
        $existingStep = $this->workflowStepModel->getWorkflowStepByOrder($workflowId, $order);
        
        if ($existingStep) {
            // Shift all steps with order >= the new order up by one
            $this->workflowStepModel->shiftWorkflowStepsOrder($workflowId, $order);
        }
        
        // Create new workflow step
        $stepId = $this->workflowStepModel->createWorkflowStep(
            $workflowId,
            $name,
            $description,
            $departmentId,
            $order,
            $isFinale
        );
        
        if ($stepId) {
            $_SESSION['workflow_step_success'] = 'Etapa cadastrada com sucesso.';
        } else {
            $_SESSION['workflow_step_error'] = 'Erro ao cadastrar etapa. Tente novamente.';
        }
        
        header('Location: /admin/workflows/steps/' . $workflowId);
        exit;
    }
    
    /**
     * Process workflow step edit form submission
     */
    public function updateWorkflowStep() {
        // Validate form data
        if (empty($_POST['id']) || empty($_POST['workflow_id']) || empty($_POST['name']) || 
            empty($_POST['department_id']) || !isset($_POST['order']) || $_POST['order'] === '') {
            $_SESSION['workflow_step_error'] = 'Todos os campos são obrigatórios.';
            header('Location: /admin/workflows/steps/' . $_POST['workflow_id']);
            exit;
        }
        
        $stepId = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
        $workflowId = filter_var($_POST['workflow_id'], FILTER_SANITIZE_NUMBER_INT);
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $departmentId = filter_var($_POST['department_id'], FILTER_SANITIZE_NUMBER_INT);
        $order = filter_var($_POST['order'], FILTER_SANITIZE_NUMBER_INT);
        $description = filter_var($_POST['description'] ?? '', FILTER_SANITIZE_STRING);
        $isFinale = isset($_POST['is_finale']) ? 1 : 0;
        
        // Get current step
        $currentStep = $this->workflowStepModel->getWorkflowStepById($stepId);
        
        if (!$currentStep) {
            $_SESSION['workflow_step_error'] = 'Etapa não encontrada.';
            header('Location: /admin/workflows/steps/' . $workflowId);
            exit;
        }
        
        // Check if order is changing
        if ($currentStep['order'] != $order) {
            // Check if order is already in use
            $existingStep = $this->workflowStepModel->getWorkflowStepByOrder($workflowId, $order);
            
            if ($existingStep) {
                // Shift all steps with order >= the new order up by one
                $this->workflowStepModel->shiftWorkflowStepsOrder($workflowId, $order);
            }
        }
        
        // Update workflow step
        $result = $this->workflowStepModel->updateWorkflowStep(
            $stepId,
            $name,
            $description,
            $departmentId,
            $order,
            $isFinale
        );
        
        if ($result) {
            $_SESSION['workflow_step_success'] = 'Etapa atualizada com sucesso.';
        } else {
            $_SESSION['workflow_step_error'] = 'Erro ao atualizar etapa. Tente novamente.';
        }
        
        header('Location: /admin/workflows/steps/' . $workflowId);
        exit;
    }
    
    /**
     * Delete a workflow step
     */
    public function deleteWorkflowStep($stepId) {
        // Get step details
        $step = $this->workflowStepModel->getWorkflowStepById($stepId);
        
        if (!$step) {
            $_SESSION['workflow_step_error'] = 'Etapa não encontrada.';
            header('Location: /admin/workflows');
            exit;
        }
        
        $workflowId = $step['workflow_id'];
        
        // Delete workflow step
        $result = $this->workflowStepModel->deleteWorkflowStep($stepId);
        
        if ($result) {
            // Reorder remaining steps
            $this->workflowStepModel->reorderWorkflowSteps($workflowId);
            
            $_SESSION['workflow_step_success'] = 'Etapa excluída com sucesso.';
        } else {
            $_SESSION['workflow_step_error'] = 'Erro ao excluir etapa. Tente novamente.';
        }
        
        header('Location: /admin/workflows/steps/' . $workflowId);
        exit;
    }
}
?>

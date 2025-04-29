<?php
/**
 * WorkflowStep model for handling workflow step data
 */
class WorkflowStep {
    private $conn;
    private $table = 'workflow_steps';
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Get all workflow steps for a specific workflow
     * 
     * @param int $workflowId Workflow ID
     * @return array Array of workflow steps
     */
    public function getWorkflowStepsByWorkflow($workflowId) {
        $query = "SELECT ws.*, d.name as department_name
                 FROM " . $this->table . " ws
                 LEFT JOIN departments d ON ws.department_id = d.id
                 WHERE ws.workflow_id = :workflow_id
                 ORDER BY ws.order ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':workflow_id', $workflowId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get a workflow step by ID
     * 
     * @param int $id Workflow step ID
     * @return array|false Workflow step data or false if not found
     */
    public function getWorkflowStepById($id) {
        $query = "SELECT ws.*, d.name as department_name
                 FROM " . $this->table . " ws
                 LEFT JOIN departments d ON ws.department_id = d.id
                 WHERE ws.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get the first step of a workflow
     * 
     * @param int $workflowId Workflow ID
     * @return array|false First step data or false if not found
     */
    public function getFirstWorkflowStep($workflowId) {
        $query = "SELECT ws.*, d.name as department_name
                 FROM " . $this->table . " ws
                 LEFT JOIN departments d ON ws.department_id = d.id
                 WHERE ws.workflow_id = :workflow_id
                 ORDER BY ws.order ASC
                 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':workflow_id', $workflowId);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get a workflow step by its order in a workflow
     * 
     * @param int $workflowId Workflow ID
     * @param int $order Step order
     * @return array|false Workflow step data or false if not found
     */
    public function getWorkflowStepByOrder($workflowId, $order) {
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE workflow_id = :workflow_id AND `order` = :order";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':workflow_id', $workflowId);
        $stmt->bindParam(':order', $order);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get the next step in a workflow
     * 
     * @param int $workflowId Workflow ID
     * @param int $currentOrder Current step order
     * @return array|false Next step data or false if not found
     */
    public function getNextWorkflowStep($workflowId, $currentOrder) {
        $query = "SELECT ws.*, d.name as department_name
                 FROM " . $this->table . " ws
                 LEFT JOIN departments d ON ws.department_id = d.id
                 WHERE ws.workflow_id = :workflow_id AND ws.order > :current_order
                 ORDER BY ws.order ASC
                 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':workflow_id', $workflowId);
        $stmt->bindParam(':current_order', $currentOrder);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Create a new workflow step
     * 
     * @param int $workflowId Workflow ID
     * @param string $name Step name
     * @param string $description Step description
     * @param int $departmentId Department ID
     * @param int $order Step order
     * @param bool $isFinale Whether this step is the final step
     * @return int|false ID of the new workflow step or false on failure
     */
    public function createWorkflowStep($workflowId, $name, $description, $departmentId, $order, $isFinale = false) {
        $query = "INSERT INTO " . $this->table . " 
                 (workflow_id, name, description, department_id, `order`, is_finale, created_at) 
                 VALUES 
                 (:workflow_id, :name, :description, :department_id, :order, :is_finale, NOW())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':workflow_id', $workflowId);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':department_id', $departmentId);
        $stmt->bindParam(':order', $order);
        $stmt->bindParam(':is_finale', $isFinale, PDO::PARAM_BOOL);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update an existing workflow step
     * 
     * @param int $id Workflow step ID
     * @param string $name Step name
     * @param string $description Step description
     * @param int $departmentId Department ID
     * @param int $order Step order
     * @param bool $isFinale Whether this step is the final step
     * @return bool Success or failure
     */
    public function updateWorkflowStep($id, $name, $description, $departmentId, $order, $isFinale = false) {
        $query = "UPDATE " . $this->table . " 
                 SET name = :name, description = :description, department_id = :department_id, 
                 `order` = :order, is_finale = :is_finale, updated_at = NOW() 
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':department_id', $departmentId);
        $stmt->bindParam(':order', $order);
        $stmt->bindParam(':is_finale', $isFinale, PDO::PARAM_BOOL);
        
        return $stmt->execute();
    }
    
    /**
     * Delete a workflow step
     * 
     * @param int $id Workflow step ID
     * @return bool Success or failure
     */
    public function deleteWorkflowStep($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Delete all steps for a workflow
     * 
     * @param int $workflowId Workflow ID
     * @return bool Success or failure
     */
    public function deleteWorkflowSteps($workflowId) {
        $query = "DELETE FROM " . $this->table . " WHERE workflow_id = :workflow_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':workflow_id', $workflowId);
        
        return $stmt->execute();
    }
    
    /**
     * Shift workflow steps order for a specific workflow
     * Used when inserting a new step in the middle of a workflow
     * 
     * @param int $workflowId Workflow ID
     * @param int $fromOrder Starting order to shift from
     * @return bool Success or failure
     */
    public function shiftWorkflowStepsOrder($workflowId, $fromOrder) {
        $query = "UPDATE " . $this->table . " 
                 SET `order` = `order` + 1, updated_at = NOW() 
                 WHERE workflow_id = :workflow_id AND `order` >= :from_order
                 ORDER BY `order` DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':workflow_id', $workflowId);
        $stmt->bindParam(':from_order', $fromOrder);
        
        return $stmt->execute();
    }
    
    /**
     * Reorder workflow steps after deleting a step
     * 
     * @param int $workflowId Workflow ID
     * @return bool Success or failure
     */
    public function reorderWorkflowSteps($workflowId) {
        // Get all remaining steps in order
        $query = "SELECT id FROM " . $this->table . " 
                 WHERE workflow_id = :workflow_id
                 ORDER BY `order` ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':workflow_id', $workflowId);
        $stmt->execute();
        
        $steps = $stmt->fetchAll();
        
        // Update each step with new consecutive order values
        $newOrder = 1;
        foreach ($steps as $step) {
            $updateQuery = "UPDATE " . $this->table . " 
                           SET `order` = :new_order, updated_at = NOW() 
                           WHERE id = :id";
            
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(':id', $step['id']);
            $updateStmt->bindParam(':new_order', $newOrder);
            $updateStmt->execute();
            
            $newOrder++;
        }
        
        return true;
    }
}
?>

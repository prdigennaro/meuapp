<?php
/**
 * AI API endpoints
 * Handles document generation using OpenAI
 */

/**
 * Generate a document based on template and variables
 * 
 * @param PDO $conn Database connection
 */
function handleGenerateDocument($conn) {
    // Check if method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJsonResponse(['error' => 'Method not allowed'], 405);
        return;
    }
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        sendJsonResponse(['error' => 'Unauthorized'], 401);
        return;
    }
    
    // Get request body
    $requestBody = file_get_contents('php://input');
    $data = json_decode($requestBody, true);
    
    // Validate request data
    if (!$data || !isset($data['template_id']) || !isset($data['variables']) || !isset($data['title'])) {
        sendJsonResponse(['error' => 'Invalid request data'], 400);
        return;
    }
    
    // Get template content
    $templateModel = new Template($conn);
    $template = $templateModel->getTemplateById($data['template_id']);
    
    if (!$template) {
        sendJsonResponse(['error' => 'Template not found'], 404);
        return;
    }
    
    // Extract template content
    $templateContent = $template['content'];
    
    try {
        // Get AI service
        $aiService = new AIService();
        
        // Generate document content
        $documentContent = $aiService->generateDocument($templateContent, $data['variables']);
        
        // Get document type from template
        $documentTypeId = $template['document_type_id'];
        
        // Get document type code for protocol generation
        $docTypeModel = new DocumentType($conn);
        $documentType = $docTypeModel->getDocumentTypeById($documentTypeId);
        $typeCode = $documentType['code'];
        
        // Generate protocol number
        $protocolNumber = generateProtocolNumber($typeCode);
        
        // Prepare document data
        $documentData = [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'document_type_id' => $documentTypeId,
            'template_id' => $data['template_id'],
            'protocol_number' => $protocolNumber,
            'content' => $documentContent,
            'variables' => json_encode($data['variables']),
            'status' => DOC_STATUS_DRAFT,
            'created_by' => $_SESSION['user_id'],
            'current_department_id' => $_SESSION['user_department']
        ];
        
        // Create document
        $documentModel = new Document($conn);
        $documentId = $documentModel->createDocument($documentData);
        
        if (!$documentId) {
            sendJsonResponse(['error' => 'Failed to create document'], 500);
            return;
        }
        
        // Log document creation
        logDocumentActivity($documentId, $_SESSION['user_id'], 'CREATE', 'Document created from template');
        
        // Send response
        sendJsonResponse([
            'success' => true,
            'message' => 'Document generated successfully',
            'document_id' => $documentId,
            'protocol_number' => $protocolNumber
        ]);
        
    } catch (Exception $e) {
        error_log('AI Document Generation Error: ' . $e->getMessage());
        sendJsonResponse(['error' => 'Error generating document: ' . $e->getMessage()], 500);
    }
}

/**
 * Send JSON response
 * 
 * @param array $data Response data
 * @param int $statusCode HTTP status code
 */
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>
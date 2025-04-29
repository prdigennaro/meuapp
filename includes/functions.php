<?php
/**
 * Utility functions for the Document Management System
 */

/**
 * Generate a protocol number for a document
 * 
 * @param string $type Document type code
 * @return string Protocol number in format CMA-TYPE-YEAR-SEQUENCE
 */
function generateProtocolNumber($type) {
    $year = date(PROTOCOL_DATE_FORMAT);
    
    // Get the last sequence number for this type and year
    global $conn;
    $sql = "SELECT MAX(CAST(SPLIT_PART(protocol_number, '-', 4) AS INTEGER)) as last_sequence 
            FROM documents 
            WHERE protocol_number LIKE :pattern";
    
    $stmt = $conn->prepare($sql);
    $pattern = PROTOCOL_PREFIX . $type . '-' . $year . '-%';
    $stmt->bindParam(':pattern', $pattern);
    $stmt->execute();
    
    $result = $stmt->fetch();
    $sequence = $result['last_sequence'] ? $result['last_sequence'] + 1 : 1;
    
    return PROTOCOL_PREFIX . $type . '-' . $year . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
}

/**
 * Format date according to Brazilian standard
 * 
 * @param string $date Date in Y-m-d format
 * @param bool $includeTime Whether to include time
 * @return string Formatted date
 */
function formatDate($date, $includeTime = false) {
    if (empty($date)) {
        return '';
    }
    
    $format = $includeTime ? 'd/m/Y H:i:s' : 'd/m/Y';
    return date($format, strtotime($date));
}

/**
 * Sanitize input data
 * 
 * @param string $data Input data
 * @return string Sanitized data
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Truncate text to a certain length
 * 
 * @param string $text The text to truncate
 * @param int $length Maximum length
 * @param string $suffix Suffix to add if truncated
 * @return string Truncated text
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . $suffix;
}

/**
 * Check if a string is a valid JSON
 * 
 * @param string $string String to check
 * @return bool True if valid JSON, false otherwise
 */
function isValidJson($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

/**
 * Generate a random string
 * 
 * @param int $length Length of the random string
 * @return string Random string
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    
    return $randomString;
}

/**
 * Extract variables from a template content
 * 
 * @param string $content Template content
 * @return array Array of variable names
 */
function extractTemplateVariables($content) {
    $matches = [];
    preg_match_all('/{{(.*?)}}/', $content, $matches);
    
    if (empty($matches[1])) {
        return [];
    }
    
    // Remove duplicates and trim variable names
    return array_values(array_unique(array_map('trim', $matches[1])));
}

/**
 * Check if a workflow exists for a document type
 * 
 * @param int $documentTypeId Document type ID
 * @return bool True if workflow exists, false otherwise
 */
function workflowExistsForDocumentType($documentTypeId) {
    global $conn;
    
    $sql = "SELECT COUNT(*) as count FROM workflows WHERE document_type_id = :document_type_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':document_type_id', $documentTypeId, PDO::PARAM_INT);
    $stmt->execute();
    
    $result = $stmt->fetch();
    return $result['count'] > 0;
}

/**
 * Get user full name by ID
 * 
 * @param int $userId User ID
 * @return string User full name
 */
function getUserName($userId) {
    global $conn;
    
    $sql = "SELECT name FROM users WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    $result = $stmt->fetch();
    return $result ? $result['name'] : 'Usuário Desconhecido';
}

/**
 * Get department name by ID
 * 
 * @param int $departmentId Department ID
 * @return string Department name
 */
function getDepartmentName($departmentId) {
    global $conn;
    
    $sql = "SELECT name FROM departments WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $departmentId, PDO::PARAM_INT);
    $stmt->execute();
    
    $result = $stmt->fetch();
    return $result ? $result['name'] : 'Departamento Desconhecido';
}

/**
 * Log document activity
 * 
 * @param int $documentId Document ID
 * @param int $userId User ID
 * @param string $action Action performed
 * @param string $comments Additional comments about the action
 * @return bool True if log entry created successfully, false otherwise
 */
function logDocumentActivity($documentId, $userId, $action, $comments = '') {
    global $conn;
    
    // Create a new DocumentLog instance
    $documentLog = new DocumentLog($conn);
    
    // Use the model method to log the action
    return $documentLog->logDocumentAction($documentId, $userId, $action, $comments);
}

/**
 * Get document status name in Portuguese
 * 
 * @param string $status Status code
 * @return string Status name in Portuguese
 */
function getDocumentStatusName($status) {
    switch ($status) {
        case DOC_STATUS_DRAFT:
            return 'Rascunho';
        case DOC_STATUS_PENDING:
            return 'Pendente';
        case DOC_STATUS_IN_REVIEW:
            return 'Em Análise';
        case DOC_STATUS_APPROVED:
            return 'Aprovado';
        case DOC_STATUS_REJECTED:
            return 'Rejeitado';
        case DOC_STATUS_COMPLETED:
            return 'Concluído';
        case DOC_STATUS_ARCHIVED:
            return 'Arquivado';
        default:
            return 'Desconhecido';
    }
}

/**
 * Get document status badge color class
 * 
 * @param string $status Status code
 * @return string CSS class for badge
 */
function getDocumentStatusBadgeClass($status) {
    switch ($status) {
        case DOC_STATUS_DRAFT:
            return 'bg-secondary';
        case DOC_STATUS_PENDING:
            return 'bg-warning text-dark';
        case DOC_STATUS_IN_REVIEW:
            return 'bg-info text-dark';
        case DOC_STATUS_APPROVED:
            return 'bg-success';
        case DOC_STATUS_REJECTED:
            return 'bg-danger';
        case DOC_STATUS_COMPLETED:
            return 'bg-primary';
        case DOC_STATUS_ARCHIVED:
            return 'bg-dark';
        default:
            return 'bg-secondary';
    }
}

/**
 * Check if file extension is allowed for upload
 * 
 * @param string $filename Filename
 * @return bool True if extension is allowed, false otherwise
 */
function isAllowedFileExtension($filename) {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($extension, ALLOWED_EXTENSIONS);
}

/**
 * Format file size in human-readable form
 * 
 * @param int $bytes Size in bytes
 * @return string Formatted size (e.g., "1.5 MB")
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, 2) . ' ' . $units[$pow];
}
?>
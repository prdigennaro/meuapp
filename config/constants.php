<?php
/**
 * Constants for the Document Management System
 */

// User roles
define('ROLE_ADMIN', 'admin');
define('ROLE_USER', 'user');

// Document statuses
define('DOC_STATUS_DRAFT', 'draft');
define('DOC_STATUS_PENDING', 'pending');
define('DOC_STATUS_IN_REVIEW', 'in_review');
define('DOC_STATUS_APPROVED', 'approved');
define('DOC_STATUS_REJECTED', 'rejected');
define('DOC_STATUS_COMPLETED', 'completed');
define('DOC_STATUS_ARCHIVED', 'archived');

// Document types
define('DOC_TYPE_ETP', 'ETP'); // Estudo Técnico Preliminar
define('DOC_TYPE_TR', 'TR'); // Termo de Referência
define('DOC_TYPE_EDITAL', 'EDITAL'); // Edital de Licitação
define('DOC_TYPE_DISPENSA', 'DISPENSA'); // Dispensa de Licitação
define('DOC_TYPE_OUTROS', 'OUTROS'); // Outros documentos

// Workflow actions
define('ACTION_CREATE', 'create');
define('ACTION_FORWARD', 'forward');
define('ACTION_APPROVE', 'approve');
define('ACTION_REJECT', 'reject');
define('ACTION_COMMENT', 'comment');
define('ACTION_REVISE', 'revise');
define('ACTION_COMPLETE', 'complete');
define('ACTION_ARCHIVE', 'archive');

// Default pagination settings
define('ITEMS_PER_PAGE', 10);

// Upload settings
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt']);
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

// Protocol number format
define('PROTOCOL_PREFIX', 'CMA-');
define('PROTOCOL_DATE_FORMAT', 'Y');
?>

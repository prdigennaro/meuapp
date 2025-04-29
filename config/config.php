<?php
/**
 * Configuration settings for the Document Management System
 */

// Application settings
define('APP_NAME', 'Sistema de Gestão Documental - Câmara Municipal de Arapongas');
define('APP_VERSION', '1.0.0');
define('APP_BASE_URL', 'http://localhost:5000');
define('APP_TIMEZONE', 'America/Sao_Paulo');

// Set default timezone
date_default_timezone_set(APP_TIMEZONE);

// OpenAI API settings
define('OPENAI_API_KEY', getenv('OPENAI_API_KEY') ?: '');
define('OPENAI_MODEL', 'gpt-4'); // Can be changed to other models as needed

// Debug settings
define('DEBUG_MODE', true);
if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// Session settings configured in index.php
?>

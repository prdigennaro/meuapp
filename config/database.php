<?php
/**
 * Database configuration for the Document Management System
 */

// Database settings
define('DB_HOST', getenv('PGHOST') ?: 'localhost');
define('DB_NAME', getenv('PGDATABASE') ?: 'document_management');
define('DB_USER', getenv('PGUSER') ?: 'postgres');
define('DB_PASS', getenv('PGPASSWORD') ?: '');
define('DB_PORT', getenv('PGPORT') ?: '5432');

// PDO settings
define('PDO_OPTIONS', [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false
]);

/**
 * Build a DSN (Data Source Name) string for the database connection
 * 
 * @return string DSN for PDO connection
 */
function getDsn() {
    return 'pgsql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';port=' . DB_PORT;
}
?>

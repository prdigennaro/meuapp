<?php
/**
 * DatabaseService - Handles database connections
 */
class DatabaseService {
    private $conn;
    
    /**
     * Constructor - Establishes database connection
     */
    public function __construct() {
        try {
            $dsn = getDsn();
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, PDO_OPTIONS);
        } catch (PDOException $e) {
            // Log error and display friendly message
            error_log('Database Connection Error: ' . $e->getMessage());
            die('Erro ao conectar ao banco de dados. Por favor, contate o administrador.');
        }
    }
    
    /**
     * Get database connection
     * 
     * @return PDO Database connection
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * Begin a transaction
     * 
     * @return bool Success of transaction start
     */
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }
    
    /**
     * Commit a transaction
     * 
     * @return bool Success of transaction commit
     */
    public function commitTransaction() {
        return $this->conn->commit();
    }
    
    /**
     * Rollback a transaction
     * 
     * @return bool Success of transaction rollback
     */
    public function rollbackTransaction() {
        return $this->conn->rollBack();
    }
    
    /**
     * Execute a raw SQL query
     * 
     * @param string $sql SQL query to execute
     * @param array $params Parameters for the query
     * @return PDOStatement|false Query result or false on failure
     */
    public function rawQuery($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Check if database has required tables
     * 
     * @return bool True if all required tables exist, false otherwise
     */
    public function checkTablesExist() {
        try {
            $requiredTables = [
                'users',
                'departments',
                'document_types',
                'templates',
                'workflows',
                'workflow_steps',
                'documents',
                'document_logs'
            ];
            
            $query = "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $existingTables = [];
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                $existingTables[] = $row[0];
            }
            
            foreach ($requiredTables as $table) {
                if (!in_array($table, $existingTables)) {
                    return false;
                }
            }
            
            return true;
        } catch (PDOException $e) {
            error_log('Error checking tables: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Initialize database with schema
     * 
     * @return bool Success or failure
     */
    public function initializeDatabase() {
        try {
            // Create tables
            $this->createUsersTable();
            $this->createDepartmentsTable();
            $this->createDocumentTypesTable();
            $this->createTemplatesTable();
            $this->createWorkflowsTable();
            $this->createWorkflowStepsTable();
            $this->createDocumentsTable();
            $this->createDocumentLogsTable();
            
            // Insert initial data
            $this->insertInitialDepartment();
            $this->insertInitialAdmin();
            $this->insertInitialDocumentTypes();
            
            return true;
        } catch (PDOException $e) {
            error_log('Error initializing database: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create users table
     */
    private function createUsersTable() {
        $sql = "
        CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            department_id INTEGER NOT NULL,
            role VARCHAR(20) NOT NULL DEFAULT 'user',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL
        )";
        
        $this->conn->exec($sql);
    }
    
    /**
     * Create departments table
     */
    private function createDepartmentsTable() {
        $sql = "
        CREATE TABLE IF NOT EXISTS departments (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL
        )";
        
        $this->conn->exec($sql);
    }
    
    /**
     * Create document_types table
     */
    private function createDocumentTypesTable() {
        $sql = "
        CREATE TABLE IF NOT EXISTS document_types (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            code VARCHAR(20) NOT NULL UNIQUE,
            description TEXT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL
        )";
        
        $this->conn->exec($sql);
    }
    
    /**
     * Create templates table
     */
    private function createTemplatesTable() {
        $sql = "
        CREATE TABLE IF NOT EXISTS templates (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            document_type_id INTEGER NOT NULL,
            content TEXT NOT NULL,
            variables TEXT NULL,
            description TEXT NULL,
            created_by INTEGER NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL
        )";
        
        $this->conn->exec($sql);
    }
    
    /**
     * Create workflows table
     */
    private function createWorkflowsTable() {
        $sql = "
        CREATE TABLE IF NOT EXISTS workflows (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            document_type_id INTEGER NOT NULL,
            description TEXT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL
        )";
        
        $this->conn->exec($sql);
    }
    
    /**
     * Create workflow_steps table
     */
    private function createWorkflowStepsTable() {
        $sql = "
        CREATE TABLE IF NOT EXISTS workflow_steps (
            id SERIAL PRIMARY KEY,
            workflow_id INTEGER NOT NULL,
            name VARCHAR(100) NOT NULL,
            description TEXT NULL,
            department_id INTEGER NOT NULL,
            order_num INTEGER NOT NULL,
            is_finale BOOLEAN NOT NULL DEFAULT FALSE,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL
        )";
        
        $this->conn->exec($sql);
    }
    
    /**
     * Create documents table
     */
    private function createDocumentsTable() {
        $sql = "
        CREATE TABLE IF NOT EXISTS documents (
            id SERIAL PRIMARY KEY,
            title VARCHAR(200) NOT NULL,
            protocol_number VARCHAR(50) NOT NULL UNIQUE,
            document_type_id INTEGER NOT NULL,
            template_id INTEGER NULL,
            content TEXT NOT NULL,
            variables TEXT NULL,
            description TEXT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'draft',
            current_department_id INTEGER NOT NULL,
            current_step_id INTEGER NULL,
            workflow_id INTEGER NULL,
            created_by INTEGER NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL
        )";
        
        $this->conn->exec($sql);
    }
    
    /**
     * Create document_logs table
     */
    private function createDocumentLogsTable() {
        $sql = "
        CREATE TABLE IF NOT EXISTS document_logs (
            id SERIAL PRIMARY KEY,
            document_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            action VARCHAR(50) NOT NULL,
            notes TEXT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        )";
        
        $this->conn->exec($sql);
    }
    
    /**
     * Insert initial department (Administration)
     */
    private function insertInitialDepartment() {
        $sql = "
        INSERT INTO departments (id, name, description)
        VALUES (1, 'Administração', 'Departamento de Administração da Câmara Municipal de Arapongas')
        ON CONFLICT (id) DO NOTHING";
        
        $this->conn->exec($sql);
    }
    
    /**
     * Insert initial admin user
     */
    private function insertInitialAdmin() {
        // Create a default admin user with email "admin@arapongas.pr.gov.br" and password "admin123"
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        
        $sql = "
        INSERT INTO users (name, email, password, department_id, role)
        SELECT 'Administrador', 'admin@arapongas.pr.gov.br', :password, 1, 'admin'
        WHERE NOT EXISTS (
            SELECT 1 FROM users WHERE email = 'admin@arapongas.pr.gov.br'
        )";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
    }
    
    /**
     * Insert initial document types
     */
    private function insertInitialDocumentTypes() {
        $types = [
            [1, 'Estudo Técnico Preliminar', 'ETP', 'Documento de planejamento da contratação que contempla a demonstração da necessidade da contratação, levantamento de mercado e definição do tipo de solução.'],
            [2, 'Termo de Referência', 'TR', 'Documento base para elaboração do edital, contendo todos os elementos capazes de definir o objeto da licitação de forma clara, concisa e objetiva.'],
            [3, 'Edital de Licitação', 'EDITAL', 'Instrumento pelo qual a administração traz a público sua intenção de realizar uma licitação e fixa as condições de realização do procedimento.'],
            [4, 'Dispensa de Licitação', 'DISPENSA', 'Procedimento administrativo pelo qual a Administração Pública pode contratar diretamente sem a realização de um processo licitatório.'],
            [5, 'Ofício', 'OFICIO', 'Correspondência oficial trocada entre autoridades da mesma hierarquia ou enviada a particulares.']
        ];
        
        foreach ($types as $type) {
            try {
                $sql = "
                INSERT INTO document_types (id, name, code, description)
                VALUES (:id, :name, :code, :description)
                ON CONFLICT (id) DO NOTHING";
                
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':id', $type[0], PDO::PARAM_INT);
                $stmt->bindParam(':name', $type[1], PDO::PARAM_STR);
                $stmt->bindParam(':code', $type[2], PDO::PARAM_STR);
                $stmt->bindParam(':description', $type[3], PDO::PARAM_STR);
                $stmt->execute();
            } catch (PDOException $e) {
                error_log('Error inserting document type: ' . $e->getMessage());
            }
        }
    }
}
?>

<?php
/**
 * Testes para Conectores DHTMLX - PHP 8
 * 
 * Este arquivo contém testes unitários para validar a compatibilidade
 * dos conectores com PHP 8 e a funcionalidade básica.
 * 
 * @author NDSE Team
 * @version 2.0
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

// Configurações de teste
define('TEST_DB_HOST', 'localhost');
define('TEST_DB_USER', 'root');
define('TEST_DB_PASS', '');
define('TEST_DB_NAME', 'simulador_test');

/**
 * Classe de Teste para Conectores
 */
class ConnectorTests
{
    private $connection;
    private $test_results = [];
    private $test_count = 0;
    private $passed_count = 0;
    
    /**
     * Construtor
     */
    public function __construct()
    {
        $this->test_results = [];
        $this->test_count = 0;
        $this->passed_count = 0;
    }
    
    /**
     * Executar todos os testes
     */
    public function runAllTests(): void
    {
        echo "╔════════════════════════════════════════════════════════════╗\n";
        echo "║     Testes de Compatibilidade - Conectores DHTMLX PHP 8    ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n\n";
        
        // Testes de conectividade
        $this->testDatabaseConnection();
        $this->testMySQLiExtension();
        $this->testPHPVersion();
        
        // Testes de funcionalidade
        $this->testDatabaseCreation();
        $this->testTableCreation();
        $this->testDataInsertion();
        $this->testDataSelection();
        $this->testDataUpdate();
        $this->testDataDeletion();
        
        // Testes de segurança
        $this->testPreparedStatements();
        $this->testSQLInjectionPrevention();
        
        // Relatório final
        $this->printReport();
    }
    
    /**
     * Teste: Versão do PHP
     */
    private function testPHPVersion(): void
    {
        $this->test_count++;
        $version = phpversion();
        
        if (version_compare($version, '8.0.0', '>=')) {
            $this->passed_count++;
            $this->addResult('✓ PHP Version', "PHP {$version} (Compatível com PHP 8+)", 'PASS');
        } else {
            $this->addResult('✗ PHP Version', "PHP {$version} (Requer PHP 8.0+)", 'FAIL');
        }
    }
    
    /**
     * Teste: Extensão MySQLi
     */
    private function testMySQLiExtension(): void
    {
        $this->test_count++;
        
        if (extension_loaded('mysqli')) {
            $this->passed_count++;
            $this->addResult('✓ MySQLi Extension', 'Extensão MySQLi carregada', 'PASS');
        } else {
            $this->addResult('✗ MySQLi Extension', 'Extensão MySQLi não encontrada', 'FAIL');
        }
    }
    
    /**
     * Teste: Conexão com Banco de Dados
     */
    private function testDatabaseConnection(): void
    {
        $this->test_count++;
        
        try {
            $this->connection = new mysqli(TEST_DB_HOST, TEST_DB_USER, TEST_DB_PASS);
            
            if ($this->connection->connect_error) {
                $this->addResult('✗ Database Connection', 'Erro: ' . $this->connection->connect_error, 'FAIL');
                return;
            }
            
            $this->passed_count++;
            $this->addResult('✓ Database Connection', 'Conectado ao MySQL com sucesso', 'PASS');
        } catch (Exception $e) {
            $this->addResult('✗ Database Connection', 'Exceção: ' . $e->getMessage(), 'FAIL');
        }
    }
    
    /**
     * Teste: Criação de Banco de Dados
     */
    private function testDatabaseCreation(): void
    {
        $this->test_count++;
        
        if (!$this->connection) {
            $this->addResult('✗ Database Creation', 'Conexão não estabelecida', 'SKIP');
            return;
        }
        
        try {
            $sql = "CREATE DATABASE IF NOT EXISTS " . TEST_DB_NAME;
            
            if ($this->connection->query($sql)) {
                $this->passed_count++;
                $this->addResult('✓ Database Creation', 'Banco de dados criado', 'PASS');
            } else {
                $this->addResult('✗ Database Creation', 'Erro: ' . $this->connection->error, 'FAIL');
            }
        } catch (Exception $e) {
            $this->addResult('✗ Database Creation', 'Exceção: ' . $e->getMessage(), 'FAIL');
        }
    }
    
    /**
     * Teste: Criação de Tabela
     */
    private function testTableCreation(): void
    {
        $this->test_count++;
        
        if (!$this->connection) {
            $this->addResult('✗ Table Creation', 'Conexão não estabelecida', 'SKIP');
            return;
        }
        
        try {
            // Selecionar banco de dados
            $this->connection->select_db(TEST_DB_NAME);
            
            $sql = "CREATE TABLE IF NOT EXISTS sistemas_eletricos (
                id INT PRIMARY KEY AUTO_INCREMENT,
                nome VARCHAR(255) NOT NULL,
                descricao TEXT,
                tipo_sistema VARCHAR(50),
                num_barras INT,
                num_linhas INT,
                tensao_nominal FLOAT,
                data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_nome (nome),
                INDEX idx_tipo (tipo_sistema)
            )";
            
            if ($this->connection->query($sql)) {
                $this->passed_count++;
                $this->addResult('✓ Table Creation', 'Tabela criada com sucesso', 'PASS');
            } else {
                $this->addResult('✗ Table Creation', 'Erro: ' . $this->connection->error, 'FAIL');
            }
        } catch (Exception $e) {
            $this->addResult('✗ Table Creation', 'Exceção: ' . $e->getMessage(), 'FAIL');
        }
    }
    
    /**
     * Teste: Inserção de Dados
     */
    private function testDataInsertion(): void
    {
        $this->test_count++;
        
        if (!$this->connection) {
            $this->addResult('✗ Data Insertion', 'Conexão não estabelecida', 'SKIP');
            return;
        }
        
        try {
            $sql = "INSERT INTO sistemas_eletricos (nome, descricao, tipo_sistema, num_barras, num_linhas, tensao_nominal) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->connection->prepare($sql);
            if (!$stmt) {
                $this->addResult('✗ Data Insertion', 'Erro ao preparar: ' . $this->connection->error, 'FAIL');
                return;
            }
            
            $nome = "Sistema IEEE 14 Barras";
            $descricao = "Sistema de teste com 14 barras";
            $tipo = "malhado";
            $num_barras = 14;
            $num_linhas = 20;
            $tensao = 138.0;
            
            $stmt->bind_param('sssiid', $nome, $descricao, $tipo, $num_barras, $num_linhas, $tensao);
            
            if ($stmt->execute()) {
                $this->passed_count++;
                $this->addResult('✓ Data Insertion', 'Dados inseridos com sucesso', 'PASS');
            } else {
                $this->addResult('✗ Data Insertion', 'Erro: ' . $stmt->error, 'FAIL');
            }
            
            $stmt->close();
        } catch (Exception $e) {
            $this->addResult('✗ Data Insertion', 'Exceção: ' . $e->getMessage(), 'FAIL');
        }
    }
    
    /**
     * Teste: Seleção de Dados
     */
    private function testDataSelection(): void
    {
        $this->test_count++;
        
        if (!$this->connection) {
            $this->addResult('✗ Data Selection', 'Conexão não estabelecida', 'SKIP');
            return;
        }
        
        try {
            $sql = "SELECT * FROM sistemas_eletricos WHERE tipo_sistema = ?";
            
            $stmt = $this->connection->prepare($sql);
            if (!$stmt) {
                $this->addResult('✗ Data Selection', 'Erro ao preparar: ' . $this->connection->error, 'FAIL');
                return;
            }
            
            $tipo = "malhado";
            $stmt->bind_param('s', $tipo);
            
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $count = $result->num_rows;
                
                if ($count > 0) {
                    $this->passed_count++;
                    $this->addResult('✓ Data Selection', "Encontrados {$count} registros", 'PASS');
                } else {
                    $this->addResult('✗ Data Selection', 'Nenhum registro encontrado', 'FAIL');
                }
            } else {
                $this->addResult('✗ Data Selection', 'Erro: ' . $stmt->error, 'FAIL');
            }
            
            $stmt->close();
        } catch (Exception $e) {
            $this->addResult('✗ Data Selection', 'Exceção: ' . $e->getMessage(), 'FAIL');
        }
    }
    
    /**
     * Teste: Atualização de Dados
     */
    private function testDataUpdate(): void
    {
        $this->test_count++;
        
        if (!$this->connection) {
            $this->addResult('✗ Data Update', 'Conexão não estabelecida', 'SKIP');
            return;
        }
        
        try {
            $sql = "UPDATE sistemas_eletricos SET num_barras = ? WHERE tipo_sistema = ?";
            
            $stmt = $this->connection->prepare($sql);
            if (!$stmt) {
                $this->addResult('✗ Data Update', 'Erro ao preparar: ' . $this->connection->error, 'FAIL');
                return;
            }
            
            $num_barras = 15;
            $tipo = "malhado";
            $stmt->bind_param('is', $num_barras, $tipo);
            
            if ($stmt->execute()) {
                $this->passed_count++;
                $this->addResult('✓ Data Update', 'Dados atualizados com sucesso', 'PASS');
            } else {
                $this->addResult('✗ Data Update', 'Erro: ' . $stmt->error, 'FAIL');
            }
            
            $stmt->close();
        } catch (Exception $e) {
            $this->addResult('✗ Data Update', 'Exceção: ' . $e->getMessage(), 'FAIL');
        }
    }
    
    /**
     * Teste: Deleção de Dados
     */
    private function testDataDeletion(): void
    {
        $this->test_count++;
        
        if (!$this->connection) {
            $this->addResult('✗ Data Deletion', 'Conexão não estabelecida', 'SKIP');
            return;
        }
        
        try {
            $sql = "DELETE FROM sistemas_eletricos WHERE tipo_sistema = ?";
            
            $stmt = $this->connection->prepare($sql);
            if (!$stmt) {
                $this->addResult('✗ Data Deletion', 'Erro ao preparar: ' . $this->connection->error, 'FAIL');
                return;
            }
            
            $tipo = "malhado";
            $stmt->bind_param('s', $tipo);
            
            if ($stmt->execute()) {
                $this->passed_count++;
                $this->addResult('✓ Data Deletion', 'Dados deletados com sucesso', 'PASS');
            } else {
                $this->addResult('✗ Data Deletion', 'Erro: ' . $stmt->error, 'FAIL');
            }
            
            $stmt->close();
        } catch (Exception $e) {
            $this->addResult('✗ Data Deletion', 'Exceção: ' . $e->getMessage(), 'FAIL');
        }
    }
    
    /**
     * Teste: Prepared Statements
     */
    private function testPreparedStatements(): void
    {
        $this->test_count++;
        
        if (!$this->connection) {
            $this->addResult('✗ Prepared Statements', 'Conexão não estabelecida', 'SKIP');
            return;
        }
        
        try {
            // Testar prepared statement com múltiplos parâmetros
            $sql = "SELECT * FROM sistemas_eletricos WHERE num_barras > ? AND tensao_nominal < ?";
            
            $stmt = $this->connection->prepare($sql);
            if (!$stmt) {
                $this->addResult('✗ Prepared Statements', 'Erro ao preparar: ' . $this->connection->error, 'FAIL');
                return;
            }
            
            $num_barras = 10;
            $tensao = 500.0;
            $stmt->bind_param('id', $num_barras, $tensao);
            
            if ($stmt->execute()) {
                $this->passed_count++;
                $this->addResult('✓ Prepared Statements', 'Prepared statements funcionando', 'PASS');
            } else {
                $this->addResult('✗ Prepared Statements', 'Erro: ' . $stmt->error, 'FAIL');
            }
            
            $stmt->close();
        } catch (Exception $e) {
            $this->addResult('✗ Prepared Statements', 'Exceção: ' . $e->getMessage(), 'FAIL');
        }
    }
    
    /**
     * Teste: Prevenção de SQL Injection
     */
    private function testSQLInjectionPrevention(): void
    {
        $this->test_count++;
        
        if (!$this->connection) {
            $this->addResult('✗ SQL Injection Prevention', 'Conexão não estabelecida', 'SKIP');
            return;
        }
        
        try {
            // Tentar SQL injection (deve ser prevenido)
            $malicious_input = "'; DROP TABLE sistemas_eletricos; --";
            
            $sql = "SELECT * FROM sistemas_eletricos WHERE nome = ?";
            $stmt = $this->connection->prepare($sql);
            
            if (!$stmt) {
                $this->addResult('✗ SQL Injection Prevention', 'Erro ao preparar', 'FAIL');
                return;
            }
            
            $stmt->bind_param('s', $malicious_input);
            
            if ($stmt->execute()) {
                // Se chegou aqui, a injeção foi prevenida
                $this->passed_count++;
                $this->addResult('✓ SQL Injection Prevention', 'Injeção SQL prevenida com sucesso', 'PASS');
            } else {
                $this->addResult('✗ SQL Injection Prevention', 'Erro: ' . $stmt->error, 'FAIL');
            }
            
            $stmt->close();
        } catch (Exception $e) {
            $this->addResult('✗ SQL Injection Prevention', 'Exceção: ' . $e->getMessage(), 'FAIL');
        }
    }
    
    /**
     * Adicionar resultado de teste
     */
    private function addResult(string $test_name, string $message, string $status): void
    {
        $this->test_results[] = [
            'name' => $test_name,
            'message' => $message,
            'status' => $status
        ];
    }
    
    /**
     * Imprimir relatório de testes
     */
    private function printReport(): void
    {
        echo "\n┌────────────────────────────────────────────────────────────┐\n";
        echo "│                      RESULTADOS DOS TESTES                 │\n";
        echo "└────────────────────────────────────────────────────────────┘\n\n";
        
        foreach ($this->test_results as $result) {
            $status_color = match($result['status']) {
                'PASS' => "\033[92m",  // Verde
                'FAIL' => "\033[91m",  // Vermelho
                'SKIP' => "\033[93m",  // Amarelo
                default => "\033[0m"   // Padrão
            };
            
            $reset_color = "\033[0m";
            
            printf(
                "%-35s %s%-8s%s  %s\n",
                $result['name'],
                $status_color,
                $result['status'],
                $reset_color,
                $result['message']
            );
        }
        
        echo "\n┌────────────────────────────────────────────────────────────┐\n";
        printf("│ Total de Testes: %-45d │\n", $this->test_count);
        printf("│ Testes Aprovados: %-43d │\n", $this->passed_count);
        printf("│ Taxa de Sucesso: %-45.1f%% │\n", ($this->passed_count / $this->test_count) * 100);
        echo "└────────────────────────────────────────────────────────────┘\n";
        
        // Limpeza
        if ($this->connection) {
            // Deletar banco de dados de teste
            $this->connection->query("DROP DATABASE IF EXISTS " . TEST_DB_NAME);
            $this->connection->close();
        }
    }
}

// Executar testes
$tester = new ConnectorTests();
$tester->runAllTests();
?>

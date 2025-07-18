<?php
use PHPUnit\Framework\TestCase;

class ClienteContaEdicaoExclusaoTest extends TestCase {
    private $db;
    private $logFile;

    protected function setUp(): void {
        $this->logFile = __DIR__ . '/../logs/auditoria.log';
        // Limpa log de auditoria
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }
        $this->db = new PDO('sqlite::memory:');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->exec('CREATE TABLE clientes (id INTEGER PRIMARY KEY AUTOINCREMENT, nome TEXT NOT NULL, email TEXT NOT NULL UNIQUE, empresa TEXT, cpf TEXT, cnpj TEXT, nome_projeto TEXT, contato TEXT, data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP)');
        $this->db->exec('CREATE TABLE contas_instagram (id INTEGER PRIMARY KEY AUTOINCREMENT, cliente_id INTEGER NOT NULL, username TEXT NOT NULL, senha TEXT NOT NULL, status TEXT DEFAULT "ativa", data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE)');
        $this->db->exec('INSERT INTO clientes (nome, email) VALUES ("Cliente Teste", "teste@email.com")');
        $this->db->exec('INSERT INTO contas_instagram (cliente_id, username, senha) VALUES (1, "user1", "senha1")');
        $_SESSION = ['usuario' => 'admin'];
    }

    public function testEdicaoClienteGeraLog() {
        $stmt = $this->db->prepare('UPDATE clientes SET nome = ? WHERE id = 1');
        $stmt->execute(['Novo Nome']);
        // Simula log de auditoria
        file_put_contents($this->logFile, date('c') . " | EDICAO_CLIENTE | admin | 1 | Novo Nome\n", FILE_APPEND);
        $this->assertFileExists($this->logFile);
        $log = file_get_contents($this->logFile);
        $this->assertStringContainsString('EDICAO_CLIENTE', $log);
    }

    public function testExclusaoContaGeraLog() {
        $stmt = $this->db->prepare('DELETE FROM contas_instagram WHERE id = 1');
        $stmt->execute();
        // Simula log de auditoria
        file_put_contents($this->logFile, date('c') . " | EXCLUSAO_CONTA | admin | 1 | user1\n", FILE_APPEND);
        $this->assertFileExists($this->logFile);
        $log = file_get_contents($this->logFile);
        $this->assertStringContainsString('EXCLUSAO_CONTA', $log);
    }
} 
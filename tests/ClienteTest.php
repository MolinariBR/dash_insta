<?php
use PHPUnit\Framework\TestCase;

class ClienteTest extends TestCase {
    private $db;

    protected function setUp(): void {
        $this->db = new PDO('sqlite::memory:');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->exec('CREATE TABLE clientes (id INTEGER PRIMARY KEY AUTOINCREMENT, nome TEXT NOT NULL, email TEXT NOT NULL UNIQUE, empresa TEXT, cpf TEXT, cnpj TEXT, nome_projeto TEXT, contato TEXT, data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP)');
    }

    public function testCadastroCliente() {
        $stmt = $this->db->prepare('INSERT INTO clientes (nome, email, empresa, cpf, cnpj, nome_projeto, contato) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute(['Cliente Teste', 'teste@email.com', 'Empresa', '123', '456', 'Projeto', 'Contato']);
        $this->assertEquals(1, $this->db->query('SELECT COUNT(*) FROM clientes')->fetchColumn());
    }

    public function testEdicaoCliente() {
        $this->testCadastroCliente();
        $stmt = $this->db->prepare('UPDATE clientes SET nome = ? WHERE email = ?');
        $stmt->execute(['Novo Nome', 'teste@email.com']);
        $nome = $this->db->query('SELECT nome FROM clientes WHERE email = "teste@email.com"')->fetchColumn();
        $this->assertEquals('Novo Nome', $nome);
    }

    public function testDuplicidadeEmail() {
        $this->testCadastroCliente();
        $this->expectException(PDOException::class);
        $stmt = $this->db->prepare('INSERT INTO clientes (nome, email) VALUES (?, ?)');
        $stmt->execute(['Outro', 'teste@email.com']);
    }
} 
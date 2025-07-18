<?php
use PHPUnit\Framework\TestCase;

class ContaInstagramTest extends TestCase {
    private $db;

    protected function setUp(): void {
        $this->db = new PDO('sqlite::memory:');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->exec('CREATE TABLE clientes (id INTEGER PRIMARY KEY AUTOINCREMENT, nome TEXT NOT NULL, email TEXT NOT NULL UNIQUE)');
        $this->db->exec('CREATE TABLE contas_instagram (id INTEGER PRIMARY KEY AUTOINCREMENT, cliente_id INTEGER NOT NULL, username TEXT NOT NULL, senha TEXT NOT NULL, status TEXT DEFAULT "ativa", data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE)');
        $this->db->exec('INSERT INTO clientes (nome, email) VALUES ("Cliente 1", "c1@email.com")');
    }

    public function testCadastroContaInstagram() {
        $stmt = $this->db->prepare('INSERT INTO contas_instagram (cliente_id, username, senha) VALUES (?, ?, ?)');
        $stmt->execute([1, 'user1', 'senha1']);
        $this->assertEquals(1, $this->db->query('SELECT COUNT(*) FROM contas_instagram')->fetchColumn());
    }

    public function testEdicaoContaInstagram() {
        $this->testCadastroContaInstagram();
        $stmt = $this->db->prepare('UPDATE contas_instagram SET username = ? WHERE id = 1');
        $stmt->execute(['novo_user']);
        $username = $this->db->query('SELECT username FROM contas_instagram WHERE id = 1')->fetchColumn();
        $this->assertEquals('novo_user', $username);
    }

    public function testExclusaoContaInstagram() {
        $this->testCadastroContaInstagram();
        $stmt = $this->db->prepare('DELETE FROM contas_instagram WHERE id = 1');
        $stmt->execute();
        $this->assertEquals(0, $this->db->query('SELECT COUNT(*) FROM contas_instagram')->fetchColumn());
    }

    public function testAssociacaoClienteConta() {
        $this->testCadastroContaInstagram();
        $cliente_id = $this->db->query('SELECT cliente_id FROM contas_instagram WHERE id = 1')->fetchColumn();
        $this->assertEquals(1, $cliente_id);
    }
} 
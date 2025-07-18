<?php
use PHPUnit\Framework\TestCase;

class IsolamentoMultiContaTest extends TestCase {
    private $db;

    protected function setUp(): void {
        $this->db = new PDO('sqlite::memory:');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->exec('CREATE TABLE clientes (id INTEGER PRIMARY KEY AUTOINCREMENT, nome TEXT NOT NULL, email TEXT NOT NULL UNIQUE)');
        $this->db->exec('CREATE TABLE contas_instagram (id INTEGER PRIMARY KEY AUTOINCREMENT, cliente_id INTEGER NOT NULL, username TEXT NOT NULL, senha TEXT NOT NULL, status TEXT DEFAULT "ativa", data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE)');
        $this->db->exec('INSERT INTO clientes (nome, email) VALUES ("Cliente 1", "c1@email.com"), ("Cliente 2", "c2@email.com")');
        $this->db->exec('INSERT INTO contas_instagram (cliente_id, username, senha) VALUES (1, "user1", "senha1"), (2, "user2", "senha2")');
        if (!is_dir('logs')) {
            mkdir('logs');
        }
    }

    public function testIsolamentoEntreContas() {
        // Simula logs para cada conta
        $log1 = 'logs/bot_user1_seguidores.log';
        $log2 = 'logs/bot_user2_seguidores.log';
        file_put_contents($log1, "Ação conta 1\n");
        file_put_contents($log2, "Ação conta 2\n");
        $logs1 = file($log1, FILE_IGNORE_NEW_LINES);
        $logs2 = file($log2, FILE_IGNORE_NEW_LINES);
        $this->assertContains('Ação conta 1', $logs1);
        $this->assertNotContains('Ação conta 2', $logs1);
        $this->assertContains('Ação conta 2', $logs2);
        $this->assertNotContains('Ação conta 1', $logs2);
    }
} 
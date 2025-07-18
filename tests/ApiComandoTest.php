<?php
use PHPUnit\Framework\TestCase;

class ApiComandoTest extends TestCase {
    private $logFile;

    protected function setUp(): void {
        $this->logFile = __DIR__ . '/../logs/auditoria.log';
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }
        $_SESSION = ['logged_in' => true, 'usuario' => 'admin', 'login_time' => time()];
        $_SERVER['REQUEST_METHOD'] = 'POST';
    }

    public function testComandoEndpoint() {
        // Simula conta existente
        $conta_id = 1;
        $acao = 'iniciar';
        // Cria conta fake no banco
        $db = new PDO('sqlite:' . __DIR__ . '/../data/database.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->exec('CREATE TABLE IF NOT EXISTS contas_instagram (id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT NOT NULL)');
        $db->exec('INSERT OR IGNORE INTO contas_instagram (id, username) VALUES (1, "user1")');
        // Simula POST
        $data = [
            'conta_id' => $conta_id,
            'acao' => $acao,
            'parametros' => []
        ];
        $input = json_encode($data);
        // Simula php://input
        $GLOBALS['HTTP_RAW_POST_DATA'] = $input;
        $GLOBALS['__PHP_INPUT__'] = $input;
        // Redefine php://input para leitura
        stream_wrapper_restore('php');
        ob_start();
        require __DIR__ . '/../api/comando.php';
        $output = ob_get_clean();
        $json = json_decode($output, true);
        $this->assertIsArray($json);
        $this->assertTrue($json['success']);
        $this->assertEquals('iniciar', $json['acao']);
        $this->assertEquals('user1', $json['conta']);
        $this->assertFileExists($this->logFile);
        $log = file_get_contents($this->logFile);
        $this->assertStringContainsString('API_COMANDO', $log);
        $this->assertStringContainsString('iniciar', $log);
    }
} 
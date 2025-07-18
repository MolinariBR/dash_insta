<?php
use PHPUnit\Framework\TestCase;

class ApiStatusTest extends TestCase {
    public function testStatusEndpoint() {
        // Simula sessão logada
        $_SESSION = ['logged_in' => true, 'usuario' => 'admin', 'login_time' => time()];
        $_SERVER['REQUEST_METHOD'] = 'GET';
        ob_start();
        require __DIR__ . '/../api/status.php';
        $output = ob_get_clean();
        $json = json_decode($output, true);
        $this->assertIsArray($json);
        $this->assertTrue($json['success']);
        $this->assertArrayHasKey('robos', $json);
        $this->assertArrayHasKey('metricas', $json);
        $this->assertArrayHasKey('total_contas', $json['metricas']);
    }
} 
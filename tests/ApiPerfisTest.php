<?php
use PHPUnit\Framework\TestCase;

class ApiPerfisTest extends TestCase {
    public function testPerfisEndpoint() {
        $_SESSION = ['logged_in' => true, 'usuario' => 'admin', 'login_time' => time()];
        $_SERVER['REQUEST_METHOD'] = 'GET';
        ob_start();
        require __DIR__ . '/../api/perfis.php';
        $output = ob_get_clean();
        $json = json_decode($output, true);
        $this->assertIsArray($json);
        $this->assertTrue($json['success']);
        $this->assertArrayHasKey('perfis', $json);
        $this->assertArrayHasKey('total', $json);
        $this->assertIsArray($json['perfis']);
    }
} 
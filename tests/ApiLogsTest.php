<?php
use PHPUnit\Framework\TestCase;

class ApiLogsTest extends TestCase {
    public function testLogsEndpoint() {
        $_SESSION = ['logged_in' => true, 'usuario' => 'admin', 'login_time' => time()];
        $_GET['type'] = 'seguidores';
        $_GET['limit'] = 2;
        ob_start();
        require __DIR__ . '/../api/logs.php';
        $output = ob_get_clean();
        $json = json_decode($output, true);
        $this->assertIsArray($json);
        $this->assertTrue($json['success']);
        $this->assertArrayHasKey('logs', $json);
        $this->assertArrayHasKey('type', $json);
        $this->assertArrayHasKey('count', $json);
        $this->assertIsArray($json['logs']);
    }
} 
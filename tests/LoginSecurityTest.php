<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../includes/auth_core.php';

if (!function_exists('authenticate')) {
    function authenticate($password) {
        if ($password === 'teste123') {
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();
            $_SESSION['usuario'] = 'admin';
            return true;
        }
        return false;
    }
}

class LoginSecurityTest extends TestCase {
    private $logFile;

    protected function setUp(): void {
        $this->logFile = __DIR__ . '/../logs/auditoria.log';
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }
        if (!defined('DASHBOARD_PASSWORD')) {
            define('DASHBOARD_PASSWORD', 'teste123');
        }
        if (!defined('SESSION_TIMEOUT')) {
            define('SESSION_TIMEOUT', 3600);
        }
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function testLoginSucesso() {
        $result = processLoginCore('teste123');
        $this->assertTrue($result['success']);
        $this->assertTrue(isset($_SESSION['logged_in']) && $_SESSION['logged_in']);
        $this->assertFileExists($this->logFile);
        $log = file_get_contents($this->logFile);
        $this->assertStringContainsString('LOGIN_SUCESSO', $log);
    }

    public function testLoginFalha() {
        $result = processLoginCore('errada');
        $this->assertFalse($result['success']);
        $this->assertFileExists($this->logFile);
        $log = file_get_contents($this->logFile);
        $this->assertStringContainsString('LOGIN_FALHA', $log);
    }

    public function testBruteForceBloqueio() {
        for ($i = 0; $i < 5; $i++) {
            processLoginCore('errada');
        }
        $this->assertEquals(5, $_SESSION['login_attempts']);
        $this->assertGreaterThan(time(), $_SESSION['login_blocked_until']);
        // Nova tentativa deve ser bloqueada
        $result = processLoginCore('teste123');
        $this->assertFalse($result['success']);
    }
} 
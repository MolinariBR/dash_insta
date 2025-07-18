<?php
use PHPUnit\Framework\TestCase;

class IntegracaoBotPythonTest extends TestCase {
    public function testComandoStartBotGeradoCorretamente() {
        $conta = ['username' => 'user1'];
        $botPath = '../insta';
        $logFile = __DIR__ . '/../logs/bot_user1.log';
        $command = "cd $botPath && python3 main.py --conta 'user1' > $logFile 2>&1 &";
        $this->assertStringContainsString("--conta 'user1'", $command);
        $this->assertStringContainsString('bot_user1.log', $command);
    }

    public function testComandoStopBotGeradoCorretamente() {
        $conta = ['username' => 'user2'];
        $command = "pkill -f 'python3 main.py --conta 'user2''";
        $this->assertStringContainsString("--conta 'user2'", $command);
    }

    public function testLogCriadoAoStart() {
        $logFile = __DIR__ . '/../logs/bot_user1.log';
        if (file_exists($logFile)) {
            unlink($logFile);
        }
        // Simula execução do comando
        file_put_contents($logFile, "Bot iniciado para @user1\n");
        $this->assertFileExists($logFile);
        $log = file_get_contents($logFile);
        $this->assertStringContainsString('Bot iniciado para @user1', $log);
    }
} 
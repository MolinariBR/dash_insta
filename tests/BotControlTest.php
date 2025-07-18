<?php
use PHPUnit\Framework\TestCase;

class BotControlTest extends TestCase {
    public function testComandoStartBotEnviaContaCorreta() {
        $conta_id = 1;
        $username = 'user1';
        // Simula shell_exec
        $command = "cd ../insta && python3 main.py --conta 'user1' > ../logs/bot_user1.log 2>&1 &";
        $this->assertStringContainsString("--conta 'user1'", $command);
        $this->assertStringContainsString('bot_user1.log', $command);
    }

    public function testComandoStopBotEnviaContaCorreta() {
        $conta_id = 2;
        $username = 'user2';
        $command = "pkill -f 'python3 main.py --conta 'user2''";
        $this->assertStringContainsString("--conta 'user2'", $command);
    }
} 
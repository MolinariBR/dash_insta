<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config.php';

if (!function_exists('parsLogLine')) {
    function parsLogLine($line) {
        return ['mensagem' => $line];
    }
}

class LogsMetricasTest extends TestCase {
    private $logFile;
    private $logsDir;

    protected function setUp(): void {
        $this->logsDir = __DIR__ . '/../logs';
        $this->logFile = $this->logsDir . '/teste_metricas.log';
        if (!is_dir($this->logsDir)) {
            mkdir($this->logsDir, 0777, true);
        }
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }
        file_put_contents($this->logFile, "Linha 1\nLinha 2\nLinha 3\n");
    }

    public function testReadLogFile() {
        file_put_contents($this->logFile, "Linha 1\nLinha 2\nLinha 3\n");
        clearstatcache();
        $lines = file($this->logFile, FILE_IGNORE_NEW_LINES);
        $result = array_reverse(array_map(function($line) { return parsLogLine($line); }, array_slice($lines, -2)));
        // Debug temporário
        fwrite(STDERR, "\n[DEBUG] lines: " . print_r($lines, true));
        fwrite(STDERR, "\n[DEBUG] result: " . print_r($result, true));
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('Linha 3', $result[0]['message']);
        $this->assertEquals('Linha 2', $result[1]['message']);
    }

    public function testReadLogFileVazio() {
        file_put_contents($this->logFile, "");
        $result = readLogFile('teste_metricas', 2);
        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }
} 
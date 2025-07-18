<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../includes/settings_core.php';

class ApiSettingsTest extends TestCase {
    private $logFile;
    private $settingsFile;
    private $configDir;

    protected function setUp(): void {
        $this->logFile = __DIR__ . '/../logs/auditoria.log';
        $this->configDir = __DIR__ . '/../config';
        $this->settingsFile = $this->configDir . '/settings.json';
        if (!is_dir($this->configDir)) {
            mkdir($this->configDir, 0777, true);
        }
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }
        if (file_exists($this->settingsFile)) {
            unlink($this->settingsFile);
        }
        $_SESSION = ['logged_in' => true, 'usuario' => 'admin', 'login_time' => time()];
    }

    public function testSalvaConfiguracoesComSucesso() {
        $settings = [
            'limites' => [
                'seguir_por_dia' => 10,
                'curtidas_por_follow' => ['min' => 2, 'max' => 4],
                'comentarios_por_dia' => 5,
                'mensagens_por_dia' => 7,
                'posts_recentes_dias' => 3
            ],
            'horarios' => [
                'inicio_atividade' => 8,
                'fim_atividade' => 18,
                'inicio_comentarios' => 9,
                'fim_comentarios' => 17
            ],
            'delays' => [
                'entre_curtidas' => ['min' => 5, 'max' => 10],
                'entre_comentarios' => ['min' => 100, 'max' => 200],
                'entre_seguir' => ['min' => 50, 'max' => 100],
                'mensagem_dm' => ['min' => 60 * 60, 'max' => 120 * 60]
            ],
            'hashtags_alvo' => ['#teste', '#php'],
            'filtros_conteudo' => [
                'palavras_bloqueadas' => ['spam', 'teste'],
                'hashtags_bloqueadas' => ['#spam', '#teste'],
                'filtrar_contas_suspeitas' => true,
                'verificar_ratio_followers' => true
            ]
        ];
        $result = saveSettingsCore($settings, $this->settingsFile);
        $this->assertTrue($result['success']);
        $this->assertFileExists($this->settingsFile);
        $this->assertFileExists($this->logFile);
        $log = file_get_contents($this->logFile);
        $this->assertStringContainsString('EDICAO_CONFIG_API', $log);
    }

    public function testErroSemAction() {
        // Simula chamada sem action
        ob_start();
        require __DIR__ . '/../api/settings.php';
        $output = ob_get_clean();
        $json = json_decode($output, true);
        $this->assertIsArray($json);
        $this->assertEquals('Nenhuma ação especificada', $json['message'] ?? '');
    }
} 
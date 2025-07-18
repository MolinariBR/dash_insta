<?php
if (!function_exists('saveSettingsCore')) {
function saveSettingsCore($settings, $configFile = null) {
    if (!$configFile) {
        $configFile = defined('CONFIG_PATH') ? CONFIG_PATH . '/settings.json' : __DIR__ . '/../config/settings.json';
    }
    $saved = file_put_contents($configFile, json_encode($settings, JSON_PRETTY_PRINT));
    if ($saved !== false) {
        // Log de auditoria
        $usuario_logado = $_SESSION['usuario'] ?? 'N/A';
        $detalhes = 'Limites: ' . json_encode($settings['limites'] ?? []);
        file_put_contents(__DIR__ . '/../logs/auditoria.log', date('c') . " | EDICAO_CONFIG_API | $usuario_logado | $detalhes\n", FILE_APPEND);
        return ['success' => true, 'message' => 'Configurações salvas com sucesso!', 'timestamp' => date('Y-m-d H:i:s')];
    } else {
        return ['success' => false, 'message' => 'Erro ao salvar arquivo de configuração'];
    }
}
} 
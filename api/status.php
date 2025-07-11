<?php
require_once '../config.php';

header('Content-Type: application/json');

// Verifica se está logado
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autorizado']);
    exit;
}

function getBotStatusFromFile() {
    $statusFile = '../logs/bot_status.json';
    if (file_exists($statusFile)) {
        $content = file_get_contents($statusFile);
        return json_decode($content, true);
    }
    return null;
}

function getProcessStatus() {
    // Verifica se o processo Python está rodando
    $output = shell_exec("pgrep -f 'python.*main.py' 2>/dev/null");
    return !empty(trim($output));
}

try {
    $statusFromFile = getBotStatusFromFile();
    $processRunning = getProcessStatus();
    
    $response = [
        'running' => $processRunning,
        'timestamp' => date('Y-m-d H:i:s'),
        'stats' => getBotStats(),
        'logs_recent' => [],
        'process_info' => [
            'file_status' => $statusFromFile,
            'process_detected' => $processRunning,
            'last_update' => $statusFromFile ? $statusFromFile['last_update'] : null,
            'mode' => $statusFromFile ? $statusFromFile['mode'] : 'unknown'
        ]
    ];
    
    // Adiciona logs recentes se solicitado
    if (isset($_GET['include_logs'])) {
        $response['logs_recent'] = [
            'main' => array_slice(readLogFile('main'), 0, 10),
            'client' => array_slice(readLogFile('client'), 0, 5),
            'production' => array_slice(readLogFile('bot_production'), 0, 5)
        ];
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno do servidor']);
}
?>

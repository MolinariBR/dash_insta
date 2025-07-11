<?php
require_once '../config.php';

header('Content-Type: application/json');

// Verifica se está logado
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autorizado']);
    exit;
}

$type = $_GET['type'] ?? 'seguidores';
$limit = intval($_GET['limit'] ?? 50);

try {
    $logs = readLogFile($type, $limit);
    
    echo json_encode([
        'success' => true,
        'type' => $type,
        'count' => count($logs),
        'logs' => $logs
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao ler logs: ' . $e->getMessage()]);
}
?>

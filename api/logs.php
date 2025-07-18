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
$conta_id = isset($_GET['conta_id']) ? (int)$_GET['conta_id'] : null;

function getContaUsername($conta_id) {
    $db = new PDO('sqlite:../data/database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $db->prepare('SELECT username FROM contas_instagram WHERE id = ?');
    $stmt->execute([$conta_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['username'] : null;
}

try {
    $logs = [];
    if ($conta_id) {
        $username = getContaUsername($conta_id);
        if ($username) {
            $logFile = '../logs/bot_' . $username . '_' . $type . '.log';
            if (file_exists($logFile)) {
                $lines = file($logFile);
                $lines = array_slice(array_reverse($lines), 0, $limit);
                foreach ($lines as $line) {
                    $logs[] = parsLogLine(trim($line));
                }
            }
        }
    } else {
        $logs = readLogFile($type, $limit);
    }
    
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

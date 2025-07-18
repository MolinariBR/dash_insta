<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Autenticação (exemplo simples, pode ser expandido)
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$conta_id = $data['conta_id'] ?? null;
$acao = $data['acao'] ?? null;
$parametros = $data['parametros'] ?? [];

if (!$conta_id || !$acao) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'conta_id e acao são obrigatórios']);
    exit;
}

// Buscar username da conta
try {
    $db = new PDO('sqlite:../data/database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $db->prepare('SELECT username FROM contas_instagram WHERE id = ?');
    $stmt->execute([$conta_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'Conta não encontrada']);
        exit;
    }
    $username = $row['username'];
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao buscar conta: ' . $e->getMessage()]);
    exit;
}

// Montar comando para o robô Python
$botPath = '../insta';
$logFile = '../logs/bot_' . $username . '.log';
$cmd = '';
if ($acao === 'iniciar') {
    $cmd = "cd $botPath && python3 main.py --conta '" . escapeshellarg($username) . "' > $logFile 2>&1 &";
} elseif ($acao === 'parar') {
    $cmd = "pkill -f 'python3 main.py --conta '" . escapeshellarg($username) . "'";
} elseif ($acao === 'custom') {
    // Exemplo de comando customizado
    $args = isset($parametros['args']) ? escapeshellarg($parametros['args']) : '';
    $cmd = "cd $botPath && python3 main.py --conta '" . escapeshellarg($username) . "' $args > $logFile 2>&1 &";
} else {
    echo json_encode(['success' => false, 'message' => 'Ação não suportada']);
    exit;
}

// Executar comando (simulação, pode ser shell_exec real)
$output = shell_exec($cmd);
sleep(1);

// Log de auditoria
file_put_contents('../logs/auditoria.log', date('c') . " | API_COMANDO | $acao | $username\n", FILE_APPEND);

echo json_encode([
    'success' => true,
    'message' => 'Comando enviado para o robô',
    'acao' => $acao,
    'conta' => $username,
    'cmd' => $cmd
]); 
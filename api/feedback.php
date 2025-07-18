<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$conta_id = $data['conta_id'] ?? null;
$tipo = $data['tipo'] ?? null;
$mensagem = $data['mensagem'] ?? null;
$extra = $data['extra'] ?? [];

if (!$conta_id || !$tipo || !$mensagem) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'conta_id, tipo e mensagem são obrigatórios']);
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

// Salvar feedback em log
$logMsg = date('c') . " | FEEDBACK | $tipo | $username | $mensagem | " . json_encode($extra) . "\n";
file_put_contents('../logs/feedback.log', $logMsg, FILE_APPEND);

echo json_encode([
    'success' => true,
    'message' => 'Feedback recebido',
    'conta' => $username,
    'tipo' => $tipo
]); 
<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

try {
    $db = new PDO('sqlite:../data/database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $contas = $db->query('SELECT ci.id, ci.username, ci.status, ci.data_cadastro, ci.ultima_atividade, c.nome as cliente_nome, c.email as cliente_email FROM contas_instagram ci JOIN clientes c ON ci.cliente_id = c.id ORDER BY c.nome, ci.username')->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode([
        'success' => true,
        'perfis' => $contas,
        'total' => count($contas)
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao buscar perfis: ' . $e->getMessage()]);
} 
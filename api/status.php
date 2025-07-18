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
    $contas = $db->query('SELECT ci.id, ci.username, ci.status, ci.data_cadastro, ci.ultima_atividade, c.nome as cliente_nome FROM contas_instagram ci JOIN clientes c ON ci.cliente_id = c.id ORDER BY c.nome, ci.username')->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao buscar contas: ' . $e->getMessage()]);
    exit;
}

// Simula status do robô (pode ser expandido para checagem real)
$robos = [];
foreach ($contas as $conta) {
    $robos[] = [
        'conta_id' => $conta['id'],
        'username' => $conta['username'],
        'status' => $conta['status'],
        'ultima_atividade' => $conta['ultima_atividade'],
        'cliente' => $conta['cliente_nome']
    ];
}

// Simula métricas (pode ser expandido)
$metricas = [
    'total_contas' => count($contas),
    'contas_ativas' => count(array_filter($contas, fn($c) => $c['status'] === 'ativa')),
    'contas_inativas' => count(array_filter($contas, fn($c) => $c['status'] === 'inativa'))
];

echo json_encode([
    'success' => true,
    'robos' => $robos,
    'metricas' => $metricas
]);
?>

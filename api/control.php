<?php
require_once '../config.php';

header('Content-Type: application/json');

// Verifica se está logado
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autorizado']);
    exit;
}

// Só aceita POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

$action = $_POST['action'] ?? '';
$conta_id = isset($_POST['conta_id']) ? (int)$_POST['conta_id'] : null;

try {
    switch ($action) {
        case 'start':
            $result = startBot($conta_id);
            break;
        case 'stop':
            $result = stopBot($conta_id);
            break;
        case 'restart':
            $result = restartBot($conta_id);
            break;
        case 'test_follow':
            $username = $_POST['username'] ?? '';
            $result = testFollow($conta_id, $username);
            break;
        case 'test_comment':
            $hashtag = $_POST['hashtag'] ?? '';
            $result = testComment($conta_id, $hashtag);
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Ação inválida']);
            exit;
    }
    echo json_encode($result);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno: ' . $e->getMessage()]);
}

function getContaInstagram($conta_id) {
    $db = new PDO('sqlite:../data/database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $db->prepare('SELECT * FROM contas_instagram WHERE id = ?');
    $stmt->execute([$conta_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function startBot($conta_id) {
    $conta = getContaInstagram($conta_id);
    if (!$conta) {
        return ['success' => false, 'message' => 'Conta não encontrada'];
    }
    $botPath = '../insta';
    $logFile = '../logs/bot_' . $conta['username'] . '.log';
    $command = "cd $botPath && python3 main.py --conta " . escapeshellarg($conta['username']) . " > $logFile 2>&1 &";
    $output = shell_exec($command);
    sleep(3);
    // Aqui você pode implementar uma checagem real se o bot está rodando para a conta
    return ['success' => true, 'message' => 'Bot iniciado para @' . $conta['username']];
}

function stopBot($conta_id) {
    $conta = getContaInstagram($conta_id);
    if (!$conta) {
        return ['success' => false, 'message' => 'Conta não encontrada'];
    }
    $command = "pkill -f 'python3 main.py --conta " . escapeshellarg($conta['username']) . "'";
    shell_exec($command);
    sleep(2);
    return ['success' => true, 'message' => 'Bot parado para @' . $conta['username']];
}

function restartBot($conta_id) {
    $stopResult = stopBot($conta_id);
    if ($stopResult['success']) {
        sleep(2);
        return startBot($conta_id);
    } else {
        return ['success' => false, 'message' => 'Falha ao parar o bot para reiniciar'];
    }
}

function testFollow($conta_id, $username) {
    if (empty($username)) {
        return ['success' => false, 'message' => 'Username não fornecido'];
    }
    $conta = getContaInstagram($conta_id);
    if (!$conta) {
        return ['success' => false, 'message' => 'Conta não encontrada'];
    }
    $botPath = '../insta';
    $command = "cd $botPath && python3 -c \"from bot.seguidores import SeguidoresBot; bot = SeguidoresBot(conta='" . addslashes($conta['username']) . "'); result = bot._seguir_usuario('" . addslashes($username) . "', 'Teste manual'); print('SUCCESS' if result else 'FAILED')\"";
    $output = trim(shell_exec($command));
    if (strpos($output, 'SUCCESS') !== false) {
        return ['success' => true, 'message' => "Follow testado com sucesso em @$username para @" . $conta['username']];
    } else {
        return ['success' => false, 'message' => "Falha no teste de follow: $output"];
    }
}

function testComment($conta_id, $hashtag) {
    if (empty($hashtag)) {
        return ['success' => false, 'message' => 'Hashtag não fornecida'];
    }
    $conta = getContaInstagram($conta_id);
    if (!$conta) {
        return ['success' => false, 'message' => 'Conta não encontrada'];
    }
    $botPath = '../insta';
    $command = "cd $botPath && python3 -c \"from bot.comentarios import ComentariosBot; bot = ComentariosBot(conta='" . addslashes($conta['username']) . "'); result = bot.comentar_posts_hashtag('" . addslashes($hashtag) . "', max_comments=1); print(f'SUCCESS: {result} comentários realizados')\"";
    $output = trim(shell_exec($command));
    if (strpos($output, 'SUCCESS') !== false) {
        return ['success' => true, 'message' => "Teste de comentário realizado: $output para @" . $conta['username']];
    } else {
        return ['success' => false, 'message' => "Falha no teste de comentário: $output"];
    }
}
?>

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

try {
    switch ($action) {
        case 'start':
            $result = startBot();
            break;
            
        case 'stop':
            $result = stopBot();
            break;
            
        case 'restart':
            $result = restartBot();
            break;
            
        case 'test_follow':
            $username = $_POST['username'] ?? '';
            $result = testFollow($username);
            break;
            
        case 'test_comment':
            $hashtag = $_POST['hashtag'] ?? '';
            $result = testComment($hashtag);
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

/**
 * Inicia o bot
 */
function startBot() {
    $botPath = BOT_PATH;
    $logFile = LOGS_PATH . '/bot_control.log';
    
    // Verifica se já está rodando
    if (isBotRunning()) {
        return ['success' => false, 'message' => 'Bot já está rodando'];
    }
    
    // Inicia o bot em background
    $command = "cd $botPath && python3 main.py > $logFile 2>&1 &";
    $output = shell_exec($command);
    
    // Aguarda um pouco e verifica se iniciou
    sleep(3);
    
    if (isBotRunning()) {
        return ['success' => true, 'message' => 'Bot iniciado com sucesso'];
    } else {
        return ['success' => false, 'message' => 'Falha ao iniciar o bot'];
    }
}

/**
 * Para o bot
 */
function stopBot() {
    // Encontra e mata o processo
    $output = shell_exec("pkill -f 'python3 main.py'");
    
    // Aguarda um pouco e verifica se parou
    sleep(2);
    
    if (!isBotRunning()) {
        return ['success' => true, 'message' => 'Bot parado com sucesso'];
    } else {
        return ['success' => false, 'message' => 'Falha ao parar o bot'];
    }
}

/**
 * Reinicia o bot
 */
function restartBot() {
    $stopResult = stopBot();
    if ($stopResult['success']) {
        sleep(2);
        return startBot();
    } else {
        return ['success' => false, 'message' => 'Falha ao parar o bot para reiniciar'];
    }
}

/**
 * Testa funcionalidade de follow
 */
function testFollow($username) {
    if (empty($username)) {
        return ['success' => false, 'message' => 'Username não fornecido'];
    }
    
    $botPath = BOT_PATH;
    $command = "cd $botPath && python3 -c \"
from bot.seguidores import SeguidoresBot
bot = SeguidoresBot()
try:
    bot.client = bot.instagram_client.get_client()
    result = bot._seguir_usuario('$username', 'Teste manual')
    print('SUCCESS' if result else 'FAILED')
except Exception as e:
    print(f'ERROR: {e}')
\"";
    
    $output = trim(shell_exec($command));
    
    if (strpos($output, 'SUCCESS') !== false) {
        return ['success' => true, 'message' => "Follow testado com sucesso em @$username"];
    } else {
        return ['success' => false, 'message' => "Falha no teste de follow: $output"];
    }
}

/**
 * Testa funcionalidade de comentário
 */
function testComment($hashtag) {
    if (empty($hashtag)) {
        return ['success' => false, 'message' => 'Hashtag não fornecida'];
    }
    
    $botPath = BOT_PATH;
    $command = "cd $botPath && python3 -c \"
from bot.comentarios import ComentariosBot
bot = ComentariosBot()
try:
    result = bot.comentar_posts_hashtag('$hashtag', max_comments=1)
    print(f'SUCCESS: {result} comentários realizados')
except Exception as e:
    print(f'ERROR: {e}')
\"";
    
    $output = trim(shell_exec($command));
    
    if (strpos($output, 'SUCCESS') !== false) {
        return ['success' => true, 'message' => "Teste de comentário realizado: $output"];
    } else {
        return ['success' => false, 'message' => "Falha no teste de comentário: $output"];
    }
}
?>

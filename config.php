<?php
/**
 * Configurações do Dashboard - Instagram Bot @fatima.escritora
 * Desenvolvido por: Tria Inova Simples (I.S.)
 */

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'instagram_bot_fatima');
define('DB_USER', 'root');
define('DB_PASS', '');

// Caminhos do projeto
define('BOT_PATH', '/home/mau/bot/insta');
define('LOGS_PATH', BOT_PATH . '/logs');
define('DATA_PATH', BOT_PATH . '/data');
define('CONFIG_PATH', BOT_PATH . '/config');

// Configurações de segurança
define('DASHBOARD_PASSWORD', 'fatima2025!');
define('SESSION_TIMEOUT', 3600); // 1 hora

// Configurações do bot (valores seguros para produção)
define('MAX_FOLLOWS_PER_DAY', 20);
define('MAX_COMMENTS_PER_DAY', 8);
define('MAX_MESSAGES_PER_DAY', 15);

// Timezone
date_default_timezone_set('America/Sao_Paulo');

// Inicia sessão
session_start();

// Inclui dados simulados e configurações
require_once 'demo_data.php';
require_once 'dashboard_config.php';

/**
 * Função para conectar ao banco de dados
 */
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        error_log("Erro de conexão: " . $e->getMessage());
        return null;
    }
}

/**
 * Verifica se o usuário está logado
 */
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && 
           $_SESSION['logged_in'] === true && 
           (time() - $_SESSION['login_time']) < SESSION_TIMEOUT;
}

/**
 * Função para autenticar usuário
 */
function authenticate($password) {
    if ($password === DASHBOARD_PASSWORD) {
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        return true;
    }
    return false;
}

/**
 * Função para fazer logout
 */
function logout() {
    session_destroy();
    header('Location: login.php');
    exit;
}

/**
 * Lê arquivo de log
 */
function readLogFile($logType, $lines = 100) {
    $logFile = LOGS_PATH . '/' . $logType . '.log';
    
    if (!file_exists($logFile)) {
        // Se arquivo não existe, usa dados simulados para demo
        if (in_array($logType, ['seguidores', 'comentarios', 'mensagens'])) {
            return getSimulatedLogs($logType, min($lines, 10));
        }
        return [];
    }
    
    $output = [];
    $handle = fopen($logFile, 'r');
    
    if ($handle) {
        // Lê as últimas linhas do arquivo
        $fileLines = file($logFile);
        $totalLines = count($fileLines);
        
        // Se arquivo está vazio ou quase vazio, usa dados simulados
        if ($totalLines < 3 && in_array($logType, ['seguidores', 'comentarios', 'mensagens'])) {
            fclose($handle);
            return getSimulatedLogs($logType, min($lines, 10));
        }
        
        $startLine = max(0, $totalLines - $lines);
        
        for ($i = $startLine; $i < $totalLines; $i++) {
            $line = trim($fileLines[$i]);
            if (!empty($line)) {
                $output[] = parsLogLine($line);
            }
        }
        
        fclose($handle);
    }
    
    return array_reverse($output); // Mais recentes primeiro
}

/**
 * Parse de linha de log
 */
function parsLogLine($line) {
    // Formato: 2025-07-09 10:30:45 | module | LEVEL | message
    $pattern = '/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\s*\|\s*([^|]+)\s*\|\s*([^|]+)\s*\|\s*(.+)$/';
    
    if (preg_match($pattern, $line, $matches)) {
        return [
            'timestamp' => $matches[1],
            'module' => trim($matches[2]),
            'level' => trim($matches[3]),
            'message' => trim($matches[4]),
            'raw' => $line
        ];
    }
    
    return [
        'timestamp' => date('Y-m-d H:i:s'),
        'module' => 'unknown',
        'level' => 'INFO',
        'message' => $line,
        'raw' => $line
    ];
}

/**
 * Lê arquivo CSV
 */
function readCSVFile($filename) {
    $filepath = DATA_PATH . '/' . $filename;
    
    if (!file_exists($filepath)) {
        return [];
    }
    
    $data = [];
    if (($handle = fopen($filepath, 'r')) !== FALSE) {
        $header = fgetcsv($handle, 1000, ',');
        
        while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
            if (count($row) === count($header)) {
                $data[] = array_combine($header, $row);
            }
        }
        fclose($handle);
    }
    
    return $data;
}

/**
 * Obtém estatísticas do bot
 */
function getBotStats() {
    // Se modo demo forçado, sempre retorna dados simulados
    if (defined('FORCE_DEMO_MODE') && FORCE_DEMO_MODE) {
        return getSimulatedStats();
    }
    
    $today = date('Y-m-d');
    
    // Lê logs de hoje
    $seguidoresLog = readLogFile('seguidores', 1000);
    $comentariosLog = readLogFile('comentarios', 1000);
    $mensagensLog = readLogFile('mensagens', 1000);
    
    // Conta ações de hoje
    $followsToday = 0;
    $commentsToday = 0;
    $messagesToday = 0;
    
    foreach ($seguidoresLog as $log) {
        if (strpos($log['timestamp'], $today) === 0 && 
            strpos($log['message'], '✅ SUCESSO | SEGUIR') !== false) {
            $followsToday++;
        }
    }
    
    foreach ($comentariosLog as $log) {
        if (strpos($log['timestamp'], $today) === 0 && 
            strpos($log['message'], '✅ SUCESSO | COMENTAR') !== false) {
            $commentsToday++;
        }
    }
    
    foreach ($mensagensLog as $log) {
        if (strpos($log['timestamp'], $today) === 0 && 
            strpos($log['message'], '✅ SUCESSO | MENSAGEM') !== false) {
            $messagesToday++;
        }
    }
    
    // Se não há dados reais, usa simulação
    if ($followsToday == 0 && $commentsToday == 0 && $messagesToday == 0 && shouldUseSimulatedData()) {
        return getSimulatedStats();
    }
    
    return [
        'follows_today' => $followsToday,
        'comments_today' => $commentsToday,
        'messages_today' => $messagesToday,
        'max_follows' => MAX_FOLLOWS_PER_DAY,
        'max_comments' => MAX_COMMENTS_PER_DAY,
        'max_messages' => MAX_MESSAGES_PER_DAY,
        'simulated' => false
    ];
}

/**
 * Verifica se o bot está rodando
 */
function isBotRunning() {
    // Método 1: Verifica processo Python
    $output = shell_exec("pgrep -f 'python.*main.py' 2>/dev/null");
    $processRunning = !empty(trim($output));
    
    // Método 2: Verifica arquivo de status
    $statusFile = LOGS_PATH . '/bot_status.json';
    $fileStatus = false;
    
    if (file_exists($statusFile)) {
        $content = file_get_contents($statusFile);
        $status = json_decode($content, true);
        
        if ($status && isset($status['running'])) {
            $fileStatus = $status['running'];
            
            // Verifica se o status não está muito antigo (mais de 5 minutos)
            $lastUpdate = new DateTime($status['last_update']);
            $now = new DateTime();
            $diff = $now->diff($lastUpdate);
            $minutesDiff = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
            
            if ($minutesDiff > 5) {
                $fileStatus = false; // Status muito antigo
            }
        }
    }
    
    // Combina ambos os métodos
    return $processRunning && $fileStatus;
}

/**
 * Obtém informações detalhadas do status do bot
 */
function getBotDetailedStatus() {
    $processRunning = !empty(trim(shell_exec("pgrep -f 'python.*main.py' 2>/dev/null")));
    $statusFile = LOGS_PATH . '/bot_status.json';
    
    $result = [
        'process_running' => $processRunning,
        'file_exists' => file_exists($statusFile),
        'file_status' => null,
        'last_update' => null,
        'mode' => 'unknown',
        'combined_status' => false
    ];
    
    if (file_exists($statusFile)) {
        $content = file_get_contents($statusFile);
        $status = json_decode($content, true);
        
        if ($status) {
            $result['file_status'] = $status['running'] ?? false;
            $result['last_update'] = $status['last_update'] ?? null;
            $result['mode'] = $status['mode'] ?? 'unknown';
            
            // Verifica se está atualizado (últimos 5 minutos)
            if ($result['last_update']) {
                $lastUpdate = new DateTime($result['last_update']);
                $now = new DateTime();
                $diff = $now->diff($lastUpdate);
                $minutesDiff = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
                
                $result['minutes_since_update'] = $minutesDiff;
                $result['status_fresh'] = $minutesDiff <= 5;
            }
        }
    }
    
    $result['combined_status'] = $result['process_running'] && 
                                 $result['file_status'] && 
                                 ($result['status_fresh'] ?? false);
    
    return $result;
}

/**
 * Formata timestamp para exibição
 */
function formatTimestamp($timestamp) {
    $date = new DateTime($timestamp);
    $now = new DateTime();
    $diff = $now->diff($date);
    
    if ($diff->days == 0) {
        if ($diff->h == 0) {
            if ($diff->i == 0) {
                return 'agora mesmo';
            }
            return $diff->i . ' min atrás';
        }
        return $diff->h . 'h ' . $diff->i . 'min atrás';
    } elseif ($diff->days == 1) {
        return 'ontem às ' . $date->format('H:i');
    } else {
        return $date->format('d/m/Y H:i');
    }
}
?>

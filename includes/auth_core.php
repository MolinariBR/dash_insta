<?php
if (!function_exists('processLoginCore')) {
function processLoginCore($password) {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['login_blocked_until'] = 0;
    }
    $max_attempts = 5;
    $block_time = 600; // 10 minutos
    $now = time();
    $error = '';
    $success = false;
    if ($_SESSION['login_blocked_until'] > $now) {
        $error = 'Muitas tentativas. Tente novamente em alguns minutos.';
    } else {
        if (authenticate($password)) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['login_blocked_until'] = 0;
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            file_put_contents('logs/auditoria.log', date('c') . " | LOGIN_SUCESSO | IP: $ip\n", FILE_APPEND);
            $success = true;
        } else {
            $_SESSION['login_attempts']++;
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            file_put_contents('logs/auditoria.log', date('c') . " | LOGIN_FALHA | IP: $ip | Tentativa: {$_SESSION['login_attempts']}\n", FILE_APPEND);
            if ($_SESSION['login_attempts'] >= $max_attempts) {
                $_SESSION['login_blocked_until'] = $now + $block_time;
                $error = 'Muitas tentativas. Tente novamente em alguns minutos.';
            } else {
                $error = 'Senha incorreta!';
            }
        }
    }
    return ['success' => $success, 'error' => $error];
}
} 
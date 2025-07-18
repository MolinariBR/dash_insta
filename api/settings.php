<?php
require_once 'config.php';
require_once __DIR__ . '/../includes/settings_core.php';

// Verifica se está logado
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autorizado']);
    exit;
}

header('Content-Type: application/json');

if ($_POST && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'save_settings') {
        try {
            // Sanitização e validação dos campos recebidos
            $max_follows = max(1, min(100, (int)($_POST['max_follows'] ?? 20)));
            $min_likes = max(1, min(10, (int)($_POST['min_likes'] ?? 3)));
            $max_likes = max($min_likes, min(10, (int)($_POST['max_likes'] ?? 5)));
            $max_comments = max(1, min(50, (int)($_POST['max_comments'] ?? 8)));
            $max_messages = max(1, min(50, (int)($_POST['max_messages'] ?? 20)));
            $recent_posts_days = max(1, min(30, (int)($_POST['recent_posts_days'] ?? 10)));
            $start_hour = htmlspecialchars(trim($_POST['start_hour'] ?? '09:00'));
            $end_hour = htmlspecialchars(trim($_POST['end_hour'] ?? '20:00'));
            $comment_start = htmlspecialchars(trim($_POST['comment_start'] ?? '10:00'));
            $comment_end = htmlspecialchars(trim($_POST['comment_end'] ?? '21:00'));
            $min_like_delay = max(1, (int)($_POST['min_like_delay'] ?? 8));
            $max_like_delay = max($min_like_delay, (int)($_POST['max_like_delay'] ?? 15));
            $min_comment_delay = max(1, (int)($_POST['min_comment_delay'] ?? 300));
            $max_comment_delay = max($min_comment_delay, (int)($_POST['max_comment_delay'] ?? 600));
            $min_follow_delay = max(1, (int)($_POST['min_follow_delay'] ?? 180));
            $max_follow_delay = max($min_follow_delay, (int)($_POST['max_follow_delay'] ?? 300));
            $min_dm_delay = max(1, (int)($_POST['min_dm_delay'] ?? 1200));
            $max_dm_delay = max($min_dm_delay, (int)($_POST['max_dm_delay'] ?? 2400));
            $hashtags = isset($_POST['hashtags']) ? array_filter(array_map('trim', explode("\n", $_POST['hashtags']))) : [];
            $blocked_words = isset($_POST['blocked_words']) ? array_filter(array_map('trim', explode(',', $_POST['blocked_words']))) : [];
            $blocked_hashtags = isset($_POST['blocked_hashtags']) ? array_filter(array_map('trim', explode(',', $_POST['blocked_hashtags']))) : [];
            $filter_suspicious = isset($_POST['filter_suspicious']);
            $check_ratio = isset($_POST['check_ratio']);

            // Constroi nova configuração
            $newSettings = [
                'limites' => [
                    'seguir_por_dia' => $max_follows,
                    'curtidas_por_follow' => [
                        'min' => $min_likes,
                        'max' => $max_likes
                    ],
                    'comentarios_por_dia' => $max_comments,
                    'mensagens_por_dia' => $max_messages,
                    'posts_recentes_dias' => $recent_posts_days
                ],
                'horarios' => [
                    'inicio_atividade' => (int)explode(':', $start_hour)[0],
                    'fim_atividade' => (int)explode(':', $end_hour)[0],
                    'inicio_comentarios' => (int)explode(':', $comment_start)[0],
                    'fim_comentarios' => (int)explode(':', $comment_end)[0]
                ],
                'delays' => [
                    'entre_curtidas' => [
                        'min' => $min_like_delay,
                        'max' => $max_like_delay
                    ],
                    'entre_comentarios' => [
                        'min' => $min_comment_delay,
                        'max' => $max_comment_delay
                    ],
                    'entre_seguir' => [
                        'min' => $min_follow_delay,
                        'max' => $max_follow_delay
                    ],
                    'mensagem_dm' => [
                        'min' => $min_dm_delay * 60,
                        'max' => $max_dm_delay * 60
                    ]
                ],
                'hashtags_alvo' => $hashtags,
                'filtros_conteudo' => [
                    'palavras_bloqueadas' => $blocked_words,
                    'hashtags_bloqueadas' => $blocked_hashtags,
                    'filtrar_contas_suspeitas' => $filter_suspicious,
                    'verificar_ratio_followers' => $check_ratio
                ]
            ];
            // Salva arquivo e log
            $result = saveSettingsCore($newSettings);
            echo json_encode($result);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ]);
        }
        
    } elseif ($action === 'get_settings') {
        // Retorna configurações atuais
        $configFile = CONFIG_PATH . '/settings.json';
        
        if (file_exists($configFile)) {
            $settings = json_decode(file_get_contents($configFile), true);
            echo json_encode([
                'success' => true,
                'settings' => $settings
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Arquivo de configuração não encontrado'
            ]);
        }
        
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Nenhuma ação especificada'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Nenhuma ação especificada'
    ]);
}
?>

<?php
require_once 'config.php';

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
            // Constroi nova configuração
            $newSettings = [
                'limites' => [
                    'seguir_por_dia' => (int)($_POST['max_follows'] ?? 20),
                    'curtidas_por_follow' => [
                        'min' => (int)($_POST['min_likes'] ?? 3),
                        'max' => (int)($_POST['max_likes'] ?? 5)
                    ],
                    'comentarios_por_dia' => (int)($_POST['max_comments'] ?? 8),
                    'posts_recentes_dias' => (int)($_POST['recent_posts_days'] ?? 10)
                ],
                'horarios' => [
                    'inicio_atividade' => (int)($_POST['start_hour'] ?? 9),
                    'fim_atividade' => (int)($_POST['end_hour'] ?? 20),
                    'inicio_comentarios' => (int)($_POST['comment_start'] ?? 10),
                    'fim_comentarios' => (int)($_POST['comment_end'] ?? 21)
                ],
                'delays' => [
                    'entre_curtidas' => [
                        'min' => (int)($_POST['min_like_delay'] ?? 8),
                        'max' => (int)($_POST['max_like_delay'] ?? 15)
                    ],
                    'entre_comentarios' => [
                        'min' => (int)($_POST['min_comment_delay'] ?? 300),
                        'max' => (int)($_POST['max_comment_delay'] ?? 600)
                    ],
                    'entre_seguir' => [
                        'min' => (int)($_POST['min_follow_delay'] ?? 180),
                        'max' => (int)($_POST['max_follow_delay'] ?? 300)
                    ],
                    'mensagem_dm' => [
                        'min' => (int)($_POST['min_dm_delay'] ?? 1200),
                        'max' => (int)($_POST['max_dm_delay'] ?? 2400)
                    ]
                ],
                'hashtags_alvo' => isset($_POST['hashtags']) ? 
                    array_filter(array_map('trim', explode("\n", $_POST['hashtags']))) : 
                    ["#livroinfantil", "#educacaoinfantil", "#literaturainfantil"],
                'emojis' => ["🌸", "💛", "📚", "🐶", "✨", "💕", "🌻", "📖", "🎈", "🌈"],
                'filtros_conteudo' => [
                    'palavras_bloqueadas' => ["nude", "adult", "sexy", "violence", "hate"],
                    'hashtags_bloqueadas' => ["#nsfw", "#adult", "#violence"]
                ]
            ];
            
            // Salva arquivo
            $configFile = CONFIG_PATH . '/settings.json';
            $saved = file_put_contents($configFile, json_encode($newSettings, JSON_PRETTY_PRINT));
            
            if ($saved !== false) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Configurações salvas com sucesso!',
                    'timestamp' => date('Y-m-d H:i:s')
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erro ao salvar arquivo de configuração'
                ]);
            }
            
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
            'message' => 'Ação inválida'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Nenhuma ação especificada'
    ]);
}
?>

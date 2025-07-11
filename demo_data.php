<?php
/**
 * Gerador de Dados Simulados - Dashboard Demo
 * Para demonstração enquanto não há dados reais
 */

/**
 * Gera estatísticas simuladas realistas
 */
function getSimulatedStats() {
    $today = date('Y-m-d');
    $hour = (int)date('H');
    $minute = (int)date('i');
    
    // Calcula progresso baseado no horário (com variação para parecer real)
    if ($hour >= 9 && $hour <= 20) {
        // Horário de funcionamento - progresso baseado no tempo
        $timeProgress = ($hour - 9 + $minute/60) / 11; // 0 a 1
        
        // Adiciona um pouco de randomização para parecer mais real
        $randomFactor = 0.15; // 15% de variação
        $variation = (rand(-100, 100) / 1000) * $randomFactor;
        $progress = max(0, min(1, $timeProgress + $variation));
        
        // Valores mais realistas e seguros para um bot de Instagram
        $follows_today = min(15, max(0, round($progress * 12) + rand(0, 3)));
        $comments_today = min(6, max(0, round($progress * 4) + rand(0, 2)));
        $messages_today = min(8, max(0, round($progress * 6) + rand(0, 2)));
    } else {
        // Fora do horário
        if ($hour < 9) {
            // Manhã cedo - valores baixos do início do dia
            $follows_today = rand(0, 2);
            $comments_today = rand(0, 1);
            $messages_today = rand(0, 1);
        } else {
            // Noite - valores finais realistas e seguros
            $follows_today = rand(10, 15);
            $comments_today = rand(3, 6);
            $messages_today = rand(4, 8);
        }
    }
    
    return [
        'follows_today' => $follows_today,
        'comments_today' => $comments_today,
        'messages_today' => $messages_today,
        'max_follows' => MAX_FOLLOWS_PER_DAY,
        'max_comments' => MAX_COMMENTS_PER_DAY,
        'max_messages' => MAX_MESSAGES_PER_DAY,
        'simulated' => true
    ];
}

/**
 * Gera logs simulados para demonstração
 */
function getSimulatedLogs($type, $count = 10) {
    $logs = [];
    $hour = (int)date('H');
    
    // Mensagens baseadas no tipo
    $messages = [
        'seguidores' => [
            '✅ SUCESSO | SEGUIR | @livros_infantis_sp | Literatura Infantil',
            '✅ SUCESSO | SEGUIR | @mamae_que_le | Mães Leitoras',
            '✅ SUCESSO | SEGUIR | @professor_leitor | Educação',
            '⏰ DELAY | Aguardando 300s antes do próximo follow',
            '📊 STATS | Follows hoje: 8/15',
            '✅ SUCESSO | SEGUIR | @contacoes_infantis | Literatura',
            '🎯 TARGET | Seguindo via hashtag #literaturainfantil',
            '✅ SUCESSO | SEGUIR | @criancas_leitoras | Leitura Infantil',
            '🔍 BUSCA | Procurando perfis relacionados a livros infantis',
            '✅ SUCESSO | SEGUIR | @mundo_dos_livros_kids | Literatura'
        ],
        'comentarios' => [
            '✅ SUCESSO | COMENTAR | Post sobre leitura infantil',
            '📝 FRASE | "Que lindo ver a literatura tocando corações! ��💛"',
            '✅ SUCESSO | COMENTAR | Post sobre educação através dos livros', 
            '📝 FRASE | "A magia dos livros é transformar vidas �"',
            '⏰ DELAY | Aguardando 420s antes do próximo comentário',
            '📊 STATS | Comentários hoje: 3/6',
            '✅ SUCESSO | COMENTAR | Post sobre aventuras da Mel e Vitório',
            '📝 FRASE | "Mel e Vitório também adoram essa aventura! 🐾"',
            '🎯 TARGET | Comentando em post com hashtag #livrosinfantis'
        ],
        'mensagens' => [
            '✅ SUCESSO | MENSAGEM | @nova_mae_leitora',
            '📱 DM | "Olá! Bem-vinda ao mundo da Mel e Vitório! 🐾📚"',
            '✅ SUCESSO | MENSAGEM | @professor_ativo',
            '📱 DM | "Oi! Sou a Fátima, criadora das aventuras da Mel e Vitório 💛"',
            '⏰ DELAY | Aguardando 900s antes da próxima mensagem',
            '📊 STATS | Mensagens hoje: 4/8',
            '✅ SUCESSO | MENSAGEM | @bibliotecaria_kids',
            '🎯 TARGET | Novo seguidor interessado em literatura infantil',
            '📱 DM | "Obrigada por seguir! Aqui você encontra muito amor pelos livros! 📖"'
        ]
    ];
    
    $typeMessages = $messages[$type] ?? ['📋 Log de ' . $type];
    
    for ($i = 0; $i < $count; $i++) {
        $timestamp = date('Y-m-d H:i:s', strtotime("-" . ($i * 15) . " minutes"));
        $message = $typeMessages[array_rand($typeMessages)];
        
        $logs[] = [
            'timestamp' => $timestamp,
            'module' => $type,
            'level' => 'INFO',
            'message' => $message,
            'raw' => "$timestamp | $type | INFO | $message",
            'simulated' => true
        ];
    }
    
    return $logs;
}

/**
 * Verifica se deve usar dados simulados
 */
function shouldUseSimulatedData() {
    // Verifica se há dados reais de SUCESSO recentes
    $today = date('Y-m-d');
    
    $seguidoresLog = readLogFile('seguidores', 50);
    $comentariosLog = readLogFile('comentarios', 50);
    $mensagensLog = readLogFile('mensagens', 50);
    
    $hasRealSuccessData = false;
    
    // Verifica se há sucessos hoje nos logs
    foreach ($seguidoresLog as $log) {
        if (strpos($log['timestamp'], $today) === 0 && 
            strpos($log['message'], '✅ SUCESSO | SEGUIR') !== false) {
            $hasRealSuccessData = true;
            break;
        }
    }
    
    if (!$hasRealSuccessData) {
        foreach ($comentariosLog as $log) {
            if (strpos($log['timestamp'], $today) === 0 && 
                strpos($log['message'], '✅ SUCESSO | COMENTAR') !== false) {
                $hasRealSuccessData = true;
                break;
            }
        }
    }
    
    if (!$hasRealSuccessData) {
        foreach ($mensagensLog as $log) {
            if (strpos($log['timestamp'], $today) === 0 && 
                strpos($log['message'], '✅ SUCESSO | MENSAGEM') !== false) {
                $hasRealSuccessData = true;
                break;
            }
        }
    }
    
    // Se não há dados de sucesso reais, usa simulação
    return !$hasRealSuccessData;
}
?>

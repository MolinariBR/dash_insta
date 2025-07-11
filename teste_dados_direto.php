<?php
/**
 * Teste Direto dos Dados Simulados
 */

require_once 'config.php';

echo "🚀 Teste dos Dados Simulados\n";
echo "=" . str_repeat("=", 40) . "\n\n";

// Testa se deve usar dados simulados
$useSimulated = shouldUseSimulatedData();
echo "🎭 Deve usar dados simulados: " . ($useSimulated ? "SIM" : "NÃO") . "\n";

// Testa modo demo
$isDemoMode = isDemoMode();
echo "📊 Modo demo ativo: " . ($isDemoMode ? "SIM" : "NÃO") . "\n\n";

// Obtém dados simulados
$stats = getSimulatedStats();
echo "📈 Dados simulados:\n";
echo "   Follows hoje: {$stats['follows_today']}/{$stats['max_follows']}\n";
echo "   Comentários hoje: {$stats['comments_today']}/{$stats['max_comments']}\n";
echo "   Mensagens hoje: {$stats['messages_today']}/{$stats['max_messages']}\n\n";

// Testa logs simulados
echo "📋 Logs simulados (seguidores):\n";
$logs = getSimulatedLogs('seguidores', 3);
foreach ($logs as $log) {
    echo "   " . $log['timestamp'] . " | " . $log['message'] . "\n";
}

echo "\n✅ Teste concluído!\n";
?>

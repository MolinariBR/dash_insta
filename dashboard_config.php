<?php
/**
 * Configurações do Dashboard - Modo Demo/Produção
 * Instagram Bot @fatima.escritora
 */

// CONFIGURAÇÃO PRINCIPAL: Define se deve usar dados simulados
// true = Sempre usar dados simulados (modo demo)
// false = Usar dados reais quando disponíveis, simulados como fallback
define('FORCE_DEMO_MODE', false);

// Configurações do modo demo
define('DEMO_FOLLOWS_PROGRESS', 0.6);    // 60% do progresso do dia
define('DEMO_COMMENTS_PROGRESS', 0.7);   // 70% do progresso do dia  
define('DEMO_MESSAGES_PROGRESS', 0.8);   // 80% do progresso do dia

// Configurações de aparência
define('SHOW_DEMO_BADGES', true);        // Mostra badges "demo" nos cards
define('SHOW_DEMO_ALERT', true);         // Mostra alerta de modo demo

/**
 * Verifica se deve usar modo demo
 */
function isDemoMode() {
    if (FORCE_DEMO_MODE) {
        return true;
    }
    
    // Verifica se há dados reais
    return shouldUseSimulatedData();
}

/**
 * Obtém configurações do dashboard
 */
function getDashboardConfig() {
    return [
        'demo_mode' => isDemoMode(),
        'force_demo' => FORCE_DEMO_MODE,
        'show_badges' => SHOW_DEMO_BADGES,
        'show_alert' => SHOW_DEMO_ALERT,
        'last_check' => date('Y-m-d H:i:s')
    ];
}
?>

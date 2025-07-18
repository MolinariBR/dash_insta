<?php
require_once 'config.php';
$pageTitle = 'Configurações do Sistema';
require_once 'includes/header.php';

// Processa salvamento de configurações
if ($_POST && isset($_POST['save_config'])) {
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
        // Salva no arquivo de configurações
        $configFile = CONFIG_PATH . '/settings.json';
        if (file_put_contents($configFile, json_encode($newSettings, JSON_PRETTY_PRINT))) {
            echo "<script>showToast('✅ Configurações salvas com sucesso!', 'success');</script>";
            // Atualiza configurações carregadas
            $currentSettings = $newSettings;
            // Log de auditoria
            $usuario_logado = $_SESSION['usuario'] ?? 'N/A';
            $detalhes = 'Limites: ' . json_encode($newSettings['limites']);
            file_put_contents('logs/auditoria.log', date('c') . " | EDICAO_CONFIG | $usuario_logado | $detalhes\n", FILE_APPEND);
        } else {
            echo "<script>showToast('❌ Erro ao salvar configurações!', 'error');</script>";
        }
    } catch (Exception $e) {
        echo "<script>showToast('❌ Erro: " . addslashes($e->getMessage()) . "', 'error');</script>";
    }
}

// Carrega configurações atuais
$currentSettings = [];
if (file_exists(CONFIG_PATH . '/settings.json')) {
    $currentSettings = json_decode(file_get_contents(CONFIG_PATH . '/settings.json'), true);
}
?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-cog text-primary mr-3"></i>
            Configurações do Sistema
        </h1>
        <p class="text-gray-600 mt-2">Gerencie as configurações e parâmetros do bot</p>
    </div>

    <form method="POST" class="space-y-6">
        <!-- Limites e Cotas -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">
                    <i class="fas fa-sliders-h text-primary mr-2"></i>
                    Limites e Cotas
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Follows por dia</span>
                        </label>
                        <input type="number" name="max_follows" value="30" class="input input-bordered" min="1" max="100">
                        <label class="label">
                            <span class="label-text-alt">Recomendado: 20-30</span>
                        </label>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Comentários por dia</span>
                        </label>
                        <input type="number" name="max_comments" value="10" class="input input-bordered" min="1" max="50">
                        <label class="label">
                            <span class="label-text-alt">Recomendado: 5-15</span>
                        </label>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Mensagens por dia</span>
                        </label>
                        <input type="number" name="max_messages" value="20" class="input input-bordered" min="1" max="50">
                        <label class="label">
                            <span class="label-text-alt">Recomendado: 15-25</span>
                        </label>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Curtidas por follow</span>
                        </label>
                        <div class="flex space-x-2">
                            <input type="number" name="min_likes" value="5" class="input input-bordered flex-1" min="1" max="10">
                            <span class="self-center">a</span>
                            <input type="number" name="max_likes" value="7" class="input input-bordered flex-1" min="1" max="10">
                        </div>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Posts recentes (dias)</span>
                        </label>
                        <input type="number" name="recent_posts_days" value="10" class="input input-bordered" min="1" max="30">
                    </div>
                </div>
            </div>
        </div>

        <!-- Horários de Funcionamento -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">
                    <i class="fas fa-clock text-success mr-2"></i>
                    Horários de Funcionamento
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-semibold mb-3">Atividade Geral</h3>
                        <div class="flex items-center space-x-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Início</span>
                                </label>
                                <input type="time" name="start_hour" value="09:00" class="input input-bordered">
                            </div>
                            <span class="self-end pb-3">às</span>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Fim</span>
                                </label>
                                <input type="time" name="end_hour" value="20:00" class="input input-bordered">
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold mb-3">Comentários</h3>
                        <div class="flex items-center space-x-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Início</span>
                                </label>
                                <input type="time" name="comment_start" value="10:00" class="input input-bordered">
                            </div>
                            <span class="self-end pb-3">às</span>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Fim</span>
                                </label>
                                <input type="time" name="comment_end" value="21:00" class="input input-bordered">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delays e Intervalos -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">
                    <i class="fas fa-hourglass-half text-warning mr-2"></i>
                    Delays e Intervalos
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-semibold mb-3">Entre Curtidas</h3>
                        <div class="flex items-center space-x-4">
                            <input type="number" name="min_like_delay" value="3" class="input input-bordered flex-1" min="1" max="60">
                            <span>a</span>
                            <input type="number" name="max_like_delay" value="7" class="input input-bordered flex-1" min="1" max="60">
                            <span class="text-sm">segundos</span>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold mb-3">Entre Comentários</h3>
                        <div class="flex items-center space-x-4">
                            <input type="number" name="min_comment_delay" value="120" class="input input-bordered flex-1" min="60" max="600">
                            <span>a</span>
                            <input type="number" name="max_comment_delay" value="240" class="input input-bordered flex-1" min="60" max="600">
                            <span class="text-sm">segundos</span>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold mb-3">Entre Follows</h3>
                        <div class="flex items-center space-x-4">
                            <input type="number" name="min_follow_delay" value="60" class="input input-bordered flex-1" min="30" max="300">
                            <span>a</span>
                            <input type="number" name="max_follow_delay" value="180" class="input input-bordered flex-1" min="30" max="300">
                            <span class="text-sm">segundos</span>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold mb-3">Mensagem DM</h3>
                        <div class="flex items-center space-x-4">
                            <input type="number" name="min_dm_delay" value="10" class="input input-bordered flex-1" min="5" max="60">
                            <span>a</span>
                            <input type="number" name="max_dm_delay" value="30" class="input input-bordered flex-1" min="5" max="60">
                            <span class="text-sm">minutos</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hashtags Alvo -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">
                    <i class="fas fa-hashtag text-info mr-2"></i>
                    Hashtags Alvo
                </h2>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Hashtags principais (uma por linha)</span>
                    </label>
                    <textarea name="hashtags" class="textarea textarea-bordered h-32" placeholder="#livroinfantil&#10;#educacaoinfantil&#10;#inclusaoescolar"><?php
                    if (isset($currentSettings['hashtags_alvo'])) {
                        echo implode("\n", $currentSettings['hashtags_alvo']);
                    } else {
                        echo "#livroinfantil\n#educacaoinfantil\n#inclusaoescolar\n#literaturainfantil\n#adocaodeanimais";
                    }
                    ?></textarea>
                </div>
            </div>
        </div>

        <!-- Filtros de Segurança -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">
                    <i class="fas fa-shield-alt text-error mr-2"></i>
                    Filtros de Segurança
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Palavras bloqueadas</span>
                        </label>
                        <textarea name="blocked_words" class="textarea textarea-bordered" placeholder="nude, adult, sexy">nude, adult, sexy, violence, hate</textarea>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Hashtags bloqueadas</span>
                        </label>
                        <textarea name="blocked_hashtags" class="textarea textarea-bordered" placeholder="#nsfw, #adult">#nsfw, #adult, #violence</textarea>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div class="form-control">
                        <label class="label cursor-pointer">
                            <span class="label-text">Filtrar contas suspeitas</span>
                            <input type="checkbox" name="filter_suspicious" class="toggle toggle-primary" checked>
                        </label>
                    </div>
                    
                    <div class="form-control">
                        <label class="label cursor-pointer">
                            <span class="label-text">Verificar ratio followers</span>
                            <input type="checkbox" name="check_ratio" class="toggle toggle-primary" checked>
                        </label>
                    </div>
                    
                    <div class="form-control">
                        <label class="label cursor-pointer">
                            <span class="label-text">Evitar contas bot</span>
                            <input type="checkbox" name="avoid_bots" class="toggle toggle-primary" checked>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensagens Personalizadas -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">
                    <i class="fas fa-comments text-secondary mr-2"></i>
                    Mensagens Personalizadas
                </h2>
                
                <div class="tabs tabs-bordered mb-4">
                    <a class="tab tab-active" onclick="showMessageTab('welcome')">Boas-vindas</a>
                    <a class="tab" onclick="showMessageTab('comments')">Comentários</a>
                    <a class="tab" onclick="showMessageTab('emojis')">Emojis</a>
                </div>
                
                <div id="welcome-tab" class="message-tab">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Mensagens de boas-vindas (uma por linha)</span>
                        </label>
                        <textarea name="welcome_messages" class="textarea textarea-bordered h-32" placeholder="Oi! Muito obrigada por me seguir!">Oi! 🌸 Muito obrigada por me seguir! Fico feliz em ter você aqui comigo nessa jornada literária! ✨
Olá! 💛 Que alegria ter você aqui! Espero que goste do conteúdo sobre literatura infantil e educação! 📚
Oi, querido(a)! 🌻 Obrigada pelo follow! Aqui compartilho sobre livros, inclusão e muito amor pelos pequenos! 💕</textarea>
                    </div>
                </div>
                
                <div id="comments-tab" class="message-tab hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Comentários gerais</span>
                            </label>
                            <textarea name="general_comments" class="textarea textarea-bordered h-24">Que lindo! 📚
Adorei este conteúdo! ✨
Muito inspirador! 💛</textarea>
                        </div>
                        
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Comentários literatura</span>
                            </label>
                            <textarea name="literature_comments" class="textarea textarea-bordered h-24">A literatura infantil é um mundo mágico! 📚✨
Que livro maravilhoso! A leitura transforma vidas! 💛
Histórias que tocam o coração das crianças! 🌸</textarea>
                        </div>
                    </div>
                </div>
                
                <div id="emojis-tab" class="message-tab hidden">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Emojis disponíveis</span>
                        </label>
                        <input type="text" name="emojis" value="🌸,💛,📚,🐶,✨,💕,🌻,📖,🎈,🌈" class="input input-bordered" placeholder="🌸,💛,📚">
                        <label class="label">
                            <span class="label-text-alt">Separe os emojis por vírgula</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Integrações de Métricas (Facebook, Google Ads, Analytics) -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">
                    <i class="fas fa-key text-blue-600 mr-2"></i>
                    Integrações de Métricas
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Facebook Access Token</span></label>
                        <input type="text" name="facebook_access_token" value="<?php echo isset($currentSettings['facebook_access_token']) ? htmlspecialchars($currentSettings['facebook_access_token']) : ''; ?>" class="input input-bordered" placeholder="Ex: EAAB..." autocomplete="off">
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Facebook Ad Account ID</span></label>
                        <input type="text" name="facebook_ad_account_id" value="<?php echo isset($currentSettings['facebook_ad_account_id']) ? htmlspecialchars($currentSettings['facebook_ad_account_id']) : ''; ?>" class="input input-bordered" placeholder="Ex: act_1234567890" autocomplete="off">
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Google Developer Token</span></label>
                        <input type="text" name="google_developer_token" value="<?php echo isset($currentSettings['google_developer_token']) ? htmlspecialchars($currentSettings['google_developer_token']) : ''; ?>" class="input input-bordered" placeholder="Ex: 1234567890" autocomplete="off">
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Google Client ID</span></label>
                        <input type="text" name="google_client_id" value="<?php echo isset($currentSettings['google_client_id']) ? htmlspecialchars($currentSettings['google_client_id']) : ''; ?>" class="input input-bordered" placeholder="Ex: xxxxx.apps.googleusercontent.com" autocomplete="off">
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Google Client Secret</span></label>
                        <input type="text" name="google_client_secret" value="<?php echo isset($currentSettings['google_client_secret']) ? htmlspecialchars($currentSettings['google_client_secret']) : ''; ?>" class="input input-bordered" placeholder="Ex: ABCDEFGHIJKLMNOP" autocomplete="off">
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Google Refresh Token</span></label>
                        <input type="text" name="google_refresh_token" value="<?php echo isset($currentSettings['google_refresh_token']) ? htmlspecialchars($currentSettings['google_refresh_token']) : ''; ?>" class="input input-bordered" placeholder="Ex: 1//0g..." autocomplete="off">
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Google Customer ID</span></label>
                        <input type="text" name="google_customer_id" value="<?php echo isset($currentSettings['google_customer_id']) ? htmlspecialchars($currentSettings['google_customer_id']) : ''; ?>" class="input input-bordered" placeholder="Ex: 1234567890" autocomplete="off">
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Google Analytics Credentials Path</span></label>
                        <input type="text" name="google_credentials_path" value="<?php echo isset($currentSettings['google_credentials_path']) ? htmlspecialchars($currentSettings['google_credentials_path']) : ''; ?>" class="input input-bordered" placeholder="Ex: /caminho/para/credentials.json" autocomplete="off">
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Google Analytics View ID</span></label>
                        <input type="text" name="google_analytics_view_id" value="<?php echo isset($currentSettings['google_analytics_view_id']) ? htmlspecialchars($currentSettings['google_analytics_view_id']) : ''; ?>" class="input input-bordered" placeholder="Ex: 123456789" autocomplete="off">
                    </div>
                </div>
                <div class="text-xs text-gray-500 mt-2">Essas credenciais são necessárias para exibir métricas reais no painel. Mantenha-as seguras!</div>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex space-x-4">
                        <button type="submit" name="save_config" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>Salvar Configurações
                        </button>
                        
                        <button type="button" onclick="resetToDefaults()" class="btn btn-outline btn-warning">
                            <i class="fas fa-undo mr-2"></i>Restaurar Padrões
                        </button>
                    </div>
                    
                    <div class="flex space-x-2">
                        <button type="button" onclick="exportConfig()" class="btn btn-outline btn-info btn-sm">
                            <i class="fas fa-download mr-2"></i>Exportar
                        </button>
                        
                        <label for="import-config" class="btn btn-outline btn-success btn-sm">
                            <i class="fas fa-upload mr-2"></i>Importar
                        </label>
                        <input type="file" id="import-config" class="hidden" accept=".json" onchange="importConfig(this)">
                    </div>
                </div>
                
                <div class="alert alert-warning mt-4">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <h3 class="font-bold">Atenção!</h3>
                        <div class="text-sm">
                            Alterações nas configurações podem afetar o desempenho do bot. 
                            Certifique-se de testar as mudanças antes de aplicar em produção.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function showMessageTab(tabName) {
    // Esconde todas as abas
    document.querySelectorAll('.message-tab').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Mostra aba selecionada
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    
    // Atualiza estado dos botões
    document.querySelectorAll('.tab').forEach(tab => {
        tab.classList.remove('tab-active');
    });
    event.target.classList.add('tab-active');
}

function resetToDefaults() {
    if (confirm('Tem certeza que deseja restaurar todas as configurações para os valores padrão?')) {
        // Implementar reset para valores padrão
        location.reload();
    }
}

function exportConfig() {
    // Coleta todos os dados do formulário
    const formData = new FormData(document.querySelector('form'));
    const config = {};
    
    for (let [key, value] of formData.entries()) {
        config[key] = value;
    }
    
    // Gera download do arquivo JSON
    const blob = new Blob([JSON.stringify(config, null, 2)], {type: 'application/json'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `bot_config_${new Date().toISOString().split('T')[0]}.json`;
    a.click();
    URL.revokeObjectURL(url);
    
    showToast('Configurações exportadas com sucesso!', 'success');
}

function importConfig(input) {
    const file = input.files[0];
    if (!file) return;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            const config = JSON.parse(e.target.result);
            
            // Preenche o formulário com os dados importados
            for (let [key, value] of Object.entries(config)) {
                const field = document.querySelector(`[name="${key}"]`);
                if (field) {
                    if (field.type === 'checkbox') {
                        field.checked = value === 'on' || value === true;
                    } else {
                        field.value = value;
                    }
                }
            }
            
            showToast('Configurações importadas com sucesso!', 'success');
            
        } catch (error) {
            showToast('Erro ao importar configurações: arquivo inválido', 'error');
        }
    };
    reader.readAsText(file);
}

// Validação em tempo real
document.addEventListener('DOMContentLoaded', function() {
    // Valida campos numéricos
    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('change', function() {
            const min = parseInt(this.min);
            const max = parseInt(this.max);
            const value = parseInt(this.value);
            
            if (value < min) {
                this.value = min;
                showToast(`Valor mínimo para ${this.name}: ${min}`, 'warning');
            } else if (value > max) {
                this.value = max;
                showToast(`Valor máximo para ${this.name}: ${max}`, 'warning');
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>

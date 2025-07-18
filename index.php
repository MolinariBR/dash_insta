<?php
require_once 'config.php';
$pageTitle = 'Dashboard Principal';
require_once 'includes/header.php';

// Obtém estatísticas do bot
$stats = getBotStats();
$isRunning = isBotRunning();

// Buscar contas do Instagram disponíveis
function getDB() {
    $db = new PDO('sqlite:data/database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}
$db = getDB();
$contas = $db->query('SELECT ci.id, ci.username, c.nome as cliente_nome FROM contas_instagram ci JOIN clientes c ON ci.cliente_id = c.id ORDER BY c.nome, ci.username')->fetchAll(PDO::FETCH_ASSOC);
$conta_id = isset($_GET['conta_id']) ? (int)$_GET['conta_id'] : (isset($_POST['conta_id']) ? (int)$_POST['conta_id'] : ($contas[0]['id'] ?? null));
?>
<div class="mb-4">
    <form method="get" class="flex flex-col md:flex-row items-center gap-2">
        <label class="font-semibold">Conta do Instagram:</label>
        <select name="conta_id" onchange="this.form.submit()" class="border p-2 rounded">
            <?php foreach ($contas as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $conta_id == $c['id'] ? 'selected' : '' ?>>@<?= htmlspecialchars($c['username']) ?> (<?= htmlspecialchars($c['cliente_nome']) ?>)</option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="container mx-auto px-4 py-6">
    <!-- Header do Dashboard -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-tachometer-alt text-primary mr-3"></i>
                    Dashboard Principal
                </h1>
                <p class="text-gray-600 mt-2">Painel de controle do Instagram Bot @fatima.escritora</p>
            </div>
            
            <!-- Controles do Bot -->
            <div class="flex space-x-2">
                <button onclick="openSettings()" class="btn btn-info btn-sm">
                    <i class="fas fa-cog mr-2"></i>Configurações
                </button>
                <?php if ($isRunning): ?>
                    <button onclick="controlBot('stop')" class="btn btn-error btn-sm">
                        <i class="fas fa-stop mr-2"></i>Parar Bot
                    </button>
                    <button onclick="controlBot('restart')" class="btn btn-warning btn-sm">
                        <i class="fas fa-redo mr-2"></i>Reiniciar
                    </button>
                <?php else: ?>
                    <button onclick="controlBot('start')" class="btn btn-success btn-sm">
                        <i class="fas fa-play mr-2"></i>Iniciar Bot
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Indicador de Dados -->
        <?php if (isset($stats['simulated']) && $stats['simulated']): ?>
        <div class="col-span-full">
            <div class="alert alert-info">
                <div class="flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    <span><strong>Modo Demonstração:</strong> Os dados abaixo são simulados para demonstração. Quando o bot estiver operando, os dados reais aparecerão aqui.</span>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Follows Hoje -->
        <div class="card bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="card-title text-white text-lg">Follows Hoje</h2>
                        <p class="text-2xl font-bold"><?= $stats['follows_today'] ?>/<?= $stats['max_follows'] ?></p>
                        <?php if (isset($stats['simulated']) && $stats['simulated']): ?>
                            <span class="badge badge-warning badge-xs">demo</span>
                        <?php endif; ?>
                    </div>
                    <i class="fas fa-users text-4xl opacity-80"></i>
                </div>
                <div class="w-full bg-white/20 rounded-full h-2 mt-2">
                    <div class="bg-white rounded-full h-2 transition-all duration-300" 
                         style="width: <?= ($stats['follows_today'] / $stats['max_follows']) * 100 ?>%"></div>
                </div>
            </div>
        </div>

        <!-- Comentários Hoje -->
        <div class="card bg-gradient-to-r from-green-500 to-green-600 text-white shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="card-title text-white text-lg">Comentários</h2>
                        <p class="text-2xl font-bold"><?= $stats['comments_today'] ?>/<?= $stats['max_comments'] ?></p>
                        <?php if (isset($stats['simulated']) && $stats['simulated']): ?>
                            <span class="badge badge-warning badge-xs">demo</span>
                        <?php endif; ?>
                    </div>
                    <i class="fas fa-comments text-4xl opacity-80"></i>
                </div>
                <div class="w-full bg-white/20 rounded-full h-2 mt-2">
                    <div class="bg-white rounded-full h-2 transition-all duration-300" 
                         style="width: <?= ($stats['comments_today'] / $stats['max_comments']) * 100 ?>%"></div>
                </div>
            </div>
        </div>

        <!-- Mensagens Hoje -->
        <div class="card bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="card-title text-white text-lg">Mensagens</h2>
                        <p class="text-2xl font-bold"><?= $stats['messages_today'] ?>/<?= $stats['max_messages'] ?></p>
                        <?php if (isset($stats['simulated']) && $stats['simulated']): ?>
                            <span class="badge badge-warning badge-xs">demo</span>
                        <?php endif; ?>
                    </div>
                    <i class="fas fa-envelope text-4xl opacity-80"></i>
                </div>
                <div class="w-full bg-white/20 rounded-full h-2 mt-2">
                    <div class="bg-white rounded-full h-2 transition-all duration-300" 
                         style="width: <?= ($stats['messages_today'] / $stats['max_messages']) * 100 ?>%"></div>
                </div>
            </div>
        </div>

        <!-- Status do Sistema -->
        <div class="card bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="card-title text-white text-lg">Status</h2>
                        <p class="text-xl font-bold"><?= $isRunning ? 'Online' : 'Offline' ?></p>
                    </div>
                    <i class="fas fa-<?= $isRunning ? 'check-circle' : 'times-circle' ?> text-4xl opacity-80"></i>
                </div>
                <p class="text-sm opacity-90 mt-2">
                    <?= $isRunning ? 'Bot funcionando normalmente' : 'Bot não está rodando' ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Seção principal com 2 colunas -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Logs Recentes (2/3 da tela) -->
        <div class="xl:col-span-2">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">
                        <i class="fas fa-file-alt text-primary mr-2"></i>
                        Atividades Recentes
                    </h2>
                    
                    <div class="tabs tabs-boxed mb-4">
                        <a class="tab tab-active" onclick="showLogs('seguidores')">Seguidores</a>
                        <a class="tab" onclick="showLogs('comentarios')">Comentários</a>
                        <a class="tab" onclick="showLogs('mensagens')">Mensagens</a>
                    </div>

                    <div id="logs-container" class="max-h-96 overflow-y-auto">
                        <!-- Logs serão carregados via JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Painel lateral (1/3 da tela) -->
        <div class="space-y-6">
            <!-- Ações Rápidas -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">
                        <i class="fas fa-bolt text-warning mr-2"></i>
                        Ações Rápidas
                    </h2>
                    
                    <div class="space-y-3">
                        <button onclick="openTestModal('follow')" class="btn btn-outline btn-primary w-full btn-sm">
                            <i class="fas fa-user-plus mr-2"></i>Testar Follow
                        </button>
                        <button onclick="openTestModal('comment')" class="btn btn-outline btn-success w-full btn-sm">
                            <i class="fas fa-comment mr-2"></i>Testar Comentário
                        </button>
                        <a href="logs.php" class="btn btn-outline btn-info w-full btn-sm">
                            <i class="fas fa-file-alt mr-2"></i>Ver Todos os Logs
                        </a>
                        <a href="stats.php" class="btn btn-outline btn-secondary w-full btn-sm">
                            <i class="fas fa-chart-bar mr-2"></i>Estatísticas Completas
                        </a>
                    </div>
                </div>
            </div>

            <!-- Configurações Rápidas -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">
                        <i class="fas fa-info-circle text-info mr-2"></i>
                        Informações do Sistema
                    </h2>
                    
                    <?php $dashboardConfig = getDashboardConfig(); ?>
                    <?php if ($dashboardConfig['demo_mode']): ?>
                    <div class="alert alert-warning alert-sm mb-3">
                        <i class="fas fa-flask text-sm"></i>
                        <span class="text-xs">Modo demonstração ativo</span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Modo de dados:</span>
                            <span class="font-semibold">
                                <?= $dashboardConfig['demo_mode'] ? 'Demonstração' : 'Produção' ?>
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span>Horário de funcionamento:</span>
                            <span class="font-semibold">9h às 20h</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Comentários:</span>
                            <span class="font-semibold">10h às 21h</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Limite diário follows:</span>
                            <span class="font-semibold"><?= $stats['max_follows'] ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Delay entre curtidas:</span>
                            <span class="font-semibold">3-7 seg</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Delay entre comentários:</span>
                            <span class="font-semibold">2-4 min</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Próximas Execuções -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">
                        <i class="fas fa-clock text-accent mr-2"></i>
                        Próximas Execuções
                    </h2>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-sun text-yellow-500"></i>
                            <span>09:00 - Rotina da manhã</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-sun text-orange-500"></i>
                            <span>14:00 - Rotina da tarde</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-moon text-purple-500"></i>
                            <span>19:00 - Rotina da noite</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-sync text-blue-500"></i>
                            <span>A cada 2h - Mensagens</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para configurações -->
<dialog id="settings-modal" class="modal">
    <div class="modal-box max-w-4xl">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="font-bold text-lg mb-4">
            <i class="fas fa-cog mr-2"></i>Configurações do Bot
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Limites -->
            <div>
                <h4 class="font-semibold mb-3">Limites Diários</h4>
                <div class="space-y-3">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Follows por dia</span>
                        </label>
                        <input type="number" id="max_follows" class="input input-bordered input-sm" value="20" min="1" max="50">
                    </div>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Comentários por dia</span>
                        </label>
                        <input type="number" id="max_comments" class="input input-bordered input-sm" value="8" min="1" max="30">
                    </div>
                </div>
            </div>
            
            <!-- Horários -->
            <div>
                <h4 class="font-semibold mb-3">Horários</h4>
                <div class="space-y-3">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Início das atividades</span>
                        </label>
                        <select id="start_hour" class="select select-bordered select-sm">
                            <option value="8">08:00</option>
                            <option value="9" selected>09:00</option>
                            <option value="10">10:00</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Fim das atividades</span>
                        </label>
                        <select id="end_hour" class="select select-bordered select-sm">
                            <option value="18">18:00</option>
                            <option value="19">19:00</option>
                            <option value="20" selected>20:00</option>
                            <option value="21">21:00</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Delays -->
            <div>
                <h4 class="font-semibold mb-3">Delays (segundos)</h4>
                <div class="space-y-3">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Entre comentários (min)</span>
                        </label>
                        <input type="number" id="min_comment_delay" class="input input-bordered input-sm" value="300" min="120" max="900">
                    </div>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Entre follows (min)</span>
                        </label>
                        <input type="number" id="min_follow_delay" class="input input-bordered input-sm" value="180" min="60" max="600">
                    </div>
                </div>
            </div>
            
            <!-- Hashtags -->
            <div>
                <h4 class="font-semibold mb-3">Hashtags Alvo</h4>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Uma por linha</span>
                    </label>
                    <textarea id="hashtags" class="textarea textarea-bordered textarea-sm" rows="4">#livroinfantil
#educacaoinfantil
#literaturainfantil
#leitura</textarea>
                </div>
            </div>
        </div>
        
        <div class="modal-action">
            <button class="btn btn-primary" onclick="saveSettings()">
                <i class="fas fa-save mr-2"></i>Salvar Configurações
            </button>
            <form method="dialog">
                <button class="btn">Cancelar</button>
            </form>
        </div>
    </div>
</dialog>

<!-- Modal para testes -->
<dialog id="test-modal" class="modal">
    <div class="modal-box">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="font-bold text-lg" id="modal-title">Teste de Funcionalidade</h3>
        <div class="py-4" id="modal-content">
            <!-- Conteúdo será inserido via JavaScript -->
        </div>
        <div class="modal-action">
            <button class="btn btn-primary" onclick="executeTest()">Executar Teste</button>
            <form method="dialog">
                <button class="btn">Cancelar</button>
            </form>
        </div>
    </div>
</dialog>

<script>
let currentLogType = 'seguidores';
let currentTestType = '';

// Carrega logs iniciais
document.addEventListener('DOMContentLoaded', function() {
    showLogs('seguidores');
    
    // Auto-refresh a cada 30 segundos
    setInterval(() => {
        showLogs(currentLogType);
    }, 30000);
});

// Função para mostrar logs
function showLogs(type) {
    currentLogType = type;
    
    // Atualiza tabs
    document.querySelectorAll('.tab').forEach(tab => {
        tab.classList.remove('tab-active');
    });
    event?.target.classList.add('tab-active');
    
    // Carrega logs
    fetch(`api/logs.php?type=${type}&limit=20`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('logs-container');
            
            if (data.logs && data.logs.length > 0) {
                container.innerHTML = data.logs.map(log => {
                    const levelClass = log.level === 'ERROR' ? 'text-error' : 
                                     log.level === 'WARNING' ? 'text-warning' : 'text-success';
                    
                    return `
                        <div class="flex items-start space-x-3 p-3 border-b border-gray-100 hover:bg-gray-50">
                            <div class="flex-shrink-0">
                                <div class="w-2 h-2 rounded-full ${log.level === 'ERROR' ? 'bg-red-500' : 'bg-green-500'} mt-2"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">${log.message}</p>
                                <p class="text-xs text-gray-500">${timeAgo(log.timestamp)}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="px-2 py-1 text-xs rounded-full ${levelClass} bg-gray-100">${log.level}</span>
                            </div>
                        </div>
                    `;
                }).join('');
            } else {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-4"></i>
                        <p>Nenhum log encontrado para ${type}</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Erro ao carregar logs:', error);
            showToast('Erro ao carregar logs', 'error');
        });
}

// Função para controlar bot
function controlBot(action) {
    const contaId = document.querySelector('select[name=conta_id]').value;
    const formData = new FormData();
    formData.append('action', action);
    formData.append('conta_id', contaId);
    
    fetch('api/control.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            // Recarrega a página após 2 segundos
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('Erro ao executar ação', 'error');
    });
}

// Função para abrir modal de teste
function openTestModal(type) {
    currentTestType = type;
    const modal = document.getElementById('test-modal');
    const title = document.getElementById('modal-title');
    const content = document.getElementById('modal-content');
    
    if (type === 'follow') {
        title.textContent = 'Teste de Follow';
        content.innerHTML = `
            <label class="label">
                <span class="label-text">Username para testar (sem @):</span>
            </label>
            <input type="text" id="test-username" placeholder="username" class="input input-bordered w-full" />
            <p class="text-sm text-gray-500 mt-2">O bot tentará seguir este usuário como teste</p>
        `;
    } else if (type === 'comment') {
        title.textContent = 'Teste de Comentário';
        content.innerHTML = `
            <label class="label">
                <span class="label-text">Hashtag para testar (com #):</span>
            </label>
            <input type="text" id="test-hashtag" placeholder="#livroinfantil" class="input input-bordered w-full" />
            <p class="text-sm text-gray-500 mt-2">O bot tentará comentar em 1 post desta hashtag</p>
        `;
    }
    
    modal.showModal();
}

// Função para executar teste
function executeTest() {
    const contaId = document.querySelector('select[name=conta_id]').value;
    const formData = new FormData();
    
    if (currentTestType === 'follow') {
        const username = document.getElementById('test-username').value;
        if (!username) {
            showToast('Digite um username', 'warning');
            return;
        }
        formData.append('action', 'test_follow');
        formData.append('username', username);
        formData.append('conta_id', contaId);
    } else if (currentTestType === 'comment') {
        const hashtag = document.getElementById('test-hashtag').value;
        if (!hashtag) {
            showToast('Digite uma hashtag', 'warning');
            return;
        }
        formData.append('action', 'test_comment');
        formData.append('hashtag', hashtag);
        formData.append('conta_id', contaId);
    }
    
    fetch('api/control.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'error');
        }
        document.getElementById('test-modal').close();
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('Erro ao executar teste', 'error');
    });
}
</script>

<?php require_once 'includes/footer.php'; ?>

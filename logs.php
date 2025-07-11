<?php
require_once 'config.php';
$pageTitle = 'Logs do Sistema';
require_once 'includes/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-file-alt text-primary mr-3"></i>
            Logs do Sistema
        </h1>
        <p class="text-gray-600 mt-2">Visualização completa dos logs de atividades do bot</p>
    </div>

    <!-- Filtros e Controles -->
    <div class="card bg-base-100 shadow-xl mb-6">
        <div class="card-body">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <!-- Seletor de tipo de log -->
                <div class="flex items-center space-x-4">
                    <label class="label">
                        <span class="label-text font-semibold">Tipo de Log:</span>
                    </label>
                    <select id="log-type" class="select select-bordered" onchange="loadLogs()">
                        <option value="seguidores">Seguidores</option>
                        <option value="comentarios">Comentários</option>
                        <option value="mensagens">Mensagens</option>
                        <option value="curtidas">Curtidas</option>
                        <option value="main">Sistema Principal</option>
                        <option value="client">Cliente Instagram</option>
                    </select>
                </div>

                <!-- Filtro por nível -->
                <div class="flex items-center space-x-4">
                    <label class="label">
                        <span class="label-text font-semibold">Nível:</span>
                    </label>
                    <select id="log-level" class="select select-bordered" onchange="filterLogs()">
                        <option value="all">Todos</option>
                        <option value="INFO">Info</option>
                        <option value="ERROR">Erro</option>
                        <option value="WARNING">Aviso</option>
                    </select>
                </div>

                <!-- Controles -->
                <div class="flex items-center space-x-2">
                    <button onclick="loadLogs()" class="btn btn-primary btn-sm">
                        <i class="fas fa-sync mr-2"></i>Atualizar
                    </button>
                    <button onclick="clearLogs()" class="btn btn-error btn-sm">
                        <i class="fas fa-trash mr-2"></i>Limpar
                    </button>
                    <button onclick="downloadLogs()" class="btn btn-success btn-sm">
                        <i class="fas fa-download mr-2"></i>Download
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas dos Logs -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="stat bg-blue-500 text-white rounded-lg">
            <div class="stat-figure">
                <i class="fas fa-info-circle text-2xl"></i>
            </div>
            <div class="stat-title text-blue-100">Info</div>
            <div class="stat-value" id="info-count">0</div>
        </div>
        
        <div class="stat bg-green-500 text-white rounded-lg">
            <div class="stat-figure">
                <i class="fas fa-check-circle text-2xl"></i>
            </div>
            <div class="stat-title text-green-100">Sucesso</div>
            <div class="stat-value" id="success-count">0</div>
        </div>
        
        <div class="stat bg-yellow-500 text-white rounded-lg">
            <div class="stat-figure">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>
            <div class="stat-title text-yellow-100">Avisos</div>
            <div class="stat-value" id="warning-count">0</div>
        </div>
        
        <div class="stat bg-red-500 text-white rounded-lg">
            <div class="stat-figure">
                <i class="fas fa-times-circle text-2xl"></i>
            </div>
            <div class="stat-title text-red-100">Erros</div>
            <div class="stat-value" id="error-count">0</div>
        </div>
    </div>

    <!-- Container dos Logs -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <h2 class="card-title">
                    <i class="fas fa-list text-primary mr-2"></i>
                    Registros de Atividade
                </h2>
                
                <!-- Busca -->
                <div class="form-control">
                    <div class="input-group input-group-sm">
                        <input 
                            type="text" 
                            id="search-input" 
                            placeholder="Buscar nos logs..." 
                            class="input input-bordered input-sm"
                            onkeyup="searchLogs()"
                        >
                        <button class="btn btn-square btn-sm">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Loading -->
            <div id="loading" class="text-center py-8 hidden">
                <span class="loading loading-spinner loading-lg"></span>
                <p class="mt-2">Carregando logs...</p>
            </div>

            <!-- Tabela de Logs -->
            <div class="overflow-x-auto" id="logs-table-container">
                <table class="table table-zebra table-sm">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Módulo</th>
                            <th>Nível</th>
                            <th>Mensagem</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="logs-tbody">
                        <!-- Logs serão inseridos aqui -->
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="flex justify-center mt-6">
                <div class="btn-group">
                    <button id="prev-page" class="btn btn-sm" onclick="changePage(-1)">«</button>
                    <button class="btn btn-sm btn-active" id="current-page">1</button>
                    <button id="next-page" class="btn btn-sm" onclick="changePage(1)">»</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalhes do log -->
<dialog id="log-detail-modal" class="modal">
    <div class="modal-box max-w-4xl">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="font-bold text-lg">Detalhes do Log</h3>
        <div class="py-4">
            <pre id="log-detail-content" class="bg-base-200 p-4 rounded text-sm overflow-auto max-h-96"></pre>
        </div>
    </div>
</dialog>

<script>
let allLogs = [];
let filteredLogs = [];
let currentPage = 1;
const logsPerPage = 50;

// Carrega logs na inicialização
document.addEventListener('DOMContentLoaded', function() {
    loadLogs();
    
    // Auto-refresh a cada 30 segundos
    setInterval(loadLogs, 30000);
});

// Função para carregar logs
function loadLogs() {
    const logType = document.getElementById('log-type').value;
    const loading = document.getElementById('loading');
    
    loading.classList.remove('hidden');
    
    fetch(`api/logs.php?type=${logType}&limit=1000`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allLogs = data.logs;
                filterLogs();
                updateStats();
            } else {
                showToast('Erro ao carregar logs', 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showToast('Erro ao carregar logs', 'error');
        })
        .finally(() => {
            loading.classList.add('hidden');
        });
}

// Função para filtrar logs
function filterLogs() {
    const levelFilter = document.getElementById('log-level').value;
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    
    filteredLogs = allLogs.filter(log => {
        const levelMatch = levelFilter === 'all' || log.level === levelFilter;
        const searchMatch = searchTerm === '' || 
                           log.message.toLowerCase().includes(searchTerm) ||
                           log.module.toLowerCase().includes(searchTerm);
        
        return levelMatch && searchMatch;
    });
    
    currentPage = 1;
    displayLogs();
}

// Função para buscar nos logs
function searchLogs() {
    filterLogs();
}

// Função para exibir logs
function displayLogs() {
    const tbody = document.getElementById('logs-tbody');
    const startIndex = (currentPage - 1) * logsPerPage;
    const endIndex = startIndex + logsPerPage;
    const pageItems = filteredLogs.slice(startIndex, endIndex);
    
    tbody.innerHTML = pageItems.map(log => {
        const levelClass = {
            'ERROR': 'badge-error',
            'WARNING': 'badge-warning',
            'INFO': 'badge-info'
        }[log.level] || 'badge-ghost';
        
        const timeClass = isRecentLog(log.timestamp) ? 'text-success font-bold' : '';
        
        return `
            <tr class="hover">
                <td class="${timeClass}">${formatDateTime(log.timestamp)}</td>
                <td><span class="badge badge-outline">${log.module}</span></td>
                <td><span class="badge ${levelClass}">${log.level}</span></td>
                <td class="max-w-md truncate" title="${log.message}">${log.message}</td>
                <td>
                    <button class="btn btn-ghost btn-xs" onclick="showLogDetail(\`${log.raw.replace(/`/g, '\\`')}\`)">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
    
    // Atualiza paginação
    document.getElementById('current-page').textContent = currentPage;
    document.getElementById('prev-page').disabled = currentPage === 1;
    document.getElementById('next-page').disabled = endIndex >= filteredLogs.length;
}

// Função para verificar se o log é recente (últimos 5 minutos)
function isRecentLog(timestamp) {
    const logTime = new Date(timestamp);
    const now = new Date();
    const diffInMinutes = (now - logTime) / (1000 * 60);
    return diffInMinutes <= 5;
}

// Função para atualizar estatísticas
function updateStats() {
    const counts = {
        info: 0,
        success: 0,
        warning: 0,
        error: 0
    };
    
    allLogs.forEach(log => {
        if (log.level === 'INFO') {
            if (log.message.includes('✅ SUCESSO')) {
                counts.success++;
            } else {
                counts.info++;
            }
        } else if (log.level === 'WARNING') {
            counts.warning++;
        } else if (log.level === 'ERROR') {
            counts.error++;
        }
    });
    
    document.getElementById('info-count').textContent = counts.info;
    document.getElementById('success-count').textContent = counts.success;
    document.getElementById('warning-count').textContent = counts.warning;
    document.getElementById('error-count').textContent = counts.error;
}

// Função para mudar página
function changePage(direction) {
    const newPage = currentPage + direction;
    const maxPage = Math.ceil(filteredLogs.length / logsPerPage);
    
    if (newPage >= 1 && newPage <= maxPage) {
        currentPage = newPage;
        displayLogs();
    }
}

// Função para mostrar detalhes do log
function showLogDetail(logRaw) {
    document.getElementById('log-detail-content').textContent = logRaw;
    document.getElementById('log-detail-modal').showModal();
}

// Função para limpar logs
function clearLogs() {
    if (confirm('Tem certeza que deseja limpar os logs? Esta ação não pode ser desfeita.')) {
        // Implementar limpeza de logs
        showToast('Funcionalidade de limpeza será implementada', 'info');
    }
}

// Função para download dos logs
function downloadLogs() {
    const logType = document.getElementById('log-type').value;
    const blob = new Blob([JSON.stringify(allLogs, null, 2)], {type: 'application/json'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${logType}_logs_${new Date().toISOString().split('T')[0]}.json`;
    a.click();
    URL.revokeObjectURL(url);
    
    showToast('Download iniciado', 'success');
}
</script>

<?php require_once 'includes/footer.php'; ?>

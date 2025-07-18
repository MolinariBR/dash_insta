<?php
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Dashboard' ?> - Instagram Bot @fatima.escritora</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.4.19/dist/full.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .animate-pulse-slow { animation: pulse 3s infinite; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .status-online { 
            background: linear-gradient(45deg, #4ade80, #22c55e);
            box-shadow: 0 0 20px rgba(34, 197, 94, 0.3);
        }
        .status-offline { 
            background: linear-gradient(45deg, #f87171, #ef4444);
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <div class="navbar bg-white shadow-lg border-b-2 border-primary/20">
        <div class="navbar-start">
            <div class="dropdown">
                <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                    <i class="fas fa-bars text-xl"></i>
                </div>
                <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                    <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="logs.php"><i class="fas fa-file-alt"></i> Logs</a></li>
                    <li><a href="config.php"><i class="fas fa-cog"></i> Configurações</a></li>
                    <li><a href="metricas.php"><i class="fas fa-chart-line text-blue-600"></i> Métricas</a></li>
                    <li><a href="stats.php"><i class="fas fa-chart-bar"></i> Estatísticas</a></li>
                    <li>
                        <a href="clientes.php" class="<?= $currentPage === 'clientes' ? 'active' : '' ?>">
                            <i class="fas fa-users"></i> Clientes
                        </a>
                    </li>
                    <li>
                        <a href="contas_instagram.php" class="<?= $currentPage === 'contas_instagram' ? 'active' : '' ?>">
                            <i class="fab fa-instagram"></i> Contas Instagram
                        </a>
                    </li>
                </ul>
            </div>
            <a href="index.php" class="btn btn-ghost text-xl">
                <i class="fab fa-instagram text-primary mr-2"></i>
                <span class="hidden sm:inline">@fatima.escritora</span>
                <span class="sm:hidden">Bot</span>
            </a>
        </div>
        
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1">
                <li>
                    <a href="index.php" class="<?= $currentPage === 'index' ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="logs.php" class="<?= $currentPage === 'logs' ? 'active' : '' ?>">
                        <i class="fas fa-file-alt"></i> Logs
                    </a>
                </li>
                <li>
                    <a href="settings.php" class="<?= $currentPage === 'settings' ? 'active' : '' ?>">
                        <i class="fas fa-cog"></i> Configurações
                    </a>
                </li>
                <li>
                    <a href="metricas.php" class="<?= $currentPage === 'metricas' ? 'active text-blue-600 font-bold' : '' ?>">
                        <i class="fas fa-chart-line text-blue-600"></i> Métricas
                    </a>
                </li>
                <li>
                    <a href="stats.php" class="<?= $currentPage === 'stats' ? 'active' : '' ?>">
                        <i class="fas fa-chart-bar"></i> Estatísticas
                    </a>
                </li>
                <li>
                    <a href="clientes.php" class="<?= $currentPage === 'clientes' ? 'active' : '' ?>">
                        <i class="fas fa-users"></i> Clientes
                    </a>
                </li>
                <li>
                    <a href="contas_instagram.php" class="<?= $currentPage === 'contas_instagram' ? 'active' : '' ?>">
                        <i class="fab fa-instagram"></i> Contas Instagram
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="navbar-end">
            <!-- Status do Bot -->
            <div class="mr-4">
                <div class="flex items-center space-x-2">
                    <div id="botStatus" class="w-3 h-3 rounded-full animate-pulse-slow"></div>
                    <span id="botStatusText" class="text-sm font-medium">Verificando...</span>
                </div>
            </div>
            
            <!-- Menu do usuário -->
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                    <div class="avatar">
                        <div class="w-8 rounded-full bg-gradient-to-r from-primary to-secondary flex items-center justify-center">
                            <i class="fas fa-user text-white text-sm"></i>
                        </div>
                    </div>
                </div>
                <ul tabindex="0" class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-52">
                    <li class="menu-title">
                        <span>Administrador</span>
                    </li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> Configurações</a></li>
                    <li><a href="?logout=1"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                </ul>
            </div>
        </div>
    </div>

    <?php
    // Processa logout
    if (isset($_GET['logout'])) {
        logout();
    }
    ?>

    <script>
        // Verifica status do bot
        function checkBotStatus() {
            fetch('api/status.php')
                .then(response => response.json())
                .then(data => {
                    const statusEl = document.getElementById('botStatus');
                    const textEl = document.getElementById('botStatusText');
                    
                    if (data.running) {
                        statusEl.className = 'w-3 h-3 rounded-full status-online animate-pulse-slow';
                        textEl.textContent = 'Online';
                        textEl.className = 'text-sm font-medium text-green-600';
                    } else {
                        statusEl.className = 'w-3 h-3 rounded-full status-offline animate-pulse-slow';
                        textEl.textContent = 'Offline';
                        textEl.className = 'text-sm font-medium text-red-600';
                    }
                })
                .catch(error => {
                    console.error('Erro ao verificar status:', error);
                    document.getElementById('botStatusText').textContent = 'Erro';
                });
        }

        // Verifica status a cada 30 segundos
        checkBotStatus();
        setInterval(checkBotStatus, 30000);
    </script>

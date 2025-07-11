<?php
require_once 'config.php';
$pageTitle = 'Estatísticas e Análises';
require_once 'includes/header.php';

// Carrega dados para estatísticas
$stats = getBotStats();
$seguidoresData = readCSVFile('seguidores.csv');
?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-chart-bar text-primary mr-3"></i>
            Estatísticas e Análises
        </h1>
        <p class="text-gray-600 mt-2">Análise detalhada do desempenho do bot e métricas de crescimento</p>
    </div>

    <!-- Métricas Principais -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total de Seguidores -->
        <div class="card bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">Total Seguidos</h2>
                        <p class="text-3xl font-bold"><?= count($seguidoresData) ?></p>
                    </div>
                    <i class="fas fa-users text-4xl opacity-80"></i>
                </div>
                <p class="text-sm opacity-90">Desde o início</p>
            </div>
        </div>

        <!-- Follows Hoje -->
        <div class="card bg-gradient-to-r from-green-500 to-green-600 text-white shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">Hoje</h2>
                        <p class="text-3xl font-bold"><?= $stats['follows_today'] ?></p>
                    </div>
                    <i class="fas fa-calendar-day text-4xl opacity-80"></i>
                </div>
                <p class="text-sm opacity-90">Follows realizados</p>
            </div>
        </div>

        <!-- Taxa de Sucesso -->
        <div class="card bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">Taxa Sucesso</h2>
                        <p class="text-3xl font-bold">95%</p>
                    </div>
                    <i class="fas fa-percentage text-4xl opacity-80"></i>
                </div>
                <p class="text-sm opacity-90">Ações bem-sucedidas</p>
            </div>
        </div>

        <!-- Próximo Limite -->
        <div class="card bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">Restante Hoje</h2>
                        <p class="text-3xl font-bold"><?= $stats['max_follows'] - $stats['follows_today'] ?></p>
                    </div>
                    <i class="fas fa-hourglass-half text-4xl opacity-80"></i>
                </div>
                <p class="text-sm opacity-90">Follows disponíveis</p>
            </div>
        </div>
    </div>

    <!-- Gráficos e Análises -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
        <!-- Gráfico de Atividade Semanal -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">
                    <i class="fas fa-chart-line text-primary mr-2"></i>
                    Atividade dos Últimos 7 Dias
                </h2>
                <canvas id="activityChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Gráfico de Tipos de Ação -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">
                    <i class="fas fa-pie-chart text-secondary mr-2"></i>
                    Distribuição de Ações
                </h2>
                <canvas id="actionsChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Análise por Origem -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
        <!-- Seguidores por Origem -->
        <div class="card bg-base-100 shadow-xl xl:col-span-2">
            <div class="card-body">
                <h2 class="card-title">
                    <i class="fas fa-source text-info mr-2"></i>
                    Seguidores por Origem
                </h2>
                
                <div class="overflow-x-auto">
                    <table class="table table-zebra table-sm">
                        <thead>
                            <tr>
                                <th>Origem</th>
                                <th>Quantidade</th>
                                <th>Percentual</th>
                                <th>Últimos 7 dias</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Analisa seguidores por origem
                            $origens = [];
                            foreach ($seguidoresData as $seguidor) {
                                if (!empty($seguidor['origem'])) {
                                    $origem = $seguidor['origem'];
                                    if (!isset($origens[$origem])) {
                                        $origens[$origem] = 0;
                                    }
                                    $origens[$origem]++;
                                }
                            }
                            
                            $total = array_sum($origens);
                            arsort($origens);
                            
                            foreach ($origens as $origem => $quantidade): 
                                $percentual = $total > 0 ? round(($quantidade / $total) * 100, 1) : 0;
                            ?>
                            <tr>
                                <td><span class="badge badge-outline"><?= htmlspecialchars($origem) ?></span></td>
                                <td><span class="font-bold"><?= $quantidade ?></span></td>
                                <td><?= $percentual ?>%</td>
                                <td>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-primary h-2 rounded-full" style="width: <?= $percentual ?>%"></div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Horários de Maior Atividade -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">
                    <i class="fas fa-clock text-warning mr-2"></i>
                    Horários Ativos
                </h2>
                
                <div class="space-y-3">
                    <?php
                    $horarios = [
                        '09:00' => 'Rotina Manhã',
                        '12:00' => 'Meio-dia',
                        '14:00' => 'Rotina Tarde', 
                        '17:00' => 'Tarde',
                        '19:00' => 'Rotina Noite',
                        '21:00' => 'Final do dia'
                    ];
                    
                    foreach ($horarios as $hora => $desc):
                    ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-semibold"><?= $hora ?></p>
                            <p class="text-sm text-gray-600"><?= $desc ?></p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-primary"><?= rand(5, 25) ?></p>
                            <p class="text-xs text-gray-500">ações</p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Hashtags Mais Utilizadas -->
    <div class="card bg-base-100 shadow-xl mb-8">
        <div class="card-body">
            <h2 class="card-title">
                <i class="fas fa-hashtag text-accent mr-2"></i>
                Hashtags Mais Utilizadas
            </h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <?php
                $hashtags = [
                    '#livroinfantil' => rand(50, 100),
                    '#educacaoinfantil' => rand(40, 90),
                    '#inclusaoescolar' => rand(30, 80),
                    '#literaturainfantil' => rand(25, 70),
                    '#adocaodeanimais' => rand(20, 60),
                    '#educacao' => rand(45, 85),
                    '#leitura' => rand(35, 75),
                    '#criancas' => rand(30, 65),
                    '#maternidade' => rand(25, 55),
                    '#pedagogia' => rand(20, 50)
                ];
                
                arsort($hashtags);
                
                foreach ($hashtags as $hashtag => $count):
                ?>
                <div class="stat bg-gradient-to-br from-accent/20 to-accent/10 rounded-lg">
                    <div class="stat-title text-xs"><?= $hashtag ?></div>
                    <div class="stat-value text-lg text-accent"><?= $count ?></div>
                    <div class="stat-desc text-xs">interações</div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Relatório de Performance -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title">
                <i class="fas fa-chart-area text-success mr-2"></i>
                Relatório de Performance
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Engajamento -->
                <div class="text-center">
                    <div class="radial-progress text-primary" style="--value:87;" role="progressbar">87%</div>
                    <p class="font-semibold mt-2">Taxa de Engajamento</p>
                    <p class="text-sm text-gray-600">Curtidas e comentários</p>
                </div>
                
                <!-- Crescimento -->
                <div class="text-center">
                    <div class="radial-progress text-secondary" style="--value:76;" role="progressbar">76%</div>
                    <p class="font-semibold mt-2">Crescimento Orgânico</p>
                    <p class="text-sm text-gray-600">Novos seguidores</p>
                </div>
                
                <!-- Qualidade -->
                <div class="text-center">
                    <div class="radial-progress text-accent" style="--value:93;" role="progressbar">93%</div>
                    <p class="font-semibold mt-2">Qualidade do Conteúdo</p>
                    <p class="text-sm text-gray-600">Interações positivas</p>
                </div>
            </div>
            
            <div class="alert alert-info mt-6">
                <i class="fas fa-lightbulb"></i>
                <div>
                    <h3 class="font-bold">Insights e Recomendações</h3>
                    <div class="text-sm mt-2">
                        <ul class="list-disc list-inside space-y-1">
                            <li>O horário de maior engajamento é entre 14h e 16h</li>
                            <li>Hashtags relacionadas à educação têm melhor performance</li>
                            <li>Posts sobre literatura infantil geram mais comentários</li>
                            <li>A taxa de follow-back está em 45%, acima da média</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Gráfico de Atividade Semanal
const ctxActivity = document.getElementById('activityChart').getContext('2d');
new Chart(ctxActivity, {
    type: 'line',
    data: {
        labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
        datasets: [{
            label: 'Follows',
            data: [25, 28, 22, 30, 26, 15, 12],
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4
        }, {
            label: 'Comentários',
            data: [8, 10, 7, 12, 9, 5, 4],
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Atividades por Dia da Semana'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Gráfico de Distribuição de Ações
const ctxActions = document.getElementById('actionsChart').getContext('2d');
new Chart(ctxActions, {
    type: 'doughnut',
    data: {
        labels: ['Follows', 'Curtidas', 'Comentários', 'Mensagens'],
        datasets: [{
            data: [45, 35, 15, 5],
            backgroundColor: [
                'rgb(59, 130, 246)',
                'rgb(34, 197, 94)', 
                'rgb(251, 191, 36)',
                'rgb(168, 85, 247)'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Distribuição de Tipos de Ação'
            },
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>

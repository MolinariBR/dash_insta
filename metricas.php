<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/metricas/alertas.php';
require_once __DIR__ . '/metricas/integracoes.php';
$pageTitle = 'Métricas e Alertas';
require_once __DIR__ . '/includes/header.php';

$dashboardConfig = function_exists('getDashboardConfig') ? getDashboardConfig() : ['demo_mode' => false, 'show_badges' => false, 'show_alert' => false];
$alertas = definir_alertas($campanhas, $LIMITES);

// Facebook Ads
$facebookMetrics = getFacebookAdsMetrics($FACEBOOK_ACCESS_TOKEN, $FACEBOOK_AD_ACCOUNT_ID);
// Google Ads
$googleAdsMetrics = getGoogleAdsMetrics($GOOGLE_DEVELOPER_TOKEN, $GOOGLE_CLIENT_ID, $GOOGLE_CLIENT_SECRET, $GOOGLE_REFRESH_TOKEN, $GOOGLE_CUSTOMER_ID);
// Google Analytics
$googleAnalyticsMetrics = getGoogleAnalyticsMetrics($GOOGLE_CREDENTIALS_PATH, $GOOGLE_ANALYTICS_VIEW_ID);
?>
<div class="container mx-auto px-4 py-6">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-chart-line text-blue-600 mr-3"></i>
                    Métricas e Alertas
                </h1>
                <p class="text-gray-600 mt-2">Monitoramento de desempenho das campanhas e alertas automáticos</p>
            </div>
        </div>
    </div>

    <?php if ($dashboardConfig['demo_mode'] && $dashboardConfig['show_alert']): ?>
    <div class="col-span-full mb-6">
        <div class="alert alert-info">
            <div class="flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                <span><strong>Modo Demonstração:</strong> Os dados abaixo são simulados para demonstração.</span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <?php if (!empty($alertas)): ?>
            <?php foreach ($alertas as $a): ?>
                <div class="card bg-gradient-to-r from-blue-500 to-purple-600 text-white shadow-xl">
                    <div class="card-body flex flex-row items-center gap-3">
                        <span class="badge badge-warning text-lg"><i class="fa-solid fa-bell"></i></span>
                        <span class="font-semibold"> <?= htmlspecialchars($a) ?> </span>
                        <?php if ($dashboardConfig['demo_mode'] && $dashboardConfig['show_badges']): ?>
                            <span class="badge badge-warning badge-xs ml-2">demo</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full">
                <div class="alert alert-success shadow-lg">
                    <div>
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Nenhum alerta no momento.</span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card bg-gradient-to-r from-blue-400 to-blue-600 text-white shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-white text-lg"><i class="fas fa-bullseye mr-2"></i> KPI: CPC Médio</h2>
                <p class="text-2xl font-bold">R$ <?= number_format($campanhas[0]['cpc'], 2, ',', '.') ?></p>
            </div>
        </div>
        <div class="card bg-gradient-to-r from-green-400 to-green-600 text-white shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-white text-lg"><i class="fas fa-shopping-cart mr-2"></i> Vendas</h2>
                <p class="text-2xl font-bold"><?= $campanhas[0]['vendas'] ?></p>
            </div>
        </div>
        <div class="card bg-gradient-to-r from-purple-400 to-purple-600 text-white shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-white text-lg"><i class="fas fa-chart-line mr-2"></i> Resultado</h2>
                <p class="text-2xl font-bold"><?= $campanhas[0]['resultado'] ?></p>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-xl mt-8">
        <div class="card-body">
            <h2 class="card-title text-purple-600"><i class="fa-solid fa-chart-pie"></i> Gráficos e Métricas</h2>
            <p class="text-gray-600">Adicione aqui seus gráficos, tabelas e KPIs.</p>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

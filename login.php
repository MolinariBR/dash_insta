<?php
require_once 'config.php';
require_once __DIR__ . '/includes/auth_core.php';

if (!function_exists('processLogin')) {
function processLogin() {
    global $error;
    $password = $_POST['password'] ?? '';
    $result = processLoginCore($password);
    $error = $result['error'];
    if ($result['success'] && php_sapi_name() !== 'cli') {
        header('Location: index.php');
        exit;
    }
}
}
// Se já está logado, redireciona para dashboard
if (isLoggedIn() && php_sapi_name() !== 'cli') {
    header('Location: index.php');
    exit;
}

$error = '';

// Processa login
if ($_POST) {
    processLogin();
}
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Instagram Bot @fatima.escritora</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.4.19/dist/full.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-pink-50 to-purple-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="card w-full max-w-md bg-base-100 shadow-2xl">
            <div class="card-body">
                <!-- Logo/Header -->
                <div class="text-center mb-6">
                    <div class="avatar mb-4">
                        <div class="w-20 rounded-full bg-gradient-to-r from-pink-500 to-purple-500 flex items-center justify-center">
                            <i class="fab fa-instagram text-white text-3xl"></i>
                        </div>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800">Instagram Bot</h1>
                    <p class="text-sm text-gray-600">@fatima.escritora</p>
                    <p class="text-xs text-gray-500 mt-2">Dashboard de Gerenciamento</p>
                </div>

                <!-- Formulário de Login -->
                <form method="POST" class="space-y-4">
                    <?php if ($error): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span><?= htmlspecialchars($error) ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Senha de Acesso</span>
                        </label>
                        <div class="input-group">
                            <span class="bg-base-200">
                                <i class="fas fa-lock text-gray-500"></i>
                            </span>
                            <input 
                                type="password" 
                                name="password" 
                                placeholder="Digite sua senha" 
                                class="input input-bordered w-full" 
                                required 
                                autofocus
                            >
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-full">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Entrar no Dashboard
                    </button>
                </form>

                <!-- Footer -->
                <div class="text-center mt-6 pt-4 border-t">
                    <p class="text-xs text-gray-500">
                        <i class="fas fa-shield-alt text-green-500 mr-1"></i>
                        Acesso seguro e criptografado
                    </p>
                    <p class="text-xs text-gray-400 mt-2">
                        Desenvolvido por <span class="font-semibold">Tria Inova Simples</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Informações do Sistema -->
    <div class="fixed bottom-4 left-4 hidden lg:block">
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body p-4">
                <h3 class="font-bold text-sm mb-2">
                    <i class="fas fa-robot text-primary mr-2"></i>
                    Sistema de Automação
                </h3>
                <div class="space-y-1 text-xs text-gray-600">
                    <p><i class="fas fa-users text-blue-500 mr-2"></i>Seguidores automáticos</p>
                    <p><i class="fas fa-heart text-red-500 mr-2"></i>Curtidas inteligentes</p>
                    <p><i class="fas fa-comments text-green-500 mr-2"></i>Comentários contextuais</p>
                    <p><i class="fas fa-envelope text-purple-500 mr-2"></i>Mensagens personalizadas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas em tempo real -->
    <div class="fixed bottom-4 right-4 hidden lg:block">
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body p-4">
                <h3 class="font-bold text-sm mb-2">
                    <i class="fas fa-chart-line text-success mr-2"></i>
                    Foco da Campanha
                </h3>
                <div class="space-y-1 text-xs text-gray-600">
                    <p><i class="fas fa-book text-orange-500 mr-2"></i>Literatura Infantil</p>
                    <p><i class="fas fa-heart text-pink-500 mr-2"></i>Educação Inclusiva</p>
                    <p><i class="fas fa-paw text-brown-500 mr-2"></i>Adoção de Animais</p>
                    <p><i class="fas fa-graduation-cap text-indigo-500 mr-2"></i>Conteúdo Educativo</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-focus no campo de senha
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.querySelector('input[name="password"]');
            if (passwordInput) {
                passwordInput.focus();
            }
        });

        // Animação suave para o formulário
        document.querySelector('.card').style.opacity = '0';
        document.querySelector('.card').style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            document.querySelector('.card').style.transition = 'all 0.5s ease';
            document.querySelector('.card').style.opacity = '1';
            document.querySelector('.card').style.transform = 'translateY(0)';
        }, 100);
    </script>
</body>
</html>

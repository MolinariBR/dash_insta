<?php
require_once 'config.php';

// Conexão com o banco SQLite
function getDB() {
    $db = new PDO('sqlite:data/database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}

// Cadastro de novo cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['novo_cliente'])) {
    $nome = htmlspecialchars(trim($_POST['nome'] ?? ''));
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $empresa = htmlspecialchars(trim($_POST['empresa'] ?? ''));
    $cpf = htmlspecialchars(trim($_POST['cpf'] ?? ''));
    $cnpj = htmlspecialchars(trim($_POST['cnpj'] ?? ''));
    $nome_projeto = htmlspecialchars(trim($_POST['nome_projeto'] ?? ''));
    $contato = htmlspecialchars(trim($_POST['contato'] ?? ''));

    if ($nome && $email) {
        $db = getDB();
        $stmt = $db->prepare('INSERT INTO clientes (nome, email, empresa, cpf, cnpj, nome_projeto, contato) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$nome, $email, $empresa, $cpf, $cnpj, $nome_projeto, $contato]);
        // Log de auditoria
        file_put_contents('logs/auditoria.log', date('c') . " | CADASTRO_CLIENTE | $nome | $email\n", FILE_APPEND);
        $msg = 'Cliente cadastrado com sucesso!';
    } else {
        $msg = 'Nome e e-mail válidos são obrigatórios.';
    }
}

// Listar clientes
$db = getDB();
$clientes = $db->query('SELECT * FROM clientes ORDER BY data_cadastro DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'includes/header.php'; ?>
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Clientes</h1>
    <?php if (!empty($msg)): ?>
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <form method="post" class="bg-white shadow rounded p-4 mb-6">
        <h2 class="text-lg font-semibold mb-2">Novo Cliente</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input name="nome" required placeholder="Nome" class="border p-2 rounded" />
            <input name="email" required type="email" placeholder="E-mail" class="border p-2 rounded" />
            <input name="empresa" placeholder="Empresa" class="border p-2 rounded" />
            <input name="cpf" placeholder="CPF" class="border p-2 rounded" />
            <input name="cnpj" placeholder="CNPJ" class="border p-2 rounded" />
            <input name="nome_projeto" placeholder="Nome do Projeto" class="border p-2 rounded" />
            <input name="contato" placeholder="Contato" class="border p-2 rounded" />
        </div>
        <button type="submit" name="novo_cliente" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">Cadastrar Cliente</button>
    </form>
    <h2 class="text-lg font-semibold mb-2">Lista de Clientes</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border rounded">
            <thead>
                <tr>
                    <th class="px-2 py-1 border">ID</th>
                    <th class="px-2 py-1 border">Nome</th>
                    <th class="px-2 py-1 border">E-mail</th>
                    <th class="px-2 py-1 border">Empresa</th>
                    <th class="px-2 py-1 border">CPF</th>
                    <th class="px-2 py-1 border">CNPJ</th>
                    <th class="px-2 py-1 border">Projeto</th>
                    <th class="px-2 py-1 border">Contato</th>
                    <th class="px-2 py-1 border">Cadastro</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $c): ?>
                <tr>
                    <td class="px-2 py-1 border text-center"><?= $c['id'] ?></td>
                    <td class="px-2 py-1 border"><?= htmlspecialchars($c['nome']) ?></td>
                    <td class="px-2 py-1 border"><?= htmlspecialchars($c['email']) ?></td>
                    <td class="px-2 py-1 border"><?= htmlspecialchars($c['empresa']) ?></td>
                    <td class="px-2 py-1 border"><?= htmlspecialchars($c['cpf']) ?></td>
                    <td class="px-2 py-1 border"><?= htmlspecialchars($c['cnpj']) ?></td>
                    <td class="px-2 py-1 border"><?= htmlspecialchars($c['nome_projeto']) ?></td>
                    <td class="px-2 py-1 border"><?= htmlspecialchars($c['contato']) ?></td>
                    <td class="px-2 py-1 border text-xs text-gray-500"><?= $c['data_cadastro'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'includes/footer.php'; ?> 
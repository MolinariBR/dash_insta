<?php
require_once 'config.php';

function getDB() {
    $db = new PDO('sqlite:data/database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}

// Buscar clientes para o select
$db = getDB();
$clientes = $db->query('SELECT id, nome, email FROM clientes ORDER BY nome')->fetchAll(PDO::FETCH_ASSOC);

// Excluir conta
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $db->prepare('DELETE FROM contas_instagram WHERE id = ?');
    $stmt->execute([$id]);
    $msg = 'Conta do Instagram excluída com sucesso!';
}

// Editar conta (carregar dados)
$edit_data = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $db->prepare('SELECT * FROM contas_instagram WHERE id = ?');
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Atualizar conta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar_conta'])) {
    $id = $_POST['conta_id'] ?? '';
    $username = $_POST['username'] ?? '';
    $status = $_POST['status'] ?? 'ativa';
    $senha = $_POST['senha'] ?? '';
    if ($id && $username) {
        if ($senha) {
            $senha_hash = password_hash($senha, PASSWORD_BCRYPT);
            $stmt = $db->prepare('UPDATE contas_instagram SET username = ?, senha = ?, status = ? WHERE id = ?');
            $stmt->execute([$username, $senha_hash, $status, $id]);
        } else {
            $stmt = $db->prepare('UPDATE contas_instagram SET username = ?, status = ? WHERE id = ?');
            $stmt->execute([$username, $status, $id]);
        }
        $msg = 'Conta do Instagram atualizada com sucesso!';
        $edit_data = null;
    }
}

// Cadastro de nova conta do Instagram
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nova_conta'])) {
    $cliente_id = $_POST['cliente_id'] ?? '';
    $username = $_POST['username'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $status = $_POST['status'] ?? 'ativa';
    if ($cliente_id && $username && $senha) {
        $senha_hash = password_hash($senha, PASSWORD_BCRYPT);
        $stmt = $db->prepare('INSERT INTO contas_instagram (cliente_id, username, senha, status) VALUES (?, ?, ?, ?)');
        $stmt->execute([$cliente_id, $username, $senha_hash, $status]);
        $msg = 'Conta do Instagram cadastrada com sucesso!';
    } else {
        $msg = 'Cliente, usuário e senha são obrigatórios.';
    }
}

// Filtro de cliente selecionado
$cliente_filtro = $_GET['cliente_id'] ?? ($_POST['cliente_id'] ?? '');
$contas = [];
if ($cliente_filtro) {
    $stmt = $db->prepare('SELECT ci.*, c.nome as cliente_nome FROM contas_instagram ci JOIN clientes c ON ci.cliente_id = c.id WHERE ci.cliente_id = ? ORDER BY ci.data_cadastro DESC');
    $stmt->execute([$cliente_filtro]);
    $contas = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Visualização detalhada
$detalhe = null;
if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $id = (int)$_GET['view'];
    $stmt = $db->prepare('SELECT ci.*, c.nome as cliente_nome, c.email as cliente_email FROM contas_instagram ci JOIN clientes c ON ci.cliente_id = c.id WHERE ci.id = ?');
    $stmt->execute([$id]);
    $detalhe = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<?php include 'includes/header.php'; ?>
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Contas do Instagram</h1>
    <?php if (!empty($msg)): ?>
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <form method="get" class="mb-6">
        <label class="block mb-2 font-semibold">Selecione o Cliente:</label>
        <select name="cliente_id" onchange="this.form.submit()" class="border p-2 rounded w-full md:w-1/2">
            <option value="">-- Escolha um cliente --</option>
            <?php foreach ($clientes as $cl): ?>
                <option value="<?= $cl['id'] ?>" <?= $cliente_filtro == $cl['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cl['nome']) ?> (<?= htmlspecialchars($cl['email']) ?>)</option>
            <?php endforeach; ?>
        </select>
    </form>
    <?php if ($cliente_filtro): ?>
        <?php if ($edit_data): ?>
            <form method="post" class="bg-yellow-50 shadow rounded p-4 mb-6">
                <input type="hidden" name="conta_id" value="<?= $edit_data['id'] ?>" />
                <h2 class="text-lg font-semibold mb-2">Editar Conta do Instagram</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input name="username" required placeholder="Usuário do Instagram" class="border p-2 rounded" value="<?= htmlspecialchars($edit_data['username']) ?>" />
                    <input name="senha" type="password" placeholder="Nova senha (deixe em branco para não alterar)" class="border p-2 rounded" />
                    <select name="status" class="border p-2 rounded">
                        <option value="ativa" <?= $edit_data['status'] === 'ativa' ? 'selected' : '' ?>>Ativa</option>
                        <option value="inativa" <?= $edit_data['status'] === 'inativa' ? 'selected' : '' ?>>Inativa</option>
                    </select>
                </div>
                <button type="submit" name="atualizar_conta" class="mt-4 bg-yellow-600 text-white px-4 py-2 rounded">Salvar Alterações</button>
                <a href="contas_instagram.php?cliente_id=<?= $cliente_filtro ?>" class="ml-4 text-blue-600">Cancelar</a>
            </form>
        <?php endif; ?>
        <?php if ($detalhe): ?>
            <div class="bg-white shadow rounded p-4 mb-6">
                <h2 class="text-lg font-semibold mb-2">Detalhes da Conta do Instagram</h2>
                <p><b>ID:</b> <?= $detalhe['id'] ?></p>
                <p><b>Cliente:</b> <?= htmlspecialchars($detalhe['cliente_nome']) ?> (<?= htmlspecialchars($detalhe['cliente_email']) ?>)</p>
                <p><b>Usuário:</b> @<?= htmlspecialchars($detalhe['username']) ?></p>
                <p><b>Status:</b> <?= htmlspecialchars($detalhe['status']) ?></p>
                <p><b>Cadastro:</b> <?= $detalhe['data_cadastro'] ?></p>
                <p><b>Última Atividade:</b> <?= $detalhe['ultima_atividade'] ?></p>
                <a href="contas_instagram.php?cliente_id=<?= $cliente_filtro ?>" class="text-blue-600">Voltar</a>
            </div>
        <?php endif; ?>
        <?php if (!$edit_data && !$detalhe): ?>
            <form method="post" class="bg-white shadow rounded p-4 mb-6">
                <input type="hidden" name="cliente_id" value="<?= $cliente_filtro ?>" />
                <h2 class="text-lg font-semibold mb-2">Nova Conta do Instagram para <?= htmlspecialchars($clientes[array_search($cliente_filtro, array_column($clientes, 'id'))]['nome'] ?? '') ?></h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input name="username" required placeholder="Usuário do Instagram" class="border p-2 rounded" />
                    <input name="senha" required type="password" placeholder="Senha (será criptografada)" class="border p-2 rounded" />
                    <select name="status" class="border p-2 rounded">
                        <option value="ativa">Ativa</option>
                        <option value="inativa">Inativa</option>
                    </select>
                </div>
                <button type="submit" name="nova_conta" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">Cadastrar Conta</button>
            </form>
        <?php endif; ?>
        <h2 class="text-lg font-semibold mb-2">Contas do Instagram do Cliente</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border rounded">
                <thead>
                    <tr>
                        <th class="px-2 py-1 border">ID</th>
                        <th class="px-2 py-1 border">Usuário</th>
                        <th class="px-2 py-1 border">Status</th>
                        <th class="px-2 py-1 border">Cadastro</th>
                        <th class="px-2 py-1 border">Última Atividade</th>
                        <th class="px-2 py-1 border">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contas as $ci): ?>
                    <tr>
                        <td class="px-2 py-1 border text-center"><?= $ci['id'] ?></td>
                        <td class="px-2 py-1 border">@<?= htmlspecialchars($ci['username']) ?></td>
                        <td class="px-2 py-1 border"><?= htmlspecialchars($ci['status']) ?></td>
                        <td class="px-2 py-1 border text-xs text-gray-500"><?= $ci['data_cadastro'] ?></td>
                        <td class="px-2 py-1 border text-xs text-gray-500"><?= $ci['ultima_atividade'] ?></td>
                        <td class="px-2 py-1 border text-center">
                            <a href="contas_instagram.php?cliente_id=<?= $cliente_filtro ?>&view=<?= $ci['id'] ?>" class="text-blue-600 mr-2" title="Ver Detalhes"><i class="fas fa-eye"></i></a>
                            <a href="contas_instagram.php?cliente_id=<?= $cliente_filtro ?>&edit=<?= $ci['id'] ?>" class="text-yellow-600 mr-2" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="contas_instagram.php?cliente_id=<?= $cliente_filtro ?>&delete=<?= $ci['id'] ?>" class="text-red-600" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir esta conta?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?> 
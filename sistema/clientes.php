<?php
require_once __DIR__ . '/bootstrap.php'; requireLogin();
$pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $nome = trim((string)($_POST['nome'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $telefone = trim((string)($_POST['telefone'] ?? ''));
    $cpf = preg_replace('/\D+/', '', (string)($_POST['cpf'] ?? ''));
    if ($nome === '' || strlen($cpf) !== 11) {
        flash('Informe o nome e um CPF com 11 dígitos.'); header('Location: clientes.php'); exit;
    }
    try {
        $stmt = $pdo->prepare('INSERT INTO clientes (nome,email,telefone,cpf_cifrado,criado_em) VALUES (?,?,?,?,NOW())');
        $stmt->execute([$nome, $email ?: null, $telefone ?: null, encryptSensitive($cpf)]);
        flash('Cliente cadastrado com sucesso.');
    } catch (PDOException $ex) {
        flash($ex->getCode() === '23000' ? 'CPF duplicado ou e-mail já cadastrado.' : 'Não foi possível cadastrar o cliente.');
    } catch (Throwable $ex) { flash('Erro ao proteger os dados. Confira APP_ENCRYPTION_KEY.'); }
    header('Location: clientes.php'); exit;
}
$search = trim((string)($_GET['q'] ?? ''));
if ($search !== '') {
    $stmt = $pdo->prepare('SELECT id,nome,email,telefone,criado_em FROM clientes WHERE nome LIKE ? OR email LIKE ? OR telefone LIKE ? ORDER BY nome');
    $like = "%$search%"; $stmt->execute([$like,$like,$like]); $clientes = $stmt->fetchAll();
} else $clientes = $pdo->query('SELECT id,nome,email,telefone,criado_em FROM clientes ORDER BY nome')->fetchAll();
$pageTitle = 'Clientes'; require __DIR__ . '/header.php';
?>
<div class="content-grid"><section class="panel"><div class="panel-head"><div><h2>Novo cliente</h2><p>Cadastre os dados de contato</p></div></div>
<form method="post" class="form-grid"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><label class="wide">Nome completo<input name="nome" maxlength="150" required></label><label>E-mail<input name="email" type="email" maxlength="150"></label><label>Telefone<input name="telefone" maxlength="30"></label><label class="wide">CPF <small>(armazenado criptografado)</small><input name="cpf" inputmode="numeric" minlength="11" maxlength="14" placeholder="000.000.000-00" required></label><div class="wide"><button class="btn" type="submit">+ Cadastrar cliente</button></div></form></section>
<section class="panel list-panel"><div class="panel-head"><div><h2>Clientes cadastrados</h2><p><?= count($clientes) ?> resultado(s)</p></div></div><form method="get" class="search-form"><input name="q" value="<?= e($search) ?>" placeholder="Buscar por nome, e-mail ou telefone"><button class="btn" type="submit">Buscar</button><?php if($search!==''): ?><a class="btn btn-muted" href="clientes.php">Limpar</a><?php endif; ?></form>
<div class="table-wrap"><table><thead><tr><th>Nome</th><th>Contato</th><th>Telefone</th></tr></thead><tbody><?php foreach($clientes as $c): ?><tr><td><strong><?= e($c['nome']) ?></strong><small class="block">Cadastro #<?= (int)$c['id'] ?></small></td><td><?= e($c['email'] ?: '—') ?></td><td><?= e($c['telefone'] ?: '—') ?></td></tr><?php endforeach; ?><?php if(!$clientes): ?><tr><td colspan="3">Nenhum cliente encontrado.</td></tr><?php endif; ?></tbody></table></div></section></div>
<?php require __DIR__ . '/footer.php'; ?>

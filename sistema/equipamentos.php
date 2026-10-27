<?php
require_once __DIR__ . '/bootstrap.php'; requireLogin(); $pdo=db();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verifyCsrf();
    $clienteId=(int)($_POST['cliente_id']??0); $tipo=trim((string)($_POST['tipo']??'')); $marca=trim((string)($_POST['marca']??'')); $modelo=trim((string)($_POST['modelo']??'')); $serie=trim((string)($_POST['numero_serie']??''));
    try {
        $stmt=$pdo->prepare('INSERT INTO equipamentos (cliente_id,tipo,marca,modelo,numero_serie,criado_em) VALUES (?,?,?,?,?,NOW())');
        $stmt->execute([$clienteId,$tipo,$marca?:null,$modelo?:null,$serie?:null]); flash('Equipamento cadastrado com sucesso.');
    } catch(Throwable $ex) { flash('Não foi possível cadastrar. Verifique o cliente e se o número de série já existe.'); }
    header('Location: equipamentos.php'); exit;
}
$clientes=$pdo->query('SELECT id,nome FROM clientes ORDER BY nome')->fetchAll();
$rows=$pdo->query('SELECT e.*,c.nome cliente FROM equipamentos e JOIN clientes c ON c.id=e.cliente_id ORDER BY e.id DESC')->fetchAll();
$pageTitle='Equipamentos'; require __DIR__.'/header.php';
?>
<div class="content-grid"><section class="panel"><div class="panel-head"><div><h2>Novo equipamento</h2><p>Vincule o equipamento ao cliente</p></div></div>
<?php if(!$clientes): ?><div class="notice">Cadastre um cliente antes de adicionar equipamentos.</div><?php endif; ?>
<form method="post" class="form-grid"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><label class="wide">Cliente<select name="cliente_id" required><option value="">Selecione um cliente</option><?php foreach($clientes as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['nome']) ?></option><?php endforeach; ?></select></label><label>Tipo<input name="tipo" required maxlength="80" placeholder="Notebook, impressora..."></label><label>Marca<input name="marca" maxlength="80"></label><label>Modelo<input name="modelo" maxlength="100"></label><label>Número de série<input name="numero_serie" maxlength="100"></label><div class="wide"><button class="btn" type="submit" <?= !$clientes?'disabled':'' ?>>+ Cadastrar equipamento</button></div></form></section>
<section class="panel list-panel"><div class="panel-head"><div><h2>Equipamentos</h2><p><?= count($rows) ?> registro(s)</p></div></div><div class="table-wrap"><table><thead><tr><th>Equipamento</th><th>Cliente</th><th>Série</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><strong><?= e($r['tipo']) ?></strong><small class="block"><?= e(trim(($r['marca']??'').' '.($r['modelo']??''))) ?></small></td><td><?= e($r['cliente']) ?></td><td><?= e($r['numero_serie']?:'—') ?></td></tr><?php endforeach; ?><?php if(!$rows): ?><tr><td colspan="3">Nenhum equipamento cadastrado.</td></tr><?php endif; ?></tbody></table></div></section></div>
<?php require __DIR__.'/footer.php'; ?>

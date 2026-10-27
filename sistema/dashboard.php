<?php
require_once __DIR__ . '/bootstrap.php'; requireLogin();
$pdo = db();
$counts = [];
foreach (['clientes'=>'Clientes cadastrados','equipamentos'=>'Equipamentos','ordens_servico'=>'Ordens de serviço'] as $table=>$label) {
    $counts[] = ['label'=>$label,'count'=>(int)$pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn()];
}
$recent = $pdo->query("SELECT os.id, os.status, os.data_abertura, c.nome AS cliente, e.tipo, e.marca, e.modelo FROM ordens_servico os JOIN clientes c ON c.id=os.cliente_id JOIN equipamentos e ON e.id=os.equipamento_id ORDER BY os.data_abertura DESC, os.id DESC LIMIT 5")->fetchAll();
$pageTitle = 'Visão geral'; require __DIR__ . '/header.php';
?>
<section class="welcome"><div><span class="eyebrow light">PAINEL PRINCIPAL</span><h2>Olá, <?= e($_SESSION['user_name']) ?> 👋</h2><p>Acompanhe os cadastros e os serviços da assistência.</p></div><a class="btn btn-light" href="ordens.php">Ver ordens de serviço →</a></section>
<div class="stats"><?php foreach($counts as $item): ?><article class="stat-card"><span><?= e($item['label']) ?></span><strong><?= $item['count'] ?></strong><small>Registros no sistema</small></article><?php endforeach; ?></div>
<section class="panel"><div class="panel-head"><div><h2>Ordens recentes</h2><p>Últimos atendimentos registrados</p></div><a class="text-link" href="ordens.php">Ver todas →</a></div>
<div class="table-wrap"><table><thead><tr><th>OS</th><th>Cliente</th><th>Equipamento</th><th>Abertura</th><th>Status</th></tr></thead><tbody>
<?php foreach($recent as $r): ?><tr><td>#<?= (int)$r['id'] ?></td><td><?= e($r['cliente']) ?></td><td><?= e(trim($r['tipo'].' '.$r['marca'].' '.$r['modelo'])) ?></td><td><?= e(date('d/m/Y', strtotime($r['data_abertura']))) ?></td><td><span class="status"><?= e($r['status']) ?></span></td></tr><?php endforeach; ?>
<?php if (!$recent): ?><tr><td colspan="5">Nenhuma ordem cadastrada.</td></tr><?php endif; ?></tbody></table></div></section>
<?php require __DIR__ . '/footer.php'; ?>

<?php
require_once __DIR__ . '/bootstrap.php'; requireLogin(); $pdo=db();
$validStatuses=['Aberta','Em diagnóstico','Aguardando aprovação','Em reparo','Concluída','Cancelada'];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verifyCsrf();
    $action=(string)($_POST['action']??'create');
    if ($action==='status') {
        $id=(int)($_POST['id']??0); $status=(string)($_POST['status']??'');
        if(in_array($status,$validStatuses,true)) {
            $stmt=$pdo->prepare('UPDATE ordens_servico SET status=? WHERE id=?'); $stmt->execute([$status,$id]); flash('Status da ordem atualizado.');
        } else flash('Status inválido.');
        header('Location: ordens.php'); exit;
    }
    $cliente=(int)($_POST['cliente_id']??0); $equip=(int)($_POST['equipamento_id']??0); $descricao=trim((string)($_POST['descricao']??''));
    try {
        $check=$pdo->prepare('SELECT COUNT(*) FROM equipamentos WHERE id=? AND cliente_id=?'); $check->execute([$equip,$cliente]);
        if((int)$check->fetchColumn()!==1) throw new RuntimeException('O equipamento precisa pertencer ao cliente selecionado.');
        $stmt=$pdo->prepare("INSERT INTO ordens_servico (cliente_id,equipamento_id,descricao,status,data_abertura) VALUES (?,?,?,'Aberta',NOW())");
        $stmt->execute([$cliente,$equip,$descricao]); flash('Ordem de serviço aberta.');
    } catch(Throwable $ex) { flash('Não foi possível abrir a ordem. Confira cliente, equipamento e descrição.'); }
    header('Location: ordens.php'); exit;
}
$clientes=$pdo->query('SELECT id,nome FROM clientes ORDER BY nome')->fetchAll();
$equipamentos=$pdo->query('SELECT id,cliente_id,tipo,marca,modelo FROM equipamentos ORDER BY tipo,marca,modelo')->fetchAll();
$rows=$pdo->query('SELECT os.*,c.nome cliente,e.tipo,e.marca,e.modelo FROM ordens_servico os JOIN clientes c ON c.id=os.cliente_id JOIN equipamentos e ON e.id=os.equipamento_id ORDER BY os.data_abertura DESC, os.id DESC')->fetchAll();
$pageTitle='Ordens de serviço'; require __DIR__.'/header.php';
?>
<section class="panel"><div class="panel-head"><div><h2>Abrir ordem de serviço</h2><p>Registre o equipamento recebido para reparo</p></div></div>
<form method="post" class="form-grid os-form"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="create"><label>Cliente<select name="cliente_id" id="clienteSelect" required><option value="">Selecione</option><?php foreach($clientes as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['nome']) ?></option><?php endforeach; ?></select></label><label>Equipamento<select name="equipamento_id" id="equipSelect" required><option value="">Selecione o cliente primeiro</option><?php foreach($equipamentos as $eq): ?><option value="<?= (int)$eq['id'] ?>" data-cliente="<?= (int)$eq['cliente_id'] ?>"><?= e(trim($eq['tipo'].' '.$eq['marca'].' '.$eq['modelo'])) ?></option><?php endforeach; ?></select></label><label class="wide">Descrição do problema<textarea name="descricao" rows="2" required maxlength="2000" placeholder="Descreva o defeito relatado..."></textarea></label><div class="wide"><button class="btn" type="submit" <?= (!$clientes||!$equipamentos)?'disabled':'' ?>>+ Abrir ordem</button></div></form></section>
<section class="panel"><div class="panel-head"><div><h2>Ordens registradas</h2><p><?= count($rows) ?> atendimento(s), ordenados pela data de abertura</p></div></div><div class="table-wrap"><table><thead><tr><th>OS / Abertura</th><th>Cliente</th><th>Equipamento</th><th>Problema</th><th>Status</th><th>Atualizar</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><strong>#<?= (int)$r['id'] ?></strong><small class="block"><?= e(date('d/m/Y H:i',strtotime($r['data_abertura']))) ?></small></td><td><?= e($r['cliente']) ?></td><td><?= e(trim($r['tipo'].' '.$r['marca'].' '.$r['modelo'])) ?></td><td class="description-cell"><?= e($r['descricao']) ?></td><td><span class="status"><?= e($r['status']) ?></span></td><td><form method="post" class="inline-form"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="status"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><select name="status" aria-label="Status da OS #<?= (int)$r['id'] ?>"><?php foreach($validStatuses as $s): ?><option <?= $r['status']===$s?'selected':'' ?> value="<?= e($s) ?>"><?= e($s) ?></option><?php endforeach; ?></select><button class="btn btn-small" type="submit">Salvar</button></form></td></tr><?php endforeach; ?>
<?php if(!$rows): ?><tr><td colspan="6">Nenhuma ordem cadastrada.</td></tr><?php endif; ?></tbody></table></div></section>
<script>
const clienteSelect=document.getElementById('clienteSelect'), equipSelect=document.getElementById('equipSelect');
function filterEquipments(){const client=clienteSelect.value;let selectedVisible=false;for(const opt of equipSelect.options){if(!opt.value)continue;const show=!client||opt.dataset.cliente===client;opt.hidden=!show;if(opt.selected&&opt.hidden){opt.selected=false;}if(opt.selected)selectedVisible=true;}if(!selectedVisible)equipSelect.value='';}
clienteSelect.addEventListener('change',filterEquipments);filterEquipments();
</script>
<?php require __DIR__.'/footer.php'; ?>

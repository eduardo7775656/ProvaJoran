<?php
require_once __DIR__ . '/bootstrap.php';
requireLogin();
$pageTitle = $pageTitle ?? 'Painel';
$flashMessage = takeFlash();
?>
<!doctype html><html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= e($pageTitle) ?> | Assistência Técnica</title><link rel="stylesheet" href="assets/style.css"></head><body>
<aside class="sidebar"><div class="brand"><span class="brand-mark small">AT</span><span>Assistência<br><strong>Técnica</strong></span></div><nav><a href="dashboard.php">Visão geral</a><a href="clientes.php">Clientes</a><a href="equipamentos.php">Equipamentos</a><a href="ordens.php">Ordens de serviço</a></nav><div class="side-bottom"><span class="user-dot"><?= e(strtoupper(substr($_SESSION['user_name'] ?? 'U',0,1))) ?></span><div><small>Conectado como</small><strong><?= e($_SESSION['user_name'] ?? 'Usuário') ?></strong></div><a class="logout" href="logout.php" title="Sair">↗</a></div></aside>
<main class="main"><header class="topbar"><div><span class="eyebrow">SISTEMA DE GESTÃO</span><h1><?= e($pageTitle) ?></h1></div><div class="top-user"><?= e($_SESSION['user_name'] ?? '') ?></div></header>
<?php if ($flashMessage): ?><div class="notice"><?= e($flashMessage) ?></div><?php endif; ?>

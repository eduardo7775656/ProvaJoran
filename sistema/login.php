<?php
require_once __DIR__ . '/bootstrap.php';
if (!empty($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    try {
        $stmt = db()->prepare('SELECT id, nome, email, senha_hash FROM usuarios WHERE email = ? AND ativo = 1 LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['senha_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['user_name'] = $user['nome'];
            $_SESSION['LAST_ACTIVITY'] = time();
            header('Location: dashboard.php');
            exit;
        }
        $error = 'E-mail ou senha inválidos.';
    } catch (Throwable $ex) {
        $error = 'Não foi possível acessar o sistema. Verifique a configuração do banco de dados.';
    }
}
$flash = takeFlash();
?>
<!doctype html><html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Entrar | Assistência Técnica</title><link rel="stylesheet" href="assets/style.css"></head>
<body class="login-page"><main class="login-card"><div class="brand-mark">AT</div><h1>Assistência Técnica</h1><p class="muted">Entre para gerenciar atendimentos</p>
<?php if ($flash): ?><div class="notice"><?= e($flash) ?></div><?php endif; ?>
<?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
<form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><label>E-mail<input type="email" name="email" required autocomplete="username"></label><label>Senha<input type="password" name="password" required autocomplete="current-password"></label><button class="btn full" type="submit">Entrar</button></form>
<p class="hint">Acesso inicial: <strong>admin@assistencia.local</strong> / <strong>password</strong></p></main></body></html>

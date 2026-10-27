<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', '1');
}
session_start();

const SESSION_TIMEOUT = 900; // 15 minutos sem atividade

if (isset($_SESSION['LAST_ACTIVITY']) && time() - (int)$_SESSION['LAST_ACTIVITY'] > SESSION_TIMEOUT) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    session_start();
    $_SESSION['flash'] = 'Sua sessão expirou. Entre novamente.';
}
$_SESSION['LAST_ACTIVITY'] = time();

function requireLogin(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function verifyCsrf(): void {
    if (!isset($_POST['csrf_token']) || !hash_equals(csrfToken(), (string)$_POST['csrf_token'])) {
        http_response_code(403);
        exit('Requisição inválida. Atualize a página e tente novamente.');
    }
}
function flash(string $message): void { $_SESSION['flash'] = $message; }
function takeFlash(): string {
    $message = (string)($_SESSION['flash'] ?? '');
    unset($_SESSION['flash']);
    return $message;
}

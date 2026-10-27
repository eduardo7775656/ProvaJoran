<?php
declare(strict_types=1);

/*
 * Configuração local XAMPP.
 * MySQL/MariaDB está configurado na porta 3307.
 * Ajuste DB_USER/DB_PASS conforme a instalação local.
 */
const DB_HOST = '127.0.0.1';
const DB_PORT = '3307';
const DB_NAME = 'saep_db';
const DB_USER = 'root';
const DB_PASS = '';

/*
 * Gere uma chave com:
 * php -r "echo base64_encode(random_bytes(32)), PHP_EOL;"
 * Defina a variável de ambiente APP_ENCRYPTION_KEY com essa chave.
 * Para ambiente de avaliação local, a chave abaixo é um fallback demonstrativo;
 * substitua-a antes de armazenar dados reais.
 */
function encryptionKey(): string {
    $encoded = getenv('APP_ENCRYPTION_KEY') ?: 'MDEyMzQ1Njc4OWFiY2RlZjAxMjM0NTY3ODlhYmNkZWY=';
    $key = base64_decode($encoded, true);
    if ($key === false || strlen($key) !== 32) {
        throw new RuntimeException('APP_ENCRYPTION_KEY deve ser Base64 de uma chave de 32 bytes.');
    }
    return $key;
}

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function encryptSensitive(string $plain): string {
    $iv = random_bytes(12);
    $tag = '';
    $cipher = openssl_encrypt($plain, 'aes-256-gcm', encryptionKey(), OPENSSL_RAW_DATA, $iv, $tag);
    if ($cipher === false) throw new RuntimeException('Falha ao proteger dado sensível.');
    return base64_encode($iv . $tag . $cipher);
}

function decryptSensitive(?string $encoded): string {
    if (!$encoded) return '';
    $raw = base64_decode($encoded, true);
    if ($raw === false || strlen($raw) < 29) return '';
    $iv = substr($raw, 0, 12);
    $tag = substr($raw, 12, 16);
    $cipher = substr($raw, 28);
    $plain = openssl_decrypt($cipher, 'aes-256-gcm', encryptionKey(), OPENSSL_RAW_DATA, $iv, $tag);
    return $plain === false ? '' : $plain;
}

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

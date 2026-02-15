<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

const ADMIN_SESSION_KEY = 'admin_logged_in';

function load_env_file_if_exists(string $filePath): void {
  if (!is_file($filePath)) {
    return;
  }

  $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  if (!$lines) {
    return;
  }

  foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '' || strpos($line, '#') === 0) {
      continue;
    }
    $parts = explode('=', $line, 2);
    if (count($parts) !== 2) {
      continue;
    }
    $key = trim($parts[0]);
    $value = trim($parts[1]);
    $value = trim($value, "\"'");
    if ($key === '') {
      continue;
    }
    putenv("$key=$value");
    $_ENV[$key] = $value;
  }
}

function admin_env(string $key, string $default = ''): string {
  $val = getenv($key);
  if ($val !== false && $val !== '') {
    return (string)$val;
  }
  if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
    return (string)$_ENV[$key];
  }
  return $default;
}

function admin_boot_env(): void {
  static $loaded = false;
  if ($loaded) {
    return;
  }

  $root = __DIR__;
  $httpHost = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
  $isLocalHost = $httpHost === ''
    || strpos($httpHost, 'localhost') !== false
    || strpos($httpHost, '.test') !== false
    || strpos($httpHost, '127.0.0.1') === 0
    || strpos($httpHost, '::1') === 0;

  load_env_file_if_exists($root . '/.env');
  $appEnv = admin_env('APP_ENV', $isLocalHost ? 'local' : 'production');
  load_env_file_if_exists($root . ($appEnv === 'production' ? '/.env.production' : '/.env.local'));
  $loaded = true;
}

function admin_username(): string {
  admin_boot_env();
  return admin_env('ADMIN_USERNAME', 'admin');
}

function admin_password(): string {
  admin_boot_env();
  return admin_env('ADMIN_PASSWORD', 'Cambia123!');
}

function admin_is_logged_in(): bool {
  return !empty($_SESSION[ADMIN_SESSION_KEY]);
}

function admin_login(string $username, string $password): bool {
  $validUser = hash_equals(admin_username(), trim($username));
  $validPass = hash_equals(admin_password(), $password);

  if ($validUser && $validPass) {
    $_SESSION[ADMIN_SESSION_KEY] = true;
    return true;
  }

  return false;
}

function admin_logout(): void {
  $_SESSION = [];
  if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
      session_name(),
      '',
      time() - 42000,
      $params['path'],
      $params['domain'],
      $params['secure'],
      $params['httponly']
    );
  }
  session_destroy();
}

function admin_require_login(): void {
  if (!admin_is_logged_in()) {
    header('Location: admin-login');
    exit;
  }
}

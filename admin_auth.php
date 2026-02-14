<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

const ADMIN_USERNAME = 'admin';
const ADMIN_PASSWORD = 'Cambia123!';
const ADMIN_SESSION_KEY = 'admin_logged_in';

function admin_is_logged_in(): bool {
  return !empty($_SESSION[ADMIN_SESSION_KEY]);
}

function admin_login(string $username, string $password): bool {
  $validUser = hash_equals(ADMIN_USERNAME, trim($username));
  $validPass = hash_equals(ADMIN_PASSWORD, $password);

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

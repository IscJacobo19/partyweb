<?php
require __DIR__ . '/admin_auth.php';

if (admin_is_logged_in()) {
  header('Location: admin');
  exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = isset($_POST['username']) ? (string)$_POST['username'] : '';
  $password = isset($_POST['password']) ? (string)$_POST['password'] : '';
  if (admin_login($username, $password)) {
    header('Location: admin');
    exit;
  }
  $error = 'Usuario o contraseña incorrectos.';
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin</title>
  <style>
    :root {
      --bg-0: #0b0f16;
      --bg-1: #131a26;
      --ink: #f2f5fb;
      --muted: #a8b3c6;
      --accent: #1AFFFD;
      --accent-2: #0BA7C5;
      --card: rgba(16, 23, 35, 0.88);
      --line: rgba(148, 163, 184, 0.22);
      --error: #ff8f7f;
    }
    body {
      margin: 0;
      min-height: 100vh;
      display: grid;
      place-items: center;
      padding: 16px;
      font-family: "IBM Plex Sans", "Segoe UI", Arial, sans-serif;
      color: var(--ink);
      background:
        radial-gradient(520px 260px at 12% 5%, rgba(79, 124, 255, 0.18), transparent 70%),
        radial-gradient(520px 280px at 88% 0%, rgba(43, 79, 166, 0.22), transparent 72%),
        linear-gradient(135deg, var(--bg-0) 0%, var(--bg-1) 45%, #0a0f17 100%);
    }
    body::before {
      content: "";
      position: fixed;
      inset: 0;
      pointer-events: none;
      background:
        linear-gradient(120deg, rgba(255,255,255,0.06), transparent 45%),
        repeating-linear-gradient(90deg, rgba(255,255,255,0.06) 0 1px, transparent 1px 6px);
      mix-blend-mode: screen;
      opacity: 0.55;
    }
    .admin-shell {
      width: min(980px, 100%);
      border: 1px solid rgba(148, 163, 184, 0.2);
      border-radius: 24px;
      background:
        linear-gradient(135deg, rgba(18, 25, 38, 0.96), rgba(12, 18, 28, 0.92));
      box-shadow: 0 28px 70px rgba(3, 7, 14, 0.6), inset 0 1px 0 rgba(255,255,255,0.06);
      overflow: hidden;
      display: grid;
      grid-template-columns: 1fr;
      position: relative;
    }
    .admin-shell::before {
      content: "";
      position: absolute;
      inset: 0;
      background:
        radial-gradient(380px 180px at 80% 8%, rgba(79, 124, 255, 0.18), transparent 70%),
        linear-gradient(90deg, rgba(255, 255, 255, 0.04), transparent 40%, rgba(255, 255, 255, 0.05) 60%, transparent 100%);
      pointer-events: none;
    }
    .admin-cover {
      padding: 26px 22px 18px;
      border-bottom: 1px solid rgba(148, 163, 184, 0.18);
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      background:
        radial-gradient(320px 180px at 12% 12%, rgba(79, 124, 255, 0.18), transparent 62%),
        radial-gradient(360px 200px at 90% 90%, rgba(43, 79, 166, 0.18), transparent 62%),
        linear-gradient(160deg, rgba(12, 18, 28, 0.9), rgba(16, 22, 34, 0.9));
    }
    .admin-cover a {
      display: flex;
      justify-content: center;
      width: 100%;
    }
    .admin-cover-logo {
      width: min(360px, 100%);
      display: block;
      filter: drop-shadow(0 14px 26px rgba(15, 23, 42, 0.28));
      animation: adminLogoFloat 5.8s ease-in-out infinite;
    }
    .admin-cover p {
      margin: 12px 0 0;
      color: var(--muted);
      font-size: 15px;
      line-height: 1.6;
      max-width: 52ch;
    }
    .admin-card {
      padding: 22px 22px 26px;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
    }
    .admin-card h1 {
      margin: 0 0 8px;
      font-size: 28px;
      line-height: 1.1;
      letter-spacing: 0.4px;
      color: var(--ink);
    }
    .admin-card h1 span { color: var(--ink); }
    .admin-card h1 .admin-accent { color: var(--accent); }
    .admin-card p {
      margin: 0 0 16px;
      color: var(--muted);
    }
    .field {
      width: min(560px, 100%);
      text-align: left;
      margin-bottom: 12px;
    }
    label {
      display: block;
      margin-bottom: 6px;
      font-size: 0.88rem;
      color: var(--muted);
      font-weight: 600;
    }
    input {
      width: 100%;
      box-sizing: border-box;
      padding: 0.68rem 0.75rem;
      border: 1px solid rgba(148, 163, 184, 0.25);
      border-radius: 12px;
      font-size: 0.96rem;
      color: var(--ink);
      background: rgba(10, 16, 26, 0.9);
      outline: none;
      transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    input:focus {
      border-color: rgba(79, 124, 255, 0.7);
      box-shadow: 0 0 0 4px rgba(79, 124, 255, 0.2);
    }
    .admin-error {
      margin: 0 0 12px;
      color: var(--error);
      border: 1px solid rgba(255, 143, 127, 0.35);
      background: rgba(255, 143, 127, 0.1);
      border-radius: 12px;
      padding: 10px 12px;
      width: min(560px, 100%);
      text-align: left;
    }
    .admin-actions {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      margin-top: 8px;
      justify-content: center;
      width: min(560px, 100%);
    }
    .admin-footer {
      margin-top: auto;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 6px;
    }
    .btn {
      border: 0;
      border-radius: 12px;
      padding: 0.7rem 1.1rem;
      background: linear-gradient(120deg, var(--accent), var(--accent-2));
      color: #0b0f16;
      font-weight: 700;
      cursor: pointer;
      letter-spacing: 0.03em;
      box-shadow: 0 12px 26px rgba(39, 67, 122, 0.25);
      text-decoration: none;
    }
    .btn.secondary {
      background: rgba(255, 255, 255, 0.06);
      color: var(--ink);
      border: 1px solid rgba(148, 163, 184, 0.25);
      box-shadow: none;
    }
    .admin-hint {
      margin-top: 10px;
      font-size: 12px;
      color: rgba(226, 232, 240, 0.65);
      text-align: center;
    }

    .admin-brand {
      margin-top: 6px;
      font-size: 0.8rem;
      letter-spacing: 0.2em;
      font-weight: 700;
      color: var(--accent);
    }

    .admin-link {
      margin-top: 4px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 0.82rem;
      color: rgba(226, 232, 240, 0.85);
      text-decoration: none;
      font-weight: 600;
    }

    .admin-link-text {
      margin-top: 2px;
      font-size: 0.82rem;
      color: rgba(226, 232, 240, 0.85);
      font-weight: 600;
    }

    .admin-link .chip {
      padding: 4px 10px;
      border-radius: 999px;
      border: 1px solid rgba(26, 255, 253, 0.35);
      color: var(--accent);
      background: rgba(26, 255, 253, 0.06);
      font-weight: 700;
      letter-spacing: 0.08em;
    }

    .admin-brand {
      position: relative;
      margin-top: 8px;
      font-size: 0.82rem;
      letter-spacing: 0.28em;
      font-weight: 700;
      color: var(--accent);
      text-decoration: none;
    }

    .admin-brand::after {
      content: "";
      position: absolute;
      left: 0;
      right: 0;
      bottom: -6px;
      height: 2px;
      background: linear-gradient(90deg, transparent, var(--accent), transparent);
      opacity: 0.7;
    }
    @media (min-width: 920px) {
      .admin-shell { grid-template-columns: 1.05fr 0.95fr; }
      .admin-cover {
        border-bottom: 0;
        border-right: 1px solid rgba(148, 163, 184, 0.25);
        padding: 32px;
      }
      .admin-card { padding: 30px; }
    }
    @media (max-width: 520px) {
      .admin-cover-logo {
        width: min(260px, 100%);
      }
      .admin-card h1 {
        font-size: 22px;
      }
    }
    @keyframes adminLogoFloat {
      0%, 100% {
        transform: translateY(0) scale(1);
        filter: drop-shadow(0 14px 26px rgba(15, 23, 42, 0.28));
      }
      50% {
        transform: translateY(-6px) scale(1.01);
        filter: drop-shadow(0 18px 30px rgba(15, 23, 42, 0.32));
      }
    }
    @media (prefers-reduced-motion: reduce) {
      .admin-cover-logo { animation: none; }
    }
  </style>
</head>
<body>
  <div class="admin-shell">
    <aside class="admin-cover" aria-label="Jelly Dev">
      <a href="https://jelly-dev.com" target="_blank" rel="noopener" aria-label="Ir a Jelly Dev">
        <img class="admin-cover-logo" src="assets/jellydev-logo.png" alt="Jelly Dev logo" loading="eager" decoding="async" />
      </a>
      <p>Soluciones digitales premium para experiencias de invitaci&oacute;n.</p>
    </aside>
    <form class="admin-card" method="post" autocomplete="off">
      <h1><span class="admin-accent">ACCESO</span> <span>ADMIN</span></h1>
      <p>Ingresa con tu usuario administrador.</p>
      <?php if ($error !== ''): ?>
        <div class="admin-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>
      <div class="field">
        <label for="username">Usuario</label>
        <input id="username" name="username" autocomplete="username" required>
      </div>
      <div class="field">
        <label for="password">Contrase&ntilde;a</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
      </div>
      <div class="admin-actions">
        <button class="btn" type="submit">Iniciar sesión</button>
        <a class="btn secondary" href="/">Ir al sitio</a>
      </div>
      <div class="admin-footer">
        <div class="admin-hint">Acceso restringido. Solo personal autorizado.</div>
        <div class="admin-hint">Si quieres vivir la experiencia, cotiza tu invitación web.</div>
        <div class="admin-link-text">Explora nuestro estudio</div>
        <a class="admin-link" href="https://jelly-dev.com" target="_blank" rel="noopener">
          <span class="chip">JELLY DEV</span>
        </a>
      </div>
    </form>
  </div>
</body>
</html>


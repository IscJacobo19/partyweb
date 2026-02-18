<?php
require __DIR__ . '/admin_auth.php';
admin_require_login();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Invitados</title>
  <script src="/assets/js/jsqr.js" defer></script>
  <style>
    :root {
      --bg0: #f6f7fb;
      --bg1: #eef1f6;
      --card: #ffffff;
      --line: #d9e0ea;
      --text: #111827;
      --muted: #4b5563;
      --accent: #1f4f8b;
      --accent2: #163d6f;
      --accent3: #4b86d6;
      --ok: #0f6b44;
      --err: #b42318;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      padding: 10px;
      font-family: "IBM Plex Sans", "Segoe UI", Arial, sans-serif;
      color: var(--text);
      background:
        radial-gradient(700px 420px at 12% -10%, rgba(75, 134, 214, 0.18), transparent 60%),
        radial-gradient(700px 420px at 88% 0%, rgba(31, 79, 139, 0.12), transparent 55%),
        linear-gradient(180deg, var(--bg0), var(--bg1));
    }

    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes glowPulse {
      0% { box-shadow: 0 0 0 rgba(31, 79, 139, 0); }
      50% { box-shadow: 0 0 0 rgba(31, 79, 139, 0.08); }
      100% { box-shadow: 0 0 0 rgba(31, 79, 139, 0); }
    }

    .wrap { max-width: 1180px; margin: 0 auto; }

    .head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      margin-bottom: 10px;
      position: relative;
      padding: 6px 2px;
    }

    .brand { display: flex; align-items: center; gap: 10px; min-width: 0; }

    .logo {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      display: grid;
      place-items: center;
      font-weight: 700;
      color: #ffffff;
      background: linear-gradient(135deg, #2a5aa0, #163d6f);
      flex-shrink: 0;
    }

    h1 {
      margin: 0;
      font-size: 1.08rem;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .top-actions {
      display: none;
      align-items: center;
      gap: 8px;
    }

    .btn {
      border: 0;
      border-radius: 9px;
      padding: 9px 12px;
      background: linear-gradient(120deg, var(--accent), var(--accent2));
      color: #ffffff;
      font-weight: 700;
      cursor: pointer;
      transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .btn:hover { transform: translateY(-1px); box-shadow: 0 10px 20px rgba(31, 79, 139, 0.18); }

    .btn:disabled { opacity: 0.7; cursor: not-allowed; }

    .btn-outline {
      border: 1px solid var(--line);
      border-radius: 9px;
      padding: 9px 12px;
      background: #f4f6f9;
      color: var(--text);
      font-weight: 700;
      cursor: pointer;
      transition: border-color 0.15s ease, color 0.15s ease;
    }
    .btn-outline:hover { border-color: var(--accent3); color: var(--accent); }

    .logout {
      text-decoration: none;
      background: linear-gradient(120deg, var(--accent), var(--accent2));
      color: #ffffff;
      border-radius: 10px;
      padding: 9px 12px;
      font-size: 0.9rem;
      font-weight: 700;
      text-align: center;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .menu-btn {
      border: 1px solid var(--line);
      border-radius: 9px;
      width: 42px;
      height: 42px;
      background: #ffffff;
      color: var(--text);
      display: grid;
      place-items: center;
      cursor: pointer;
      flex-shrink: 0;
    }

    .menu-btn span,
    .menu-btn span::before,
    .menu-btn span::after {
      display: block;
      width: 18px;
      height: 2px;
      background: currentColor;
      border-radius: 2px;
      content: "";
      position: relative;
      transition: transform 0.2s ease, opacity 0.2s ease;
    }

    .menu-btn span::before { top: -6px; position: absolute; }
    .menu-btn span::after { top: 6px; position: absolute; }

    .menu-btn.active span { transform: rotate(45deg); }
    .menu-btn.active span::before { transform: rotate(90deg); top: 0; }
    .menu-btn.active span::after { opacity: 0; }

    .mobile-menu {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.35);
      z-index: 40;
      display: none;
      padding: 0;
    }

    .mobile-menu.open { display: block; }

    .mobile-panel {
      margin-left: auto;
      width: min(82vw, 320px);
      height: 100%;
      background:
        radial-gradient(220px 180px at 80% 0%, rgba(31, 79, 139, 0.08), transparent 70%),
        linear-gradient(180deg, #ffffff, #f6f8fc);
      border-left: 1px solid var(--line);
      padding: 18px 16px;
      display: flex;
      flex-direction: column;
      gap: 12px;
      position: relative;
    }

    .mobile-panel .menu-link.logout-link {
      margin-top: auto;
    }

    .mobile-panel .logout,
    .mobile-panel .btn,
    .mobile-panel .btn-outline { width: 100%; }

    .mobile-panel .menu-link {
      display: flex;
      align-items: center;
      gap: 12px;
      width: 100%;
      padding: 12px 14px;
      border-radius: 12px;
      border: 1px solid #e6ecf5;
      background: #ffffff;
      color: var(--text);
      font-weight: 700;
      text-transform: none;
      letter-spacing: 0.01em;
      font-size: 0.98rem;
      box-shadow: 0 8px 16px rgba(15, 23, 42, 0.06);
      transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }

    .mobile-panel .menu-link:hover {
      border-color: rgba(31, 79, 139, 0.35);
      color: var(--accent);
      transform: translateY(-1px);
      box-shadow: 0 12px 22px rgba(15, 23, 42, 0.12);
    }

    .menu-icon {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      display: grid;
      place-items: center;
      background: linear-gradient(135deg, rgba(31, 79, 139, 0.15), rgba(75, 134, 214, 0.08));
      color: var(--accent);
      flex-shrink: 0;
    }

    .menu-icon svg {
      width: 18px;
      height: 18px;
      stroke: currentColor;
      fill: none;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round;
    }

    .menu-sub {
      display: block;
      margin-top: 2px;
      font-weight: 500;
      color: var(--muted);
      font-size: 0.82rem;
    }

    .mobile-panel .menu-title {
      font-size: 0.8rem;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: 0.08em;
      font-weight: 700;
      margin-bottom: 2px;
    }

    .menu-close {
      position: absolute;
      top: 10px;
      right: 10px;
      width: 34px;
      height: 34px;
      border-radius: 10px;
      border: 1px solid var(--line);
      background: #f4f6f9;
      color: var(--muted);
      font-size: 1rem;
      font-weight: 700;
      display: grid;
      place-items: center;
      cursor: pointer;
    }

    .kpis {
      display: grid;
      grid-template-columns: 1fr;
      gap: 8px;
      margin-bottom: 10px;
    }

    .kpi {
      background: var(--card);
      border: 1px solid var(--line);
      border-radius: 12px;
      padding: 9px 10px;
      animation: fadeUp 0.5s ease both;
      transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
      cursor: pointer;
    }
    .kpi:hover {
      transform: translateY(-1px);
      border-color: rgba(31, 79, 139, 0.35);
      box-shadow: 0 10px 20px rgba(15, 23, 42, 0.10);
    }
    .kpi.active {
      border-color: rgba(31, 79, 139, 0.55);
      box-shadow: 0 12px 24px rgba(31, 79, 139, 0.16);
    }
    .kpi:nth-child(1) { background: linear-gradient(180deg, #ffffff, #f2f7ff); }
    .kpi:nth-child(2) { background: linear-gradient(180deg, #ffffff, #f1fbf6); }
    .kpi:nth-child(3) { background: linear-gradient(180deg, #ffffff, #fff6f2); }
    .kpi:nth-child(4) { background: linear-gradient(180deg, #ffffff, #f5f3ff); }

    .kpi-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
    }

    .kpi-icon {
      width: 34px;
      height: 34px;
      border-radius: 10px;
      display: grid;
      place-items: center;
      background: rgba(31, 79, 139, 0.12);
      color: var(--accent2);
      flex-shrink: 0;
      animation: glowPulse 2.2s ease-in-out infinite;
    }

    .kpi-icon svg {
      width: 18px;
      height: 18px;
      stroke: currentColor;
      fill: none;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round;
    }

    .kpi-label {
      font-size: 0.76rem;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: 0.06em;
    }

    .kpi-value {
      margin-top: 4px;
      font-size: 1.6rem;
      font-weight: 700;
      color: #0b1220;
      letter-spacing: 0.02em;
    }

    .card {
      background: var(--card);
      border: 1px solid var(--line);
      border-radius: 12px;
      padding: 10px;
      box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
      min-width: 0;
      margin-bottom: 10px;
      animation: fadeUp 0.6s ease both;
    }

    .card h2 {
      margin: 0 0 10px 0;
      font-size: 0.92rem;
      color: var(--muted);
      letter-spacing: 0.03em;
      text-transform: uppercase;
    }

    .msg {
      margin-bottom: 10px;
      border-radius: 10px;
      padding: 9px 11px;
      font-size: 0.92rem;
      border: 1px solid var(--line);
      display: none;
    }

    .msg.ok { background: rgba(31, 79, 139, 0.1); color: #163d6f; }
    .msg.error { background: rgba(180, 35, 24, 0.1); color: #7a1b14; }

    .filters {
      display: grid;
      grid-template-columns: 1fr;
      gap: 8px;
      margin-bottom: 10px;
    }

    .pagination {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      margin-top: 10px;
      flex-wrap: wrap;
    }

    .page-info {
      font-size: 0.88rem;
      color: var(--muted);
      font-weight: 600;
    }

    .page-actions {
      display: flex;
      align-items: center;
      gap: 6px;
      flex-wrap: wrap;
    }

    .page-actions .btn-outline,
    .page-actions .btn {
      padding: 6px 10px;
      border-radius: 8px;
      font-size: 0.85rem;
    }

    .page-actions .btn-outline.active {
      border-color: var(--accent);
      color: var(--accent);
      background: #e9f0fb;
    }

    .page-ellipsis {
      padding: 0 4px;
      color: var(--muted);
      font-weight: 600;
    }

    .filter-field label {
      display: block;
      margin-bottom: 4px;
      font-size: 0.8rem;
      color: var(--muted);
    }

    input,
    select,
    textarea {
      width: 100%;
      margin: 0;
      border-radius: 8px;
      border: 1px solid var(--line);
      padding: 9px 10px;
      font-size: 15px;
      color: var(--text);
      background: #ffffff;
    }

    textarea { min-height: 95px; resize: vertical; }

    .table-wrap {
      overflow-x: auto;
      width: 100%;
      -webkit-overflow-scrolling: touch;
    }

    table { width: 100%; border-collapse: collapse; }

    th,
    td {
      border: 1px solid var(--line);
      padding: 7px 9px;
      font-size: 0.84rem;
      white-space: nowrap;
      text-align: left;
    }

    th { background: linear-gradient(180deg, #f6f8fb, #eef3fa); color: #111827; }
    th.sortable { cursor: pointer; user-select: none; }
    th.sortable .sort {
      display: inline-block;
      margin-left: 6px;
      font-size: 0.7rem;
      color: rgba(17, 24, 39, 0.55);
    }
    th.sortable.active .sort { color: #111827; }

    .members { white-space: normal; min-width: 220px; }

    .member {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      border-radius: 999px;
      border: 1px solid var(--line);
      padding: 2px 8px;
      margin: 2px 4px 2px 0;
      font-size: 0.76rem;
    }

    .member.ok { background: rgba(15, 107, 68, 0.12); color: var(--ok); }
    .member.no { background: rgba(180, 35, 24, 0.1); color: var(--err); }
    .member.att { background: rgba(31, 79, 139, 0.12); color: var(--accent); border-color: rgba(31, 79, 139, 0.35); }

    .badge {
      display: inline-block;
      border-radius: 999px;
      padding: 3px 8px;
      font-size: 0.74rem;
      font-weight: 700;
      border: 1px solid var(--line);
      color: #111827;
      background: linear-gradient(180deg, #f8fafc, #eef2f7);
    }

    .btn-mini {
      border: 0;
      border-radius: 8px;
      padding: 6px 8px;
      font-size: 0.78rem;
      font-weight: 700;
      cursor: pointer;
      color: #ffffff;
      background: linear-gradient(120deg, #2a5aa0, #1a4a8a);
      margin-right: 4px;
      transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .btn-mini:hover { transform: translateY(-1px); box-shadow: 0 8px 16px rgba(31, 79, 139, 0.18); }

    .actions-cell { white-space: nowrap; }

    .mobile-list { display: grid; gap: 8px; }

    .invite-card {
      border: 1px solid var(--line);
      border-radius: 10px;
      padding: 9px;
      background: linear-gradient(180deg, #ffffff, #f7f9fc);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .invite-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 22px rgba(15, 23, 42, 0.12);
    }

    .invite-head {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 8px;
      margin-bottom: 6px;
    }

    .invite-name { font-size: 0.94rem; font-weight: 700; }
    .invite-meta { font-size: 0.82rem; color: var(--muted); margin-bottom: 6px; }

    .desktop-table { display: none; }

    .hist-ok { color: var(--ok); font-weight: 700; }
    .hist-no { color: var(--err); font-weight: 700; }

    .modal {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.55);
      backdrop-filter: blur(2px);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 50;
      padding: 12px;
    }

    .modal.open { display: flex; }

    .modal-card {
      width: min(100%, 680px);
      max-height: calc(100vh - 24px);
      overflow: auto;
      background: var(--card);
      border: 1px solid #cfd7e3;
      border-radius: 14px;
      padding: 14px;
      box-shadow: 0 24px 60px rgba(15, 23, 42, 0.28);
      font-size: 0.95rem;
      font-family: "IBM Plex Sans", "Segoe UI", Arial, sans-serif;
    }

    .modal-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 4px 4px 12px 4px;
      border-bottom: 1px solid var(--line);
    }

    .modal-title {
      font-size: 1rem;
      font-weight: 700;
      color: #111827;
      letter-spacing: 0.02em;
      text-transform: uppercase;
    }

    .modal-subtitle {
      font-size: 0.88rem;
      color: var(--muted);
      margin-top: 4px;
    }

    .modal-body {
      padding: 12px 4px 4px 4px;
    }

    .modal-footer {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      padding: 12px 4px 4px 4px;
      border-top: 1px solid var(--line);
      margin-top: 8px;
    }

    .modal form label {
      font-weight: 600;
      color: #1f2937;
      display: block;
      margin-bottom: 6px;
    }

    .modal input,
    .modal select,
    .modal textarea {
      font-size: 0.95rem;
      font-family: inherit;
      margin-bottom: 12px;
    }

    .modal input,
    .modal select,
    .modal textarea {
      background: #ffffff;
      border-color: #cfd7e3;
      box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    .modal input:focus,
    .modal select:focus,
    .modal textarea:focus {
      outline: none;
      border-color: #7aa3e6;
      box-shadow: 0 0 0 3px rgba(31, 79, 139, 0.15);
    }

    .modal .btn {
      box-shadow: 0 12px 22px rgba(31, 79, 139, 0.25);
    }

    .scan-video {
      width: 100%;
      border-radius: 12px;
      border: 1px solid var(--line);
      background: #0f172a;
    }

    .scan-hint {
      font-size: 0.88rem;
      color: var(--muted);
      margin-top: 8px;
    }

    .scan-status {
      margin-top: 10px;
      font-weight: 600;
      color: var(--accent);
    }

    .scan-manual {
      margin-top: 12px;
      display: grid;
      gap: 8px;
    }

    .scan-manual-row {
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 8px;
      align-items: stretch;
    }

    .scan-manual-row .btn {
      height: 100%;
    }

    .scan-upload {
      margin-top: 10px;
      display: flex;
      gap: 8px;
      align-items: center;
    }

    #scan-upload-input {
      display: none;
    }

    .person-list {
      display: grid;
      gap: 8px;
      margin-top: 8px;
    }

    .person-btn {
      width: 100%;
      text-align: left;
      background: #f4f6f9;
      border: 1px solid var(--line);
      border-radius: 10px;
      padding: 10px 12px;
      font-weight: 600;
      cursor: pointer;
    }

    .confirm-list {
      display: grid;
      gap: 8px;
      margin-top: 8px;
    }

    .confirm-options {
      display: flex;
      gap: 10px;
      margin-top: 12px;
      justify-content: flex-end;
    }

    .confirm-status {
      margin-top: 12px;
      font-weight: 600;
      color: var(--accent);
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 6px;
      text-align: center;
    }

    .spinner {
      width: 16px;
      height: 16px;
      border-radius: 50%;
      border: 2px solid rgba(31, 79, 139, 0.25);
      border-top-color: var(--accent);
      animation: spin 0.9s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .check-list {
      display: grid;
      gap: 6px;
      margin-top: 8px;
    }

    .check-item {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 8px 10px;
      border: 1px solid var(--line);
      border-radius: 10px;
      background: #f8fafc;
    }

    .check-item input {
      margin: 0;
      width: auto;
      flex: 0 0 auto;
    }

    .copy-note {
      font-weight: 600;
      color: var(--accent);
    }

    .modal-card.compact {
      max-width: 360px;
      text-align: center;
    }

    .copy-only {
      font-size: 1rem;
      font-weight: 700;
      color: var(--accent);
      padding: 12px 0;
    }

    .checkmark {
      width: 26px;
      height: 26px;
      border-radius: 50%;
      background: rgba(15, 107, 68, 0.12);
      color: var(--ok);
      display: grid;
      place-items: center;
      font-size: 1rem;
      font-weight: 800;
    }

    .hist-more {
      display: flex;
      justify-content: flex-end;
      margin-top: 10px;
    }

    .hint { color: var(--muted); font-size: 0.82rem; margin-top: 6px; margin-bottom: 10px; }

    .miembros-edit-wrap {
      border: 1px solid var(--line);
      border-radius: 10px;
      padding: 10px;
      background: #f8fafc;
      margin-bottom: 12px;
    }

    .miembros-edit-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      margin-bottom: 8px;
    }

    .miembros-edit-head label {
      margin: 0;
    }

    .miembros-edit-list {
      display: grid;
      gap: 8px;
    }

    .miembro-row {
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 8px;
      align-items: center;
    }

    .miembro-row.is-locked input {
      background: #eef2f7;
      color: #6b7280;
      cursor: not-allowed;
    }

    .footer {
      max-width: 1180px;
      margin: 14px auto 24px auto;
      padding: 10px 14px;
      color: var(--muted);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      font-size: 0.85rem;
    }

    .footer-link {
      color: var(--accent);
      text-decoration: none;
      font-weight: 700;
    }

    .footer-link:hover { text-decoration: underline; }

    .footer-sep {
      color: #94a3b8;
    }

    .footer-role {
      color: var(--muted);
      font-weight: 600;
      letter-spacing: 0.02em;
    }

    @media (min-width: 700px) {
      body { padding: 14px; }
      h1 { font-size: 1.2rem; }
      .kpis { grid-template-columns: repeat(4, minmax(0, 1fr)); }
      .kpi-value { font-size: 1.9rem; }
      th, td { font-size: 0.9rem; padding: 8px 10px; }
      .filters { grid-template-columns: 1fr; }
    }

    @media (min-width: 860px) {
      .menu-btn,
      .mobile-menu { display: none !important; }
      .top-actions { display: flex; }
      .filters { grid-template-columns: 1fr; }
      .desktop-table { display: block; }
      .mobile-list { display: none; }
    }

    @media (max-width: 859px) {
      .units-table { display: none; }
      .footer { flex-direction: column; align-items: flex-start; }
    }
</style>
</head>
<body>
<div class="wrap">
  <header class="head">
    <div class="brand">
      <div class="logo">JM</div>
      <h1>Admin de Invitados</h1>
    </div>

    <button type="button" class="menu-btn" id="menu-btn" aria-label="Abrir menú">
      <span></span>
    </button>

    <div class="top-actions">
      <button type="button" class="btn" id="open-modal-btn">Generar invitación</button>
      <button type="button" class="btn-outline" id="open-scan-btn">Escanear QR</button>
      <a href="admin-logout" class="logout">Cerrar sesión</a>
    </div>
  </header>

    <div id="mobile-menu" class="mobile-menu" aria-hidden="true">
    <div class="mobile-panel">
      <button type="button" class="menu-close" id="close-menu-btn" aria-label="Cerrar menú">×</button>
      <div class="menu-title">Menú</div>
      <button type="button" class="btn menu-link" id="open-modal-btn-mobile">
        <span class="menu-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"></path></svg>
        </span>
        <span>
          Generar invitación
          <span class="menu-sub">Crea una nueva invitación</span>
        </span>
      </button>
      <button type="button" class="btn menu-link" id="open-scan-btn-mobile">
        <span class="menu-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M4 7V4h3"></path><path d="M17 4h3v3"></path><path d="M20 17v3h-3"></path><path d="M7 20H4v-3"></path><path d="M7 12h10"></path></svg>
        </span>
        <span>
          Escanear QR
          <span class="menu-sub">Registrar asistencia</span>
        </span>
      </button>
      <a href="admin-logout" class="logout menu-link logout-link">
        <span class="menu-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M10 17l5-5-5-5"></path><path d="M15 12H3"></path><path d="M21 21V3"></path></svg>
        </span>
        <span>
          Cerrar sesión
          <span class="menu-sub">Salir del administrador</span>
        </span>
      </a>
    </div>
  </div>

  <div id="flash-msg" class="msg"></div>

  <section class="kpis">
    <article class="kpi active" data-kpi-filter="all" tabindex="0" role="button" aria-label="Mostrar todas las unidades">
      <div class="kpi-head">
        <div class="kpi-label">Total personas invitadas</div>
        <div class="kpi-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="3"></circle>
            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>
        </div>
      </div>
      <div class="kpi-value" id="kpi-total">0</div>
    </article>
    <article class="kpi" data-kpi-filter="confirmados" tabindex="0" role="button" aria-label="Mostrar unidades confirmadas">
      <div class="kpi-head">
        <div class="kpi-label">Total confirmados</div>
        <div class="kpi-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <path d="M20 6L9 17l-5-5"></path>
          </svg>
        </div>
      </div>
      <div class="kpi-value" id="kpi-confirm">0</div>
    </article>
    <article class="kpi" data-kpi-filter="pendientes" tabindex="0" role="button" aria-label="Mostrar unidades pendientes">
      <div class="kpi-head">
        <div class="kpi-label">Total pendientes</div>
        <div class="kpi-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="9"></circle>
            <path d="M12 7v5l3 3"></path>
          </svg>
        </div>
      </div>
      <div class="kpi-value" id="kpi-pending">0</div>
    </article>
    <article class="kpi" data-kpi-filter="asistidos" tabindex="0" role="button" aria-label="Mostrar unidades asistidas">
      <div class="kpi-head">
        <div class="kpi-label">Total asistidos</div>
        <div class="kpi-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <path d="M12 2l3 7 7 1-5 5 1 7-6-3-6 3 1-7-5-5 7-1z"></path>
          </svg>
        </div>
      </div>
      <div class="kpi-value" id="kpi-asistio">0</div>
    </article>
  </section>

  <section class="card">
    <h2>Dashboard de unidades</h2>

    <div class="filters">
      <div class="filter-field">
        <label for="filter-global">Busqueda global</label>
        <input id="filter-global" type="text" placeholder="Invitado, grupo, código o estado">
      </div>
    </div>

    <div id="unidades-cards" class="mobile-list"></div>

    <div class="table-wrap desktop-table">
      <table class="stack-table units-table">
        <thead>
        <tr>
          <th class="sortable" data-sort="tipo">Tipo <span class="sort">?</span></th>
          <th class="sortable" data-sort="nombre">Nombre <span class="sort">?</span></th>
          <th class="sortable" data-sort="miembros">Miembros <span class="sort">?</span></th>
          <th class="sortable" data-sort="confirmados">Confirmados <span class="sort">?</span></th>
          <th class="sortable" data-sort="detalle">Detalle por persona <span class="sort">?</span></th>
          <th>Acciones</th>
        </tr>
        </thead>
        <tbody id="unidades-body"></tbody>
      </table>
    </div>

    <div id="units-pagination" class="pagination" style="display:none;">
      <div id="units-page-info" class="page-info"></div>
      <div id="units-page-actions" class="page-actions"></div>
    </div>
  </section>

  <section class="card">
    <h2>Historial de confirmaciones / cancelaciones</h2>
    <div class="table-wrap">
      <table class="stack-table">
        <thead>
        <tr>
          <th>Fecha y hora</th>
          <th>Acción</th>
          <th>Invitación</th>
          <th>Persona</th>
        </tr>
        </thead>
        <tbody id="historial-body"></tbody>
      </table>
    </div>
    <div class="hist-more">
      <button type="button" class="btn-outline" id="open-hist-btn">Ver mas</button>
    </div>
  </section>
</div>

<footer class="footer">
  <a class="footer-link" href="https://jelly-dev.com" target="_blank" rel="noopener">Jelly Dev</a>
  <span class="footer-sep">—</span>
  <span class="footer-role">Administrador de invitados</span>
</footer>

<div id="inv-modal" class="modal" aria-hidden="true" role="dialog" aria-modal="true">
  <div class="modal-card">
    <div class="modal-header">
      <div>
        <div class="modal-title" id="inv-modal-title">Generar invitación</div>
        <div class="modal-subtitle" id="inv-modal-subtitle">Completa los datos para crear una invitación.</div>
      </div>
      <button type="button" class="btn-outline" id="close-modal-btn">Cerrar</button>
    </div>

    <div class="modal-body">
      <form id="inv-form">
        <label for="tipo">Tipo de invitación</label>
        <select id="tipo" name="tipo" required>
          <option value="">Seleccionar</option>
          <option value="persona">Persona</option>
          <option value="familia">Familia / Grupo</option>
        </select>

        <div id="inv-details">
          <label for="nombre_unidad" id="nombre-unidad-label">Nombre visible</label>
          <input id="nombre_unidad" name="nombre_unidad" type="text" required placeholder="Nombre y apellido">


          <div id="miembros-wrap">
            <label for="miembros">Miembros (una persona por linea)</label>
            <textarea id="miembros" name="miembros" placeholder="Ej: Ana Lopez&#10;Jose Martinez&#10;Lucia Perez"></textarea>
            <div class="hint">Para familia/grupo, agrega un participante por linea.</div>
          </div>
          <div id="miembros-edit-wrap" class="miembros-edit-wrap" style="display:none;">
            <div class="miembros-edit-head">
              <label>Miembros de la invitación</label>
              <button type="button" class="btn-outline" id="add-member-btn">Agregar nombre</button>
            </div>
            <div id="miembros-edit-list" class="miembros-edit-list"></div>
            <div class="hint">Los miembros confirmados aparecen bloqueados y no se pueden editar ni eliminar.</div>
          </div>
        </div>
      </form>
    </div>

    <div class="modal-footer">
      <button class="btn-outline" type="button" id="cancel-modal-btn">Cancelar</button>
      <button class="btn" type="submit" form="inv-form" id="save-btn">Guardar invitación</button>
    </div>
  </div>
</div>

<div id="hist-modal" class="modal" aria-hidden="true" role="dialog" aria-modal="true">
  <div class="modal-card">
    <div class="modal-header">
      <div>
        <div class="modal-title">Historial completo</div>
        <div class="modal-subtitle">Ultimos movimientos registrados.</div>
      </div>
      <button type="button" class="btn-outline" id="close-hist-btn">Cerrar</button>
    </div>
    <div class="modal-body">
      <div class="table-wrap">
        <table class="stack-table">
          <thead>
          <tr>
            <th>Fecha y hora</th>
            <th>Acción</th>
            <th>Invitación</th>
            <th>Persona</th>
          </tr>
          </thead>
          <tbody id="historial-modal-body"></tbody>
        </table>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn-outline" id="close-hist-btn-footer">Cerrar</button>
    </div>
  </div>
</div>

<div id="code-modal" class="modal" aria-hidden="true" role="dialog" aria-modal="true">
  <div class="modal-card" id="code-modal-card">
    <div class="modal-header" id="code-modal-header">
      <div>
        <div class="modal-title">Código de confirmación</div>
        <div class="modal-subtitle">Comparte este código con el invitado.</div>
      </div>
    </div>
    <div class="modal-body" id="code-modal-body">
      <div class="card" id="code-modal-content" style="margin-bottom:0;">
        <div id="code-name" class="invite-name"></div>
        <div id="code-value" class="kpi-value" style="margin-top:6px;"></div>
      </div>
      <div id="code-copy-only" class="copy-only" style="display:none;">Copiado</div>
    </div>
    <div class="modal-footer" id="code-modal-footer">
      <button type="button" class="btn-outline" id="close-code-btn-footer">Cerrar</button>
      <button type="button" class="btn" id="copy-code-btn">Copiar código</button>
    </div>
  </div>
</div>

<div id="scan-modal" class="modal" aria-hidden="true" role="dialog" aria-modal="true">
  <div class="modal-card">
    <div class="modal-header">
      <div>
        <div class="modal-title">Escanear QR</div>
        <div class="modal-subtitle">Apunta la cámara al código para registrar asistencia.</div>
      </div>
      <button type="button" class="btn-outline" id="close-scan-btn">Cerrar</button>
    </div>
    <div class="modal-body">
      <video id="scan-video" class="scan-video" autoplay playsinline></video>
      <div class="scan-hint">Si no detecta el código, acércalo o mejora la luz.</div>
      <div id="scan-status" class="scan-status"></div>
      <div id="scan-manual" class="scan-manual is-hidden">
        <label for="scan-manual-code">Ingresar código manualmente</label>
        <div class="scan-manual-row">
          <input id="scan-manual-code" type="text" placeholder="Código de confirmación">
          <button type="button" class="btn" id="scan-manual-btn">Buscar</button>
        </div>
        <div class="scan-upload">
          <input id="scan-upload-input" type="file" accept="image/*" capture="environment">
          <button type="button" class="btn-outline" id="scan-upload-btn">Subir foto del QR</button>
        </div>
      </div>
    </div>
    <div class="modal-footer"></div>
  </div>
</div>

<div id="scan-select-modal" class="modal" aria-hidden="true" role="dialog" aria-modal="true">
  <div class="modal-card">
    <div class="modal-header">
      <div>
        <div class="modal-title">Selecciona quién asistió</div>
        <div id="scan-select-subtitle" class="modal-subtitle"></div>
      </div>
      <button type="button" class="btn-outline" id="close-scan-select-btn">Cerrar</button>
    </div>
    <div class="modal-body">
      <div id="scan-person-list" class="person-list"></div>
    </div>
    <div class="modal-footer"></div>
  </div>
</div>

<div id="checkin-confirm-modal" class="modal" aria-hidden="true" role="dialog" aria-modal="true">
  <div class="modal-card">
    <div class="modal-header">
      <div>
        <div class="modal-title">Confirmar asistencia</div>
        <div id="checkin-confirm-subtitle" class="modal-subtitle"></div>
      </div>
      <button type="button" class="btn-outline" id="close-checkin-confirm-btn">Cerrar</button>
    </div>
    <div class="modal-body">
      <div id="checkin-confirm-list" class="confirm-list"></div>
    </div>
    <div class="modal-footer"></div>
  </div>
</div>


<script>
(() => {
  const tipo = document.getElementById("tipo");
  const miembrosWrap = document.getElementById("miembros-wrap");
  const invDetails = document.getElementById("inv-details");
  const nombreUnidadLabel = document.getElementById("nombre-unidad-label");
  const nombreUnidadInput = document.getElementById("nombre_unidad");
  const unidadesBody = document.getElementById("unidades-body");
  const unidadesCards = document.getElementById("unidades-cards");
  const unitsPagination = document.getElementById("units-pagination");
  const unitsPageInfo = document.getElementById("units-page-info");
  const unitsPageActions = document.getElementById("units-page-actions");
  const historialBody = document.getElementById("historial-body");
  const historialModalBody = document.getElementById("historial-modal-body");
  const flashMsg = document.getElementById("flash-msg");
  const saveBtn = document.getElementById("save-btn");
  const form = document.getElementById("inv-form");
  const invModalTitle = document.getElementById("inv-modal-title");
  const invModalSubtitle = document.getElementById("inv-modal-subtitle");
  const miembrosEditWrap = document.getElementById("miembros-edit-wrap");
  const miembrosEditList = document.getElementById("miembros-edit-list");
  const addMemberBtn = document.getElementById("add-member-btn");

  const filterGlobal = document.getElementById("filter-global");

  const openModalBtn = document.getElementById("open-modal-btn");
  const openModalBtnMobile = document.getElementById("open-modal-btn-mobile");
  const openScanBtn = document.getElementById("open-scan-btn");
  const openScanBtnMobile = document.getElementById("open-scan-btn-mobile");
  const closeModalBtn = document.getElementById("close-modal-btn");
  const cancelModalBtn = document.getElementById("cancel-modal-btn");
  const modal = document.getElementById("inv-modal");
  const openHistBtn = document.getElementById("open-hist-btn");
  const closeHistBtn = document.getElementById("close-hist-btn");
  const closeHistBtnFooter = document.getElementById("close-hist-btn-footer");
  const histModal = document.getElementById("hist-modal");
  const codeModal = document.getElementById("code-modal");
  const codeModalCard = document.getElementById("code-modal-card");
  const codeModalHeader = document.getElementById("code-modal-header");
  const codeModalBody = document.getElementById("code-modal-body");
  const codeModalFooter = document.getElementById("code-modal-footer");
  const codeModalContent = document.getElementById("code-modal-content");
  const codeCopyOnly = document.getElementById("code-copy-only");
  const closeCodeBtnFooter = document.getElementById("close-code-btn-footer");
  const copyCodeBtn = document.getElementById("copy-code-btn");
  const codeName = document.getElementById("code-name");
  const codeValue = document.getElementById("code-value");
  const scanModal = document.getElementById("scan-modal");
  const closeScanBtn = document.getElementById("close-scan-btn");
  const scanVideo = document.getElementById("scan-video");
  const scanStatus = document.getElementById("scan-status");
  const scanManual = document.getElementById("scan-manual");
  const scanManualCode = document.getElementById("scan-manual-code");
  const scanManualBtn = document.getElementById("scan-manual-btn");
  const scanUploadInput = document.getElementById("scan-upload-input");
  const scanUploadBtn = document.getElementById("scan-upload-btn");
  const scanSelectModal = document.getElementById("scan-select-modal");
  const closeScanSelectBtn = document.getElementById("close-scan-select-btn");
  const scanPersonList = document.getElementById("scan-person-list");
  const scanSelectSubtitle = document.getElementById("scan-select-subtitle");
  const checkinConfirmModal = document.getElementById("checkin-confirm-modal");
  const checkinConfirmList = document.getElementById("checkin-confirm-list");
  const checkinConfirmSubtitle = document.getElementById("checkin-confirm-subtitle");
  const closeCheckinConfirmBtn = document.getElementById("close-checkin-confirm-btn");

  const sortHeaders = Array.from(document.querySelectorAll("th.sortable"));

  const menuBtn = document.getElementById("menu-btn");
  const closeMenuBtn = document.getElementById("close-menu-btn");
  const mobileMenu = document.getElementById("mobile-menu");
  const kpiCards = Array.from(document.querySelectorAll(".kpi[data-kpi-filter]"));

  let state = {
    unidades: [],
    historial: [],
    kpis: { total_personas: 0, total_confirmadas: 0, total_pendientes: 0, total_asistidos: 0 }
  };
  let sortKey = "";
  let sortDir = "asc";
  let currentKpiFilter = "all";
  let currentPage = 1;
  const pageSize = 10;
  let selectedCode = "";
  let scanStream = null;
  let scanActive = false;
  let scanDetector = null;
  let scanCanvas = null;
  let scanCtx = null;
  let scanLastTs = 0;
  let scanFrameIndex = 0;
  const scanIntervalMs = 180;
  let pendingCheckin = null;
  let jsQrPromise = null;
  let editingUnitId = 0;

  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/\"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function showMsg(type, text) {
    flashMsg.className = "msg " + (type === "ok" ? "ok" : "error");
    flashMsg.textContent = text;
    flashMsg.style.display = "block";
  }

  function clearMsg() {
    flashMsg.className = "msg";
    flashMsg.textContent = "";
    flashMsg.style.display = "none";
  }

  function normalize(text) {
    return String(text || "").toLowerCase().trim();
  }

  function paginateList(list) {
    const totalPages = Math.max(1, Math.ceil(list.length / pageSize));
    if (currentPage > totalPages) currentPage = totalPages;
    const start = (currentPage - 1) * pageSize;
    return {
      totalPages,
      items: list.slice(start, start + pageSize),
    };
  }

  function renderPagination(totalItems, totalPages) {
    if (!unitsPagination || !unitsPageInfo || !unitsPageActions) return;
    if (totalItems <= pageSize) {
      unitsPagination.style.display = "none";
      return;
    }
    unitsPagination.style.display = "flex";
    unitsPageInfo.textContent = `Página ${currentPage} de ${totalPages} — ${totalItems} registros`;

    const buttons = [];
    buttons.push(`<button type="button" class="btn-outline" data-page="${currentPage - 1}" ${currentPage === 1 ? "disabled" : ""}>Anterior</button>`);

    const maxButtons = 5;
    let start = Math.max(1, currentPage - 2);
    let end = Math.min(totalPages, start + maxButtons - 1);
    start = Math.max(1, end - maxButtons + 1);

    if (start > 1) {
      buttons.push(`<button type="button" class="btn-outline" data-page="1">1</button>`);
      if (start > 2) buttons.push(`<span class="page-ellipsis">…</span>`);
    }

    for (let p = start; p <= end; p += 1) {
      const active = p === currentPage ? " active" : "";
      buttons.push(`<button type="button" class="btn-outline${active}" data-page="${p}">${p}</button>`);
    }

    if (end < totalPages) {
      if (end < totalPages - 1) buttons.push(`<span class="page-ellipsis">…</span>`);
      buttons.push(`<button type="button" class="btn-outline" data-page="${totalPages}">${totalPages}</button>`);
    }

    buttons.push(`<button type="button" class="btn-outline" data-page="${currentPage + 1}" ${currentPage === totalPages ? "disabled" : ""}>Siguiente</button>`);
    unitsPageActions.innerHTML = buttons.join("");
  }

    function buildInviteMessage(unidad) {
    const code = String(unidad.codigo_confirmacion || "").trim().toUpperCase();
    const inviteUrl = new URL("./", window.location.href).toString();
    return (
      "Gabriela y Jimmy te invitan a una celebracion muy especial.\n\n" +
      "Hola " + unidad.nombre + ", aqui esta tu invitacion:\n" +
      inviteUrl + "\n\n" +
      "CODIGO DE CONFIRMACION:\n" +
      code + "\n\n" +
      "Copia solo este codigo para confirmar tu asistencia."
    );
  }

  async function copyInvite(unidad) {
    const text = buildInviteMessage(unidad);
    try {
      if (navigator.clipboard && navigator.clipboard.writeText) {
        await navigator.clipboard.writeText(text);
      } else {
        const ta = document.createElement("textarea");
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand("copy");
        ta.remove();
      }
      showMsg("ok", "Mensaje copiado al portapapeles.");
    } catch (e) {
      showMsg("error", "No se pudo copiar. Intenta nuevamente.");
    }
  }

  function sendWhatsApp(unidad) {
    const text = buildInviteMessage(unidad);
    const url = "https://wa.me/?text=" + encodeURIComponent(text);
    window.open(url, "_blank", "noopener,noreferrer");
  }

  function syncTypeFields() {
    if (editingUnitId > 0) {
      invDetails.style.display = "block";
      miembrosWrap.style.display = "none";
      miembrosEditWrap.style.display = "block";
      return;
    }
    const hasType = tipo.value === "persona" || tipo.value === "familia";
    invDetails.style.display = hasType ? "block" : "none";
    if (tipo.value === "persona") {
      nombreUnidadLabel.textContent = "Nombre visible (ej: Juan Perez)";
      nombreUnidadInput.placeholder = "Nombre y apellido";
    } else if (tipo.value === "familia") {
      nombreUnidadLabel.textContent = "Nombre visible (ej: Perez o Nombre del grupo)";
      nombreUnidadInput.placeholder = "Apellido de familia o nombre de grupo";
    } else {
      nombreUnidadLabel.textContent = "Nombre visible";
      nombreUnidadInput.placeholder = "Nombre y apellido";
    }
    if (tipo.value === "familia") {
      miembrosWrap.style.display = "block";
      return;
    }
    if (tipo.value === "persona") {
      miembrosWrap.style.display = "none";
      return;
    }
    miembrosWrap.style.display = "block";
  }

  function renderEditMembers(members) {
    const rows = (members || []).map((m) => {
      const id = Number(m.id) || 0;
      const locked = Number(m.asistencia) === 1 || Number(m.asistio) === 1;
      const disabled = locked ? "disabled" : "";
      const lockTag = locked ? `<span class="badge">Confirmado</span>` : "";
      const removeBtn = locked
        ? `<button type="button" class="btn-outline" disabled>Bloqueado</button>`
        : `<button type="button" class="btn-outline" data-member-remove="1">Eliminar</button>`;
      return `
        <div class="miembro-row${locked ? " is-locked" : ""}" data-member-row="1">
          <input type="text" value="${escapeHtml(m.nombre || "")}" data-member-name="1" data-member-id="${id > 0 ? id : ""}" ${disabled} required>
          <div>${lockTag} ${removeBtn}</div>
        </div>
      `;
    }).join("");
    miembrosEditList.innerHTML = rows;
  }

  function appendEditableMemberRow(name = "") {
    const row = document.createElement("div");
    row.className = "miembro-row";
    row.setAttribute("data-member-row", "1");
    row.innerHTML = `
      <input type="text" value="${escapeHtml(name)}" data-member-name="1" data-member-id="" required>
      <div><button type="button" class="btn-outline" data-member-remove="1">Eliminar</button></div>
    `;
    miembrosEditList.appendChild(row);
  }

  function collectEditMembers() {
    return Array.from(miembrosEditList.querySelectorAll("[data-member-name]"))
      .map((input) => {
        const idRaw = String(input.getAttribute("data-member-id") || "").trim();
        const id = idRaw ? Number(idRaw) : null;
        const nombre = String(input.value || "").trim();
        if (!nombre) return null;
        return { id: Number.isFinite(id) && id > 0 ? id : null, nombre };
      })
      .filter(Boolean);
  }

  function setCreateMode() {
    editingUnitId = 0;
    form.reset();
    tipo.disabled = false;
    invModalTitle.textContent = "Generar invitación";
    invModalSubtitle.textContent = "Completa los datos para crear una invitación.";
    saveBtn.textContent = "Guardar invitación";
    miembrosEditList.innerHTML = "";
    syncTypeFields();
  }

  function openEditModal(unidad) {
    if (!unidad) return;
    editingUnitId = Number(unidad.id) || 0;
    form.reset();
    tipo.value = unidad.tipo || "";
    tipo.disabled = true;
    nombreUnidadInput.value = unidad.nombre || "";
    invModalTitle.textContent = "Editar invitación";
    invModalSubtitle.textContent = "Puedes editar y agregar miembros. Confirmados quedan bloqueados.";
    saveBtn.textContent = "Guardar cambios";
    renderEditMembers(unidad.members || []);
    if (!miembrosEditList.querySelector("[data-member-row]")) {
      appendEditableMemberRow();
    }
    syncTypeFields();
    openModal();
  }

  function animateCount(el, toValue, delayMs = 0) {
    const target = Number(toValue) || 0;
    const current = Number(el.getAttribute("data-value") || 0);
    if (current === target) return;
    el.setAttribute("data-value", String(target));
    const duration = 900;
    const from = current;

    setTimeout(() => {
      const start = performance.now();

      function tick(now) {
        const t = Math.min(1, (now - start) / duration);
        const eased = 1 - Math.pow(1 - t, 3);
        const value = Math.round(from + (target - from) * eased);
        el.textContent = String(value);
        if (t < 1) requestAnimationFrame(tick);
      }

      requestAnimationFrame(tick);
    }, delayMs);
  }

  function renderKpis() {
    const duration = 900;
    const gap = 120;
    animateCount(document.getElementById("kpi-total"), state.kpis.total_personas || 0, 0);
    animateCount(document.getElementById("kpi-confirm"), state.kpis.total_confirmadas || 0, duration + gap);
    animateCount(document.getElementById("kpi-pending"), state.kpis.total_pendientes || 0, (duration + gap) * 2);
    animateCount(document.getElementById("kpi-asistio"), state.kpis.total_asistidos || 0, (duration + gap) * 3);
  }

  function setActiveKpiCard() {
    kpiCards.forEach((card) => {
      const cardFilter = card.getAttribute("data-kpi-filter") || "all";
      card.classList.toggle("active", cardFilter === currentKpiFilter);
    });
  }

  function getFilteredUnidades() {
    const qGlobal = normalize(filterGlobal.value);
    return state.unidades.filter((u) => {
      if (qGlobal && !(normalize(u.search).includes(qGlobal))) return false;
      return true;
    });
  }

  function renderUnidades() {
    const filtered = getFilteredUnidades().slice();
    if (sortKey) {
      const dir = sortDir === "asc" ? 1 : -1;
      filtered.sort((a, b) => {
        if (sortKey === "miembros") {
          return (Number(a.personas_total) - Number(b.personas_total)) * dir;
        }
        if (sortKey === "confirmados") {
          return (Number(a.personas_confirmadas) - Number(b.personas_confirmadas)) * dir;
        }
        if (sortKey === "detalle") {
          return ((a.members || []).length - (b.members || []).length) * dir;
        }
        const av = normalize(a[sortKey]);
        const bv = normalize(b[sortKey]);
        return av.localeCompare(bv) * dir;
      });
    }
    const pageData = paginateList(filtered);
    const pageItems = pageData.items;

    const rows = pageItems.map((u) => {
      const members = (u.members || []).map((m) => {
        const klass = Number(m.asistencia) === 1 ? "ok" : "no";
        const att = Number(m.asistio) === 1 ? " att" : "";
        const icon = Number(m.asistencia) === 1 ? "&#10003;" : "&#10005;";
        return `<span class="member ${klass}${att}">${icon} ${escapeHtml(m.nombre)}</span>`;
      }).join("");

      return `<tr>
        <td data-label="Tipo"><span class="badge">${escapeHtml(u.tipo)}</span></td>
        <td data-label="Nombre">${escapeHtml(u.nombre)}</td>
        <td data-label="Miembros">${Number(u.personas_total) || 0}</td>
        <td data-label="Confirmados">${Number(u.personas_confirmadas) || 0}</td>
        <td data-label="Detalle" class="members">${members}</td>
        <td data-label="Acciones" class="actions-cell">
          <button class="btn-mini" type="button" data-action="edit" data-id="${u.id}">Editar</button>
          <button class="btn-mini" type="button" data-action="code" data-id="${u.id}">Ver código</button>
          <button class="btn-mini" type="button" data-action="copy" data-id="${u.id}">Copiar</button>
          <button class="btn-mini" type="button" data-action="wa" data-id="${u.id}">WhatsApp</button>
        </td>
      </tr>`;
    }).join("");

    unidadesBody.innerHTML = rows || `<tr><td colspan="7">Sin resultados</td></tr>`;

    const cards = pageItems.map((u) => {
      const members = (u.members || []).map((m) => {
        const klass = Number(m.asistencia) === 1 ? "ok" : "no";
        const att = Number(m.asistio) === 1 ? " att" : "";
        const icon = Number(m.asistencia) === 1 ? "&#10003;" : "&#10005;";
        return `<span class="member ${klass}${att}">${icon} ${escapeHtml(m.nombre)}</span>`;
      }).join("");

      return `<article class="invite-card">
        <div class="invite-head">
          <span class="badge">${escapeHtml(u.tipo)}</span>
        </div>
        <div class="invite-name">${escapeHtml(u.nombre)}</div>
        <div class="invite-meta">Miembros: ${Number(u.personas_total) || 0} | Confirmados: ${Number(u.personas_confirmadas) || 0}</div>
        <div class="members">${members}</div>
        <div style="margin-top:8px;">
          <button class="btn-mini" type="button" data-action="edit" data-id="${u.id}">Editar</button>
          <button class="btn-mini" type="button" data-action="code" data-id="${u.id}">Ver código</button>
          <button class="btn-mini" type="button" data-action="copy" data-id="${u.id}">Copiar</button>
          <button class="btn-mini" type="button" data-action="wa" data-id="${u.id}">WhatsApp</button>
        </div>
      </article>`;
    }).join("");

    unidadesCards.innerHTML = cards || `<div class="invite-card">Sin resultados</div>`;
    renderPagination(filtered.length, pageData.totalPages);
  }

  function renderHistorial() {
    const rowsAll = state.historial.map((h) => {
      const action = h.accion === "confirmo"
        ? `<span class="hist-ok">Confirmo</span>`
        : h.accion === "asistio"
          ? `<span class="hist-ok">Asistio</span>`
          : `<span class="hist-no">Cancelo</span>`;
      return `<tr>
        <td data-label="Fecha y hora">${escapeHtml(h.fecha_evento)}</td>
        <td data-label="Accion">${action}</td>
        <td data-label="Invitacion">${escapeHtml(h.unidad_nombre)}</td>
        <td data-label="Persona">${escapeHtml(h.persona_nombre)}</td>
      </tr>`;
    }).join("");

    const rowsTop = state.historial.slice(0, 2).map((h) => {
      const action = h.accion === "confirmo"
        ? `<span class="hist-ok">Confirmo</span>`
        : h.accion === "asistio"
          ? `<span class="hist-ok">Asistio</span>`
          : `<span class="hist-no">Cancelo</span>`;
      return `<tr>
        <td data-label="Fecha y hora">${escapeHtml(h.fecha_evento)}</td>
        <td data-label="Accion">${action}</td>
        <td data-label="Invitacion">${escapeHtml(h.unidad_nombre)}</td>
        <td data-label="Persona">${escapeHtml(h.persona_nombre)}</td>
      </tr>`;
    }).join("");

    historialBody.innerHTML = rowsTop || `<tr><td colspan="4">Sin historial</td></tr>`;
    historialModalBody.innerHTML = rowsAll || `<tr><td colspan="4">Sin historial</td></tr>`;

    if (state.historial.length > 2) {
      openHistBtn.style.display = "inline-flex";
    } else {
      openHistBtn.style.display = "none";
    }
  }

  function renderAll() {
    renderKpis();
    renderUnidades();
    renderHistorial();
  }

  async function loadState() {
    const qs = new URLSearchParams({
      action: "state",
      _t: String(Date.now()),
      kpi_filter: currentKpiFilter,
    });
    const res = await fetch("admin_api.php?" + qs.toString(), {
      headers: { Accept: "application/json" },
      cache: "no-store"
    });
    const data = await res.json();
    if (!data.ok) throw new Error(data.message || "No se pudo cargar.");
    state = data;
    clearMsg();
    setActiveKpiCard();
    renderAll();
  }

  async function saveInvitation(formData, action = "") {
    const endpoint = action ? ("admin_api.php?action=" + encodeURIComponent(action)) : "admin_api.php";
    const res = await fetch(endpoint, {
      method: "POST",
      body: formData,
      headers: { Accept: "application/json" }
    });
    const data = await res.json();
    if (!data.ok) throw new Error(data.message || "No se pudo guardar.");
    state = data;
    renderAll();
    showMsg("ok", data.message || "Guardado correctamente.");
  }

  function openModal() {
    modal.classList.add("open");
    modal.setAttribute("aria-hidden", "false");
    closeMenu();
  }

  function openCreateModal() {
    setCreateMode();
    openModal();
  }

  function closeModal() {
    modal.classList.remove("open");
    modal.setAttribute("aria-hidden", "true");
    setCreateMode();
  }

  function openHistModal() {
    histModal.classList.add("open");
    histModal.setAttribute("aria-hidden", "false");
  }

  function closeHistModal() {
    histModal.classList.remove("open");
    histModal.setAttribute("aria-hidden", "true");
  }

  function resetCodeModalView() {
    if (codeModalCard) codeModalCard.classList.remove("compact");
    if (codeModalHeader) codeModalHeader.style.display = "";
    if (codeModalBody) codeModalBody.style.display = "";
    if (codeModalFooter) codeModalFooter.style.display = "";
    if (codeModalContent) codeModalContent.style.display = "";
    if (codeCopyOnly) codeCopyOnly.style.display = "none";
  }

  function showCodeCopied() {
    if (codeModalCard) codeModalCard.classList.add("compact");
    if (codeModalHeader) codeModalHeader.style.display = "none";
    if (codeModalFooter) codeModalFooter.style.display = "none";
    if (codeModalContent) codeModalContent.style.display = "none";
    if (codeCopyOnly) codeCopyOnly.style.display = "block";
    setTimeout(() => {
      closeCodeModal();
    }, 700);
  }

  function openCodeModal(unidad) {
    resetCodeModalView();
    selectedCode = unidad.codigo_confirmacion || "";
    codeName.textContent = unidad.nombre || "Invitación";
    codeValue.textContent = selectedCode || "Sin código";
    codeModal.classList.add("open");
    codeModal.setAttribute("aria-hidden", "false");
  }

  function closeCodeModal() {
    codeModal.classList.remove("open");
    codeModal.setAttribute("aria-hidden", "true");
    selectedCode = "";
    resetCodeModalView();
  }

  function openScanModal() {
    scanModal.classList.add("open");
    scanModal.setAttribute("aria-hidden", "false");
    closeMenu();
    resetScanModal();
    startScan();
  }

  function closeScanModal() {
    scanModal.classList.remove("open");
    scanModal.setAttribute("aria-hidden", "true");
    stopScan();
  }

  function resetScanModal() {
    if (scanStatus) scanStatus.textContent = "";
    if (scanManualCode) scanManualCode.value = "";
  }

  function openScanSelectModal(title) {
    scanSelectModal.classList.add("open");
    scanSelectModal.setAttribute("aria-hidden", "false");
    scanSelectSubtitle.textContent = title || "";
  }

  function closeScanSelectModal() {
    scanSelectModal.classList.remove("open");
    scanSelectModal.setAttribute("aria-hidden", "true");
    scanPersonList.innerHTML = "";
  }

  function openCheckinConfirmModal(subtitle, unidadNombre, personas) {
    pendingCheckin = { unidad: unidadNombre || "", personas: personas || [] };
    checkinConfirmSubtitle.textContent = subtitle || "Confirma para registrar asistencia.";
    if ((personas || []).length <= 1) {
      const p = personas && personas.length ? personas[0] : null;
      if (p && Number(p.asistencia) !== 1) {
        checkinConfirmList.innerHTML = `
          <div>Esta persona no confirmo asistencia.</div>
        `;
        checkinConfirmModal.classList.add("open");
        checkinConfirmModal.setAttribute("aria-hidden", "false");
        return;
      }
      if (p && Number(p.asistio) === 1) {
        checkinConfirmList.innerHTML = `
          <div><strong>${escapeHtml(p.nombre)}</strong> ya registro su asistencia.</div>
        `;
        checkinConfirmModal.classList.add("open");
        checkinConfirmModal.setAttribute("aria-hidden", "false");
        return;
      }
      checkinConfirmList.innerHTML = `
        <div>¿Deseas registrar asistencia para <strong>${escapeHtml(p ? p.nombre : "esta persona")}</strong>?</div>
        <div class="confirm-options">
          <button type="button" class="btn-outline" id="checkin-no-btn">No</button>
          <button type="button" class="btn" id="checkin-yes-btn" data-confirm-persona="${p ? p.id : ""}">Sí</button>
        </div>
        <div id="checkin-status" class="confirm-status" style="display:none;"></div>
      `;
    } else {
      const confirmados = (personas || []).filter((p) => Number(p.asistencia) === 1);
      if (confirmados.length === 0) {
        checkinConfirmList.innerHTML = `
          <div>No hay personas confirmadas en esta familia.</div>
        `;
      } else if (confirmados.every((p) => Number(p.asistio) === 1)) {
        checkinConfirmList.innerHTML = `
          <div>La asistencia de <strong>${escapeHtml(unidadNombre || "esta familia")}</strong> ya esta registrada.</div>
        `;
      } else {
        checkinConfirmList.innerHTML = `
          <div>Asistencia para <strong>${escapeHtml(unidadNombre || "la familia")}</strong></div>
          <div class="check-list">
            ${confirmados.map((p) => {
              const checked = p.asistio ? "checked" : "";
              return `<label class="check-item">
                <input type="checkbox" data-confirm-persona="${p.id}" ${checked}>
                <span>${escapeHtml(p.nombre)}</span>
              </label>`;
            }).join("")}
          </div>
          <div class="confirm-options">
            <button type="button" class="btn-outline" id="checkin-no-btn">Cancelar</button>
            <button type="button" class="btn" id="checkin-yes-multi-btn">Registrar asistencia</button>
          </div>
          <div id="checkin-status" class="confirm-status" style="display:none;"></div>
        `;
      }
    }
    checkinConfirmModal.classList.add("open");
    checkinConfirmModal.setAttribute("aria-hidden", "false");
  }

  function closeCheckinConfirmModal() {
    checkinConfirmModal.classList.remove("open");
    checkinConfirmModal.setAttribute("aria-hidden", "true");
    checkinConfirmList.innerHTML = "";
    pendingCheckin = null;
  }

  function ensureJsQr() {
    if (typeof window.jsQR === "function") return Promise.resolve(true);
    if (jsQrPromise) return jsQrPromise;
    jsQrPromise = new Promise((resolve) => {
      const tryLoad = (src, onFail) => {
        const script = document.createElement("script");
        script.src = src;
        script.async = true;
        script.onload = () => resolve(typeof window.jsQR === "function");
        script.onerror = () => {
          if (onFail) onFail();
          else resolve(false);
        };
        document.head.appendChild(script);
      };
      tryLoad("/assets/js/jsqr.js", () => {
        tryLoad("assets/js/jsqr.js", () => {
          tryLoad("https://unpkg.com/jsqr@1.4.0/dist/jsQR.js");
        });
      });
    });
    return jsQrPromise;
  }

  async function startScan() {
    scanStatus.textContent = "";
    if (scanManual) scanManual.classList.add("is-hidden");
    if (!("mediaDevices" in navigator) || !navigator.mediaDevices.getUserMedia) {
      scanStatus.textContent = "Tu dispositivo no soporta cámara.";
      if (scanManual) scanManual.classList.remove("is-hidden");
      return;
    }
    const isLocalhost = location.hostname === "localhost" || location.hostname === "127.0.0.1";
    if (location.protocol !== "https:" && !isLocalhost) {
      scanStatus.textContent = "La cámara requiere HTTPS. Abre el sitio con https://";
      if (scanManual) scanManual.classList.remove("is-hidden");
      return;
    }
    const hasBarcodeApi = "BarcodeDetector" in window;
    let hasJsQr = typeof window.jsQR === "function";
    if (!hasJsQr) {
      hasJsQr = await ensureJsQr();
    }

    let canUseBarcode = false;
    if (hasBarcodeApi) {
      try {
        if (typeof BarcodeDetector.getSupportedFormats === "function") {
          const formats = await BarcodeDetector.getSupportedFormats();
          canUseBarcode = Array.isArray(formats) && formats.includes("qr_code");
        } else {
          canUseBarcode = true;
        }
      } catch (e) {
        canUseBarcode = true;
      }
    }

    if (!canUseBarcode && !hasJsQr) {
      scanStatus.textContent = "Escaneo no disponible en este navegador.";
      if (scanManual) scanManual.classList.remove("is-hidden");
      return;
    }
    try {
      try {
        scanStream = await navigator.mediaDevices.getUserMedia({
          video: {
            facingMode: { ideal: "environment" },
            width: { ideal: 1280 },
            height: { ideal: 720 },
            advanced: [{ focusMode: "continuous" }],
          },
          audio: false,
        });
      } catch (innerErr) {
        scanStream = await navigator.mediaDevices.getUserMedia({
          video: true,
          audio: false,
        });
      }
      scanVideo.srcObject = scanStream;
      scanVideo.muted = true;
      scanVideo.setAttribute("muted", "true");
      await scanVideo.play().catch(() => {});
      scanDetector = null;
      if (canUseBarcode) {
        try {
          scanDetector = new BarcodeDetector({ formats: ["qr_code"] });
        } catch (e) {
          scanDetector = null;
        }
      }
      if (hasJsQr) {
        scanCanvas = document.createElement("canvas");
        scanCtx = scanCanvas.getContext("2d", { willReadFrequently: true });
      }
      scanLastTs = 0;
      scanFrameIndex = 0;
      scanStatus.textContent = "Escaneando...";
      scanActive = true;
      scanLoop();
    } catch (err) {
      const msg = err && err.name === "NotAllowedError"
        ? "Permiso de cámara denegado. Actívalo en ajustes del navegador."
        : "No se pudo abrir la cámara.";
      const fullMsg = msg + (err && err.name ? " (" + err.name + ")" : "");
      scanStatus.textContent = fullMsg;
      alert(fullMsg);
      if (scanManual) scanManual.classList.remove("is-hidden");
    }
  }

  function stopScan() {
    scanActive = false;
    if (scanStream) {
      scanStream.getTracks().forEach((t) => t.stop());
      scanStream = null;
    }
    if (scanVideo) {
      scanVideo.srcObject = null;
    }
    scanCanvas = null;
    scanCtx = null;
    scanLastTs = 0;
    scanFrameIndex = 0;
  }

  async function scanLoop(now) {
    if (!scanActive) return;
    const ts = typeof now === "number" ? now : performance.now();
    if (scanLastTs && (ts - scanLastTs) < scanIntervalMs) {
      requestAnimationFrame(scanLoop);
      return;
    }
    scanLastTs = ts;
    try {
      let detected = false;
      if (scanDetector) {
        try {
          const results = await scanDetector.detect(scanVideo);
          if (results && results.length > 0) {
            const raw = results[0].rawValue || "";
            const code = normalizeScannedCode(raw);
            scanActive = false;
            detected = true;
            await handleScannedCode(code.trim());
            return;
          }
        } catch (detectorErr) {
          scanDetector = null;
        }
      }
      if (!detected && scanCanvas && scanCtx && typeof window.jsQR === "function" && scanVideo.readyState >= 2) {
        const w = scanVideo.videoWidth;
        const h = scanVideo.videoHeight;
        if (w && h) {
          if (!scanStatus.textContent) scanStatus.textContent = "Escaneando...";
          scanCtx.imageSmoothingEnabled = false;
          const tryDecode = async (targetW, cropCenter) => {
            const scale = Math.min(1, targetW / w);
            const sw = Math.floor(w * scale);
            const sh = Math.floor(h * scale);
            if (scanCanvas.width !== sw) scanCanvas.width = sw;
            if (scanCanvas.height !== sh) scanCanvas.height = sh;
            scanCtx.drawImage(scanVideo, 0, 0, sw, sh);
            let sx = 0;
            let sy = 0;
            let cw = sw;
            let ch = sh;
            if (cropCenter) {
              const size = Math.min(sw, sh);
              sx = Math.floor((sw - size) / 2);
              sy = Math.floor((sh - size) / 2);
              cw = size;
              ch = size;
            }
            const imageData = scanCtx.getImageData(sx, sy, cw, ch);
            return window.jsQR(imageData.data, cw, ch, { inversionAttempts: "attemptBoth" });
          };

          const attempts = [
            [1280, false],
            [1280, true],
            [960, false],
            [960, true],
            [720, true],
          ];
          const pick = attempts[scanFrameIndex % attempts.length];
          scanFrameIndex += 1;
          let result = await tryDecode(pick[0], pick[1]);
          if (!result && pick[1]) {
            result = await tryDecode(pick[0], false);
          }
          if (result && result.data) {
            const raw = result.data || "";
            const code = normalizeScannedCode(raw);
            scanActive = false;
            await handleScannedCode(code.trim());
            return;
          }
        }
      }
    } catch (e) {}
    requestAnimationFrame(scanLoop);
  }

  function normalizeScannedCode(rawValue) {
    const raw = String(rawValue || "").trim();
    if (!raw) return "";
    const lower = raw.toLowerCase();
    if (lower.startsWith("confirmacion:")) {
      return raw.slice(raw.indexOf(":") + 1).trim().toUpperCase();
    }
    const match = raw.match(/(?:confirmacion[:=\s-]*)?([a-z0-9]{4,12})/i);
    if (match && match[1]) {
      return String(match[1]).toUpperCase();
    }
    return raw.toUpperCase();
  }

  function decodeImageWithJsQr(img) {
    if (typeof window.jsQR !== "function") return "";
    const canvas = document.createElement("canvas");
    const ctx = canvas.getContext("2d", { willReadFrequently: true });
    if (!ctx) return "";

    const attempts = [
      { maxW: 1400, cropCenter: false },
      { maxW: 1200, cropCenter: true },
      { maxW: 900, cropCenter: false },
      { maxW: 700, cropCenter: true },
      { maxW: 560, cropCenter: false },
    ];

    for (const attempt of attempts) {
      const scale = Math.min(1, attempt.maxW / img.width);
      const sw = Math.max(1, Math.floor(img.width * scale));
      const sh = Math.max(1, Math.floor(img.height * scale));
      canvas.width = sw;
      canvas.height = sh;
      ctx.clearRect(0, 0, sw, sh);
      ctx.drawImage(img, 0, 0, sw, sh);

      let sx = 0;
      let sy = 0;
      let cw = sw;
      let ch = sh;
      if (attempt.cropCenter) {
        const size = Math.min(sw, sh);
        sx = Math.floor((sw - size) / 2);
        sy = Math.floor((sh - size) / 2);
        cw = size;
        ch = size;
      }

      const imageData = ctx.getImageData(sx, sy, cw, ch);
      const result = window.jsQR(imageData.data, cw, ch, { inversionAttempts: "attemptBoth" });
      if (result && result.data) {
        return normalizeScannedCode(result.data);
      }
    }

    return "";
  }

  async function handleScannedCode(code) {
    if (!code) {
      scanStatus.textContent = "Código inválido.";
      scanActive = true;
      scanLoop();
      return;
    }
    scanStatus.textContent = "Buscando código...";
    try {
      const res = await fetch("admin_api.php?action=lookup_code&codigo=" + encodeURIComponent(code), {
        headers: { Accept: "application/json" }
      });
      const data = await res.json();
      if (!data.ok) throw new Error(data.message || "Código no encontrado.");
      const personas = data.personas || [];
      if (personas.length <= 1) {
        closeScanModal();
        openCheckinConfirmModal("Confirmar asistencia", data.unidad?.nombre || "", personas);
      } else {
        closeScanModal();
        openCheckinConfirmModal("Selecciona quienes asistieron", data.unidad?.nombre || "", personas);
      }
    } catch (err) {
      scanStatus.textContent = err.message || "Error al buscar código.";
      scanActive = true;
      scanLoop();
    }
  }

  function renderPersonSelect() {
    closeScanSelectModal();
  }

  async function registerCheckin(personaIds) {
    try {
      checkinConfirmList.innerHTML = `
        <div id="checkin-status" class="confirm-status">
          <span class="spinner"></span>
          <span>Actualizando...</span>
        </div>
      `;
      const formData = new FormData();
      formData.append("persona_ids", String(personaIds.join(",")));
      const res = await fetch("admin_api.php?action=checkin", {
        method: "POST",
        body: formData,
        headers: { Accept: "application/json" },
      });
      const data = await res.json();
      if (!data.ok) throw new Error(data.message || "No se pudo registrar.");
      state = data;
      renderAll();
      if (personaIds.length === 1 && pendingCheckin && pendingCheckin.personas) {
        const persona = pendingCheckin.personas.find((p) => Number(p.id) === Number(personaIds[0]));
        if (persona) {
          showMsg("ok", "Bienvenido, " + persona.nombre + ".");
        } else {
          showMsg("ok", data.message || "Asistencia registrada.");
        }
      } else {
        showMsg("ok", data.message || "Asistencia registrada.");
      }
      checkinConfirmList.innerHTML = `
        <div id="checkin-status" class="confirm-status">
          <span class="checkmark">?</span>
          <span>Listo</span>
        </div>
      `;
      setTimeout(() => {
        closeCheckinConfirmModal();
      }, 900);
      resetScanModal();
    } catch (err) {
      checkinConfirmList.innerHTML = `
        <div id="checkin-status" class="confirm-status">
          <span>Error al registrar.</span>
        </div>
      `;
      showMsg("error", err.message || "No se pudo registrar.");
    }
  }

  function openMenu() {
    mobileMenu.classList.add("open");
    mobileMenu.setAttribute("aria-hidden", "false");
    menuBtn.classList.add("active");
  }

  function closeMenu() {
    mobileMenu.classList.remove("open");
    mobileMenu.setAttribute("aria-hidden", "true");
    menuBtn.classList.remove("active");
  }

  tipo.addEventListener("change", syncTypeFields);
  addMemberBtn.addEventListener("click", () => {
    appendEditableMemberRow();
  });
  miembrosEditList.addEventListener("click", (e) => {
    const removeBtn = e.target.closest("[data-member-remove]");
    if (!removeBtn) return;
    const row = removeBtn.closest("[data-member-row]");
    if (!row) return;
    row.remove();
  });

  filterGlobal.addEventListener("input", () => {
    currentPage = 1;
    renderUnidades();
  });
  kpiCards.forEach((card) => {
    const apply = () => {
      const next = card.getAttribute("data-kpi-filter") || "all";
      if (next === currentKpiFilter) return;
      currentKpiFilter = next;
      currentPage = 1;
      loadState().catch((err) => {
        showMsg("error", err.message || "No se pudo cargar el filtro.");
      });
    };
    card.addEventListener("click", apply);
    card.addEventListener("keydown", (e) => {
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        apply();
      }
    });
  });
  if (unitsPageActions) {
    unitsPageActions.addEventListener("click", (e) => {
      const btn = e.target.closest("button[data-page]");
      if (!btn || btn.disabled) return;
      const nextPage = Number(btn.getAttribute("data-page") || 1);
      if (Number.isNaN(nextPage)) return;
      currentPage = nextPage;
      renderUnidades();
    });
  }

  document.addEventListener("click", (e) => {
    const btn = e.target.closest("[data-action][data-id]");
    if (!btn) return;
    const id = Number(btn.getAttribute("data-id"));
    const unidad = state.unidades.find((u) => Number(u.id) === id);
    if (!unidad) return;
    const action = btn.getAttribute("data-action");
    if (action === "edit") openEditModal(unidad);
    if (action === "copy") copyInvite(unidad);
    if (action === "wa") sendWhatsApp(unidad);
    if (action === "code") openCodeModal(unidad);
  });

  openModalBtn.addEventListener("click", openCreateModal);
  openModalBtnMobile.addEventListener("click", openCreateModal);
  openScanBtn.addEventListener("click", openScanModal);
  openScanBtnMobile.addEventListener("click", openScanModal);
  closeModalBtn.addEventListener("click", closeModal);
  cancelModalBtn.addEventListener("click", closeModal);
  menuBtn.addEventListener("click", openMenu);
  closeMenuBtn.addEventListener("click", closeMenu);
  openHistBtn.addEventListener("click", openHistModal);
  closeHistBtn.addEventListener("click", closeHistModal);
  closeHistBtnFooter.addEventListener("click", closeHistModal);
  closeCodeBtnFooter.addEventListener("click", closeCodeModal);
  closeScanBtn.addEventListener("click", closeScanModal);
  closeScanSelectBtn.addEventListener("click", closeScanSelectModal);
  closeCheckinConfirmBtn.addEventListener("click", closeCheckinConfirmModal);

  copyCodeBtn.addEventListener("click", async () => {
    if (!selectedCode) return;
    try {
      if (navigator.clipboard && navigator.clipboard.writeText) {
        await navigator.clipboard.writeText(selectedCode);
      } else {
        const ta = document.createElement("textarea");
        ta.value = selectedCode;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand("copy");
        ta.remove();
      }
      closeHistModal();
      closeScanModal();
      closeScanSelectModal();
      closeCheckinConfirmModal();
      showCodeCopied();
    } catch (e) {
      showMsg("error", "No se pudo copiar el código.");
    }
  });

  modal.addEventListener("click", (e) => {
    if (e.target === modal) closeModal();
  });

  histModal.addEventListener("click", (e) => {
    if (e.target === histModal) closeHistModal();
  });

  codeModal.addEventListener("click", (e) => {
    if (e.target === codeModal) closeCodeModal();
  });

  scanModal.addEventListener("click", (e) => {
    if (e.target === scanModal) closeScanModal();
  });

  scanSelectModal.addEventListener("click", (e) => {
    if (e.target === scanSelectModal) closeScanSelectModal();
  });

  checkinConfirmModal.addEventListener("click", (e) => {
    if (e.target === checkinConfirmModal) closeCheckinConfirmModal();
  });


  mobileMenu.addEventListener("click", (e) => {
    if (e.target === mobileMenu) closeMenu();
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      closeModal();
      closeHistModal();
      closeCodeModal();
      closeScanModal();
      closeScanSelectModal();
      closeCheckinConfirmModal();
      closeMenu();
    }
  });

  scanPersonList.addEventListener("click", () => {});

  scanManualBtn.addEventListener("click", () => {
    const code = (scanManualCode.value || "").trim();
    if (!code) {
      scanStatus.textContent = "Ingresa un código.";
      return;
    }
    handleScannedCode(code);
  });
  scanManualCode.addEventListener("keyup", (e) => {
    if (e.key === "Enter") {
      const code = (scanManualCode.value || "").trim();
      if (code) handleScannedCode(code);
    }
  });

  if (scanUploadBtn && scanUploadInput) {
    scanUploadBtn.addEventListener("click", () => {
      scanUploadInput.click();
    });
    scanUploadInput.addEventListener("change", async () => {
      const file = scanUploadInput.files && scanUploadInput.files[0];
      if (!file) return;
      scanStatus.textContent = "Leyendo imagen...";
      const ok = await ensureJsQr();
      const canUseBarcodeUpload = "BarcodeDetector" in window;
      if (!ok && !canUseBarcodeUpload) {
        scanStatus.textContent = "No se pudo cargar el lector de QR.";
        scanUploadInput.value = "";
        return;
      }
      const img = new Image();
      img.onload = async () => {
        let code = "";
        if (canUseBarcodeUpload) {
          try {
            const detector = new BarcodeDetector({ formats: ["qr_code"] });
            const results = await detector.detect(img);
            if (results && results.length > 0) {
              code = normalizeScannedCode(results[0].rawValue || "");
            }
          } catch (e) {}
        }
        if (!code && ok) {
          code = decodeImageWithJsQr(img);
        }
        if (!code) {
          try {
            const fd = new FormData();
            fd.append("qr_image", file);
            const res = await fetch("admin_api.php?action=decode_qr_image", {
              method: "POST",
              body: fd,
              headers: { Accept: "application/json" },
            });
            const data = await res.json().catch(() => ({}));
            if (res.ok && data && data.ok && data.code) {
              code = String(data.code).trim();
            }
          } catch (e) {}
        }
        if (code) {
          handleScannedCode(code.trim());
        } else {
          scanStatus.textContent = "No se detectó un QR en la imagen.";
        }
        scanUploadInput.value = "";
      };
      img.onerror = () => {
        scanStatus.textContent = "No se pudo leer la imagen.";
        scanUploadInput.value = "";
      };
      const reader = new FileReader();
      reader.onload = () => {
        img.src = reader.result;
      };
      reader.readAsDataURL(file);
    });
  }

  checkinConfirmList.addEventListener("click", (e) => {
    const yesBtn = e.target.closest("#checkin-yes-btn");
    const noBtn = e.target.closest("#checkin-no-btn");
    const yesMultiBtn = e.target.closest("#checkin-yes-multi-btn");
    if (noBtn) {
      closeCheckinConfirmModal();
      return;
    }
    if (yesBtn) {
      const pid = Number(yesBtn.getAttribute("data-confirm-persona"));
      if (pid) registerCheckin([pid]);
      return;
    }
    if (yesMultiBtn) {
      const ids = Array.from(checkinConfirmList.querySelectorAll("[data-confirm-persona]"))
        .filter((input) => input.checked && !input.disabled)
        .map((input) => Number(input.getAttribute("data-confirm-persona")));
      if (ids.length === 0) {
        showMsg("error", "Selecciona al menos una persona.");
        return;
      }
      registerCheckin(ids);
    }
  });


  sortHeaders.forEach((th) => {
    th.addEventListener("click", () => {
      const key = th.getAttribute("data-sort") || "";
      if (sortKey === key) {
        sortDir = sortDir === "asc" ? "desc" : "asc";
      } else {
        sortKey = key;
        sortDir = "asc";
      }
      currentPage = 1;
      sortHeaders.forEach((el) => el.classList.remove("active"));
      th.classList.add("active");
      renderUnidades();
    });
  });

  setCreateMode();

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    saveBtn.disabled = true;
    try {
      if (editingUnitId > 0) {
        const members = collectEditMembers();
        if (members.length === 0) {
          throw new Error("Debes agregar al menos una persona.");
        }
        const fd = new FormData(form);
        fd.append("unidad_id", String(editingUnitId));
        fd.append("tipo", tipo.value || "");
        fd.append("miembros_json", JSON.stringify(members));
        await saveInvitation(fd, "update_invitation");
      } else {
        await saveInvitation(new FormData(form));
      }
      closeModal();
    } catch (err) {
      showMsg("error", err.message || "Error al guardar.");
    } finally {
      saveBtn.disabled = false;
    }
  });

  loadState().catch((err) => {
    showMsg("error", err.message || "No se pudo cargar el panel.");
  });

  setInterval(() => {
    loadState().catch(() => {});
  }, 8000);

  document.addEventListener("visibilitychange", () => {
    if (!document.hidden) {
      loadState().catch(() => {});
    }
  });
})();
</script>
</body>
</html>


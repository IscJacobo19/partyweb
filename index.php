<?php
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
  || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
$scheme = $isHttps ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
$baseUrl = $scheme . '://' . $host . ($scriptDir !== '' ? $scriptDir : '');
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$pageUrl = $scheme . '://' . $host . $requestUri;
$previewImage = $baseUrl . '/assets/img/background.png';
?><!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Invitación</title>
  <meta property="og:type" content="website" />
  <meta property="og:title" content="Invitación Gaby & Jimmy" />
  <meta property="og:description" content="Te hago llegar esta invitación ❤️" />
  <meta property="og:url" content="<?php echo htmlspecialchars($pageUrl, ENT_QUOTES, 'UTF-8'); ?>" />
  <meta property="og:image" content="<?php echo htmlspecialchars($previewImage, ENT_QUOTES, 'UTF-8'); ?>" />
  <meta property="og:image:secure_url" content="<?php echo htmlspecialchars($previewImage, ENT_QUOTES, 'UTF-8'); ?>" />
  <meta property="og:image:type" content="image/png" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />
  <meta property="og:image:alt" content="Invitación Gaby & Jimmy" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="Invitación Gaby & Jimmy" />
  <meta name="twitter:description" content="Te hago llegar esta invitación ❤️" />
  <meta name="twitter:image" content="<?php echo htmlspecialchars($previewImage, ENT_QUOTES, 'UTF-8'); ?>" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Montserrat:wght@300;400;500;600;700&family=Libre+Baskerville:wght@400;700&family=Rochester&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/styles.css?v=3" />
</head>
<body>
  <main id="intro" class="screen screen--intro" aria-label="Pantalla de bienvenida">
    <div class="intro-bg"></div>
    <div class="intro-center">
      <button id="envelopeBtn" class="envelope" aria-label="Presiona la carta para continuar">
        <span class="envelope__shadow"></span>
        <span class="envelope__back"></span>
        <span class="envelope__paper">
          <span class="paper__text">Para ti...</span>
        </span>
        <span class="envelope__front"></span>
        <span class="envelope__flap"></span>
      </button>
      <p class="intro-hint">Presiona la carta para continuar</p>
    </div>
    <p class="intro-tip intro-tip--footer">Sube el volumen para una mejor experiencia</p>
  </main>

  <section id="loading" class="screen screen--loading is-hidden" aria-label="Cargando">
    <div class="loading-center">
      <div class="ring"></div>
      <div class="loading-text">¿?</div>
    </div>
  </section>

  <section id="invite" class="screen screen--invite is-hidden" aria-label="Invitación">
    <div class="invite-wrap">
      <article class="invite-scene" role="article" aria-label="Tarjeta de invitación">
        <div class="scene-overlay" aria-hidden="true"></div>
        <div class="scene-confetti scene-confetti--left" aria-hidden="true"></div>
        <div class="scene-confetti scene-confetti--right" aria-hidden="true"></div>

        <div class="scene-card">
          <div class="scene-bear" aria-hidden="true">
            <img src="assets/img/oso.png" alt="">
          </div>

          <div class="hero-wrap">
            <img class="hero-image" src="assets/img/background.png" alt="Invitación principal" />
          </div>

          <div class="scene-copy">
            <div class="honor-block">
              <span class="honor-line"></span>
              <span class="honor-label type" data-type data-text="HONORANDO"></span>
              <span class="honor-line"></span>
            </div>

            <div class="honor-names-svg">
              <svg viewBox="0 0 500 90" aria-hidden="true">
                <defs>
                  <linearGradient id="goldBorder" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#bfa14b" />
                    <stop offset="100%" stop-color="#ffe066" />
                  </linearGradient>
                </defs>
                <path d="M20,45 Q25,10 60,10 H440 Q475,10 480,45 Q475,80 440,80 H60 Q25,80 20,45 Z" fill="none" stroke="url(#goldBorder)" stroke-width="3" />
              </svg>
              <p class="honor-main">
                <span class="honor-name type" data-type data-text="Gaby"></span>
                <span class="honor-amp">&amp;</span>
                <span class="honor-name type" data-type data-text="Jimmy"></span>
              </p>
            </div>

            <p class="reveal-text">
              <span class="type" data-type data-text="Acompáñanos a"></span><br>
              <span class="reveal-main type" data-type data-text="LA REVELACIÓN DE GÉNERO"></span>
              <span class="reveal-sub type" data-type data-text="de nuestro bebé."></span>
            </p>

            <p class="event-info">
              <span class="type" data-type data-text="08-MARZO-2026 - 3:00 PM"></span><br>
              <span class="event-place type" data-type data-text="Salón El Trébol - Local B"></span>
            </p>

            <div class="scene-footer">
              <p class="assist-question type" data-type data-text="¿Nos ayudas a descubrirlo?"></p>

              <div class="card__actions">
                <button class="btn btn--ghost" id="btnLocation" type="button">
                  <span class="btn__icon" aria-hidden="true">&#128205;</span>
                  <span class="btn__label">Ubicación</span>
                </button>
                <button class="btn btn--solid" id="btnConfirm" type="button">
                  <span class="btn__icon" aria-hidden="true">&#10003;</span>
                  <span class="btn__label">Confirmar</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </article>
      <footer class="tiny">Jelly Dev - Invitación web</footer>
    </div>
  </section>

  <div id="modal" class="modal is-hidden" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal__backdrop" data-close></div>
    <div class="modal__card">
      <div class="modal__header">
        <h2 id="modalTitle">Confirmar asistencia</h2>
        <button class="iconbtn" data-close aria-label="Cerrar">&times;</button>
      </div>

      <form id="rsvpForm" class="form">
        <label>
          <span>Grupo o nombre</span>
          <select id="unidadSelect" required>
            <option value="">Selecciona una opción</option>
          </select>
        </label>

        <div id="miembrosWrap" class="is-hidden">
          <span class="group-label">Selecciona los miembros que asistirán</span>
          <div id="miembrosList" class="members-list"></div>
        </div>

        <label>
          <span>Código de confirmación</span>
          <input id="codigoInput" type="text" placeholder="Ingresa tu código" required />
        </label>

        <button class="btn btn--solid btn--full" type="submit">Guardar confirmación</button>
        <p id="formStatus" class="form__status" aria-live="polite"></p>
      </form>

      <section id="confirmExperience" class="confirm-exp is-hidden" aria-live="polite">
        <div id="confirmSpinner" class="modal-spinner"></div>
        <div id="confirmInfo" class="confirm-info is-hidden">
          <p id="confirmMainMessage" class="mi-sub">Nos alegra contar contigo en este momento especial.</p>
          <p id="confirmSecondaryMessage" class="mi-sub mi-sub--secondary">Te esperamos con mucho cariño.</p>
          <div id="confirmQrCard" class="mi-card qr-card">
            <span class="mi-label">Tu código QR</span>
            <img id="confirmQr" class="qr-img" alt="Código QR para tu confirmación" />
            <span id="confirmQrFallback" class="mi-value qr-fallback is-hidden"></span>
            <span class="mi-value">Guárdalo para presentarlo en la entrada.</span>
            <button id="downloadQrBtn" class="btn btn--solid btn--full qr-download" type="button">Descargar QR</button>
            <button id="btnCalendar" class="btn btn--ghost btn--full qr-download" type="button">Agregar al calendario</button>
          </div>
          <div id="confirmDressCode" class="mi-card">
            <div>
              <span class="mi-label">Código de vestimenta</span>
              <span class="mi-value">Café · Blanco · Negro</span>
            </div>
            <div>
              <span class="mi-label">Indicaciones</span>
              <span class="mi-value">Trae tu traje de baño</span>
            </div>
          </div>
          <p id="confirmCancelHint" class="mi-warning">
            Si necesitas cancelar tu asistencia, hazlo desde esta invitación retirando tu selección e ingresando tu código.
          </p>
          <p class="mi-warning subtle">
            Los cambios deben realizarse con al menos <strong>7 días de anticipación</strong>.
          </p>
          <p class="mi-promo">
            ¿Te gustó esta experiencia digital?<br>
            Cotiza tu invitación personalizada:
            <span class="mi-email">contacto@jelly-dev.com</span>
          </p>
        </div>
      </section>
    </div>
  </div>

  <div id="lockModal" class="modal is-hidden" role="dialog" aria-modal="true" aria-labelledby="lockModalTitle">
    <div class="modal__backdrop" data-lock-close></div>
    <div class="modal__card">
      <div class="modal__header">
        <h2 id="lockModalTitle">Confirmaciones cerradas</h2>
        <button class="iconbtn" data-lock-close aria-label="Cerrar">&times;</button>
      </div>
      <p class="lock-modal__text">
        Ya no puedes confirmar, ya pasó la fecha límite de confirmación.
      </p>
      <button id="lockModalOk" class="btn btn--solid btn--full" type="button">Entendido</button>
    </div>
  </div>


  <script src="assets/js/qrcode-generator.js?v=1"></script>
  <script src="assets/js/app.js?v=3"></script>
</body>
</html>

const CONFIG = {
  locationUrl: "https://maps.app.goo.gl/2tG1ZH92JrkJaf6aA",
  calendar: {
    title: "Revelación de sexo - Gaby & Jimmy",
    details: "Acompáñanos a la revelación de sexo de nuestro bebé.",
    location: "Salón El Trébol - Local B",
    timezone: "America/Mexico_City",
    startLocal: "20260308T150000",
    endLocal: "20260308T190000",
  },
  loadingMs: 1550,
  typeDelayMs: 38,
  typeDelayMsSmall: 22,
  musicUrl: "assets/musica/estrasenmicorazon.ogg",
  typingSoundUrl: "assets/musica/tecladoescribiendo.ogg",
};

const $ = (sel, root = document) => root.querySelector(sel);
const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

const intro = $("#intro");
const loading = $("#loading");
const invite = $("#invite");
const envelopeBtn = $("#envelopeBtn");

const btnLocation = $("#btnLocation");
const btnCalendar = $("#btnCalendar");
const btnConfirm = $("#btnConfirm");

const modal = $("#modal");
const rsvpForm = $("#rsvpForm");
const formStatus = $("#formStatus");
const unidadSelect = $("#unidadSelect");
const miembrosWrap = $("#miembrosWrap");
const miembrosList = $("#miembrosList");
const codigoInput = $("#codigoInput");
const modalTitle = $("#modalTitle");
const confirmExperience = $("#confirmExperience");
const confirmSpinner = $("#confirmSpinner");
const confirmInfo = $("#confirmInfo");
const confirmMainMessage = $("#confirmMainMessage");
const confirmSecondaryMessage = $("#confirmSecondaryMessage");
const confirmQr = $("#confirmQr");
const confirmQrFallback = $("#confirmQrFallback");
const confirmQrCard = $("#confirmQrCard");
const downloadQrBtn = $("#downloadQrBtn");

let qrLibPromise = null;
let lastQrDataUrl = "";
let lastQrCode = "";
let lastGuestName = "";

function ensureQrLib() {
  if (typeof window.qrcode === "function") return Promise.resolve(true);
  if (qrLibPromise) return qrLibPromise;
  qrLibPromise = new Promise((resolve) => {
    const script = document.createElement("script");
    script.src = "assets/js/qrcode-generator.js?v=1";
    script.async = true;
    script.onload = () => resolve(typeof window.qrcode === "function");
    script.onerror = () => resolve(false);
    document.head.appendChild(script);
  });
  return qrLibPromise;
}

function wrapText(ctx, text, x, y, maxWidth, lineHeight) {
  const words = String(text).split(" ");
  let line = "";
  let offsetY = 0;
  for (let i = 0; i < words.length; i += 1) {
    const testLine = line + words[i] + " ";
    const metrics = ctx.measureText(testLine);
    if (metrics.width > maxWidth && i > 0) {
      ctx.fillText(line.trim(), x, y + offsetY);
      line = words[i] + " ";
      offsetY += lineHeight;
    } else {
      line = testLine;
    }
  }
  if (line.trim()) {
    ctx.fillText(line.trim(), x, y + offsetY);
  }
  return offsetY + lineHeight;
}

async function buildPassImage() {
  if (!lastQrCode) return null;
  let qrSource = "";
  if (confirmQr && confirmQr.src) {
    qrSource = confirmQr.src;
  } else if (lastQrDataUrl) {
    qrSource = lastQrDataUrl;
  } else {
    const canGen = await ensureQrLib();
    if (canGen && typeof window.qrcode === "function") {
      const qr = window.qrcode(0, "H");
      qr.addData("confirmacion:" + lastQrCode);
      qr.make();
      qrSource = qr.createDataURL(12, 40);
    }
  }
  if (!qrSource) return null;

  const width = 1200;
  const height = 1680;
  const canvas = document.createElement("canvas");
  canvas.width = width;
  canvas.height = height;
  const ctx = canvas.getContext("2d");

  // Premium warm theme: coffee background + light beige card.
  const bg = ctx.createLinearGradient(0, 0, 0, height);
  bg.addColorStop(0, "#6f4f34");
  bg.addColorStop(0.55, "#5a3f2b");
  bg.addColorStop(1, "#4a3424");
  ctx.fillStyle = bg;
  ctx.fillRect(0, 0, width, height);

  const glow = ctx.createRadialGradient(width * 0.5, height * 0.12, 40, width * 0.5, height * 0.12, 560);
  glow.addColorStop(0, "rgba(255, 226, 173, 0.20)");
  glow.addColorStop(1, "rgba(255, 226, 173, 0)");
  ctx.fillStyle = glow;
  ctx.fillRect(0, 0, width, height);

  const cardX = 78;
  const cardY = 78;
  const cardW = width - 156;
  const cardH = height - 156;
  ctx.fillStyle = "#f6ead4";
  ctx.strokeStyle = "#e2cfa4";
  ctx.lineWidth = 2;
  const radius = 30;
  ctx.beginPath();
  if (typeof ctx.roundRect === "function") {
    ctx.roundRect(cardX, cardY, cardW, cardH, radius);
  } else {
    ctx.moveTo(cardX + radius, cardY);
    ctx.lineTo(cardX + cardW - radius, cardY);
    ctx.quadraticCurveTo(cardX + cardW, cardY, cardX + cardW, cardY + radius);
    ctx.lineTo(cardX + cardW, cardY + cardH - radius);
    ctx.quadraticCurveTo(cardX + cardW, cardY + cardH, cardX + cardW - radius, cardY + cardH);
    ctx.lineTo(cardX + radius, cardY + cardH);
    ctx.quadraticCurveTo(cardX, cardY + cardH, cardX, cardY + cardH - radius);
    ctx.lineTo(cardX, cardY + radius);
    ctx.quadraticCurveTo(cardX, cardY, cardX + radius, cardY);
  }
  ctx.fill();
  ctx.stroke();

  // Top label
  ctx.fillStyle = "#8a6a2b";
  ctx.font = "600 30px 'Segoe UI', Arial, sans-serif";
  ctx.textAlign = "center";
  ctx.fillText("PASE DIGITAL", width / 2, cardY + 86);

  ctx.fillStyle = "#3a2b1f";
  ctx.font = "700 68px 'Segoe UI', Arial, sans-serif";
  ctx.fillText(lastGuestName || "Invitado", width / 2, cardY + 190);

  ctx.fillStyle = "#5f4528";
  ctx.font = "700 56px 'Segoe UI', Arial, sans-serif";
  ctx.fillText("Gaby & Jimmy", width / 2, cardY + 286);

  ctx.fillStyle = "#6b5339";
  ctx.font = "600 42px 'Segoe UI', Arial, sans-serif";
  wrapText(ctx, "Revelación de sexo de nuestro bebé", width / 2, cardY + 350, 860, 50);

  ctx.fillStyle = "#4d3b2a";
  ctx.font = "700 44px 'Segoe UI', Arial, sans-serif";
  ctx.fillText("08-MARZO-2026 - 3:00 PM", width / 2, cardY + 496);
  ctx.font = "600 36px 'Segoe UI', Arial, sans-serif";
  ctx.fillText("Salón El Trébol - Local B", width / 2, cardY + 550);

  ctx.strokeStyle = "#dfc89e";
  ctx.lineWidth = 2;
  ctx.beginPath();
  ctx.moveTo(cardX + 72, cardY + 598);
  ctx.lineTo(cardX + cardW - 72, cardY + 598);
  ctx.stroke();

  const qrImg = new Image();
  qrImg.src = qrSource;
  await new Promise((resolve) => {
    qrImg.onload = resolve;
    qrImg.onerror = resolve;
  });

  const qrSize = 510;
  const qrX = (width - qrSize) / 2;
  const qrY = cardY + 658;
  ctx.fillStyle = "#ffffff";
  // Large quiet zone for reliable scan.
  ctx.fillRect(qrX - 66, qrY - 66, qrSize + 132, qrSize + 132);
  ctx.strokeStyle = "#c8b085";
  ctx.strokeRect(qrX - 52, qrY - 52, qrSize + 104, qrSize + 104);
  ctx.drawImage(qrImg, qrX, qrY, qrSize, qrSize);

  ctx.fillStyle = "#4b3726";
  ctx.font = "700 46px 'Segoe UI', Arial, sans-serif";
  ctx.fillText("Código: " + String(lastQrCode).toUpperCase(), width / 2, qrY + qrSize + 112);
  ctx.fillStyle = "#6d553d";
  ctx.font = "500 30px 'Segoe UI', Arial, sans-serif";
  ctx.fillText("Guarda este pase para presentarlo en la entrada.", width / 2, qrY + qrSize + 160);

  return canvas;
}
const confirmDressCode = $("#confirmDressCode");
const confirmCancelHint = $("#confirmCancelHint");

let unidades = [];
let selectedUnidad = null;
let bgMusic = null;
let typingSfx = null;

function show(el) {
  [intro, loading, invite].forEach((s) => s.classList.add("is-hidden"));
  el.classList.remove("is-hidden");
}

function sleep(ms) {
  return new Promise((r) => setTimeout(r, ms));
}

function playChime() {
  try {
    const ctx = new (window.AudioContext || window.webkitAudioContext)();
    const now = ctx.currentTime;

    const mkTone = (freq, start, dur, gainVal) => {
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.type = "sine";
      osc.frequency.setValueAtTime(freq, start);
      gain.gain.setValueAtTime(0.0001, start);
      gain.gain.exponentialRampToValueAtTime(gainVal, start + 0.01);
      gain.gain.exponentialRampToValueAtTime(0.0001, start + dur);
      osc.connect(gain).connect(ctx.destination);
      osc.start(start);
      osc.stop(start + dur + 0.02);
    };

    mkTone(880, now, 0.18, 0.08);
    mkTone(1175, now + 0.17, 0.22, 0.07);
    setTimeout(() => ctx.close?.(), 700);
  } catch (e) {}
}

function escapeIcsText(value) {
  return String(value || "")
    .replace(/\\/g, "\\\\")
    .replace(/\r?\n/g, "\\n")
    .replace(/,/g, "\\,")
    .replace(/;/g, "\\;");
}

function getBaseUrl() {
  const path = location.pathname.replace(/\/[^/]*$/, "");
  return location.origin + path;
}

function buildQrUrlFromCode(code) {
  if (!code) return "";
  const payload = "confirmacion:" + String(code).trim().toUpperCase();
  return getBaseUrl() + "/api/qr.php?size=420&data=" + encodeURIComponent(payload);
}

function buildCalendarDetails() {
  const c = CONFIG.calendar;
  const lines = [
    c.details,
    "Ubicación: " + c.location,
    "Mapa: " + CONFIG.locationUrl,
  ];
  if (lastQrCode) {
    const code = String(lastQrCode).trim().toUpperCase();
    const qrUrl = buildQrUrlFromCode(code);
    lines.push("Código de acceso: " + code);
    if (qrUrl) {
      lines.push("QR: " + qrUrl);
    }
  }
  return lines.join("\n");
}

function buildGoogleCalendarUrl() {
  const c = CONFIG.calendar;
  const details = buildCalendarDetails();
  const params = new URLSearchParams({
    action: "TEMPLATE",
    text: c.title,
    details: details,
    location: c.location,
    dates: c.startLocal + "/" + c.endLocal,
    ctz: c.timezone,
  });
  return "https://calendar.google.com/calendar/render?" + params.toString();
}

function downloadCalendarIcs() {
  const c = CONFIG.calendar;
  const details = buildCalendarDetails();
  const stamp = new Date().toISOString().replace(/[-:]/g, "").replace(/\.\d{3}Z$/, "Z");
  const uid = "inv-" + Date.now() + "@jelly-dev.com";
  const lines = [
    "BEGIN:VCALENDAR",
    "VERSION:2.0",
    "PRODID:-//Jelly Dev//Invitación//ES",
    "CALSCALE:GREGORIAN",
    "METHOD:PUBLISH",
    "BEGIN:VEVENT",
    "UID:" + uid,
    "DTSTAMP:" + stamp,
    "DTSTART;TZID=" + c.timezone + ":" + c.startLocal,
    "DTEND;TZID=" + c.timezone + ":" + c.endLocal,
    "SUMMARY:" + escapeIcsText(c.title),
    "DESCRIPTION:" + escapeIcsText(details),
    "LOCATION:" + escapeIcsText(c.location),
    "BEGIN:VALARM",
    "ACTION:DISPLAY",
    "TRIGGER:-P7D",
    "DESCRIPTION:Recordatorio (1 semana): " + escapeIcsText(c.title),
    "END:VALARM",
    "BEGIN:VALARM",
    "ACTION:DISPLAY",
    "TRIGGER:-P1D",
    "DESCRIPTION:Recordatorio (1 día): " + escapeIcsText(c.title),
    "END:VALARM",
    "END:VEVENT",
    "END:VCALENDAR",
  ];
  const blob = new Blob([lines.join("\r\n")], { type: "text/calendar;charset=utf-8" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = "recordatorio-gaby-jimmy.ics";
  document.body.appendChild(a);
  a.click();
  a.remove();
  setTimeout(() => URL.revokeObjectURL(url), 1200);
}

function addCalendarReminder() {
  const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
  if (isIOS) {
    downloadCalendarIcs();
    return;
  }
  const googleWin = window.open(buildGoogleCalendarUrl(), "_blank", "noopener,noreferrer");
  if (!googleWin) {
    downloadCalendarIcs();
  }
}

function startBackgroundMusic() {
  try {
    if (!bgMusic) {
      bgMusic = new Audio(CONFIG.musicUrl);
      bgMusic.loop = true;
      bgMusic.volume = 0.05;
    }
    bgMusic.currentTime = 0;
    bgMusic.play().catch(() => {});
  } catch (e) {}
}

function startTypingSound() {
  try {
    if (!typingSfx) {
      typingSfx = new Audio(CONFIG.typingSoundUrl);
      typingSfx.loop = true;
      typingSfx.volume = 0.02;
    }
    if (typingSfx.paused) {
      typingSfx.currentTime = 0;
      typingSfx.play().catch(() => {});
    }
  } catch (e) {}
}

function stopTypingSound() {
  try {
    if (!typingSfx) return;
    typingSfx.pause();
    typingSfx.currentTime = 0;
  } catch (e) {}
}

async function startFlow() {
  envelopeBtn.classList.add("is-open");
  playChime();
  startBackgroundMusic();
  await sleep(650);
  show(loading);
  await sleep(CONFIG.loadingMs);
  show(invite);
  invite.classList.add("is-typing-phase");
  startTypingSound();
  await sleep(180);
  await runTypewriter();
  invite.classList.remove("is-typing-phase");
}

async function runTypewriter() {
  startTypingSound();
  const nodes = $$("[data-type]");
  try {
    for (const node of nodes) {
      const txt = node.getAttribute("data-text") || "";
      node.textContent = "";
      node.classList.add("is-typing");
      const delay = txt.length <= 6 ? CONFIG.typeDelayMsSmall : CONFIG.typeDelayMs;
      for (let i = 0; i < txt.length; i++) {
        node.textContent += txt[i];
        await sleep(delay);
      }
      node.classList.remove("is-typing");
      await sleep(120);
    }
  } finally {
    stopTypingSound();
  }
}

function setStatus(message, isError) {
  formStatus.textContent = message;
  formStatus.style.color = isError ? "#9c2d2d" : "rgba(106,76,59,.85)";
}

function formatNames(names) {
  if (!Array.isArray(names) || names.length === 0) return "";
  if (names.length === 1) return names[0];
  if (names.length === 2) return names[0] + " y " + names[1];
  return names.slice(0, -1).join(", ") + " y " + names[names.length - 1];
}

function buildResultMessage(unidad, selectedIds) {
  const personas = Array.isArray(unidad?.personas) ? unidad.personas : [];
  const confirmed = personas
    .filter((p) => selectedIds.includes(Number(p.id)))
    .map((p) => p.nombre);
  const cancelled = personas
    .filter((p) => !selectedIds.includes(Number(p.id)))
    .map((p) => p.nombre);

  if (confirmed.length === 0) {
    return {
      title: "Asistencia cancelada",
      main:
        personas.length > 1
          ? "Lamentamos que no podrán asistir."
          : "Lamentamos que no puedas asistir.",
      secondary: "Esperamos coincidir en una próxima celebración.",
      fullCancel: true,
    };
  }

  if (cancelled.length > 0) {
    return {
      title: "Registro actualizado",
      main:
        cancelled.length === 1
          ? "Lamentamos que " +
            formatNames(cancelled) +
            " no pueda asistir, con gusto estaremos esperando a los demás invitados."
          : "Lamentamos que " +
            formatNames(cancelled) +
            " no puedan asistir, con gusto estaremos esperando a los demás invitados.",
      secondary: "Gracias por confirmar a " + formatNames(confirmed) + ".",
      fullCancel: false,
    };
  }

  return {
    title: "Registro actualizado",
    main:
      confirmed.length > 1
        ? "Nos alegra que puedan acompañarnos en este momento especial."
        : "Nos alegra contar contigo en este momento especial.",
    secondary: "Te esperamos con mucho cariño.",
    fullCancel: false,
  };
}

function resetModal() {
  rsvpForm.classList.remove("is-hidden");
  formStatus.textContent = "";
  codigoInput.value = "";
  miembrosWrap.classList.add("is-hidden");
  miembrosList.innerHTML = "";
  selectedUnidad = null;
  unidadSelect.innerHTML = "";

  if (modalTitle) modalTitle.textContent = "Confirmar asistencia";
  if (confirmExperience) confirmExperience.classList.add("is-hidden");
  if (confirmSpinner) confirmSpinner.classList.remove("is-hidden");
  if (confirmInfo) confirmInfo.classList.add("is-hidden");
  if (confirmMainMessage) {
    confirmMainMessage.textContent = "Nos alegra contar contigo en este momento especial.";
  }
  if (confirmSecondaryMessage) {
    confirmSecondaryMessage.textContent = "Te esperamos con mucho cariño.";
  }
  if (confirmQrCard) confirmQrCard.classList.add("is-hidden");
  if (confirmDressCode) confirmDressCode.classList.remove("is-hidden");
  if (confirmCancelHint) confirmCancelHint.classList.remove("is-hidden");

  const first = document.createElement("option");
  first.value = "";
  first.textContent = "Selecciona una opción";
  unidadSelect.appendChild(first);

  unidades.forEach((unidad) => {
    const option = document.createElement("option");
    option.value = String(unidad.id);
    option.textContent = unidad.nombre;
    unidadSelect.appendChild(option);
  });
}

function renderMiembros(unidad) {
  if (!unidad || !Array.isArray(unidad.personas) || unidad.personas.length === 0) {
    miembrosWrap.classList.add("is-hidden");
    miembrosList.innerHTML = "";
    return;
  }

  miembrosList.innerHTML = "";
  unidad.personas.forEach((persona) => {
    const label = document.createElement("label");
    const check = document.createElement("input");
    check.type = "checkbox";
    check.className = "miembro-check";
    check.value = String(persona.id);
    check.checked = persona.asistencia === 1;
    label.appendChild(check);
    label.appendChild(document.createTextNode(persona.nombre));
    miembrosList.appendChild(label);
  });
  miembrosWrap.classList.remove("is-hidden");
}

async function loadUnidades() {
  try {
    const res = await fetch("api/invitaciones.php", { headers: { Accept: "application/json" } });
    const data = await res.json();
    if (!data.ok || !Array.isArray(data.unidades)) throw new Error("Lista inválida");
    unidades = data.unidades;
  } catch (err) {
    unidades = [];
    setStatus("No se pudo cargar la lista de invitados.", true);
  }
}

function openModal() {
  modal.classList.remove("is-hidden");
  loadUnidades().then(resetModal);
}

function closeModal() {
  modal.classList.add("is-hidden");
  resetModal();
}

async function submitConfirmacion(e) {
  e.preventDefault();
  if (!selectedUnidad) {
    setStatus("Selecciona tu grupo o nombre.", true);
    return;
  }

  const codigo = codigoInput.value.trim();
  if (!codigo) {
    setStatus("Ingresa el código de confirmación.", true);
    return;
  }

  const miembros = Array.from(miembrosList.querySelectorAll(".miembro-check:checked")).map((n) =>
    Number(n.value),
  );

  setStatus("Guardando...", false);

  try {
    const res = await fetch("api/rsvp.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        unidad_id: selectedUnidad.id,
        codigo: codigo,
        miembros: miembros,
      }),
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok || !data.ok) throw new Error(data.message || "No se pudo guardar.");
    setStatus(data.message || "Confirmación guardada correctamente.", false);

    const resultMessage = buildResultMessage(selectedUnidad, miembros);
    if (modalTitle) modalTitle.textContent = resultMessage.title;
    if (confirmMainMessage) confirmMainMessage.textContent = resultMessage.main;
    if (confirmSecondaryMessage) confirmSecondaryMessage.textContent = resultMessage.secondary;
    if (confirmDressCode) {
      confirmDressCode.classList.toggle("is-hidden", !!resultMessage.fullCancel);
    }
    if (confirmCancelHint) {
      confirmCancelHint.classList.toggle("is-hidden", !!resultMessage.fullCancel);
    }

    rsvpForm.classList.add("is-hidden");
    if (confirmExperience) confirmExperience.classList.remove("is-hidden");
    if (confirmSpinner) confirmSpinner.classList.remove("is-hidden");
    if (confirmInfo) confirmInfo.classList.add("is-hidden");

    await sleep(1400);
    if (confirmSpinner) confirmSpinner.classList.add("is-hidden");
    if (confirmInfo) confirmInfo.classList.remove("is-hidden");
    if (confirmQr && !resultMessage.fullCancel) {
      const qrData = "confirmacion:" + codigo;
      confirmQr.style.display = "block";
      confirmQr.removeAttribute("src");
      confirmQr.referrerPolicy = "no-referrer";

      if (confirmQrFallback) {
        confirmQrFallback.textContent = "Código: " + codigo;
        confirmQrFallback.classList.add("is-hidden");
      }
      if (confirmQrCard) confirmQrCard.classList.remove("is-hidden");
      if (downloadQrBtn) {
        downloadQrBtn.classList.remove("is-hidden");
        downloadQrBtn.dataset.qrFilename = "QR-" + codigo + ".png";
      }
      lastQrCode = codigo;
      lastGuestName = (selectedUnidad && selectedUnidad.nombre) ? selectedUnidad.nombre : "";
      const qrUrl = "api/qr.php?size=420&data=" + encodeURIComponent(qrData) + "&_t=" + Date.now();

      try {
        confirmQr.src = qrUrl;
        lastQrDataUrl = qrUrl;
      } catch (e) {
        confirmQr.src = qrUrl;
        lastQrDataUrl = qrUrl;
      }

      confirmQr.onerror = () => {
        ensureQrLib().then((hasLib) => {
          if (!hasLib || typeof window.qrcode !== "function") {
            confirmQr.style.display = "none";
            if (confirmQrFallback) confirmQrFallback.classList.remove("is-hidden");
            if (downloadQrBtn) downloadQrBtn.classList.add("is-hidden");
            return;
          }
          try {
            const qr = window.qrcode(0, "H");
            qr.addData(qrData);
            qr.make();
            confirmQr.src = qr.createDataURL(12, 48);
            lastQrDataUrl = confirmQr.src;
          } catch (err) {
            confirmQr.style.display = "none";
            if (confirmQrFallback) confirmQrFallback.classList.remove("is-hidden");
            if (downloadQrBtn) downloadQrBtn.classList.add("is-hidden");
          }
        });
      };
      confirmQr.onload = () => {
        if (!lastQrDataUrl) {
          lastQrDataUrl = confirmQr.src;
        }
      };
    } else if (confirmQrCard) {
      confirmQrCard.classList.add("is-hidden");
      if (downloadQrBtn) downloadQrBtn.classList.add("is-hidden");
      lastQrDataUrl = "";
      lastQrCode = "";
      lastGuestName = "";
    }
  } catch (err) {
    setStatus(err.message || "No se pudo guardar la confirmación.", true);
  }
}

function init() {
  const params = new URLSearchParams(location.search);
  if (params.get("skip") === "1") {
    show(invite);
    invite.classList.add("is-typing-phase");
    runTypewriter().finally(() => {
      invite.classList.remove("is-typing-phase");
    });
  } else {
    show(intro);
  }

  envelopeBtn.addEventListener("click", startFlow, { once: true });

  btnLocation.addEventListener("click", () => {
    window.open(CONFIG.locationUrl, "_blank", "noopener,noreferrer");
  });

  if (btnCalendar) {
    btnCalendar.addEventListener("click", addCalendarReminder);
  }

  btnConfirm.addEventListener("click", openModal);
  modal.addEventListener("click", (e) => {
    if (e.target && e.target.matches("[data-close], .modal__backdrop")) closeModal();
  });
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && !modal.classList.contains("is-hidden")) closeModal();
  });

  unidadSelect.addEventListener("change", () => {
    const id = Number(unidadSelect.value);
    selectedUnidad = unidades.find((u) => u.id === id) || null;
    renderMiembros(selectedUnidad);
    formStatus.textContent = "";
  });

  rsvpForm.addEventListener("submit", submitConfirmacion);

  if (downloadQrBtn) {
    downloadQrBtn.addEventListener("click", async () => {
      const canvas = await buildPassImage();
      if (!canvas) return;
      const filename = downloadQrBtn.dataset.qrFilename || "pase.png";
      const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
      const isAndroid = /Android/i.test(navigator.userAgent);
      const dataUrl = canvas.toDataURL("image/png");

      if (isIOS || isAndroid) {
        if (canvas.toBlob && navigator.canShare && navigator.share) {
          canvas.toBlob(async (blob) => {
            if (!blob) return;
            const file = new File([blob], filename, { type: "image/png" });
            try {
              if (navigator.canShare({ files: [file] })) {
                await navigator.share({
                  files: [file],
                  title: "Pase digital",
                  text: "Tu pase digital",
                });
                return;
              }
            } catch (e) {}
            const url = URL.createObjectURL(blob);
            window.open(url, "_blank");
            setTimeout(() => URL.revokeObjectURL(url), 1500);
          }, "image/png");
        } else {
          window.open(dataUrl, "_blank");
        }
        return;
      }

      let frame = document.getElementById("download-frame");
      if (!frame) {
        frame = document.createElement("iframe");
        frame.id = "download-frame";
        frame.name = "download-frame";
        frame.style.display = "none";
        document.body.appendChild(frame);
      }

      const form = document.createElement("form");
      form.method = "POST";
      form.action = "api/download_pass.php";
      form.target = "download-frame";

      const inputImg = document.createElement("input");
      inputImg.type = "hidden";
      inputImg.name = "image";
      inputImg.value = dataUrl;
      form.appendChild(inputImg);

      const inputName = document.createElement("input");
      inputName.type = "hidden";
      inputName.name = "filename";
      inputName.value = filename;
      form.appendChild(inputName);

      document.body.appendChild(form);
      form.submit();
      form.remove();
    });
  }

}

init();

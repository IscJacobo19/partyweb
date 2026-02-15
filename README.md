# Invitacion Web (PHP + JS)

Proyecto de invitacion web con flujo de confirmacion (RSVP), codigo QR, panel admin y check-in por escaneo.

## Requisitos
- PHP 8.1+ (probado con PHP 8.3)
- MySQL/MariaDB
- HTTPS en produccion (requerido para camara en navegador)

## Estructura principal
- `index.php`: experiencia del invitado (intro, invitacion, modal RSVP, QR, calendario)
- `admin.php`: panel de administracion (invitaciones, historial, escaneo/check-in)
- `admin_api.php`: API interna del admin
- `api/invitaciones.php`: lista de unidades/personas activas para el invitado
- `api/rsvp.php`: guarda confirmaciones/cancelaciones
- `api/qr.php`: genera imagen PNG de QR
- `api/download_pass.php`: descarga imagen del pase generado en frontend
- `admin_auth.php`: credenciales/sesion de admin

## Funcionalidades del invitado
- Carta de entrada con animacion y musica.
- Confirmacion de asistencia por codigo.
- Generacion y visualizacion de QR de confirmacion.
- Descarga del pase digital (imagen con QR).
- Boton "Agregar al calendario" en el modal del QR:
  - Google Calendar (web)
  - Archivo `.ics` (iOS y fallback)
  - Incluye 2 recordatorios: 1 semana y 1 dia antes.
  - Si existe codigo, agrega codigo y URL del QR en la descripcion.

## Funcionalidades del admin
- Dashboard con KPI:
  - Total personas invitadas
  - Total confirmados
  - Total pendientes
  - Total asistidos
- Los cards KPI son clickeables y consultan base de datos para filtrar tabla:
  - `all`
  - `confirmados`
  - `pendientes`
  - `asistidos`
- Alta de invitaciones (persona/familia) con codigo unico.
- Historial de acciones (confirmo/cancelo/asistio).
- Escaneo QR:
  - Camara en vivo (BarcodeDetector + fallback jsQR)
  - Carga de imagen
  - Fallback de decodificacion en servidor (`decode_qr_image`)
  - Entrada manual de codigo.

## Endpoints admin relevantes
- `admin_api.php?action=state&kpi_filter=all|confirmados|pendientes|asistidos`
- `admin_api.php?action=lookup_code&codigo=XXXX`
- `admin_api.php?action=checkin` (POST)
- `admin_api.php?action=decode_qr_image` (POST multipart, campo `qr_image`)

## Configuracion rapida
1. Configura DB en `api/db.php` (o segun tu flujo de entorno).
2. Verifica `.env.local` / `.env.production` segun despliegue.
3. Ajusta textos y datos del evento en:
   - `index.php` (contenido visible)
   - `assets/js/app.js` (`CONFIG`)

## Desarrollo local
- Laragon (Windows):
  - Ejemplo PHP local: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe`
- Recomendado:
  - Probar con `?v=...` para evitar cache.
  - Verificar permisos de camara en HTTPS.

## Despliegue y cache
Al subir cambios de frontend, prueba con query string:
- `index.php?v=YYYYMMDD-a`
- `admin?v=YYYYMMDD-a`
- `assets/js/app.js?v=YYYYMMDD-a`
- `assets/css/styles.css?v=YYYYMMDD-a`

## Flujo de ramas y versionado
- Ramas permanentes:
  - `main`: produccion estable.
  - `QA`: validacion previa a produccion.
  - `Desarrollo`: integracion de cambios.
- Ramas temporales:
  - `feature/*`: una rama por cambio puntual. Se crea desde `Desarrollo` y se elimina al terminar.
- Flujo recomendado:
  1. Crear `feature/*` desde `Desarrollo`.
  2. Merge `feature/*` -> `Desarrollo`.
  3. Merge `Desarrollo` -> `QA`.
  4. Merge `QA` -> `main`.
  5. Crear tag en `main` al desplegar.

### Versiones (tags)
- No crear una rama por version.
- Cada despliegue a produccion debe tener un tag.
- Formato recomendado: SemVer (`vMAJOR.MINOR.PATCH`):
  - `PATCH` (`v1.0.1`): correcciones pequenas sin romper compatibilidad.
  - `MINOR` (`v1.1.0`): nuevas funcionalidades compatibles.
  - `MAJOR` (`v2.0.0`): cambios que rompen compatibilidad.

Comandos base:
```bash
git checkout main
git pull
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin v1.0.0
```

## Seguridad
- Cambia credenciales de admin en `admin_auth.php`.
- No compartas usuario/contrasena en canales no seguros.
- Mantener HTTPS activo en produccion.

## Nota de compatibilidad QR
Para mejor lectura:
- Mantener QR con alto contraste.
- Evitar compresion agresiva de imagen.
- Usar el flujo actual de escaneo (ya incluye fallback cliente + servidor).

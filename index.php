<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitación Revelación de Género</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&family=Lora:wght@700&family=Poppins:wght@400;700&family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div id="envelope-screen">
        <div class="envelope-outer" id="envelope">
            <img src="img/tarjeta-sobre-marron.png" alt="Sobre" class="envelope-img" loading="eager">
            <div class="envelope-text-img inside">Para ti...</div>
        </div>
        <div class="envelope-msg">Haz clic en la carta para abrir</div>
    </div>
    <div id="preloader" style="display:none;">
        <div class="spinner"></div>
        <div class="preloader-msg">¿?</div>
    </div>
    <div class="invitation-container" style="display:none;">
        <div class="bear" style="display:none;"><img src="img/Oso.png" alt="Oso decorativo"></div>
        <div class="inv-content-wrapper">
            <div class="main-title" id="main-title"><span id="typewriter"></span></div>
            <div class="subtitle-script">¿lo descubrimos juntos?</div>
            <div id="main-content" style="opacity:0;transition:opacity 0.7s;display:none;">
                <p class="inv-text">Acompáñanos a<br><span class="bold">LA REVELACIÓN DE GÉNERO</span><br>de nuestro bebé.</p>
                <h3>Gaby & Jimmy</h3>
                <div class="details">
                    <p><span class="bold">08 Marzo</span></p>
                    <p>3:00 p.m.</p>
                    <p>"EL TREBOL" local B</p>
                </div>
                <div class="dresscode">
                    <p><span class="bold">CÓDIGO DE VESTIMENTA:</span> Beige, café o blanco</p>
                    <p>Lleva tu traje de baño</p>
                </div>
                <div class="confirm-section">
                    <span class="bold">Confirma tu asistencia:</span><br>
                    <button id="confirm-btn">Confirma tu asistencia</button>
                    <form id="confirm-form" style="display:none;" method="POST" action="confirm.php">
                        <input type="text" name="codigo" placeholder="Ingresa tu código" required>
                        <button type="submit">Confirmar</button>
                    </form>
                    <div id="msg"></div>
                </div>
                <div class="stars">
                    <div class="star" style="top:30px;left:20px;"></div>
                    <div class="star" style="top:80px;right:30px;"></div>
                    <div class="star" style="top:220px;left:40px;"></div>
                    <div class="star" style="top:320px;right:50px;"></div>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/script.js"></script>
    <script>
    // Forzar que el oso esté detrás y solo aparezca tras la animación
    document.addEventListener('DOMContentLoaded', function() {
        const bear = document.querySelector('.bear');
        const mainContent = document.getElementById('main-content');
        if (bear && mainContent) {
            const observer = new MutationObserver(() => {
                if (mainContent.style.opacity === '1') {
                    bear.style.display = 'flex';
                    bear.style.zIndex = '0';
                }
            });
            observer.observe(mainContent, { attributes: true, attributeFilter: ['style'] });
        }
    });
    </script>
</body>
</html>

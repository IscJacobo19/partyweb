

// Carta animada: al hacer clic, spinner y luego animación máquina de escribir para '¿Niño o niña?'
const envelope = document.getElementById('envelope');
const envelopeScreen = document.getElementById('envelope-screen');
const preloader = document.getElementById('preloader');
const mainTitle = document.getElementById('main-title');
const typewriter = document.getElementById('typewriter');
const mainContent = document.getElementById('main-content');

function typeWriterEffect(text, element, speed, callback) {
    let i = 0;
    function typing() {
        if (i < text.length) {
            element.innerHTML += text.charAt(i);
            i++;
            setTimeout(typing, speed);
        } else if (callback) {
            callback();
        }
    }
    element.innerHTML = '';
    typing();
}

envelope.onclick = function() {
    envelopeScreen.style.display = 'none';
    preloader.style.display = 'flex';
    setTimeout(() => {
        preloader.style.display = 'none';
        document.querySelector('.invitation-container').style.display = 'block';
        mainTitle.style.display = 'flex';
        typeWriterEffect('¿Niño o niña?', typewriter, 90, () => {
            setTimeout(() => {
                mainContent.style.display = 'block';
                setTimeout(()=>{
                    mainContent.style.opacity = 1;
                }, 100);
            }, 1200);
        });
    }, 2000);
};

// Confirmación
document.getElementById('confirm-btn').onclick = function() {
    document.getElementById('confirm-form').style.display = 'block';
    this.style.display = 'none';
};

// Hago que todo el código se ejecute solo al cargar la página y que las variables queden privadas
(() => {

    // Activo el modo estricto para que el navegador me avise de errores típicos
    'use strict';

    // Guardo cuánto va a durar la pantalla de carga antes de cambiar de página (2,8 segundos)
    const DURACION = 2800;

    // Guardo los tres mensajes que voy a mostrar mientras carga
    const MENSAJES = [
        'Verificando credenciales…',
        'Preparando tu espacio…',
        '¡Todo listo!'
    ];

    // Busco el formulario que está dentro de la tarjeta de login
    const form = document.getElementById('login-form') || document.querySelector('.login-card form');

    // Si la página no tiene ese formulario, paro aquí para que no dé error
    if (!form) return;

    // Hago que todos los campos sean obligatorios para que el navegador los valide antes de cargar
    form.querySelectorAll('input').forEach(input => { input.required = true; });

    // Creo un div nuevo para la pantalla de carga (todavía no se ve)
    const pantalla = document.createElement('div');

    // Le pongo su clase para darle estilo con CSS
    pantalla.className = 'pantalla-carga';

    // Le digo a los lectores de pantalla que es un aviso de estado
    pantalla.setAttribute('role', 'status');

    // Hago que lean los cambios de texto sin interrumpir lo que estén leyendo
    pantalla.setAttribute('aria-live', 'polite');

    // Mientras esté oculta, los lectores de pantalla la ignoran
    pantalla.setAttribute('aria-hidden', 'true');

    // Meto dentro el contenido: un spinner, el mensaje (empieza con el primero) y una barra de progreso
    pantalla.innerHTML = `
        <div class="carga-contenido">
            <div class="carga-spinner">
            </div>
            <p class="carga-mensaje">${MENSAJES[0]}</p>
            <div class="carga-barra"><span class="carga-barra-progreso"></span></div>
        </div>
    `;

    // Añado la pantalla de carga a la página (sigue oculta hasta que el CSS la muestre)
    document.body.appendChild(pantalla);

    // Guardo el párrafo del mensaje para poder cambiarle el texto después
    const mensaje = pantalla.querySelector('.carga-mensaje');

    // Creo la función que muestra la carga y va cambiando los mensajes
    function mostrarCarga() {

        // Le pongo la clase "visible" para que el CSS la enseñe
        pantalla.classList.add('visible');

        // Ahora sí dejo que los lectores de pantalla la lean
        pantalla.setAttribute('aria-hidden', 'false');

        // Recorro los mensajes uno a uno
        MENSAJES.forEach((texto, i) => {

            // Cambio el texto cada cierto tiempo: a los 0 ms, a los 933 ms y a los 1867 ms
            setTimeout(() => { mensaje.textContent = texto; }, i * (DURACION / MENSAJES.length));
        });
    }

    // Cuando se envía el formulario, hago lo siguiente
    form.addEventListener('submit', (e) => {

        // Cancelo el envío normal para que la página no se recargue
        e.preventDefault();

        // Miro qué botón he pulsado (estudiante, profesor o técnico) y cojo su página de destino
        const destino = e.submitter?.getAttribute('formaction') || form.getAttribute('action');

        // Desactivo todos los botones para que no se pueda hacer doble clic
        form.querySelectorAll('button').forEach(btn => { btn.disabled = true; });

        // Muestro la pantalla de carga
        mostrarCarga();

        // Cuando pasan los 2,8 segundos, llevo al usuario a la página de destino
        setTimeout(() => { window.location.href = destino; }, DURACION);
    });

})();

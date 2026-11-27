// Tema claro/oscuro con preferencia guardada en localStorage.
// Se carga en <head> SIN defer para aplicar el tema antes de pintar la página
// (así no hay un destello del tema claro al recargar).
(function () {
    'use strict';

    const CLAVE = 'clicarte-tema';
    const raiz = document.documentElement;

    function leerTema() {
        try {
            return localStorage.getItem(CLAVE);
        } catch (e) {
            return null; // navegación privada o almacenamiento bloqueado
        }
    }

    function guardarTema(tema) {
        try {
            localStorage.setItem(CLAVE, tema);
        } catch (e) {
            /* si no se puede guardar, el tema solo dura hasta recargar */
        }
    }

    function aplicarTema(tema) {
        if (tema === 'dark') {
            raiz.dataset.theme = 'dark';
        } else {
            raiz.removeAttribute('data-theme');
        }
    }

    // 1. Antes de pintar: aplicar la preferencia guardada.
    aplicarTema(leerTema());

    // 2. Cuando exista el botón: sincronizar su estado y escuchar los clics.
    document.addEventListener('DOMContentLoaded', function () {
        const boton = document.getElementById('theme-toggle');
        if (!boton) return;

        const icono = boton.querySelector('i');

        function pintarBoton() {
            const oscuro = raiz.dataset.theme === 'dark';
            // Nombre accesible fijo ("Tema oscuro") y estado con aria-pressed.
            boton.setAttribute('aria-pressed', String(oscuro));
            if (icono) {
                icono.className = oscuro ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
            }
        }

        boton.addEventListener('click', function () {
            const nuevo = raiz.dataset.theme === 'dark' ? 'light' : 'dark';
            aplicarTema(nuevo);
            guardarTema(nuevo);
            pintarBoton();
        });

        pintarBoton();
    });
})();

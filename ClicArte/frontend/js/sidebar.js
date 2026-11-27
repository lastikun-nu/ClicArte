// Menú lateral compartido: genera los enlaces según el perfil, marca la página
// activa (clase .activo + aria-current="page") y crea el botón de plegar/desplegar.
(function () {
    'use strict';

    const baseUrl = new URL('../', document.currentScript.src);
    const MOVIL = window.matchMedia('(max-width: 760px)');

    const menuItems = {
        alumno: [
            ['dashboard', 'comun/dashboard.html', 'dashboard.png', 'Dashboard'],
            ['equipos', 'alumno/misEquipos.html', 'equipo.png', 'Equipos'],
            ['historial', 'alumno/misIncidencias.html', 'grafico-de-barras.png', 'Mi Historial'],
            ['incidencias', 'alumno/formularioTicket.html', 'incidencia.png', 'Incidencias/Solicitudes']
        ],
        profesor: [
            ['dashboard', 'comun/dashboard.html', 'dashboard.png', 'Dashboard'],
            ['equipos', 'profe/profesorEquipos.html', 'equipo.png', 'Equipos'],
            ['alumnos', 'profe/gestionAlumnos.html', 'alumno.png', 'Alumnos y profesores'],
            ['aulas', 'profe/gestionAulas.html', 'aula.png', 'Aulas'],
            ['incidencias', 'profe/profesorIncidencias.html', 'incidencia.png', 'Incidencias/Solicitudes']
        ],
        tecnico: [
            ['dashboard', 'comun/dashboard.html', 'dashboard.png', 'Dashboard'],
            ['equipos', 'tecnico/tecnicoEquipos.html', 'equipo.png', 'Equipos'],
            ['alumnos', 'profe/gestionAlumnos.html', 'alumno.png', 'Alumnos y profesores'],
            ['aulas', 'profe/gestionAulas.html', 'aula.png', 'Aulas'],
            ['incidencias', 'tecnico/tecnicoIncidencias.html', 'incidencia.png', 'Incidencias/Solicitudes']
        ]
    };

    // Las vistas comunes (dashboard, detalle y nuevo ticket) usan data-role="auto":
    // se muestra el menú del perfil con el que se inició sesión (lo guarda login.js).
    function rolActual(nav) {
        if (nav.dataset.role !== 'auto') return nav.dataset.role;
        let rol = null;
        try {
            rol = sessionStorage.getItem('clicarte-rol');
        } catch (e) { /* sin almacenamiento: perfil por defecto */ }
        return menuItems[rol] ? rol : 'alumno';
    }

    document.querySelectorAll('.sidebar-nav[data-role][data-active]').forEach(function (nav) {
        const items = menuItems[rolActual(nav)];
        if (!items) return;

        items.forEach(function ([page, href, icon, label]) {
            const link = document.createElement('a');
            link.href = new URL(href, baseUrl);
            link.innerHTML = `<img src="${new URL(`IMGBLANCO/${icon}`, baseUrl)}" alt=""><span data-i18n="${page}">${label}</span>`;

            if (page === nav.dataset.active) {
                link.classList.add('activo');
                link.setAttribute('aria-current', 'page');
            }

            nav.append(link);
        });
    });

    // Botón de plegar/desplegar el menú.
    const nav = document.querySelector('.sidebar-nav');
    if (!nav) return;

    const sidebar = nav.closest('.sidebar') || nav.parentElement;
    if (!sidebar || sidebar.querySelector('.sidebar-toggle')) return;

    const appShell = sidebar.closest('.app-shell');
    if (!nav.id) nav.id = 'menu-principal';

    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'sidebar-toggle';
    button.setAttribute('aria-controls', nav.id);
    // Detrás del logotipo: así el orden del foco coincide con el orden visual.
    sidebar.insertBefore(button, nav);

    function aplicar(plegado) {
        sidebar.classList.toggle('sidebar-collapsed', plegado);
        if (appShell) appShell.classList.toggle('sidebar-collapsed', plegado);
        button.textContent = plegado ? '☰' : '✕';
        button.setAttribute('aria-expanded', String(!plegado));
        // En móvil el botón abre y cierra el menú; en escritorio lo contrae a iconos.
        if (MOVIL.matches) {
            button.setAttribute('aria-label', plegado ? 'Abrir menú' : 'Cerrar menú');
        } else {
            button.setAttribute('aria-label', plegado ? 'Desplegar menú' : 'Contraer menú');
        }
    }

    // Estado inicial: en móvil el menú empieza cerrado para no tapar el contenido.
    aplicar(MOVIL.matches);

    button.addEventListener('click', function () {
        aplicar(!sidebar.classList.contains('sidebar-collapsed'));
    });

    // Al cruzar el punto de corte se vuelve al estado por defecto de ese tamaño.
    MOVIL.addEventListener('change', function (e) {
        aplicar(e.matches);
    });

    // Escape cierra el menú móvil y devuelve el foco al botón.
    sidebar.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && MOVIL.matches && !sidebar.classList.contains('sidebar-collapsed')) {
            aplicar(true);
            button.focus();
        }
    });
})();

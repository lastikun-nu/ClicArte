(function () {
    const baseUrl = new URL("../", document.currentScript.src);
    const menuItems = {
        alumno: [
            ["dashboard", "comun/dashboard.html", "dashboard.png", "Dashboard"],
            ["equipos", "alumno/misEquipos.html", "equipo.png", "Equipos"],
            ["historial", "alumno/misIncidencias.html", "grafico-de-barras.png", "Mi Historial"],
            ["incidencias", "alumno/formularioTicket.html", "incidencia.png", "Incidencias/Solicitudes"]
        ],
        profesor: [
            ["dashboard", "comun/dashboard.html", "dashboard.png", "Dashboard"],
            ["equipos", "profe/profesorEquipos.html", "equipo.png", "Equipos"],
            ["alumnos", "profe/gestionAlumnos.html", "alumno.png", "Alumnos y profesores"],
            ["aulas", "profe/gestionAulas.html", "aula.png", "Aulas"],
            ["incidencias", "profe/profesorIncidencias.html", "incidencia.png", "Incidencias/Solicitudes"]
        ],
        tecnico: [
            ["dashboard", "comun/dashboard.html", "dashboard.png", "Dashboard"],
            ["equipos", "tecnico/tecnicoEquipos.html", "equipo.png", "Equipos"],
            ["alumnos", "profe/gestionAlumnos.html", "alumno.png", "Alumnos y profesores"],
            ["aulas", "profe/gestionAulas.html", "aula.png", "Aulas"],
            ["incidencias", "tecnico/tecnicoIncidencias.html", "incidencia.png", "Incidencias/Solicitudes"]
        ]
    };

    document.querySelectorAll(".sidebar-nav[data-role][data-active]").forEach(nav => {
        const items = menuItems[nav.dataset.role];
        if (!items) return;

        items.forEach(([page, href, icon, label]) => {
            const link = document.createElement("a");
            link.href = new URL(href, baseUrl);
            link.innerHTML = `<img src="${new URL(`IMGBLANCO/${icon}`, baseUrl)}" alt=""><span data-i18n="${page}">${label}</span>`;

            if (page === nav.dataset.active) {
                link.classList.add("activo");
                link.setAttribute("aria-current", "page");
            }

            nav.append(link);
        });
    });
}());


(function () {
    const nav = document.querySelector(
        '.sidebar-nav, nav[data-role]'
    );

    if (!nav) return;

    const sidebar = nav.closest(
        'aside, .sidebar, .sidebar-container'
    ) || nav.parentElement;

    if (!sidebar || sidebar.querySelector('.sidebar-toggle')) {
        return;
    }

    const appShell = sidebar.closest('.app-shell');
    sidebar.classList.add('sidebar-menu');

    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'sidebar-toggle';
    button.textContent = '☰';
    button.setAttribute('aria-label', 'Contraer menú');
    button.setAttribute('aria-expanded', 'true');

    sidebar.insertBefore(button, sidebar.firstChild);

    button.addEventListener('click', function () {
        const collapsed = sidebar.classList.toggle(
            'sidebar-collapsed'
        );
        if (appShell) {
            appShell.classList.toggle('sidebar-collapsed', collapsed);
        }

        button.textContent = collapsed ? '☰' : '✕';
        button.setAttribute(
            'aria-expanded',
            String(!collapsed)
        );
        button.setAttribute(
            'aria-label',
            collapsed ? 'Desplegar menú' : 'Contraer menú'
        );
    });
})();

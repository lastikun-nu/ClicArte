(function () {
    const scriptUrl = document.currentScript.src;
    const frontendUrl = new URL("../", scriptUrl);
    const menuItems = {
        alumno: [
            { page: "dashboard", href: "comun/dashboard.html", icon: "dashboard.png", label: "Dashboard" },
            { page: "equipos", href: "alumno/misEquipos.html", icon: "equipo.png", label: "Equipos" },
            { page: "historial", href: "alumno/misIncidencias.html", icon: "grafico-de-barras.png", label: "Mi Historial" },
            { page: "incidencias", href: "alumno/formularioTicket.html", icon: "incidencia.png", label: "Incidencias/Solicitudes" }
        ],
        profesor: [
            { page: "dashboard", href: "comun/dashboard.html", icon: "dashboard.png", label: "Dashboard" },
            { page: "equipos", href: "profe/profesorEquipos.html", icon: "equipo.png", label: "Equipos" },
            { page: "alumnos", href: "profe/gestionAlumnos.html", icon: "alumno.png", label: "Alumnos y profesores" },
            { page: "aulas", href: "profe/gestionAulas.html", icon: "aula.png", label: "Aulas" },
            { page: "incidencias", href: "profe/profesorIncidencias.html", icon: "incidencia.png", label: "Incidencias/Solicitudes" }
        ],
        tecnico: [
            { page: "dashboard", href: "comun/dashboard.html", icon: "dashboard.png", label: "Dashboard" },
            { page: "equipos", href: "tecnico/tecnicoEquipos.html", icon: "equipo.png", label: "Equipos" },
            { page: "alumnos", href: "profe/gestionAlumnos.html", icon: "alumno.png", label: "Alumnos y profesores" },
            { page: "aulas", href: "profe/gestionAulas.html", icon: "aula.png", label: "Aulas" },
            { page: "incidencias", href: "tecnico/tecnicoIncidencias.html", icon: "incidencia.png", label: "Incidencias/Solicitudes" }
        ]
    };

    document.querySelectorAll(".sidebar-nav[data-role][data-active]").forEach(function (nav) {
        const items = menuItems[nav.dataset.role];
        if (!items) {
            return;
        }

        items.forEach(function (item) {
            const link = document.createElement("a");
            link.href = new URL(item.href, frontendUrl).href;

            if (item.page === nav.dataset.active) {
                link.classList.add("activo");
                link.setAttribute("aria-current", "page");
            }

            const icon = document.createElement("img");
            icon.src = new URL("IMGBLANCO/" + item.icon, frontendUrl).href;
            icon.alt = "";
            link.append(icon, document.createTextNode(item.label));
            nav.append(link);
        });
    });
}());
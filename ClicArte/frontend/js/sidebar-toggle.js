
document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("sidebar");
    const toggle = document.getElementById("sidebarToggle");

    if (!sidebar || !toggle) return;

    toggle.addEventListener("click", function () {
        const collapsed = sidebar.classList.toggle("collapsed");

        toggle.textContent = collapsed ? "☰" : "✕";

        toggle.setAttribute(
            "aria-expanded",
            String(!collapsed)
        );

        toggle.setAttribute(
            "aria-label",
            collapsed ? "Mostrar menú" : "Ocultar menú"
        );
    });
});

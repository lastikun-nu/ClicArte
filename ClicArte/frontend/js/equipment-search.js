document.addEventListener("DOMContentLoaded", function () {
    const search = document.getElementById("equipmentSearch");
    const status = document.getElementById("equipmentSearchStatus");
    const items = Array.from(document.querySelectorAll(".equipment-search-item"));

    if (!search || !status || items.length === 0) return;

    function normalize(value) {
        return value
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .toLocaleLowerCase();
    }

    function filterItems() {
        const query = normalize(search.value.trim());
        let visibleCount = 0;

        items.forEach(function (item) {
            const matches = normalize(item.textContent).includes(query);
            item.hidden = !matches;
            if (matches) visibleCount += 1;
        });

        status.textContent = visibleCount === 1
            ? "1 equipo encontrado"
            : `${visibleCount} equipos encontrados`;
    }

    search.addEventListener("input", filterItems);
    filterItems();
});

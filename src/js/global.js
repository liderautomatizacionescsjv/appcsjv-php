document.addEventListener("DOMContentLoaded", function () {
    menu();
});

function menu() {
    const menuIcon = document.querySelector(".menu-icon");
    const menuDeslizante = document.querySelector(".menu-deslizante");
    const menuCerrar = document.querySelector(".menu-cerrar");
    const menuOverlay = document.querySelector(".menu-overlay");

    if (menuIcon && menuDeslizante && menuCerrar && menuOverlay) {
        // Abrir el menú
        menuIcon.addEventListener("click", () => {
            menuDeslizante.classList.add("menu-abierto");
            menuOverlay.classList.add("activo");
        });

        // Cerrar el menú con el botón ✖
        menuCerrar.addEventListener("click", () => {
            menuDeslizante.classList.remove("menu-abierto");
            menuOverlay.classList.remove("activo");
        });

        // Cerrar el menú al hacer clic fuera de él
        menuOverlay.addEventListener("click", () => {
            menuDeslizante.classList.remove("menu-abierto");
            menuOverlay.classList.remove("activo");
        });
    }
}


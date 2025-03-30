// admin-sections.js - Controla la visualización de las diferentes secciones del panel de administración

document.addEventListener("DOMContentLoaded", function() {
    // Botones de navegación
    const toggleTagsButton = document.getElementById("toggleTags");
    const placesButton = document.getElementById("placesButton");
    const usersButton = document.getElementById("usersButton");
    const gymkhanaButton = document.getElementById("gymkhanaButton");
    const checkpointButton = document.getElementById("checkpointButton");

    // Contenedores de las tablas
    const tagsTableContainer = document.getElementById("tagsTableContainer");
    const placesTableContainer = document.getElementById("placesTableContainer");
    const usersTableContainer = document.getElementById("usersTableContainer");
    const gymkhanaSection = document.getElementById("gymkhanaSection");
    const checkpointSection = document.getElementById("checkpointSection");

    // Configuración inicial (mostrar gymkhanas por defecto)
    hideAllSections();
    gymkhanaSection.style.display = "block";

    // Eventos para los botones
    if (toggleTagsButton) {
        toggleTagsButton.addEventListener("click", function() {
            hideAllSections();
            tagsTableContainer.style.display = "block";
        });
    }

    if (placesButton) {
        placesButton.addEventListener("click", function() {
            hideAllSections();
            placesTableContainer.style.display = "block";
        });
    }

    if (usersButton) {
        usersButton.addEventListener("click", function() {
            hideAllSections();
            usersTableContainer.style.display = "block";
        });
    }

    if (gymkhanaButton) {
        gymkhanaButton.addEventListener("click", function() {
            hideAllSections();
            gymkhanaSection.style.display = "block";
        });
    }

    if (checkpointButton) {
        checkpointButton.addEventListener("click", function() {
            hideAllSections();
            checkpointSection.style.display = "block";
        });
    }

    // Función para ocultar todas las secciones
    function hideAllSections() {
        tagsTableContainer.style.display = "none";
        placesTableContainer.style.display = "none";
        usersTableContainer.style.display = "none";
        gymkhanaSection.style.display = "none";
        checkpointSection.style.display = "none";
    }
});

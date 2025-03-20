document.addEventListener("DOMContentLoaded", function () {
    function validateField(input) {
        const value = input.value.trim();
        const fieldName = input.getAttribute("name");

        const errorMessage = input.parentNode.querySelector(".error-message");
        if (errorMessage) {
            errorMessage.remove();
        }
        input.classList.remove("is-invalid");

        if (!value) {
            showError(input, "Este campo es obligatorio.");
            return false;
        }

        if (fieldName === "latitude" || fieldName === "longitude") {
            if (isNaN(value)) {
                showError(input, "Este campo debe ser un número.");
                return false;
            }
        }

        if (fieldName === "name" && value.length > 20) {
            showError(input, "El nombre no puede tener más de 20 caracteres.");
            return false;
        }

        return true;
    }

    function showError(input, message) {
        const errorMessage = document.createElement("p");
        errorMessage.className = "error-message text-danger mt-1";
        errorMessage.textContent = message;

        input.classList.add("is-invalid");
        input.parentNode.appendChild(errorMessage);
    }

    function addOnBlurValidation(form) {
        const inputs = form.querySelectorAll("input, textarea");
        inputs.forEach(input => {
            input.addEventListener("blur", function () {
                validateField(input);
            });
        });
    }

    const tagForm = document.getElementById("tagForm");
    if (tagForm) {
        addOnBlurValidation(tagForm);
    }

    const editTagForm = document.getElementById("editTagForm");
    if (editTagForm) {
        addOnBlurValidation(editTagForm);
    }

    const placeForm = document.getElementById("placeForm");
    if (placeForm) {
        addOnBlurValidation(placeForm);
    }

    const editPlaceForm = document.getElementById("editPlaceForm");
    if (editPlaceForm) {
        addOnBlurValidation(editPlaceForm);
    }
});
document.addEventListener("DOMContentLoaded", function () {
    function validateField(input) {
        const value = input.value.trim();
        const fieldName = input.getAttribute("name");

        const errorMessage = input.parentNode.querySelector(".error-message");
        if (errorMessage) {
            errorMessage.remove();
        }
        input.classList.remove("is-invalid");

        // Validar campos vacíos
        if (!value) {
            showError(input, "Este campo es obligatorio.");
            return false;
        }

        // Validar campos específicos
        switch (fieldName) {
            case "name":
                if (value.length > 255) {
                    showError(input, "El nombre no puede tener más de 255 caracteres.");
                    return false;
                }
                break;

            case "email":
                if (!validateEmail(value)) {
                    showError(input, "El email no es válido.");
                    return false;
                }
                break;

            case "password":
                if (value.length < 8) {
                    showError(input, "La contraseña debe tener al menos 8 caracteres.");
                    return false;
                }
                break;

            case "role_id":
                if (value !== "1" && value !== "2") {
                    showError(input, "Selecciona un rol válido.");
                    return false;
                }
                break;

            case "latitude":
            case "longitude":
                if (isNaN(value)) {
                    showError(input, "Este campo debe ser un número.");
                    return false;
                }
                break;

            default:
                break;
        }

        return true;
    }

    // Función para validar el formato del email
    function validateEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    // Función para mostrar errores debajo de los campos
    function showError(input, message) {
        const errorMessage = document.createElement("p");
        errorMessage.className = "error-message text-danger mt-1";
        errorMessage.textContent = message;

        input.classList.add("is-invalid");
        input.parentNode.appendChild(errorMessage);
    }

    // Añadir validación onblur a todos los campos de los formularios
    function addOnBlurValidation(form) {
        const inputs = form.querySelectorAll("input, textarea, select");
        inputs.forEach(input => {
            input.addEventListener("blur", function () {
                validateField(input);
            });
        });
    }

    // Añadir validación onblur a los formularios de usuarios
    const userForm = document.getElementById("userForm");
    if (userForm) {
        addOnBlurValidation(userForm);
    }

    const editUserForm = document.getElementById("editUserForm");
    if (editUserForm) {
        addOnBlurValidation(editUserForm);
    }

    // Añadir validación onblur a los formularios de tags
    const tagForm = document.getElementById("tagForm");
    if (tagForm) {
        addOnBlurValidation(tagForm);
    }

    const editTagForm = document.getElementById("editTagForm");
    if (editTagForm) {
        addOnBlurValidation(editTagForm);
    }

    // Añadir validación onblur a los formularios de places
    const placeForm = document.getElementById("placeForm");
    if (placeForm) {
        addOnBlurValidation(placeForm);
    }

    const editPlaceForm = document.getElementById("editPlaceForm");
    if (editPlaceForm) {
        addOnBlurValidation(editPlaceForm);
    }
});
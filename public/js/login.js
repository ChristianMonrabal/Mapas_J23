document.addEventListener("DOMContentLoaded", function () {
    emailField = document.getElementById('email');
    passwordField = document.getElementById('password');
    repeatPasswordField = document.getElementById('password_confirmation');
    nameField = document.getElementById('name'); 

    function validateField(field) {
        errorMessage = document.createElement('p');
        errorMessage.style.color = 'red';

        if (field.nextElementSibling && field.nextElementSibling.tagName === 'P') {
            field.nextElementSibling.remove();
            field.style.border = '2px solid #ccc'; 
        }

        if (field.value.trim() === '') {
            errorMessage.textContent = `${field.placeholder} es obligatorio.`;
            field.style.border = '3px solid red';
            field.parentNode.appendChild(errorMessage); 
            return false;
        }

        field.style.border = '3px solid green';
        return true;
    }

    function validateEmail() {
        if (!validateField(emailField)) {
            return false;
        }

        errorMessage = document.createElement('p');
        errorMessage.style.color = 'red';

        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailPattern.test(emailField.value.trim())) {
            errorMessage.textContent = "El email no es válido.";
            emailField.style.border = '3px solid red';
            emailField.parentNode.appendChild(errorMessage);
            return false;
        }

        emailField.style.border = '3px solid green';
        return true;
    }

    function validatePassword() {
        if (!validateField(passwordField)) {
            return false;
        }

        errorMessage = document.createElement('p');
        errorMessage.style.color = 'red';

        if (passwordField.value.length < 8) {
            errorMessage.textContent = "La contraseña debe tener al menos 6 caracteres.";
            passwordField.style.border = '3px solid red';
            passwordField.parentNode.appendChild(errorMessage);
            return false;
        }

        passwordField.style.border = '3px solid green';
        return true;
    }

    function validatePasswordMatch() {
        if (!validateField(repeatPasswordField)) {
            return false;
        }

        errorMessage = document.createElement('p');
        errorMessage.style.color = 'red';

        if (passwordField.value !== repeatPasswordField.value) {
            errorMessage.textContent = "Las contraseñas no coinciden.";
            repeatPasswordField.style.border = '3px solid red';
            repeatPasswordField.parentNode.appendChild(errorMessage);
            return false;
        }

        repeatPasswordField.style.border = '3px solid green';
        return true;
    }

    emailField.addEventListener('blur', function () {
        validateEmail();
    });

    passwordField.addEventListener('blur', function () {
        validatePassword();
    });

    repeatPasswordField.addEventListener('blur', function () {
        validatePasswordMatch(); 
    });

    nameField.addEventListener('blur', function () {
        validateField(nameField);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Obtén las referencias de los campos
    var nombreInput = document.getElementById('nombreGrupo');
    var capacidadInput = document.getElementById('capacidadGrupo');
    var gymkhanaSelect = document.getElementById('gymkhanaId');
  
    // Función para validar el campo y mostrar error
    function validateField(inputElement, message) {
      // Elimina mensajes de error previos
      var errorElem = inputElement.parentElement.querySelector('.error-message');
      if (errorElem) {
        errorElem.remove();
      }
      // Si el campo está vacío, crea y agrega un span con el mensaje
      if (!inputElement.value.trim()) {
        var span = document.createElement('span');
        span.className = 'error-message';
        span.style.color = 'red';
        span.style.fontSize = '0.9em';
        span.innerText = message;
        inputElement.parentElement.appendChild(span);
      }
    }
  
    // Agregar event listener "blur" a cada campo
    if (nombreInput) {
      nombreInput.addEventListener('blur', function() {
        validateField(nombreInput, 'Por favor, ingresa el nombre del grupo.');
      });
    }
    if (capacidadInput) {
      capacidadInput.addEventListener('blur', function() {
        validateField(capacidadInput, 'Por favor, ingresa la capacidad del grupo.');
      });
    }
    if (gymkhanaSelect) {
      gymkhanaSelect.addEventListener('blur', function() {
        validateField(gymkhanaSelect, 'Por favor, selecciona una gymkhana.');
      });
    }
  });
  
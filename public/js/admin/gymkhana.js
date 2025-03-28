// gymkhana.js

// Función para cargar las Gymkhanas y actualizar la tabla
function fetchGymkhanas() {
    fetch('/gymkhanas')
      .then(function(response) {
        return response.json();
      })
      .then(function(data) {
        var tableBody = document.getElementById('gymkhanaTableBody');
        tableBody.innerHTML = ''; // Limpiar la tabla
        data.forEach(function(gymkhana) {
          var row = document.createElement('tr');
          row.innerHTML = `
            <td>${gymkhana.name}</td>
            <td>${gymkhana.description}</td>
            <td>
              <button class="btn btn-warning btn-sm" onclick="editGymkhana(${gymkhana.id})">Editar</button>
              <button class="btn btn-danger btn-sm" onclick="deleteGymkhana(${gymkhana.id})">Eliminar</button>
            </td>
          `;
          tableBody.appendChild(row);
        });
        // Mostrar el contenedor si hay datos
        document.getElementById('gymkhanaTableContainer').style.display = data.length ? 'block' : 'none';
      })
      .catch(function(error) {
        console.error('Error fetching gymkhanas:', error);
      });
  }
  
  // Crear Gymkhana
  document.getElementById('gymkhanaForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var name = document.getElementById('gymkhanaName').value;
    var description = document.getElementById('gymkhanaDescription').value;
  
    fetch('/gymkhanas', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ name: name, description: description })
    })
    .then(function(response) {
      if (!response.ok) {
        throw new Error('Error al crear gymkhana');
      }
      return response.json();
    })
    .then(function(data) {
      alert(data.message || 'Gymkhana creada con éxito');
      fetchGymkhanas(); // Recargar la tabla
      // Opcional: reiniciar el formulario
      document.getElementById('gymkhanaForm').reset();
      // Cerrar el modal (usando jQuery si Bootstrap 5 con jQuery)
      $('#gymkhanaModal').modal('hide');
    })
    .catch(function(error) {
      console.error('Error creating gymkhana:', error);
    });
  });
  
  // Eliminar Gymkhana
  function deleteGymkhana(id) {
    if (confirm('¿Estás seguro de eliminar esta Gymkhana?')) {
      fetch('/gymkhanas/' + id, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(function(response) {
        return response.json();
      })
      .then(function(data) {
        alert(data.message || 'Gymkhana eliminada con éxito');
        fetchGymkhanas(); // Recargar la tabla
      })
      .catch(function(error) {
        console.error('Error deleting gymkhana:', error);
      });
    }
  }
  
  // Edit Gymkhana: cargar datos en un modal de edición (asegúrate de tener los inputs correctos en el modal de edición)
 // Función para editar Gymkhana (este es un ejemplo)
function editGymkhana(id) {
    fetch('/gymkhanas/' + id)
      .then(response => response.json())
      .then(data => {
        document.getElementById('editGymkhanaId').value = data.id;
        document.getElementById('editGymkhanaName').value = data.name;
        document.getElementById('editGymkhanaDescription').value = data.description;
        var modalEl = document.getElementById('editGymkhanaModal');
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
      })
      .catch(error => console.error('Error fetching gymkhana for edit:', error));
  }
  
  document.getElementById('editGymkhanaForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    var id = document.getElementById('editGymkhanaId').value;
    var name = document.getElementById('editGymkhanaName').value;
    var description = document.getElementById('editGymkhanaDescription').value;
  
    fetch('/gymkhanas/' + id, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ name: name, description: description })
    })
    .then(response => response.json())
    .then(data => {
      alert(data.message || 'Gymkhana actualizada con éxito');
      // Aquí podrías actualizar la lista de gymkhanas o redirigir
      var modalEl = document.getElementById('editGymkhanaModal');
      var modal = bootstrap.Modal.getInstance(modalEl);
      if (modal) modal.hide();
    })
    .catch(error => console.error('Error updating gymkhana:', error));
  });
  
  
  // Actualizar Gymkhana
  document.getElementById('editTagForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var id = document.getElementById('editTagId').value;
    var name = document.getElementById('editTagName').value;
    var description = document.getElementById('editTagDescription') ? document.getElementById('editTagDescription').value : '';
  
    fetch('/gymkhanas/' + id, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ name: name, description: description })
    })
    .then(function(response) {
      return response.json();
    })
    .then(function(data) {
      alert(data.message || 'Gymkhana actualizada con éxito');
      fetchGymkhanas(); // Recargar la tabla
      $('#editTagModal').modal('hide');
    })
    .catch(function(error) {
      console.error('Error updating gymkhana:', error);
    });
  });
  
  // Llamada inicial
  document.addEventListener('DOMContentLoaded', fetchGymkhanas);
  
// gymkhana.js

// Función para cargar las Gymkhanas y actualizar la tabla
function fetchGymkhanas() {
    fetch('/gymkhanas')
      .then(function(response) {
        if (!response.ok) {
          throw new Error('Error al cargar las gymkhanas');
        }
        return response.json();
      })
      .then(function(data) {
        var tableBody = document.getElementById('gymkhanaTableBody');
        if (!tableBody) {
          console.error("No se encontró el elemento gymkhanaTableBody");
          return;
        }
        
        tableBody.innerHTML = ''; // Limpiar la tabla
        
        if (data.length === 0) {
          tableBody.innerHTML = '<tr><td colspan="3" class="text-center">No hay gymkhanas registradas</td></tr>';
          return;
        }
        
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
        
        // Asegúrate de que el contenedor de Gymkhanas se muestre
        var section = document.getElementById('gymkhanaSection');
        if (section) {
          section.style.display = 'block';
        }
      })
      .catch(function(error) {
        console.error('Error fetching gymkhanas:', error);
        // Sólo mostrar SweetAlert si es un error real, no durante la carga inicial
        if (error.message !== 'Error al cargar las gymkhanas' || document.getElementById('gymkhanaTableBody').innerHTML !== '') {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron cargar las gymkhanas'
          });
        }
      });
  }
  
  // Crear Gymkhana
  document.getElementById('gymkhanaForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var name = document.getElementById('gymkhanaName').value;
    var description = document.getElementById('gymkhanaDescription').value;
    
    // Validación de campos vacíos
    if (!name.trim() || !description.trim()) {
      Swal.fire({
        icon: 'warning',
        title: 'Campos incompletos',
        text: 'Por favor completa todos los campos obligatorios'
      });
      return;
    }
  
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
      Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: data.message || 'Gymkhana creada con éxito'
      });
      
      fetchGymkhanas(); // Recargar la tabla
      
      // También actualizar la lista de gymkhanas en checkpoints
      if (typeof cargarOpcionesGymkhanas === 'function') {
        cargarOpcionesGymkhanas();
      }
      
      // Opcional: reiniciar el formulario
      document.getElementById('gymkhanaForm').reset();
      
      // Cerrar el modal (usando Bootstrap 5)
      var gymkhanaModal = bootstrap.Modal.getInstance(document.getElementById('gymkhanaModal'));
      if (gymkhanaModal) {
        gymkhanaModal.hide();
      }
    })
    .catch(function(error) {
      console.error('Error creating gymkhana:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'No se pudo crear la gymkhana'
      });
    });
  });
  
  // Eliminar Gymkhana
  function deleteGymkhana(id) {
    Swal.fire({
      title: '¿Estás seguro?',
      text: '¿Deseas eliminar esta gymkhana? Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
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
          Swal.fire({
            icon: 'success',
            title: 'Eliminado',
            text: data.message || 'Gymkhana eliminada con éxito'
          });
          
          fetchGymkhanas(); // Recargar la tabla
          
          // También actualizar la lista de gymkhanas en checkpoints
          if (typeof cargarOpcionesGymkhanas === 'function') {
            cargarOpcionesGymkhanas();
          }
        })
        .catch(function(error) {
          console.error('Error deleting gymkhana:', error);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo eliminar la gymkhana'
          });
        });
      }
    });
  }
  
  // Edit Gymkhana: cargar datos en un modal de edición
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
      .catch(error => {
        console.error('Error fetching gymkhana for edit:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'No se pudo cargar la información de la gymkhana'
        });
      });
  }

  // Actualizar Gymkhana
  document.getElementById('editGymkhanaForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    var id = document.getElementById('editGymkhanaId').value;
    var name = document.getElementById('editGymkhanaName').value;
    var description = document.getElementById('editGymkhanaDescription').value;
    
    // Validación de campos vacíos
    if (!name.trim() || !description.trim()) {
      Swal.fire({
        icon: 'warning',
        title: 'Campos incompletos',
        text: 'Por favor completa todos los campos obligatorios'
      });
      return;
    }
    
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
      Swal.fire({
        icon: 'success',
        title: 'Actualizado',
        text: data.message || 'Gymkhana actualizada con éxito'
      });
      
      fetchGymkhanas(); // Recargar la tabla
      
      // También actualizar la lista de gymkhanas en checkpoints
      if (typeof cargarOpcionesGymkhanas === 'function') {
        cargarOpcionesGymkhanas();
      }
      
      // Cerrar el modal
      var editGymkhanaModal = bootstrap.Modal.getInstance(document.getElementById('editGymkhanaModal'));
      if (editGymkhanaModal) {
        editGymkhanaModal.hide();
      }
    })
    .catch(function(error) {
      console.error('Error updating gymkhana:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'No se pudo actualizar la gymkhana'
      });
    });
  });

  // Cargar las gymkhanas al iniciar la página
  document.addEventListener('DOMContentLoaded', function() {
    fetchGymkhanas();
  });
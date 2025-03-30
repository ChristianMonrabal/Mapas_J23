// admin/checkpoints.js

// Función para cargar los Checkpoints y actualizar la tabla
function fetchCheckpoints() {
    fetch('/checkpoints')
      .then(function(response) {
        return response.json();
      })
      .then(function(data) {
        console.log("Datos recibidos:", data); // Debug
        var tableBody = document.getElementById('checkpointTableBody');
        if (!tableBody) {
          console.error("No se encontró el elemento checkpointTableBody");
          return;
        }
        tableBody.innerHTML = ''; // Limpiar la tabla
        data.forEach(function(checkpoint) {
          // Se asume que el objeto checkpoint incluye las relaciones 'gymkhana' y 'place'
          var gymkhanaName = checkpoint.gymkhana ? checkpoint.gymkhana.name : '';
          var placeName = checkpoint.place ? checkpoint.place.name : '';
          var row = document.createElement('tr');
          row.innerHTML = `
            <td>${checkpoint.pista}</td>
            <td>${gymkhanaName}</td>
            <td>${placeName}</td>
            <td>
              <button class="btn btn-warning btn-sm" onclick="editCheckpoint(${checkpoint.id})">Editar</button>
              <button class="btn btn-danger btn-sm" onclick="deleteCheckpoint(${checkpoint.id})">Eliminar</button>
            </td>
          `;
          tableBody.appendChild(row);
        });
        // Asegúrate de que el contenedor de Checkpoints se muestre
        var section = document.getElementById('checkpointSection');
        if (section) {
          section.style.display = 'block';
        }
      })
      .catch(function(error) {
        console.error('Error fetching checkpoints:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'No se pudieron cargar los checkpoints'
        });
      });
  }
  
  // Crear Checkpoint
  var checkpointForm = document.getElementById('checkpointForm');
  if (checkpointForm) {
    checkpointForm.addEventListener('submit', function(e) {
      e.preventDefault();
  
      var pista = document.getElementById('checkpointPista').value;
      var gymkhana_id = document.getElementById('gymkhanaId').value;
      var place_id = document.getElementById('placeId').value;
      
      // Validación de campos vacíos
      if (!pista.trim() || !gymkhana_id || !place_id) {
        Swal.fire({
          icon: 'warning',
          title: 'Campos incompletos',
          text: 'Por favor completa todos los campos obligatorios'
        });
        return;
      }
  
      fetch('/checkpoints', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          pista: pista,
          gymkhana_id: gymkhana_id,
          place_id: place_id
        })
      })
      .then(function(response) {
        return response.json();
      })
      .then(function(data) {
        Swal.fire({
          icon: 'success',
          title: 'Éxito',
          text: 'Checkpoint creado con éxito'
        });
        
        fetchCheckpoints(); // Recargar la tabla
        
        // Limpiar el formulario
        checkpointForm.reset();
        
        // Cerrar el modal
        var modalElement = document.getElementById('checkpointModal');
        var modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
          modal.hide();
        }
      })
      .catch(function(error) {
        console.error('Error creating checkpoint:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'No se pudo crear el checkpoint'
        });
      });
    });
  }
  
  // Eliminar Checkpoint
  function deleteCheckpoint(id) {
    Swal.fire({
      title: '¿Estás seguro?',
      text: '¿Deseas eliminar este checkpoint? Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        fetch('/checkpoints/' + id, {
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
            text: 'Checkpoint eliminado con éxito'
          });
          fetchCheckpoints(); // Recargar la tabla
        })
        .catch(function(error) {
          console.error('Error deleting checkpoint:', error);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo eliminar el checkpoint'
          });
        });
      }
    });
  }
  
  // Editar Checkpoint: cargar datos en el modal de edición
  function editCheckpoint(id) {
    fetch('/checkpoints/' + id)
      .then(function(response) {
        return response.json();
      })
      .then(function(checkpoint) {
        // Llenar el formulario con los datos del checkpoint
        document.getElementById('editCheckpointId').value = checkpoint.id;
        document.getElementById('editCheckpointPista').value = checkpoint.pista;
        document.getElementById('editGymkhanaId').value = checkpoint.gymkhana_id;
        document.getElementById('editPlaceId').value = checkpoint.place_id;
        
        // Mostrar el modal
        var modalElement = document.getElementById('editCheckpointModal');
        var modal = new bootstrap.Modal(modalElement);
        modal.show();
      })
      .catch(function(error) {
        console.error('Error fetching checkpoint for edit:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'No se pudo cargar la información del checkpoint'
        });
      });
  }
  
  // Actualizar Checkpoint
  var editCheckpointForm = document.getElementById('editCheckpointForm');
  if (editCheckpointForm) {
    editCheckpointForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      var id = document.getElementById('editCheckpointId').value;
      var pista = document.getElementById('editCheckpointPista').value;
      var gymkhana_id = document.getElementById('editGymkhanaId').value;
      var place_id = document.getElementById('editPlaceId').value;
      
      // Validación de campos vacíos
      if (!pista.trim() || !gymkhana_id || !place_id) {
        Swal.fire({
          icon: 'warning',
          title: 'Campos incompletos',
          text: 'Por favor completa todos los campos obligatorios'
        });
        return;
      }
      
      fetch('/checkpoints/' + id, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          pista: pista,
          gymkhana_id: gymkhana_id,
          place_id: place_id
        })
      })
      .then(function(response) {
        return response.json();
      })
      .then(function(data) {
        Swal.fire({
          icon: 'success',
          title: 'Actualizado',
          text: 'Checkpoint actualizado con éxito'
        });
        
        fetchCheckpoints(); // Recargar la tabla
        
        // Cerrar el modal
        var editModal = bootstrap.Modal.getInstance(document.getElementById('editCheckpointModal'));
        if (editModal) {
          editModal.hide();
        }
      })
      .catch(function(error) {
        console.error('Error updating checkpoint:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'No se pudo actualizar el checkpoint'
        });
      });
    });
  }
  
  // Función para cargar las opciones de Gymkhana en los selects
  function cargarOpcionesGymkhanas() {
    fetch('/checkpoints/gymkhanas')
      .then(function(response) {
        return response.json();
      })
      .then(function(data) {
        // Llenar el select de creación
        var gymkhanaSelect = document.getElementById('gymkhanaId');
        if (gymkhanaSelect) {
          gymkhanaSelect.innerHTML = '';
          gymkhanaSelect.innerHTML = '<option value="">Seleccione una Gymkhana</option>';
          data.forEach(function(gymkhana) {
            gymkhanaSelect.innerHTML += `<option value="${gymkhana.id}">${gymkhana.name}</option>`;
          });
        }
        
        // Llenar el select de edición
        var editGymkhanaSelect = document.getElementById('editGymkhanaId');
        if (editGymkhanaSelect) {
          editGymkhanaSelect.innerHTML = '';
          editGymkhanaSelect.innerHTML = '<option value="">Seleccione una Gymkhana</option>';
          data.forEach(function(gymkhana) {
            editGymkhanaSelect.innerHTML += `<option value="${gymkhana.id}">${gymkhana.name}</option>`;
          });
        }
      })
      .catch(function(error) {
        console.error('Error loading gymkhanas:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'No se pudieron cargar las gymkhanas'
        });
      });
  }
  
  // Función para cargar las opciones de Place en los selects
  function cargarOpcionesPlaces() {
    fetch('/checkpoints/places')
      .then(function(response) {
        return response.json();
      })
      .then(function(data) {
        // Llenar el select de creación
        var placeSelect = document.getElementById('placeId');
        if (placeSelect) {
          placeSelect.innerHTML = '';
          placeSelect.innerHTML = '<option value="">Seleccione un Lugar</option>';
          data.forEach(function(place) {
            placeSelect.innerHTML += `<option value="${place.id}">${place.name}</option>`;
          });
        }
        
        // Llenar el select de edición
        var editPlaceSelect = document.getElementById('editPlaceId');
        if (editPlaceSelect) {
          editPlaceSelect.innerHTML = '';
          editPlaceSelect.innerHTML = '<option value="">Seleccione un Lugar</option>';
          data.forEach(function(place) {
            editPlaceSelect.innerHTML += `<option value="${place.id}">${place.name}</option>`;
          });
        }
      })
      .catch(function(error) {
        console.error('Error loading places:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'No se pudieron cargar los lugares'
        });
      });
  }
  
  // Llamada inicial: al cargar el DOM, carga los checkpoints y también las opciones de Gymkhana y Place
  document.addEventListener('DOMContentLoaded', function() {
    fetchCheckpoints();
    cargarOpcionesGymkhanas();
    cargarOpcionesPlaces();
  });
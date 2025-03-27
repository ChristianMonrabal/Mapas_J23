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
  
      fetch('/checkpoints', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute('content')
        },
        body: JSON.stringify({ pista: pista, gymkhana_id: gymkhana_id, place_id: place_id })
      })
      .then(function(response) {
        if (!response.ok) {
          throw new Error('Error creating checkpoint');
        }
        return response.json();
      })
      .then(function(data) {
        alert(data.message || 'Checkpoint creado con éxito');
        fetchCheckpoints(); // Recargar la tabla
        checkpointForm.reset();
        // Ocultar el modal de creación usando Bootstrap 5
        var checkpointModalEl = document.getElementById('checkpointModal');
        var checkpointModal = bootstrap.Modal.getInstance(checkpointModalEl);
        if (checkpointModal) {
          checkpointModal.hide();
        }
      })
      .catch(function(error) {
        console.error('Error creating checkpoint:', error);
      });
    });
  }
  
  // Eliminar Checkpoint
  function deleteCheckpoint(id) {
    if (confirm('¿Estás seguro de eliminar este Checkpoint?')) {
      fetch('/checkpoints/' + id, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute('content')
        }
      })
      .then(function(response) {
        return response.json();
      })
      .then(function(data) {
        alert(data.message || 'Checkpoint eliminado con éxito');
        fetchCheckpoints();
      })
      .catch(function(error) {
        console.error('Error deleting checkpoint:', error);
      });
    }
  }
  
  // Editar Checkpoint: cargar datos en el modal de edición
  function editCheckpoint(id) {
    fetch('/checkpoints/' + id)
      .then(function(response) {
        return response.json();
      })
      .then(function(checkpoint) {
        // Rellenar los campos del formulario de edición
        document.getElementById('editCheckpointId').value = checkpoint.id;
        document.getElementById('editCheckpointPista').value = checkpoint.pista;
        document.getElementById('editGymkhanaId').value = checkpoint.gymkhana_id;
        document.getElementById('editPlaceId').value = checkpoint.place_id;
        // Mostrar el modal de edición
        var editModalEl = document.getElementById('editCheckpointModal');
        if (editModalEl) {
          var editModal = new bootstrap.Modal(editModalEl);
          editModal.show();
        } else {
          console.error("No se encontró el modal de edición (editCheckpointModal)");
        }
      })
      .catch(function(error) {
        console.error('Error fetching checkpoint for edit:', error);
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
      
      fetch('/checkpoints/' + id, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute('content')
        },
        body: JSON.stringify({ pista: pista, gymkhana_id: gymkhana_id, place_id: place_id })
      })
      .then(function(response) {
        return response.json();
      })
      .then(function(data) {
        alert(data.message || 'Checkpoint actualizado con éxito');
        fetchCheckpoints();
        // Ocultar el modal de edición
        var editModalEl = document.getElementById('editCheckpointModal');
        var editModal = bootstrap.Modal.getInstance(editModalEl);
        if (editModal) {
          editModal.hide();
        }
      })
      .catch(function(error) {
        console.error('Error updating checkpoint:', error);
      });
    });
  }
  
  // Función para cargar las opciones de Gymkhana en los selects
  function cargarOpcionesGymkhanas() {
    fetch('/checkpoints/gymkhanas')
      .then(response => response.json())
      .then(data => {
        console.log("Gymkhanas recibidas:", data); // Verificación en consola
        // Select del formulario de creación
        var selectGymkhana = document.getElementById('gymkhanaId');
        if (selectGymkhana) {
          selectGymkhana.innerHTML = '<option value="">-- Seleccione Gymkhana --</option>';
          data.forEach(function(item) {
            var opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.name;
            selectGymkhana.appendChild(opt);
          });
        }
        // Select del formulario de edición
        var selectEditGymkhana = document.getElementById('editGymkhanaId');
        if (selectEditGymkhana) {
          selectEditGymkhana.innerHTML = '<option value="">-- Seleccione Gymkhana --</option>';
          data.forEach(function(item) {
            var opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.name;
            selectEditGymkhana.appendChild(opt);
          });
        }
      })
      .catch(error => console.error('Error al cargar gymkhanas:', error));
  }
  
  
  // Función para cargar las opciones de Place en los selects
  function cargarOpcionesPlaces() {
    fetch('/checkpoints/places')
      .then(response => response.json())
      .then(data => {
        console.log("Gymkhanas places:", data); // Verificación en consola

        // Select del formulario de creación
        var selectPlace = document.getElementById('placeId');
        if (selectPlace) {
          selectPlace.innerHTML = '<option value="">-- Seleccione Place --</option>';
          data.forEach(function(item) {
            var opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.name;
            selectPlace.appendChild(opt);
          });
        }
        // Select del formulario de edición
        var selectEditPlace = document.getElementById('editPlaceId');
        if (selectEditPlace) {
          selectEditPlace.innerHTML = '<option value="">-- Seleccione Place --</option>';
          data.forEach(function(item) {
            var opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.name;
            selectEditPlace.appendChild(opt);
          });
        }
      })
      .catch(error => console.error('Error al cargar places:', error));
  }
  
  // Llamada inicial: al cargar el DOM, carga los checkpoints y también las opciones de Gymkhana y Place
  document.addEventListener('DOMContentLoaded', function() {
    fetchCheckpoints();
    cargarOpcionesGymkhanas();
    cargarOpcionesPlaces();
  });
  
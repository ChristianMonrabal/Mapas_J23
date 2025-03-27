// checkpoint.js

// Función para cargar los Checkpoints y actualizar la tabla
function fetchCheckpoints() {
    fetch('/checkpoints')
      .then(function(response) {
        return response.json();
      })
      .then(function(data) {
        var tableBody = document.getElementById('checkpointTableBody');
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
        // Siempre mostrar el contenedor de Checkpoints
        document.getElementById('checkpointTableContainer').style.display = 'block';
      })
      .catch(function(error) {
        console.error('Error fetching checkpoints:', error);
      });
  }
  
  // Crear Checkpoint
  document.getElementById('checkpointForm').addEventListener('submit', function(e) {
    e.preventDefault();
  
    var pista = document.getElementById('checkpointPista').value;
    var gymkhana_id = document.getElementById('gymkhanaId').value;
    var place_id = document.getElementById('placeId').value;
  
    fetch('/checkpoints', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
      document.getElementById('checkpointForm').reset();
      $('#checkpointModal').modal('hide');
    })
    .catch(function(error) {
      console.error('Error creating checkpoint:', error);
    });
  });
  
  // Eliminar Checkpoint
  function deleteCheckpoint(id) {
    if (confirm('¿Estás seguro de eliminar este Checkpoint?')) {
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
        // Asegúrate de que en tu modal de edición tengas los inputs con los siguientes IDs:
        // editCheckpointId, editCheckpointPista, editGymkhanaId, editPlaceId
        document.getElementById('editCheckpointId').value = checkpoint.id;
        document.getElementById('editCheckpointPista').value = checkpoint.pista;
        document.getElementById('editGymkhanaId').value = checkpoint.gymkhana_id;
        document.getElementById('editPlaceId').value = checkpoint.place_id;
        $('#editCheckpointModal').modal('show');
      })
      .catch(function(error) {
        console.error('Error fetching checkpoint for edit:', error);
      });
  }
  
  // Actualizar Checkpoint
  document.getElementById('editCheckpointForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    var id = document.getElementById('editCheckpointId').value;
    var pista = document.getElementById('editCheckpointPista').value;
    var gymkhana_id = document.getElementById('editGymkhanaId').value;
    var place_id = document.getElementById('editPlaceId').value;
  
    fetch('/checkpoints/' + id, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ pista: pista, gymkhana_id: gymkhana_id, place_id: place_id })
    })
    .then(function(response) {
      return response.json();
    })
    .then(function(data) {
      alert(data.message || 'Checkpoint actualizado con éxito');
      fetchCheckpoints();
      $('#editCheckpointModal').modal('hide');
    })
    .catch(function(error) {
      console.error('Error updating checkpoint:', error);
    });
  });
  
  // Llamada inicial para cargar los Checkpoints
  document.addEventListener('DOMContentLoaded', fetchCheckpoints);
  
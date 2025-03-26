// Variable global para almacenar la instancia del modal de detalle
var detalleGroupModalInstance = null;

// Función auxiliar para obtener (o crear) la instancia del modal de detalle
function getDetalleGroupModalInstance() {
  var modalEl = document.getElementById('detalleGroupModal');
  if (!detalleGroupModalInstance) {
    detalleGroupModalInstance = new bootstrap.Modal(modalEl);
  }
  return detalleGroupModalInstance;
}

// Función auxiliar para realizar peticiones fetch y retornar JSON
function fetchJSON(url, options) {
  return fetch(url, options).then(function(response) {
    return response.json().then(function(data) {
      if (!response.ok) {
        throw new Error(data.message || 'Error en la petición');
      }
      return data;
    });
  });
}

document.addEventListener('DOMContentLoaded', function() {
  var btnBuscar = document.getElementById('btnBuscar');
  var btnClearFilters = document.getElementById('btnClearFilters');
  var inputSearchName = document.getElementById('searchName');
  var inputSearchCode = document.getElementById('searchCode');
  var formCrearGrupo = document.getElementById('formCrearGrupo');
  var createGroupModalEl = document.getElementById('createGroupModal');

  // Al cargar la página, listamos el grupo al que pertenece el usuario.
  cargarGrupos();

  // Botón Buscar (filtros acumulativos)
  if (btnBuscar) {
    btnBuscar.addEventListener('click', function() {
      var nameQuery = inputSearchName ? inputSearchName.value.trim() : '';
      var codeQuery = inputSearchCode ? inputSearchCode.value.trim() : '';
      if (nameQuery || codeQuery) {
        buscarGrupos(nameQuery, codeQuery);
      } else {
        cargarGrupos();
      }
    });
  }

  // Botón Limpiar Filtros
  if (btnClearFilters) {
    btnClearFilters.addEventListener('click', function() {
      if (inputSearchName) inputSearchName.value = '';
      if (inputSearchCode) inputSearchCode.value = '';
      cargarGrupos(); // Recarga la lista sin filtros
    });
  }

  // Cuando se abra el modal de "Crear Grupo", cargamos la lista de Gymkhanas
  if (createGroupModalEl) {
    createGroupModalEl.addEventListener('shown.bs.modal', function() {
      console.log('Modal abierto, llamando cargarGymkhanas...');
      cargarGymkhanas();
    });
  }

  // Formulario de Crear Grupo
  if (formCrearGrupo) {
    formCrearGrupo.addEventListener('submit', function(e) {
      e.preventDefault();
      crearGrupo();
    });
  }
});

/* 1. Cargar el grupo del usuario */
function cargarGrupos() {
  var listaGrupos = document.getElementById('listaGrupos');
  if (!listaGrupos) return;
  listaGrupos.innerHTML = '<p>Cargando grupo...</p>';

  fetchJSON('/groups/list', {
    method: 'GET',
    credentials: 'same-origin',
    headers: { 'Accept': 'application/json' }
  })
  .then(function(data) {
    if (!data || data.length === 0) {
      listaGrupos.innerHTML = '<p>No estás unido a ningún grupo.</p>';
      // Si no está unido a ningún grupo, mostramos los grupos disponibles:
      cargarGruposDisponibles();
    } else {
      mostrarListaGrupos(data);
    }
  })
  .catch(function(error) {
    console.error(error);
    listaGrupos.innerHTML = '<p class="text-danger">Error al cargar grupo.</p>';
  });
}

/* 2. Cargar grupos disponibles (a los que el usuario no está unido) */
function cargarGruposDisponibles() {
  var listaGrupos = document.getElementById('listaGrupos');
  if (!listaGrupos) return;
  listaGrupos.innerHTML += '<p>Cargando grupos disponibles...</p>';

  fetchJSON('/groups/available', {
    method: 'GET',
    credentials: 'same-origin',
    headers: { 'Accept': 'application/json' }
  })
  .then(function(data) {
    if (!data || data.length === 0) {
      listaGrupos.innerHTML += '<p>No hay grupos disponibles.</p>';
    } else {
      mostrarListaGrupos(data);
    }
  })
  .catch(function(error) {
    console.error(error);
    listaGrupos.innerHTML += '<p class="text-danger">Error al cargar grupos disponibles.</p>';
  });
}

/* 3. Buscar grupo(s) con filtros acumulativos */
function buscarGrupos(nameQuery, codeQuery) {
  var listaGrupos = document.getElementById('listaGrupos');
  if (!listaGrupos) return;
  listaGrupos.innerHTML = '<p>Buscando grupo...</p>';

  var url = '/groups/search?';
  var params = [];
  if (nameQuery) params.push('name=' + encodeURIComponent(nameQuery));
  if (codeQuery) params.push('codigo=' + encodeURIComponent(codeQuery));
  url += params.join('&');

  fetchJSON(url, { method: 'GET', headers: { 'Accept': 'application/json' } })
  .then(function(data) {
    if (!data || data.length === 0) {
      listaGrupos.innerHTML = '<p>No se encontraron grupos con esos filtros.</p>';
    } else {
      mostrarListaGrupos(data);
    }
  })
  .catch(function(error) {
    console.error(error);
    listaGrupos.innerHTML = '<p class="text-danger">Error en la búsqueda.</p>';
  });
}

/* 4. Mostrar grupo (único) */
function mostrarListaGrupos(grupos) {
  var listaGrupos = document.getElementById('listaGrupos');
  if (!listaGrupos) return;
  if (!grupos || !grupos.length) {
    listaGrupos.innerHTML = '<p>No se encontraron grupos.</p>';
    return;
  }
  var html = grupos.map(function(g) {
    var numMiembros = (typeof g.users_count !== 'undefined')
                      ? g.users_count
                      : (g.users ? g.users.length : 0);
   
    return `
      <div class="border p-3 mb-2">
        <h5>${g.name}</h5>
        <p>Código: <strong>${g.codigo}</strong></p>
        <p>Capacidad: <strong>${g.max_miembros}</strong></p>
        <p>Miembros Actuales: <strong>${numMiembros}</strong></p>
        <button class="btn btn-sm btn-info" onclick="verDetalleGrupo(${g.id})">
            Ver Detalle
        </button>
      </div>
    `;
  }).join('');
  listaGrupos.innerHTML = html;
}

/* 5. Ver Detalle de Grupo */
function verDetalleGrupo(groupId) {
  var modalBody = document.getElementById('detalleGroupBody');
  if (modalBody) {
    var modalInstance = getDetalleGroupModalInstance();
    modalInstance.show();
    modalBody.innerHTML = '<p>Cargando detalle...</p>';
  }
  fetchJSON('/groups/' + groupId, { method: 'GET', headers: { 'Accept': 'application/json' } })
  .then(function(data) {
    if (modalBody) {
      modalBody.innerHTML = renderDetalleGrupo(data.group, data.is_creator, data.is_member);
    }
  })
  .catch(function(error) {
    console.error(error);
    if (modalBody) {
      modalBody.innerHTML = '<p class="text-danger">Error al cargar detalle: ' + error.message + '</p>';
    }
  });
}

/* 6. Render Detalle de Grupo */
function renderDetalleGrupo(grupo, isCreator, isMember) {
  var numMiembros = grupo.users.length;
  var html = `
      <h3>Detalle del Grupo</h3>
      <hr>
      <h4>${grupo.name}</h4>
      <p>Código: <strong>${grupo.codigo}</strong></p>
      <p>Capacidad: <strong>${grupo.max_miembros}</strong></p>
      <p>Miembros Actuales: <strong>${numMiembros}</strong></p>
      <hr>
      <h5>Miembros:</h5>
      <ul>
  `;
  grupo.users.forEach(function(u) {
    html += `<li>${u.name || ('UsuarioID:' + u.id)}`;
    if (isCreator && u.id !== grupo.creador) {
      html += `
          <button class="btn btn-sm btn-outline-danger ms-2"
                  onclick="expulsarMiembro(${grupo.id}, ${u.id})">
              Expulsar
          </button>
      `;
    }
    html += '</li>';
  });
  html += `</ul><hr>`;
  if (!isMember && !isCreator && numMiembros < grupo.max_miembros) {
    html += `<button class="btn btn-primary me-2" onclick="unirseGrupo(${grupo.id})">Unirse</button>`;
  }
  if (isMember && !isCreator) {
    html += `<button class="btn btn-warning me-2" onclick="salirGrupo(${grupo.id})">Salir</button>`;
  }
  if (isCreator) {
    html += `<button class="btn btn-danger me-2" onclick="eliminarGrupo(${grupo.id})">Eliminar Grupo</button>`;
    if (numMiembros == grupo.max_miembros) {
      html += `<button class="btn btn-success me-2" onclick="iniciarJuego(${grupo.id})">Iniciar Juego</button>`;
    }
  }
  return html;
}

/* 7. Cargar Gymkhanas para el select */
function cargarGymkhanas() {
  console.log('Llamando a cargarGymkhanas...');
  fetchJSON('/groups/gymkhanas', { method: 'GET', headers: { 'Accept': 'application/json' } })
  .then(function(data) {
    console.log('Data Gymkhanas:', data);
    const selectG = document.getElementById('gymkhanaId');
    if (!selectG) return;
    selectG.innerHTML = '<option style="color:#0000FF;" value="">-- Seleccione Gymkhana --</option>';
    data.forEach(function(g) {
      const opt = document.createElement('option');
      opt.value = g.id;
      opt.textContent = g.name;
      selectG.appendChild(opt);
    });
  })
  .catch(function(error) {
    console.error('Error al cargar gymkhanas:', error);
  });
}

/* 8. Crear Grupo */
function crearGrupo() {
  var nombre = document.getElementById('nombreGrupo').value.trim();
  var maxMiembros = document.getElementById('capacidadGrupo').value;
  var gymkhanaId = document.getElementById('gymkhanaId').value;

  fetchJSON('/groups', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
      name: nombre,
      max_miembros: maxMiembros,
      gymkhana_id: gymkhanaId
    })
  })
  .then(function(data) {
    Swal.fire({
      title: 'Éxito',
      text: 'Grupo creado: ' + data.group.name,
      icon: 'success',
      confirmButtonText: 'OK'
    });
    var createModalEl = document.getElementById('createGroupModal');
    if (createModalEl) {
      var modalInstance = bootstrap.Modal.getInstance(createModalEl);
      if (modalInstance) {
        modalInstance.hide();
      }
    }
    cargarGrupos();
    var formCrearGrupo = document.getElementById('formCrearGrupo');
    if (formCrearGrupo) {
      formCrearGrupo.reset();
    }
  })
  .catch(function(error) {
    Swal.fire({
      title: 'Error',
      text: error.message,
      icon: 'error',
      confirmButtonText: 'OK'
    });
    console.error(error);
  });
}

function unirseGrupo(groupId) {
  fetchJSON('/groups/' + groupId + '/join', {
    method: 'POST',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
  })
  .then(function(data) {
    Swal.fire({
      title: 'Unido',
      text: data.message || 'Te has unido al grupo',
      icon: 'success',
      confirmButtonText: 'OK'
    });
    verDetalleGrupo(groupId);
    cargarGrupos();
  })
  .catch(function(error) {
    Swal.fire({
      title: 'Error',
      text: error.message,
      icon: 'error',
      confirmButtonText: 'OK'
    });
    console.error(error);
  });
}

function salirGrupo(groupId) {
  Swal.fire({
    title: '¿Salir del grupo?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, salir',
    cancelButtonText: 'Cancelar'
  }).then(function(result) {
    if (!result.isConfirmed) return;
    fetchJSON('/groups/' + groupId + '/leave', {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      }
    })
    .then(function(data) {
      Swal.fire({
        title: 'Saliste',
        text: data.message || 'Has salido del grupo',
        icon: 'success',
        confirmButtonText: 'OK'
      });
      var detailModalEl = document.getElementById('detalleGroupModal');
      if (detailModalEl) {
        var modalInstance = bootstrap.Modal.getInstance(detailModalEl);
        if (modalInstance) {
          modalInstance.hide();
        }
      }
      cargarGrupos();
    })
    .catch(function(error) {
      Swal.fire({
        title: 'Error',
        text: error.message,
        icon: 'error',
        confirmButtonText: 'OK'
      });
      console.error(error);
    });
  });
}

function expulsarMiembro(groupId, userId) {
  Swal.fire({
    title: '¿Expulsar a este miembro?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, expulsar',
    cancelButtonText: 'Cancelar'
  }).then(function(result) {
    if (!result.isConfirmed) return;
    fetchJSON('/groups/' + groupId + '/kick/' + userId, {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      }
    })
    .then(function(data) {
      Swal.fire({
        title: 'Expulsado',
        text: data.mensaje || 'Miembro expulsado',
        icon: 'info',
        confirmButtonText: 'OK'
      }).then(function() {
        var detailModalEl = document.getElementById('detalleGroupModal');
        if (detailModalEl) {
          var modalInstance = bootstrap.Modal.getInstance(detailModalEl);
          if (modalInstance) {
            modalInstance.hide();
          }
        }
        verDetalleGrupo(groupId);
        cargarGrupos();
      });
    })
    .catch(function(error) {
      Swal.fire({
        title: 'Error',
        text: error.message,
        icon: 'error',
        confirmButtonText: 'OK'
      });
      console.error(error);
    });
  });
}

function iniciarJuego(groupId) {
    fetchJSON('/groups/' + groupId + '/start', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      }
    })
    .then(function(data) {
      Swal.fire({
        title: 'Juego Iniciado',
        text: data.message || 'El juego ha comenzado',
        icon: 'success',
        confirmButtonText: 'OK'
      }).then(function() {
        // Redirige al usuario a dashboard/gimcana
        window.location.href = '/dashboard/gimcana';
      });
    })
    .catch(function(error) {
      Swal.fire({
        title: 'Error',
        text: error.message,
        icon: 'error',
        confirmButtonText: 'OK'
      });
      console.error(error);
    });
  }
  

function eliminarGrupo(groupId) {
  Swal.fire({
    title: '¿Eliminar este grupo?',
    text: 'Esta acción no se puede revertir.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  }).then(function(result) {
    if (!result.isConfirmed) return;
    fetchJSON('/groups/' + groupId, {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      }
    })
    .then(function(data) {
      Swal.fire({
        title: 'Eliminado',
        text: data.message || 'Grupo eliminado',
        icon: 'success',
        confirmButtonText: 'OK'
      });
      var detailModalEl = document.getElementById('detalleGroupModal');
      if (detailModalEl) {
        var modalInstance = bootstrap.Modal.getInstance(detailModalEl);
        if (modalInstance) {
          modalInstance.hide();
        }
      }
      cargarGrupos();
    })
    .catch(function(error) {
      Swal.fire({
        title: 'Error',
        text: error.message,
        icon: 'error',
        confirmButtonText: 'OK'
      });
      console.error(error);
    });
  });
}

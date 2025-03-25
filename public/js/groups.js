document.addEventListener('DOMContentLoaded', function() {
    var btnBuscar      = document.getElementById('btnBuscar');
    var inputSearch    = document.getElementById('searchQuery');
    var formCrearGrupo = document.getElementById('formCrearGrupo');
    var createGroupModalEl = document.getElementById('createGroupModal');


    // Al cargar la página, listamos grupos
    cargarGrupos();

    // Botón Buscar
    if (btnBuscar) {
        btnBuscar.addEventListener('click', function() {
            var query = inputSearch.value.trim();
            if (query) {
                buscarGrupos(query);
            } else {
                cargarGrupos();
            }
        });
    }

    // Cuando se abra el modal de "Crear Grupo", cargamos la lista de Gymkhanas:
    if (createGroupModalEl) {
        createGroupModalEl.addEventListener('shown.bs.modal', function() {
            console.log('Modal abierto, llamando cargarGymkhanas...');
            cargarGymkhanas();  // NUEVO
        });
    }

    // Form Crear Grupo
    if (formCrearGrupo) {
        formCrearGrupo.addEventListener('submit', function(e) {
            e.preventDefault();
            crearGrupo();
        });
    }
});

/* 1. Cargar Grupos (existente) */
function cargarGrupos() {
    var listaGrupos = document.getElementById('listaGrupos');
    if (!listaGrupos) return;

    listaGrupos.innerHTML = '<p>Cargando grupos...</p>';

    fetch('/groups/list', {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error('Error al listar grupos');
        }
        return response.json();
    })
    .then(function(data) {
        mostrarListaGrupos(data);
    })
    .catch(function(error) {
        console.error(error);
        listaGrupos.innerHTML = '<p class="text-danger">Error al cargar grupos.</p>';
    });
}

/* 2. Buscar Grupos (existe) */
function buscarGrupos(query) {
    var listaGrupos = document.getElementById('listaGrupos');
    if (!listaGrupos) return;

    listaGrupos.innerHTML = '<p>Buscando grupos...</p>';

    fetch('/groups/search?query=' + encodeURIComponent(query), {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error('Error en la búsqueda de grupos');
        }
        return response.json();
    })
    .then(function(data) {
        mostrarListaGrupos(data);
    })
    .catch(function(error) {
        console.error(error);
        listaGrupos.innerHTML = '<p class="text-danger">Error en la búsqueda.</p>';
    });
}

/* 3. Mostrar lista (existe) */
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
        // Verifica si el grupo es "tu grupo" (por ejemplo, si el creador es el usuario actual)
        var mensajePropio = (g.creador == window.currentUserId)
                            ? '<p style="color:green; font-weight:bold;">Este es tu grupo</p>'
                            : '';

        return `
          <div class="border p-3 mb-2">
            <h5>${g.name}</h5>
            ${mensajePropio}
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


/* 4. Ver Detalle de Grupo (existe) */
function verDetalleGrupo(groupId) {
    var modalEl = document.getElementById('detalleGroupModal');
    var modalBody = document.getElementById('detalleGroupBody');
    if (modalEl && modalBody) {
        var modalInstance = new bootstrap.Modal(modalEl);
        modalInstance.show();
        modalBody.innerHTML = '<p>Cargando detalle...</p>';
    }

    fetch('/groups/' + groupId, {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error('Error al cargar detalle del grupo');
        }
        return response.json();
    })
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

/* Render Detalle (existe) */
function renderDetalleGrupo(grupo, isCreator, isMember) {
    var numMiembros = grupo.users.length;

    // Agregamos un mensaje si el usuario es creador o ya forma parte del grupo.
    var mensajePropio = (isCreator || isMember)
        ? '<p style="color:green; font-weight: bold;">Este es tu grupo</p>'
        : '';

    var html = `
        <h3>Detalle del Grupo</h3>
        <hr>
        ${mensajePropio}
        <p><strong>Creador:</strong> ${grupo.creator_name ? grupo.creator_name : 'Desconocido'}</p>
        <hr>
        <h4>${grupo.name}</h4>
        <p>Gymkhana: <strong>${grupo.gymkhana ? grupo.gymkhana.name : 'No asignada'}</strong></p>
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



/* 5. NUEVO: cargarGymkhanas() para el select */
function cargarGymkhanas() {
    console.log('Llamando a cargarGymkhanas...');
    
    fetch('/groups/gymkhanas', {
      method: 'GET',
      headers: { 'Accept': 'application/json' }
    })
    .then(response => {
      console.log('Respuesta fetch:', response);
      if (!response.ok) {
        throw new Error('Error al cargar gymkhanas');
      }
      return response.json();
    })
    .then(data => {
      console.log('Data Gymkhanas:', data);
  
      // Ubicamos el <select> en el DOM
      const selectG = document.getElementById('gymkhanaId');
      if (!selectG) return;
  
      // Limpiamos las opciones previas y agregamos una por defecto
      selectG.innerHTML = '<option style="color:#0000FF;" value="">-- Seleccione Gymkhana --</option>';
  
      // Recorremos el array "data"
      data.forEach(function(g) {
        // Creamos un nuevo <option>
        const opt = document.createElement('option');
        opt.value = g.id;       // Asignamos el id de la gymkhana como value
        opt.textContent = g.name; // El texto visible será el nombre de la gymkhana
  
        // Agregamos el <option> generado al <select>
        selectG.appendChild(opt);
      });
  
    })
    .catch(error => {
      console.error('Error al cargar gymkhanas:', error);
    });
  }
  
  
  

/* 6. Crear Grupo (actualizado) */
function crearGrupo() {
    var nombre      = document.getElementById('nombreGrupo').value.trim();
    var maxMiembros = document.getElementById('capacidadGrupo').value;
    
    // NUEVO: obtener la gymkhana elegida
    var gymkhanaId  = document.getElementById('gymkhanaId').value;

    fetch('/groups', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        // Añadir 'gymkhana_id' en el body
        body: JSON.stringify({
            name: nombre,
            max_miembros: maxMiembros,
            gymkhana_id: gymkhanaId
        })
    })
    .then(function(response) {
        return response.json().then(function(data) {
            if (!response.ok) {
                throw new Error(data.message || 'Error al crear el grupo');
            }
            return data;
        });
    })
    .then(function(data) {
        Swal.fire({
            title: 'Éxito',
            text: 'Grupo creado: ' + data.group.name,
            icon: 'success',
            confirmButtonText: 'OK'
        });

        // Cerrar modal de crear
        var createModalEl = document.getElementById('createGroupModal');
        if (createModalEl) {
            var modalInstance = bootstrap.Modal.getInstance(createModalEl);
            if (modalInstance) {
                modalInstance.hide();
            }
        }
        // Refrescar la lista
        cargarGrupos();

        // Limpiar form
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
    fetch('/groups/' + groupId + '/join', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(function(response) {
        if (!response.ok) {
            return response.json().then(function(err) {
                throw new Error(err.message || 'Error al unirse al grupo');
            });
        }
        return response.json();
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

        fetch('/groups/' + groupId + '/leave', {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(function(response) {
            if (!response.ok) {
                return response.json().then(function(err) {
                    throw new Error(err.message || 'Error al salir del grupo');
                });
            }
            return response.json();
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

        fetch('/groups/' + groupId + '/kick/' + userId, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(function(response) {
            if (!response.ok) {
                return response.json().then(function(err) {
                    throw new Error(err.mensaje || 'Error al expulsar miembro');
                });
            }
            return response.json();
        })
        .then(function(data) {
            Swal.fire({
                title: 'Expulsado',
                text: data.mensaje || 'Miembro expulsado',
                icon: 'info',
                confirmButtonText: 'OK'
            }).then(function() {
                // Cerrar el modal actual antes de actualizarlo
                var detailModalEl = document.getElementById('detalleGroupModal');
                if (detailModalEl) {
                    var modalInstance = bootstrap.Modal.getInstance(detailModalEl);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                }
                // Ahora, recargar el detalle del grupo para reflejar el cambio
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
    fetch('/groups/' + groupId + '/start', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(function(response) {
        if (!response.ok) {
            return response.json().then(function(err) {
                throw new Error(err.message || 'Error al iniciar juego');
            });
        }
        return response.json();
    })
    .then(function(data) {
        Swal.fire({
            title: 'Juego Iniciado',
            text: data.message || 'El juego ha comenzado',
            icon: 'success',
            confirmButtonText: 'OK'
        });
        verDetalleGrupo(groupId);
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

        fetch('/groups/' + groupId, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(function(response) {
            if (!response.ok) {
                return response.json().then(function(err) {
                    throw new Error(err.message || 'Error al eliminar grupo');
                });
            }
            return response.json();
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

document.addEventListener('DOMContentLoaded', function() {
    var btnBuscar      = document.getElementById('btnBuscar');
    var inputSearch    = document.getElementById('searchQuery');
    var formCrearGrupo = document.getElementById('formCrearGrupo');

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

    // Form Crear Grupo
    if (formCrearGrupo) {
        formCrearGrupo.addEventListener('submit', function(e) {
            e.preventDefault();
            crearGrupo();
        });
    }
});

/* 1. LISTAR GRUPOS */
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
            <h5>${g.nombre}</h5>
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

/* 3. VER DETALLES (GET /groups/{id}) */
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

function renderDetalleGrupo(grupo, isCreator, isMember) {
    var numMiembros = grupo.users.length;
    var html = `
        <h4>${grupo.nombre}</h4>
        <p>Código: <strong>${grupo.codigo}</strong></p>
        <p>Capacidad: <strong>${grupo.max_miembros}</strong></p>
        <p>Miembros Actuales: <strong>${numMiembros}</strong></p>
        <p>Gimkhana: <strong>${grupo.gymkhana ? grupo.gymkhana.nombre : 'No asignada'}</strong></p>
        <hr>
        <h5>Miembros:</h5>
        <ul>
    `;

    grupo.users.forEach(function(u) {
        html += `<li>${u.nombre || ('UsuarioID:' + u.id)}`;
        // Botón expulsar si soy creador y no soy yo
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

    // Unirse (si NO es miembro, NO es creator, y no está lleno)
    // (aunque con la nueva lógica, no haría falta, 
    //  pues el controller te dará 400 si ya estás en uno)
    if (!isMember && !isCreator && numMiembros < grupo.max_miembros) {
        html += `<button class="btn btn-primary me-2" onclick="unirseGrupo(${grupo.id})">Unirse</button>`;
    }

    // Salir (solo si eres miembro normal)
    if (isMember && !isCreator) {
        html += `<button class="btn btn-warning me-2" onclick="salirGrupo(${grupo.id})">Salir</button>`;
    }

    // Eliminar (solo si eres creador)
    if (isCreator) {
        html += `<button class="btn btn-danger me-2" onclick="eliminarGrupo(${grupo.id})">Eliminar Grupo</button>`;
        if (numMiembros == grupo.max_miembros) {
            html += `<button class="btn btn-success me-2" onclick="iniciarJuego(${grupo.id})">Iniciar Juego</button>`;
        }
    }

    return html;
}

/* 4. CREAR GRUPO */
function crearGrupo() {
    var nombre       = document.getElementById('nombreGrupo').value.trim();
    var gymkhanaId   = document.getElementById('gymkhanaId').value;
    var maxMiembros  = document.getElementById('capacidadGrupo').value;

    fetch('/groups', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            name: nombre,
            gymkhana_id: gymkhanaId,
            max_miembros: maxMiembros
        })
    })
    .then(function(response) {
        if (!response.ok) {
            return response.json().then(function(err) {
                throw new Error(err.message || 'Error al crear el grupo');
            });
        }
        return response.json();
    })
    .then(function(data) {
        Swal.fire({
            title: 'Éxito',
            text: 'Grupo creado: ' + data.group.nombre,
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

        // Refrescar lista
        cargarGrupos();

        // Limpiar el form
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

/* 5. UNIRSE A UN GRUPO */
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

/* 6. SALIR DE UN GRUPO */
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

            // Cerrar modal detalle
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

/* 7. EXPULSAR MIEMBRO */
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
    });
}

/* 8. INICIAR JUEGO */
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

/* 9. ELIMINAR GRUPO */
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

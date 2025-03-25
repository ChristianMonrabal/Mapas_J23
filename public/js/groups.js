document.addEventListener('DOMContentLoaded', () => {
    const btnBuscar = document.getElementById('btnBuscar');
    const inputSearch = document.getElementById('searchQuery');
    const formCrearGrupo = document.getElementById('formCrearGrupo');

    // Cargar lista al inicio
    cargarGrupos();

    // Evento buscar
    if (btnBuscar) {
        btnBuscar.addEventListener('click', () => {
            const query = inputSearch.value.trim();
            if (query) {
                buscarGrupos(query);
            } else {
                cargarGrupos(); // si el input está vacío, cargamos todo
            }
        });
    }

    // Crear grupo (submit del form)
    if (formCrearGrupo) {
        formCrearGrupo.addEventListener('submit', async (e) => {
            e.preventDefault();
            await crearGrupo();
        });
    }
});

/**
 * Carga todos los grupos (GET /groups/list).
 */
async function cargarGrupos() {
    const listaGrupos = document.getElementById('listaGrupos');
    if (!listaGrupos) return;

    listaGrupos.innerHTML = '<p>Cargando grupos...</p>';

    try {
        const resp = await fetch('/groups/list', {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        });
        const data = await resp.json();
        mostrarListaGrupos(data);
    } catch (err) {
        console.error(err);
        listaGrupos.innerHTML = '<p class="text-danger">Error al cargar grupos.</p>';
    }
}

/**
 * Buscar grupos por nombre o código (GET /groups/search?query=...).
 */
async function buscarGrupos(query) {
    const listaGrupos = document.getElementById('listaGrupos');
    if (!listaGrupos) return;

    listaGrupos.innerHTML = '<p>Buscando...</p>';

    try {
        const resp = await fetch(`/groups/search?query=${encodeURIComponent(query)}`, {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        });
        const data = await resp.json();
        mostrarListaGrupos(data);
    } catch (err) {
        console.error(err);
        listaGrupos.innerHTML = '<p class="text-danger">Error en la búsqueda.</p>';
    }
}

/**
 * Muestra la lista de grupos en pantalla.
 */
function mostrarListaGrupos(grupos) {
    const listaGrupos = document.getElementById('listaGrupos');
    if (!listaGrupos) return;

    if (!grupos || !grupos.length) {
        listaGrupos.innerHTML = '<p>No se encontraron grupos.</p>';
        return;
    }

    const html = grupos.map(g => {
        const numMiembros = g.users_count || (g.users ? g.users.length : 0);

        return `
          <div class="border p-3 mb-2">
            <h5>${g.nombre}</h5>
            <p>Código: <strong>${g.codigo}</strong></p>
            <p>Capacidad: <strong>${g.max_miembros}</strong></p>
            <p>Miembros Actuales: <strong>${numMiembros}</strong></p>
            <button class="btn btn-sm btn-info" onclick="verDetalleGrupo(${g.id})">
                Ver Detalles
            </button>
          </div>
        `;
    }).join('');

    listaGrupos.innerHTML = html;
}

/**
 * Ver detalle de un grupo en un modal (GET /groups/{id}).
 * Muestra opciones según si eres creador, miembro, etc.
 */
async function verDetalleGrupo(groupId) {
    const modalBody = document.getElementById('detalleGroupBody');
    if (!modalBody) return;

    // Abre el modal
    const modalEl = document.getElementById('detalleGroupModal');
    const modalInstance = new bootstrap.Modal(modalEl);
    modalInstance.show();

    // Mientras carga, pon un texto
    modalBody.innerHTML = '<p>Cargando detalle...</p>';

    try {
        const resp = await fetch(`/groups/${groupId}`, {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        });
        const data = await resp.json();
        if (!resp.ok) throw new Error(data.message || 'Error al cargar detalle');

        const grupo = data.group;
        const isCreator = data.is_creator;
        const isMember = data.is_member;

        // Renderizamos
        modalBody.innerHTML = renderDetalleGrupo(grupo, isCreator, isMember);

    } catch (err) {
        console.error(err);
        modalBody.innerHTML = `<p class="text-danger">Error al cargar detalle: ${err.message}</p>`;
    }
}

/**
 * Genera el HTML para el detalle de un grupo: miembros, botones de unirse/salir, etc.
 */
function renderDetalleGrupo(grupo, isCreator, isMember) {
    const numMiembros = grupo.users.length;
    let html = `
        <h4>${grupo.nombre}</h4>
        <p>Código: <strong>${grupo.codigo}</strong></p>
        <p>Capacidad: <strong>${grupo.max_miembros}</strong></p>
        <p>Miembros Actuales: <strong>${numMiembros}</strong></p>
        <p>Gimkhana: <strong>${grupo.gymkhana ? grupo.gymkhana.nombre : 'No asignada'}</strong></p>
        <hr>
        <h5>Miembros:</h5>
        <ul>
    `;

    grupo.users.forEach(user => {
        html += `<li>${user.nombre || ('UsuarioID:' + user.id)}`;
        // Botón Expulsar (si soy el creador y no es el creador)
        if (isCreator && user.id !== grupo.creador) {
            html += `
                <button class="btn btn-sm btn-outline-danger ms-2"
                        onclick="expulsarMiembro(${grupo.id}, ${user.id})">
                    Expulsar
                </button>
            `;
        }
        html += '</li>';
    });

    html += `</ul><hr>`;

    // Botones de acción
    // Si NO soy miembro y NO soy creador => Unirse
    if (!isMember && !isCreator && numMiembros < grupo.max_miembros) {
        html += `<button class="btn btn-primary me-2" onclick="unirseGrupo(${grupo.id})">
                    Unirse
                 </button>`;
    }

    // Si soy miembro normal => Salir
    if (isMember && !isCreator) {
        html += `<button class="btn btn-warning me-2" onclick="salirGrupo(${grupo.id})">
                    Salir del Grupo
                 </button>`;
    }

    // Si soy creador => Eliminar
    if (isCreator) {
        html += `<button class="btn btn-danger me-2" onclick="eliminarGrupo(${grupo.id})">
                    Eliminar Grupo
                 </button>`;
        // Iniciar juego si está lleno
        if (numMiembros == grupo.max_miembros) {
            html += `<button class="btn btn-success me-2" onclick="iniciarJuego(${grupo.id})">
                        Iniciar Juego
                     </button>`;
        }
    }

    return html;
}

/**
 * Crear grupo (POST /groups)
 */
async function crearGrupo() {
    const nombre = document.getElementById('nombreGrupo').value.trim();
    const gymkhana_id = document.getElementById('gymkhanaId').value;
    const max_miembros = document.getElementById('capacidadGrupo').value;

    try {
        const resp = await fetch('/groups', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                nombre,
                gymkhana_id,
                max_miembros
            })
        });
        const data = await resp.json();
        if (!resp.ok) throw new Error(data.message || 'Error creando grupo');

        // Cerrar modal de crear
        const createModal = document.getElementById('createGroupModal');
        const modal = bootstrap.Modal.getInstance(createModal);
        modal.hide();

        // Refrescar lista
        await cargarGrupos();

        alert('Grupo creado correctamente.');

        // Limpia el formulario
        document.getElementById('formCrearGrupo').reset();

    } catch (err) {
        alert(err.message);
        console.error('Error creando grupo:', err);
    }
}

/**
 * Unirse a un grupo (POST /groups/{id}/join)
 */
async function unirseGrupo(groupId) {
    try {
        const resp = await fetch(`/groups/${groupId}/join`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await resp.json();
        if (!resp.ok) throw new Error(data.message || 'Error al unirse');

        alert('Te has unido al grupo');

        // Tras unirse, recargamos el detalle
        await verDetalleGrupo(groupId);
        // Y refrescamos la lista principal si quieres
        await cargarGrupos();

    } catch (err) {
        alert(err.message);
        console.error('Error al unirse al grupo:', err);
    }
}

/**
 * Salir de un grupo (DELETE /groups/{id}/leave)
 */
async function salirGrupo(groupId) {
    if (!confirm('¿Estás seguro de salir del grupo?')) return;

    try {
        const resp = await fetch(`/groups/${groupId}/leave`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await resp.json();
        if (!resp.ok) throw new Error(data.message || 'Error al salir');

        alert(data.message || 'Has salido del grupo');
        // Cierra el modal o recarga detalle
        await verDetalleGrupo(groupId);
        await cargarGrupos();

    } catch (err) {
        alert(err.message);
        console.error('Error al salir del grupo:', err);
    }
}

/**
 * Expulsar miembro (DELETE /groups/{groupId}/kick/{userId})
 */
async function expulsarMiembro(groupId, userId) {
    if (!confirm('¿Expulsar a este miembro?')) return;

    try {
        const resp = await fetch(`/groups/${groupId}/kick/${userId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await resp.json();
        if (!resp.ok) throw new Error(data.mensaje || 'Error al expulsar miembro');

        alert('Miembro expulsado');
        // Recargamos el detalle
        await verDetalleGrupo(groupId);
        await cargarGrupos();

    } catch (err) {
        alert(err.message);
        console.error('Error al expulsar miembro:', err);
    }
}

/**
 * Eliminar grupo (DELETE /groups/{id})
 */
async function eliminarGrupo(groupId) {
    if (!confirm('¿Eliminar este grupo?')) return;

    try {
        const resp = await fetch(`/groups/${groupId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await resp.json();
        if (!resp.ok) throw new Error(data.message || 'Error al eliminar grupo');

        alert('Grupo eliminado correctamente');

        // Cierra el modal
        const detailModalEl = document.getElementById('detalleGroupModal');
        const modalInstance = bootstrap.Modal.getInstance(detailModalEl);
        modalInstance.hide();

        // Refrescar lista
        await cargarGrupos();

    } catch (err) {
        alert(err.message);
        console.error('Error al eliminar grupo:', err);
    }
}

/**
 * Iniciar juego (POST /groups/{id}/start)
 */
async function iniciarJuego(groupId) {
    try {
        const resp = await fetch(`/groups/${groupId}/start`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await resp.json();
        if (!resp.ok) throw new Error(data.message || 'Error al iniciar juego');

        alert(data.message || 'Juego iniciado');
        // Recargar el detalle
        await verDetalleGrupo(groupId);

    } catch (err) {
        alert(err.message);
        console.error('Error al iniciar juego:', err);
    }
}

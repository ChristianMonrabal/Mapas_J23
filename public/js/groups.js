document.addEventListener('DOMContentLoaded', function() {
    $('#joinGroupModal').on('shown.bs.modal', obtenerGrupos);

    const formCrearGrupo = document.getElementById('form-crear-grupo');
    formCrearGrupo.addEventListener('submit', async function(e) {
        e.preventDefault();

        const nombre       = document.getElementById('groupName').value;
        const gymkhana_id  = document.getElementById('gymkhanaSelect').value;
        const max_miembros = document.getElementById('groupCapacity').value;

        try {
            const respuesta = await fetch('/groups/crear', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    name: nombre,
                    gymkhana_id,
                    max_miembros
                })
            });

            const datos = await respuesta.json();
            if (!respuesta.ok) throw new Error(datos.message || 'Error creando grupo');

            console.log('Grupo creado:', datos.grupo);
            $('#createGroupModal').modal('hide');
            obtenerGrupos();
        } catch (error) {
            console.error('Error al crear el grupo:', error);
            alert(error.message);
        }
    });
});

async function obtenerGrupos() {
    try {
        const respuesta = await fetch('/groups', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });

        const { groups } = await respuesta.json();
        mostrarGrupos(groups);
    } catch (error) {
        console.error('Error al obtener los grupos:', error);
    }
}

function mostrarGrupos(grupos) {
    const contenedor = document.getElementById('listaGrupos');
    contenedor.innerHTML = grupos.length === 0
        ? '<p class="text-center">No hay grupos disponibles.</p>'
        : grupos.map(g => `
            <div class="border p-2 mb-2">
                <p><strong>Nombre:</strong> ${g.name}</p>
                <p><strong>Código:</strong> ${g.codigo}</p>
                <p><strong>Gimkhana:</strong> ${g.gymkhana?.nombre || 'No asignada'}</p>
                <p><strong>Capacidad:</strong> ${g.max_miembros}</p>
                <p><strong>Miembros:</strong> ${g.users.length}</p>
                <button class="btn btn-primary btn-join" data-id="${g.id}">Unirse</button>
            </div>
        `).join('');

    document.querySelectorAll('.btn-join').forEach(btn => {
        btn.addEventListener('click', async () => {
            const grupoId = btn.dataset.id;
            try {
                const respuesta = await fetch(`/groups/${grupoId}/unirse`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const datos = await respuesta.json();
                if (!respuesta.ok) throw new Error(datos.mensaje);
                console.log('Te has unido al grupo:', datos);
                obtenerGrupos();
            } catch (error) {
                console.error('Error al unirse al grupo:', error);
                alert(error.message);
            }
        });
    });
}

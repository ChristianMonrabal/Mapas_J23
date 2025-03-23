document.addEventListener('DOMContentLoaded', function () {
    // Obtener el elemento del modal
    var joinGroupModalEl = document.getElementById('joinGroupModal');

    // Escuchar el evento de que el modal se muestra
    joinGroupModalEl.addEventListener('shown.bs.modal', function () {
        loadGroups();
    });
});

function loadGroups() {
    const groupsListEl = document.getElementById('groupsList');
    groupsListEl.innerHTML = '<p class="text-center">Cargando grupos...</p>';

    // Se asume que el endpoint devuelve un JSON con un arreglo de grupos
    fetch('/api/groups')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al obtener los grupos');
            }
            return response.json();
        })
        .then(data => {
            if (data.length > 0) {
                // Crear una lista de grupos
                const ul = document.createElement('ul');
                ul.classList.add('list-group');
                data.forEach(group => {
                    const li = document.createElement('li');
                    li.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
                    li.textContent = group.nombre;

                    const joinButton = document.createElement('button');
                    joinButton.classList.add('btn', 'btn-success', 'btn-sm');
                    joinButton.textContent = 'Unirse';
                    joinButton.onclick = function () {
                        joinGroup(group.id);
                    };

                    li.appendChild(joinButton);
                    ul.appendChild(li);
                });
                groupsListEl.innerHTML = '';
                groupsListEl.appendChild(ul);
            } else {
                groupsListEl.innerHTML = '<p class="text-center">No hay grupos disponibles.</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            groupsListEl.innerHTML = '<p class="text-center text-danger">Error al cargar los grupos.</p>';
        });
}

function joinGroup(groupId) {
    fetch(`/groups/join/${groupId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({})
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error al unirse al grupo');
        }
        return response.json();
    })
    .then(data => {
        alert('Te has unido al grupo exitosamente');
        // Cerrar el modal usando la API de Bootstrap 5
        var joinModal = bootstrap.Modal.getInstance(document.getElementById('joinGroupModal'));
        joinModal.hide();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Hubo un error al unirse al grupo');
    });
}

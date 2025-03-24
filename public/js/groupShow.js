// groupShow.js

document.addEventListener('DOMContentLoaded', () => {
    // Obtenemos el ID del grupo desde el atributo data-group-id
    const groupInfo = document.getElementById('group-info');
    if (!groupInfo) return;

    window.GROUP_ID = groupInfo.getAttribute('data-group-id');

    // Aquí podrías cargar detalles adicionales si lo deseas vía AJAX,
    // pero en este ejemplo ya se muestran en el Blade con $group.
});

/**
 * Expulsar a un miembro del grupo (solo el creador).
 * @param {number} userId 
 */
window.expulsarMiembro = async function(userId) {
    if (!confirm('¿Deseas expulsar a este miembro?')) return;

    try {
        const resp = await fetch(`/groups/${GROUP_ID}/kick/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        const data = await resp.json();

        if (!resp.ok) {
            throw new Error(data.mensaje || 'Error al expulsar el miembro');
        }

        alert('Miembro expulsado del grupo.');
        // Recarga la página para actualizar la lista de miembros
        location.reload();

    } catch (error) {
        alert(error.message);
    }
};

/**
 * Eliminar grupo (solo el creador).
 */
window.eliminarGrupo = async function() {
    if (!confirm('¿Estás seguro de que deseas eliminar este grupo?')) return;

    try {
        const resp = await fetch(`/groups/${GROUP_ID}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        const data = await resp.json();

        if (!resp.ok) {
            throw new Error(data.message || 'Error al eliminar el grupo');
        }

        alert('Grupo eliminado correctamente');
        // Redirige donde necesites, por ejemplo a la lista de grupos
        window.location.href = '/groups';

    } catch (error) {
        alert(error.message);
    }
};

/**
 * Iniciar juego (solo el creador).
 */
window.iniciarJuego = async function() {
    try {
        const resp = await fetch(`/groups/${GROUP_ID}/start`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        const data = await resp.json();

        if (!resp.ok) {
            throw new Error(data.mensaje || 'Error al iniciar el juego');
        }

        alert(data.mensaje || 'Juego iniciado');
        // Aquí podrías redirigir a la pantalla del juego 
        // window.location.href = `/juego/${GROUP_ID}`;

    } catch (error) {
        alert(error.message);
    }
};

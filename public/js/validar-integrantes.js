setInterval(() => {
  fetch('/grupo/estado')
    .then(response => {
      if (!response.ok) {
        throw new Error('Error en la respuesta del servidor: ' + response.status);
      }
      return response.json();
    })
    .then(data => {
      console.log('Estado del grupo:', data);

      if (data.game_started) {
        // Verificar que tengamos los IDs necesarios para la redirección
        if (data.group && data.group.id && data.gymkhana_id) {
          console.log('Juego iniciado. Redirigiendo a:', `/dashboard/gimcana?group_id=${data.group.id}&gymkhana_id=${data.gymkhana_id}`);
          window.location.href = `/dashboard/gimcana?group_id=${data.group.id}&gymkhana_id=${data.gymkhana_id}`;
        } else {
          console.error('No se pudo redireccionar. Faltan datos:',
            'group_id:', data.group ? data.group.id : 'no disponible',
            'gymkhana_id:', data.gymkhana_id || 'no disponible');
        }
      } else {
        console.log('El juego aún no ha sido iniciado.');
      }
    })
    .catch(error => {
      console.error('Error al verificar el estado del grupo:', error);
    });
}, 5000); // Verificar cada 5 segundos
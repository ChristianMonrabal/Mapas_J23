setInterval(() => {
    fetch('/grupo/estado')
      .then(response => response.json())
      .then(data => {
        if (data.game_started) {
          // Redirige a la pantalla del juego
          window.location.href = '/dashboard/gimcana';
        }
      })
      .catch(error => console.error('Error al obtener el estado del grupo:', error));
  }, 5000); // Consulta cada 5 segundos
  
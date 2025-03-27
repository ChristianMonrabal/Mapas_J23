
var grupoActivo = { id: 1, gymkhana_id: 1, gymkhana_id2: 1 };

iniciarJuego();

function iniciarJuego() {
    if (!grupoActivo) {
        console.error("Error: No hay grupo activo.");
        return;
    }

    var map = L.map("map");

    navigator.geolocation.getCurrentPosition(pos => {
        var lat = pos.coords.latitude;
        var lng = pos.coords.longitude;

        map.setView([lat, lng], 18);
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(map);

        var miUbicacion = L.marker([lat, lng], {
            icon: L.divIcon({
                className: 'user-location-marker',
                html: '<i class="fas fa-circle"></i>',
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            })
        }).addTo(map);

        var radioSeguridad = L.circle([lat, lng], {
            color: "blue", fillColor: "blue", fillOpacity: 0.3, radius: 85
        }).addTo(map);

        cargarSitios(map, miUbicacion, radioSeguridad);

        navigator.geolocation.watchPosition(pos => {
            miUbicacion.setLatLng([pos.coords.latitude, pos.coords.longitude]);
            radioSeguridad.setLatLng([pos.coords.latitude, pos.coords.longitude]);
            verificarProgreso(miUbicacion);
        });

    }, error => {
        alert("No se pudo obtener la ubicación.");
    }, {
        enableHighAccuracy: true,
        timeout: 3000,
        maximumAge: 0
    });
}

function cargarSitios(map, miUbicacion, radioSeguridad) {
    if (!grupoActivo) {
        console.error("Error: No hay grupo activo.");
        return;
    }

    fetch("/buscarGymkhana/" + grupoActivo.id + "/" + grupoActivo.id)
        .then(response => {
            if (!response.ok) {
                throw new Error("Error al obtener los datos. Código de estado: " + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log(data);
            grupoActivo = data.grupo;
            grupoActivo.progreso = data.progreso.map(p => p.checkpoint_id);

            var sitios = data.sitios;
            sitios.forEach(sitio => {
                console.log(sitio);
                var icono = L.icon({
                    iconUrl: "https://cdn-icons-png.flaticon.com/128/2830/2830191.png", 
                    iconSize: [40, 40]
                });

                var marcador = L.marker([sitio.latitude, sitio.longitude], { icon: icono })
                    .addTo(map)
                    .bindPopup("<strong>" + sitio.name + "</strong><br>" + sitio.description);
            });
        })
        .catch(error => console.error("Error al cargar sitios:", error));
}

function verificarProgreso(miUbicacion) {

    // Verifica si hay un grupo activo antes de continuar
    if (!grupoActivo) {
        console.error("Error: No hay grupo activo.");
        return;
    }

    // Hace una petición al backend para obtener los datos de la gymkhana y los usuarios del grupo
    fetch("/buscarGymkhana/" + grupoActivo.id + "/" + grupoActivo.id)
        .then(response => {
            if (!response.ok) {
                throw new Error("Error al obtener los datos. Código de estado: " + response.status);
            }
            return response.json();
        })
        .then(data => {
            
            var sitios = data.sitios;  // Lista de sitios de la gymkhana
            var usuariosDelGrupo = data.usuariosDelGrupo; // Lista de usuarios del grupo
            var gymkhanaId = data.gymkhanaId; // ID de la gymkhana actual

            // Recorre cada sitio de la gymkhana
            sitios.forEach((sitio) => {

                if (sitio.is_gymkhana && sitio.completed === 0) {

                    var usuariosEnRango = [];
                    // Obtener el usuario de la sesión actual
                    var usuarioSesion = usuariosDelGrupo.find(usuario => usuario.id === usuarioActualId);

                    if (usuarioSesion) {
                        var distancia = calcularDistancia(
                            miUbicacion.getLatLng().lat, 
                            miUbicacion.getLatLng().lng, 
                            sitio.latitude,
                            sitio.longitude
                        );

                        if (distancia < 85) {
                            usuariosEnRango.push(usuarioSesion.id);
                        }
                    }


                    // Marcar el progreso de los usuarios en la base de datos
                    usuariosEnRango.forEach(usuarioId => {
                        fetch("/actualizarProgresoUsuario/" + usuarioId + "/" + sitio.id, {
                            method: "POST",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify({ completed: 1 })
                        });
                    });

                    // Verifica en el backend si TODOS los usuarios del grupo han completado este checkpoint
                    fetch("/verificarUsuariosCompletados/" + grupoActivo.id)
                        .then(response => response.json())
                        .then(resultado => {
                            if (resultado.todosCompletados) {
                                alert(sitio.pista);

                                // Marcar el sitio como completado en checkpoints
                                fetch("/actualizarCheckpointCompletado/" + sitio.id, {
                                    method: "POST",
                                    headers: { "Content-Type": "application/json" },
                                    body: JSON.stringify({ completed: 1 })
                                })
                                .then(() => {
                                    // 🔹 Verificar si la gymkhana está completa
                                    fetch("/verificarGymkhanaCompletada/" + gymkhanaId)
                                        .then(response => response.json())
                                        .then(resultado => {
                                            if (resultado.gymkhanaCompletada) {
                                                // Si la gymkhana está completa, marcarla como terminada
                                                fetch("/actualizarProgresoGimcana/" + grupoActivo.id, {
                                                    method: "POST",
                                                    headers: { "Content-Type": "application/json" },
                                                    body: JSON.stringify({ completed: 1 })
                                                })
                                                .then(() => {
                                                    // 🔹 Reiniciar el progreso de todos los usuarios a 0 en `group_users`
                                                    fetch("/reiniciarProgresoUsuarios/" + grupoActivo.id, {
                                                        method: "POST",
                                                        headers: { "Content-Type": "application/json" }
                                                    });
                                                });
                                            }
                                        });
                                });
                            }
                        });
                }
            });
        })
        .catch(error => console.error("Error en verificarProgreso:", error));
}


function calcularDistancia(lat1, lon1, lat2, lon2) {
    var rad = Math.PI / 180;
    var R = 6371;
    var dLat = (lat2 - lat1) * rad;
    var dLon = (lon2 - lon1) * rad;
    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * rad) * Math.cos(lat2 * rad) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c * 1000;
}
// Obtener parámetros de la URL
var params = new URLSearchParams(window.location.search);
var grupoActivo = {
    id: parseInt(params.get('group_id')) || null,
    gymkhana_id: parseInt(params.get('gymkhana_id')) || null
};

// Verificar si los parámetros están presentes
if (!grupoActivo.id || !grupoActivo.gymkhana_id) {
    // Redirigir a la página de grupos si no están los parámetros
    window.location.href = "/groups";
} else {
    console.log('Grupo activo:', grupoActivo);
    iniciarJuego();
}

// Obtener el token CSRF desde el meta tag
var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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
            color: "blue", fillColor: "blue", fillOpacity: 0.3, radius: 20
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

    fetch(`/buscarGymkhana/${grupoActivo.gymkhana_id}/${grupoActivo.id}`)
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
                    iconUrl: "/img/icon.png", 
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
    fetch(`/buscarGymkhana/${parseInt(params.get('gymkhana_id'))}/${grupoActivo.id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error("Error al obtener los datos. Código de estado: " + response.status);
            }
            return response.json();
        })
        .then(data => {
            var sitios = data.sitios;  // Lista de sitios de la gymkhana
            var usuariosGrupo = data.todosLosUsuariosDeUnGrupo;
            var idUsuarioActual = data.idUsuarioActual;
            
            sitios.forEach((sitio) => {

                if (sitio.is_gymkhana && sitio.completed === 0) {
                    
                    var usuarioSesion = usuariosGrupo.find(usuario => usuario === idUsuarioActual);
                    
                    if (usuarioSesion) {
                        var distancia = calcularDistancia(
                            miUbicacion.getLatLng().lat, 
                            miUbicacion.getLatLng().lng, 
                            sitio.latitude,
                            sitio.longitude
                        );
                        if (distancia < 20) {
                            // Marcar el progreso del usuario en la base de datos
                            fetch("/actualizarProgresoUsuario/" + usuarioSesion, {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": csrfToken
                                },
                                body: JSON.stringify({ completed: 1 })
                            })
                            .then(() => {
                                // Verifica si TODOS los usuarios del grupo han completado este checkpoint
                                return fetch(`/verificarUsuariosCompletados/${grupoActivo.id}`);
                            })
                            .then(response => response.json())
                            .then(resultado => {
                                if (resultado.todosCompletados) {
                                    Swal.fire({
                                        title: 'Pista',
                                        text: sitio.pista,
                                        icon: 'info',
                                        confirmButtonText: 'Aceptar'
                                    }).then(() => {
                                        // Marcar el sitio como completado en checkpoints
                                        return fetch("/actualizarCheckpointCompletado/" + sitio.id, {
                                            method: "POST",
                                            headers: {
                                                "Content-Type": "application/json",
                                                "X-CSRF-TOKEN": csrfToken
                                            },
                                            body: JSON.stringify({ completed: 1 })
                                        });
                                    })
                                    .then(() => {
                                        // 🔹 Verificar si la gymkhana está completa
                                        return fetch(`/verificarGymkhanaCompletada/${parseInt(params.get('gymkhana_id'))}`);
                                    })
                                    .then(response => response.json())
                                    .then(resultado => {
                                        if (resultado.gymkhanaCompletada) {
                                            // Si la gymkhana está completa, marcarla como terminada
                                            return fetch(`/actualizarProgresoGimcana/${grupoActivo.id}`, {
                                                method: "POST",
                                                headers: {
                                                    "Content-Type": "application/json",
                                                    "X-CSRF-TOKEN": csrfToken
                                                },
                                                body: JSON.stringify({ sitioId: sitio.id, completed: 1 })
                                            })
                                        }
                                    })
                                    .then(() => {
                                        // 🔹 Verificar si la gymkhana ha finalizado aquí mismo
                                        return fetch(`/verificarGymkhanaFinalizada/${parseInt(params.get('gymkhana_id'))}`)
                                    })
                                    .then(response => response.json())
                                    .then(resultado => {
                                        if (resultado.gymkhanaCompletada) {
                                            // Si la gymkhana está completa, redirigir a la página de "Gimkhana Acabada"
                                            window.location.href = "/gimcanaAcabada";
                                        }
                                    })
                                    .then(() => {
                                        // 🔹 Reiniciar el progreso de todos los usuarios a 0 en `group_users`
                                        return fetch(`/reiniciarProgresoUsuarios/${grupoActivo.id}`, {
                                            method: "POST",
                                            headers: {
                                                "Content-Type": "application/json",
                                                "X-CSRF-TOKEN": csrfToken
                                            },
                                        });
                                    });
                                }
                            });
                        }
                    }
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
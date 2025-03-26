
var grupoActivo = { id: 1, gymkhana_id: 1 };

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
            icon: L.icon({ iconUrl: "https://cdn-icons-png.flaticon.com/128/3699/3699532.png", iconSize: [30, 30] })
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

    fetch("/buscarGymkhana/" + grupoActivo.gymkhana_id + "/" + grupoActivo.id)
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

    fetch("/buscarGymkhana/" + grupoActivo.gymkhana_id + "/" + grupoActivo.id)
        .then(response => response.json())
        .then(data => {
            var sitios = data.sitios;
            var progreso = data.progreso;  // Recogemos el progreso actual del grupo

            sitios.forEach((sitio) => {
                // Verificar si el sitio ya está desbloqueado
                if (sitio.completed === 0) {  // Solo procedemos si el sitio aún no está desbloqueado
                    // Comprobamos si todos los usuarios están dentro del rango de 85 metros
                    var todosEnRango = true;

                    // Iteramos sobre todos los usuarios del grupo
                    grupoActivo.usuarios.forEach(usuario => {
                        var distancia = calcularDistancia(
                            miUbicacion.getLatLng().lat, 
                            miUbicacion.getLatLng().lng, 
                            sitio.latitude,
                            sitio.longitude
                        );

                        // Si algún usuario está fuera del rango, marcamos como falso
                        if (distancia >= 85) {
                            todosEnRango = false;
                        }
                    });

                    // Si todos los usuarios están en el rango adecuado, desbloqueamos la pista
                    if (todosEnRango) {
                        alert("¡Pista desbloqueada en " + sitio.name + "!");
                        grupoActivo.progreso.push(sitio.id);  // Añadimos el sitio al progreso

                        
                        // Marcar el sitio como completado en el servidor (para no volver a desbloquearlo)
                        fetch("/actualizarCheckpoint/" + sitio.id, {
                            method: "POST",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify({ completed: 1 })
                        });

                        // Actualizar el progreso en el backend
                        fetch("/actualizarProgreso/" + grupoActivo.id, {
                            method: "POST",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify({ sitioId: sitio.id })
                        });
                        
                        // Salir del ciclo una vez que se desbloquea el sitio
                        return;
                    }
                }
            });
        });
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
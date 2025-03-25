// document.getElementById("unirseGrupoBtn").addEventListener("click", unirseAGrupo);

var grupoActivo = { id: 1, gymkhana_id: 1 };

// function unirseAGrupo() {
//     var codigoIngresado = document.getElementById("codigoGrupo").value;
//     fetch("/api/unirse-grupo/" + codigoIngresado)
//         .then(response => response.json())
//         .then(data => {
//             if (data.success) {
//                 grupoActivo = data.grupo; // Se asigna desde la respuesta del servidor
//                 document.getElementById("mensaje").innerHTML = "¡Juego iniciado!";

//                 // Ocultar formulario y mostrar el mapa
//                 document.getElementById("unirseGrupo").style.display = "none";
//                 document.getElementById("map").style.display = "block";
                
//                 iniciarJuego();
//             } else {
//                 document.getElementById("mensaje").innerHTML = "Código incorrecto.";
//             }
//         })
//         .catch(error => console.error("Error al unirse al grupo:", error));
// }

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
                var icono = L.icon({
                    iconUrl: sitio.icono, 
                    iconSize: [40, 40]
                });

                var marcador = L.marker([sitio.latitude, sitio.longitude], { icon: icono })
                    .addTo(map)
                    .bindPopup("<strong>" + sitio.name + "</strong><br>" + sitio.pista);
            });
        })
        .catch(error => console.error("Error al cargar sitios:", error));
}

function verificarProgreso(miUbicacion) {
    
    if (!grupoActivo || !grupoActivo.progreso) return;

    fetch("/buscarGymkhana/" + grupoActivo.gymkhana_id + "/" + grupoActivo.id)
        .then(response => response.json())
        .then(data => {
            var sitios = data.sitios;
            sitios.forEach(sitio => {
                if (!grupoActivo.progreso.includes(sitio.id)) {
                    var distancia = calcularDistancia(
                        miUbicacion.getLatLng().lat, 
                        miUbicacion.getLatLng().lng, 
                        sitio.latitude, 
                        sitio.longitude
                    );

                    if (distancia < 85) {
                        alert("¡Pista desbloqueada en " + sitio.name + "!");
                        grupoActivo.progreso.push(sitio.id);

                        fetch("/api/actualizar-progreso/" + grupoActivo.id, {
                            method: "POST",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify({ sitioId: sitio.id })
                        });
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
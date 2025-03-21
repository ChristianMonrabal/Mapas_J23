// Inicializar el mapa (sin setView inicial)
var map = L.map('map', {
    zoomControl: false
});

// Agregar capa de OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

// Ejemplo: Agregar un marcador en Madrid
var popupContent = 'Madrid';
L.marker([40.416775, -3.703790])
    .addTo(map)
    .bindPopup(popupContent)
    .openPopup()
    .on('contextmenu', function() {
        alert('Has presionado ' + popupContent);
    });

// Función para cargar los lugares
function loadPlaces() {
    const loading = document.getElementById('loading');
    loading.style.display = 'block';

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch('/places/list', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Limpiar marcadores existentes si es necesario
                map.eachLayer((layer) => {
                    if (layer instanceof L.Marker) {
                        map.removeLayer(layer);
                    }
                });

                // Agregar nuevos marcadores
                data.places.forEach(place => {
                    const marker = L.marker([place.latitude, place.longitude])
                        .addTo(map)
                        .bindPopup(`
                        <strong>${place.name}</strong><br>
                        ${place.address}<br>
                        ${place.description}
                    `);

                    marker.on('contextmenu', function() {
                        alert('Has seleccionado: ' + place.name);
                    });
                });
            } else {
                console.error('Error al cargar los lugares:', data.error);
            }
        })
        .catch(error => {
            console.error('Error en la petición:', error);
        })
        .finally(() => {
            loading.style.display = 'none';
        });
}

// Función para obtener la ubicación y cargar lugares
function initializeMapAndLoadPlaces() {
    const loading = document.getElementById('loading');
    loading.style.display = 'block';
    loading.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Obteniendo tu ubicación...';

    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                // Centrar el mapa en la ubicación del usuario
                map.setView([latitude, longitude], 16);

                // Agregar marcador de la ubicación del usuario
                const userMarker = L.marker([latitude, longitude], {
                    icon: L.divIcon({
                        className: 'user-location-marker',
                        html: '<i class="fas fa-dot-circle"></i>',
                        iconSize: [20, 20]
                    })
                }).addTo(map);

                userMarker.bindPopup('Tu ubicación actual').openPopup();

                // Cargar los lugares después de centrar el mapa
                loadPlaces();
            },
            function(error) {
                console.error("Error obteniendo ubicación:", error);
                loading.innerHTML = 'Error al obtener tu ubicación';
                // Si falla la geolocalización, centrar en España y cargar lugares
                map.setView([40.416775, -3.703790], 16);
                loadPlaces();
                setTimeout(() => {
                    loading.style.display = 'none';
                }, 3000);
            }, {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            }
        );
    } else {
        loading.innerHTML = 'Tu navegador no soporta geolocalización';
        // Si no hay soporte de geolocalización, centrar en España y cargar lugares
        map.setView([40.416775, -3.703790], 16);
        loadPlaces();
        setTimeout(() => {
            loading.style.display = 'none';
        }, 3000);
    }
}

// Inicializar el mapa cuando se carga el DOM
document.addEventListener('DOMContentLoaded', function() {
    initializeMapAndLoadPlaces();
});

document.getElementById('sidebar-btn').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('active');
});

document.getElementById('map').addEventListener('click', function() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar.classList.contains('active')) {
        sidebar.classList.remove('active');
    }
});

// Agregar botón de ubicación al mapa
L.Control.LocationButton = L.Control.extend({
    onAdd: function(map) {
        const button = L.DomUtil.create('button', 'location-button');
        button.innerHTML = '<i class="fas fa-location-arrow"></i>';
        button.title = 'Obtener mi ubicación';

        button.onclick = function() {
            initializeMapAndLoadPlaces();
        };

        return button;
    }
});

// Agregar el control al mapa
new L.Control.LocationButton({ position: 'bottomright' }).addTo(map);

// Estilos para el botón y el marcador
const style = document.createElement('style');
style.textContent = `
    .location-button {
        background: white;
        border: 2px solid rgba(0,0,0,0.2);
        border-radius: 4px;
        padding: 5px 8px;
        cursor: pointer;
        box-shadow: 0 1px 5px rgba(0,0,0,0.65);
    }

    .location-button:hover {
        background: #f4f4f4;
    }

    .user-location-marker {
        color: #2196F3;
        font-size: 20px;
        text-shadow: 2px 2px 3px rgba(0,0,0,0.3);
    }

    .user-location-marker i {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.2);
            opacity: 0.8;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);
// Inicializar el mapa (sin setView inicial)
var map = L.map('map', {
    zoomControl: false
});

// Variable global para el marcador del usuario
var userMarker = null;
var watchId = null;

// Agregar capa de OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

// Función para actualizar la posición del marcador
function updateUserLocation(position) {
    const latitude = position.coords.latitude;
    const longitude = position.coords.longitude;

    // Si el marcador existe, elimínalo primero
    if (userMarker) {
        map.removeLayer(userMarker);
    }

    // Crear un nuevo marcador
    userMarker = L.marker([latitude, longitude], {
        icon: L.divIcon({
            className: 'user-location-marker',
            html: '<i class="fas fa-circle"></i>',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        })
    }).addTo(map);

    userMarker.bindPopup('Estás aquí');
}

// Función para iniciar el seguimiento de ubicación
function startLocationTracking() {
    if ("geolocation" in navigator) {
        watchId = navigator.geolocation.watchPosition(
            updateUserLocation,
            function(error) {
                console.error("Error en el seguimiento:", error);
            }, {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            }
        );
    }
}

// Función para obtener la ubicación inicial y cargar lugares
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

                // Crear el marcador inicial
                updateUserLocation(position);

                // Iniciar el seguimiento de ubicación
                startLocationTracking();

                // Cargar los lugares después de centrar el mapa
                loadPlaces();
                loading.style.display = 'none';
            },
            function(error) {
                console.error("Error obteniendo ubicación:", error);
                loading.innerHTML = 'Error al obtener tu ubicación';
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
        map.setView([40.416775, -3.703790], 16);
        loadPlaces();
        setTimeout(() => {
            loading.style.display = 'none';
        }, 3000);
    }
}

// Limpiar el seguimiento cuando se cierra la página
window.addEventListener('beforeunload', function() {
    if (watchId !== null) {
        navigator.geolocation.clearWatch(watchId);
    }
});

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
        button.title = 'Centrar en mi ubicación';

        button.onclick = function() {
            if (userMarker) {
                const userLatLng = userMarker.getLatLng();
                map.setView(userLatLng, 16);
            }
        };

        return button;
    }
});

// Agregar el control al mapa
new L.Control.LocationButton({ position: 'bottomright' }).addTo(map);

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
                data.places.forEach(place => {
                    const marker = L.marker([place.latitude, place.longitude])
                        .addTo(map)
                        .bindPopup(`<strong>${place.name}</strong>`);

                    marker.on('click', function() {
                        showPlaceDetails(place.id);
                    });
                });
            }
        })
        .catch(error => console.error('Error:', error))
        .finally(() => {
            loading.style.display = 'none';
        });
}

function showPlaceDetails(placeId) {
    fetch(`/places/${placeId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const panel = document.getElementById('place-details');
                const tagsContainer = document.getElementById('place-tags');

                // Actualizar información básica
                document.getElementById('place-name').textContent = data.place.name;
                document.getElementById('place-address').textContent = data.place.address;
                document.getElementById('place-description').textContent = data.place.description;

                // Limpiar y actualizar tags
                tagsContainer.innerHTML = '';
                if (data.place.tags && data.place.tags.length > 0) {
                    data.place.tags.forEach(tag => {
                        const tagElement = document.createElement('span');
                        tagElement.className = 'tag';
                        tagElement.textContent = tag.name;
                        tagsContainer.appendChild(tagElement);
                    });
                }

                panel.classList.add('active');
            }
        })
        .catch(error => console.error('Error:', error));
}

// Agregar manejador para cerrar el panel
document.querySelector('.close-panel-btn').addEventListener('click', function() {
    document.getElementById('place-details').classList.remove('active');
});

document.getElementById('map').addEventListener('click', function() {
    const place_details = document.getElementById('place-details');
    if (place_details.classList.contains('active')) {
        place_details.classList.remove('active');
    }
});
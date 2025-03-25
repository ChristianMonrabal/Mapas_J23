// Inicializar el mapa
var map = L.map('map', {
    zoomControl: false
});

// Variable global para el marcador del usuario y el círculo
var userMarker = null;
var userRadius = null;
var watchId = null;

// Variable global para almacenar los marcadores
let markers = [];

// Variable global para almacenar los tags activos
let activeTags = new Set();

// Agregar capa de OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

// Ocultar el logo de OpenStreetMap
document.querySelector('.leaflet-control-attribution').style.display = 'none';

// Función para actualizar la posición del marcador
function updateUserLocation(position) {
    const latitude = position.coords.latitude;
    const longitude = position.coords.longitude;

    // Si el marcador existe, elimínalo primero
    if (userMarker) {
        map.removeLayer(userMarker);
    }
    // Si el círculo existe, elimínalo también
    if (userRadius) {
        map.removeLayer(userRadius);
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

    // Crear el círculo alrededor del usuario
    userRadius = L.circle([latitude, longitude], {
        radius: 10, // Radio en metros
        color: '#2196F3', // Color del borde
        fillColor: '#2196F3', // Color del relleno
        fillOpacity: 0.2, // Opacidad del relleno
        weight: 1 // Grosor del borde
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
    loadTags();
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
        button.innerHTML = '<i id="location-button" class="fa-solid fa-location-crosshairs"></i>';
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

// Modificar la función loadPlaces para guardar los marcadores
function loadPlaces() {
    // Limpiar marcadores existentes
    clearMarkers();

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
                    addMarker(place);
                });
            }
        })
        .catch(error => console.error('Error:', error))
        .finally(() => {
            loading.style.display = 'none';
        });
}

// Función para añadir un marcador
function addMarker(place) {
    const customIcon = L.icon({
        iconUrl: '/img/icon.png',
        iconSize: [50, 50],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34]
    });

    const marker = L.marker([place.latitude, place.longitude], {
            icon: customIcon
        })
        .addTo(map)
        .bindPopup(`<strong>${place.name}</strong>`);

    marker.on('click', function() {
        showPlaceDetails(place.id);
    });

    markers.push(marker);
}

// Función para limpiar todos los marcadores
function clearMarkers() {
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];
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
                // Actualizar imagen con mejor manejo y logs
                const placeImage = document.getElementById('place-image');
                console.log('Datos de imagen:', data.place.image); // Log para depuración

                if (data.place.image) {
                    const imageUrl = `/storage/${data.place.image}`;
                    console.log('URL completa:', imageUrl); // Log para depuración

                    // Intentar cargar la imagen
                    placeImage.onerror = function() {
                        console.error('Error al cargar la imagen:', imageUrl);
                        this.src = '/img/no_img.png';
                        this.alt = 'Imagen no disponible';
                    };

                    placeImage.onload = function() {
                        console.log('Imagen cargada correctamente:', imageUrl);
                    };

                    placeImage.src = imageUrl;
                    placeImage.alt = data.place.name;
                } else {
                    console.log('No hay imagen definida');
                    placeImage.src = '/img/no_img.png';
                    placeImage.alt = 'Imagen no disponible';
                }

                // Actualizar información básica
                document.getElementById('place-name').textContent = data.place.name;
                document.getElementById('place-address').innerHTML = '<i class="fa-solid fa-location-dot"></i> ' + data.place.address;
                document.getElementById('place-description').textContent = data.place.description;

                // Limpiar y actualizar tags
                const tagsContainer = document.getElementById('place-tags');
                tagsContainer.innerHTML = '';
                if (data.place.tags && data.place.tags.length > 0) {
                    data.place.tags.forEach(tag => {
                        const tagElement = document.createElement('span');
                        tagElement.className = 'tag';
                        tagElement.textContent = tag.name;
                        tagsContainer.appendChild(tagElement);
                    });
                }

                const panel = document.getElementById('place-details');
                panel.classList.add('active');
            }
        })
        .catch(error => console.error('Error:', error));
}

// Agregar manejador para cerrar el panel
document.querySelector('.close-panel-btn').addEventListener('click', function() {
    document.getElementById('place-details').classList.remove('active');
});

// Al clicar id sidebar-btn, si place-details si tiene la clase active se cierra
document.getElementById('sidebar-btn').addEventListener('click', function() {
    const place_details = document.getElementById('place-details');
    if (place_details.classList.contains('active')) {
        place_details.classList.remove('active');
    }
});

// Al clicar id map, si place-details si tiene la clase active se cierra
document.getElementById('map').addEventListener('click', function() {
    const place_details = document.getElementById('place-details');
    if (place_details.classList.contains('active')) {
        place_details.classList.remove('active');
    }
});

// Función para cargar los tags
function loadTags() {
    fetch('/tags/list')
        .then(response => response.json())
        .then(data => {
            const tagsContainer = document.getElementById('tagsContainer');
            tagsContainer.innerHTML = ''; // Limpiar contenedor

            // Crear botón "Todos"
            const allButton = document.createElement('button');
            allButton.className = 'filter-tag active';
            allButton.textContent = 'Todos';
            allButton.onclick = () => {
                activeTags.clear();
                document.querySelectorAll('.filter-tag').forEach(tag => {
                    tag.classList.remove('active');
                });
                allButton.classList.add('active');
                searchPlaces(); // Llamar a búsqueda al cambiar filtros
            };
            tagsContainer.appendChild(allButton);

            // Crear botones para cada tag
            data.tags.forEach(tag => {
                const button = document.createElement('button');
                button.className = 'filter-tag';
                button.textContent = tag.name;
                button.onclick = () => {
                    button.classList.toggle('active');
                    if (button.classList.contains('active')) {
                        activeTags.add(tag.id);
                        allButton.classList.remove('active');
                    } else {
                        activeTags.delete(tag.id);
                        if (activeTags.size === 0) {
                            allButton.classList.add('active');
                        }
                    }
                    searchPlaces(); // Llamar a búsqueda al cambiar filtros
                };
                tagsContainer.appendChild(button);
            });
        })
        .catch(error => console.error('Error cargando tags:', error));
}

// Función unificada de búsqueda
function searchPlaces() {
    clearMarkers();
    const query = document.getElementById('searchInput').value.trim();

    const loading = document.getElementById('loading');
    loading.style.display = 'block';
    loading.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Buscando lugares...';

    // Obtener todos los lugares y aplicar filtros en el cliente
    fetch('/places/list', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let filteredPlaces = data.places;

                // Filtrar por texto de búsqueda
                if (query) {
                    const searchLower = query.toLowerCase();
                    filteredPlaces = filteredPlaces.filter(place =>
                        place.name.toLowerCase().includes(searchLower) ||
                        place.address.toLowerCase().includes(searchLower) ||
                        place.description.toLowerCase().includes(searchLower) ||
                        place.tags.some(tag => tag.name.toLowerCase().includes(searchLower))
                    );
                }

                // Filtrar por tags seleccionados
                if (activeTags.size > 0) {
                    filteredPlaces = filteredPlaces.filter(place =>
                        place.tags.some(tag => activeTags.has(tag.id))
                    );
                }

                // Mostrar lugares filtrados
                clearMarkers();
                filteredPlaces.forEach(place => addMarker(place));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            loading.innerHTML = 'Error en la búsqueda';
        })
        .finally(() => {
            loading.style.display = 'none';
        });
}

// Event listeners
document.getElementById('searchButton').addEventListener('click', function() {
    searchPlaces();
});

document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchPlaces();
    }
});
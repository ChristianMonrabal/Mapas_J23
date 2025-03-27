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

// Variable global para la ruta
let routingControl = null;

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
            function() {
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
    clearMarkers();

    const loading = document.getElementById('loading');
    loading.style.display = 'block';
    loading.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cargando lugares...';

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
            if (data.places) {
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

    if (routingControl) {
        map.removeControl(routingControl);
        routingControl = null;
    }
}

function showPlaceDetails(placeId) {
    const loading = document.getElementById('loading');
    loading.style.display = 'block';
    loading.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cargando detalles del lugar...';

    fetch(`/places/${placeId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const placeImage = document.getElementById('place-image');

                if (data.place.img && data.place.img !== null && data.place.img !== '') {
                    // Corregir la ruta de la imagen del lugar
                    const imgPath = data.place.img.startsWith('/') ? data.place.img : `/img/places/${data.place.img}`;
                    placeImage.src = imgPath;
                    placeImage.alt = data.place.name;
                    placeImage.onerror = function() {
                        this.src = '/img/no_img.png';
                        this.alt = 'Imagen no disponible';
                    };
                } else {
                    placeImage.src = '/img/no_img.png';
                    placeImage.alt = 'Imagen no disponible';
                }

                document.getElementById('place-name').textContent = data.place.name;
                document.getElementById('place-address').innerHTML = '<i class="fa-solid fa-location-dot"></i> ' + data.place.address;
                document.getElementById('place-description').textContent = data.place.description;

                // Actualizar tags
                const tagsContainer = document.getElementById('place-tags');
                tagsContainer.innerHTML = '';
                if (data.place.tags && data.place.tags.length > 0) {
                    data.place.tags.forEach(tag => {
                        const tagElement = document.createElement('div');
                        tagElement.className = 'tag';

                        // Añadir imagen del tag si existe
                        if (tag.img) {
                            const tagImg = document.createElement('img');
                            const tagImgPath = tag.img.startsWith('/') ? tag.img : `/img/tags/${tag.img}`;
                            tagImg.src = tagImgPath;
                            tagImg.alt = tag.name;
                            tagImg.className = 'tag-img';
                            tagImg.onerror = function() {
                                this.src = '/img/no_img.png';
                            };
                            tagElement.appendChild(tagImg);
                        }

                        // Añadir nombre del tag
                        const tagName = document.createElement('span');
                        tagName.textContent = tag.name;
                        tagElement.appendChild(tagName);

                        tagsContainer.appendChild(tagElement);
                    });
                }

                // Verificar si el lugar está en favoritos
                checkFavoriteStatus(placeId);

                // Configurar el botón de favoritos
                const favoriteBtn = document.getElementById('favorite-btn');
                favoriteBtn.onclick = () => toggleFavorite(placeId);

                // Configurar el botón de ruta
                const routeBtn = document.getElementById('route-btn');
                routeBtn.onclick = () => {
                    if (userMarker) {
                        calculateRoute(
                            userMarker.getLatLng(), [data.place.latitude, data.place.longitude]
                        );
                    } else {
                        alert('No se puede obtener tu ubicación actual');
                    }
                };

                document.getElementById('place-details').classList.add('active');
            }
        })
        .catch(error => console.error('Error:', error))
        .finally(() => {
            loading.style.display = 'none';
        });
}

// Función para verificar el estado de favorito
function checkFavoriteStatus(placeId) {
    fetch(`/favorites/${placeId}/check`)
        .then(response => response.json())
        .then(data => {
            updateFavoriteButton(data.isFavorite);
        })
        .catch(error => console.error('Error:', error));
}

// Función para actualizar el aspecto del botón
function updateFavoriteButton(isFavorite) {
    const btn = document.getElementById('favorite-btn');
    if (isFavorite) {
        btn.classList.remove('btn-outline-danger');
        btn.classList.add('btn-danger');
        btn.querySelector('span').textContent = 'Eliminar de favoritos';
    } else {
        btn.classList.remove('btn-danger');
        btn.classList.add('btn-outline-danger');
        btn.querySelector('span').textContent = 'Añadir a favoritos';
    }
}

// Función para alternar el estado de favorito
function toggleFavorite(placeId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/favorites/${placeId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            updateFavoriteButton(data.isFavorite);
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
            tagsContainer.innerHTML = '';

            // Crear botón "Todos"
            const allButton = document.createElement('button');
            allButton.className = 'filter-tag active';

            // Contenedor flex para icono y texto de "Todos"
            const allButtonContent = document.createElement('div');
            allButtonContent.style.display = 'flex';
            allButtonContent.style.alignItems = 'center';
            allButtonContent.style.gap = '8px';

            // Añadir imagen de mundo
            const worldImg = document.createElement('img');
            worldImg.src = '/img/tags/mundo.png';
            worldImg.alt = 'Todos';
            worldImg.className = 'tag-filter-img';
            allButtonContent.appendChild(worldImg);

            // Añadir texto
            const text = document.createElement('span');
            text.textContent = 'Todos';
            allButtonContent.appendChild(text);

            allButton.appendChild(allButtonContent);

            allButton.onclick = () => {
                activeTags.clear();
                document.querySelectorAll('.filter-tag').forEach(tag => {
                    tag.classList.remove('active');
                });
                allButton.classList.add('active');
                searchPlaces();
            };
            tagsContainer.appendChild(allButton);

            // Crear botón "Favoritos"
            const favButton = document.createElement('button');
            favButton.className = 'filter-tag';

            // Contenedor flex para icono y texto de "Favoritos"
            const favButtonContent = document.createElement('div');
            favButtonContent.style.display = 'flex';
            favButtonContent.style.alignItems = 'center';
            favButtonContent.style.gap = '8px';

            // Añadir imagen de estrella
            const starImg = document.createElement('img');
            starImg.src = '/img/tags/estrella.png';
            starImg.alt = 'Favoritos';
            starImg.className = 'tag-filter-img';
            favButtonContent.appendChild(starImg);

            // Añadir texto
            const favText = document.createElement('span');
            favText.textContent = 'Favoritos';
            favButtonContent.appendChild(favText);

            favButton.appendChild(favButtonContent);

            favButton.onclick = () => {
                activeTags.clear();
                document.querySelectorAll('.filter-tag').forEach(tag => {
                    tag.classList.remove('active');
                });
                favButton.classList.add('active');
                searchPlaces();
            };
            tagsContainer.appendChild(favButton);

            // Crear botones para cada tag
            data.tags.forEach(tag => {
                const button = document.createElement('button');
                button.className = 'filter-tag';

                // Contenedor flex para imagen y texto
                const buttonContent = document.createElement('div');
                buttonContent.style.display = 'flex';
                buttonContent.style.alignItems = 'center';
                buttonContent.style.gap = '8px';

                // Añadir imagen si existe
                if (tag.img) {
                    const img = document.createElement('img');
                    const imgPath = tag.img.startsWith('/') ? tag.img : `/img/tags/${tag.img}`;
                    img.src = imgPath;
                    img.alt = tag.name;
                    img.className = 'tag-filter-img';
                    buttonContent.appendChild(img);
                }

                // Añadir texto
                const tagName = document.createElement('span');
                tagName.textContent = tag.name;
                buttonContent.appendChild(tagName);

                button.appendChild(buttonContent);

                button.onclick = () => {
                    const allButton = document.querySelector('.filter-tag:nth-child(1)');
                    const favButton = document.querySelector('.filter-tag:nth-child(2)');

                    // Desactivar botones "Todos" y "Favoritos"
                    allButton.classList.remove('active');
                    favButton.classList.remove('active');

                    button.classList.toggle('active');
                    if (button.classList.contains('active')) {
                        activeTags.add(tag.id);
                    } else {
                        activeTags.delete(tag.id);
                        if (activeTags.size === 0) {
                            allButton.classList.add('active');
                        }
                    }
                    searchPlaces();
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
    const isFavoritesSelected = document.querySelector('.filter-tag:nth-child(2)').classList.contains('active');

    const loading = document.getElementById('loading');
    loading.style.display = 'block';
    loading.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Buscando lugares...';

    fetch('/places/list', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(async data => {
            let filteredPlaces = data.places;

            // Aplicar filtro de búsqueda por texto primero
            if (query) {
                const searchLower = query.toLowerCase();
                filteredPlaces = filteredPlaces.filter(place =>
                    place.name.toLowerCase().includes(searchLower) ||
                    place.address.toLowerCase().includes(searchLower) ||
                    place.description.toLowerCase().includes(searchLower) ||
                    place.tags.some(tag => tag.name.toLowerCase().includes(searchLower))
                );
            }

            // Aplicar filtro de tags si hay tags activos
            if (activeTags.size > 0) {
                filteredPlaces = filteredPlaces.filter(place =>
                    place.tags.some(tag => activeTags.has(tag.id))
                );
            }

            // Si el filtro de favoritos está activo, verificar favoritos
            if (isFavoritesSelected) {
                const favoritesChecks = await Promise.all(
                    filteredPlaces.map(place =>
                        fetch(`/favorites/${place.id}/check`)
                        .then(response => response.json())
                        .then(data => ({
                            ...place,
                            isFavorite: data.isFavorite
                        }))
                    )
                );
                filteredPlaces = favoritesChecks.filter(place => place.isFavorite);
            }

            // Limpiar y añadir los marcadores filtrados
            clearMarkers();
            filteredPlaces.forEach(place => addMarker(place));
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

// Función para calcular y mostrar la ruta
function calculateRoute(start, end) {
    // Si ya existe una ruta, la eliminamos
    if (routingControl) {
        map.removeControl(routingControl);
    }

    // Crear nuevo control de ruta para caminar
    routingControl = L.Routing.control({
        waypoints: [
            L.latLng(start.lat, start.lng),
            L.latLng(end[0], end[1])
        ],
        routeWhileDragging: false,
        lineOptions: {
            styles: [{ color: '#2196F3', weight: 6 }]
        },
        createMarker: function() { return null; }, // No crear marcadores adicionales
        language: 'es', // Establecer idioma a español
        show: false, // No mostrar el panel de instrucciones
        router: L.Routing.osrmv1({
            serviceUrl: 'https://routing.openstreetmap.de/routed-foot/route/v1', // Servidor OSRM con soporte para peatones
            profile: 'foot'
        })
    }).addTo(map);

    // Cerrar el panel de detalles
    document.getElementById('place-details').classList.remove('active');

    // Ajustar la vista para mostrar toda la ruta
    routingControl.on('routesfound', function(e) {
        const bounds = L.latLngBounds(start, end);
        map.fitBounds(bounds, { padding: [50, 50] });
    });
}
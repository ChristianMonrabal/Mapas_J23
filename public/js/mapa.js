// Inicializar el mapa centrado en España
var map = L.map('map', {
    zoomControl: false // Desactivamos el control de zoom predeterminado
}).setView([40.416775, -3.703790], 16);

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
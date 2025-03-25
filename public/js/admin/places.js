document.addEventListener("DOMContentLoaded", function () {
    const createPlaceForm = document.getElementById("placeForm");
    const editPlaceForm = document.getElementById("editPlaceForm");
    const createPlaceModal = new bootstrap.Modal(document.getElementById("placeModal"));
    const editPlaceModal = new bootstrap.Modal(document.getElementById("editPlaceModal"));
    const placesTableBody = document.getElementById("placesTableBody");
    const togglePlacesButton = document.getElementById("placesButton");
    const placesTableContainer = document.getElementById("placesTableContainer");
    const tagsTableContainer = document.getElementById("tagsTableContainer");
    const usersTableContainer = document.getElementById("usersTableContainer");
    const placeSearchInput = document.getElementById("placeSearchInput");
    const clearPlaceSearch = document.getElementById("clearPlaceSearch");
    const getCoordinatesBtn = document.getElementById("getCoordinatesBtn");
    const placeAddressInput = document.getElementById("placeAddress");
    const placeLatitudeInput = document.getElementById("placeLatitude");
    const placeLongitudeInput = document.getElementById("placeLongitude");
    const placeTagsSelect = document.getElementById("placeTags");
    const editPlaceTagsSelect = document.getElementById("editPlaceTags");

    tagsTableContainer.style.display = "none";
    placesTableContainer.style.display = "none";
    usersTableContainer.style.display = "block";

    let searchTimeout;

    // Función para obtener coordenadas usando OpenStreetMap Nominatim
    function getCoordinatesFromAddress(address) {
        if (!address || address.trim() === '') {
            showSweetAlert('error', 'Por favor ingresa una dirección válida');
            return;
        }

        showSweetAlert('info', 'Buscando coordenadas...', 'Espere por favor');

        const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`;

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data && data.length > 0) {
                    placeLatitudeInput.value = data[0].lat;
                    placeLongitudeInput.value = data[0].lon;
                    Swal.close();
                    showSweetAlert('success', 'Coordenadas obtenidas correctamente');
                } else {
                    throw new Error('No se encontraron coordenadas para esta dirección');
                }
            })
            .catch(error => {
                showSweetAlert('error', error.message || 'Error al obtener las coordenadas. Intenta con una dirección más específica.');
            });
    }

    // Función para cargar todos los tags disponibles
    function loadAllTags() {
        fetch("/tags", {
            headers: {
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al cargar los tags');
            }
            return response.json();
        })
        .then(data => {
            placeTagsSelect.innerHTML = '';
            editPlaceTagsSelect.innerHTML = '';
            
            if (data.tags && data.tags.length > 0) {
                data.tags.forEach(tag => {
                    const option = document.createElement("option");
                    option.value = tag.id;
                    option.textContent = tag.name;
                    placeTagsSelect.appendChild(option);
                    
                    const editOption = option.cloneNode(true);
                    editPlaceTagsSelect.appendChild(editOption);
                });
            }
        })
        .catch(error => {
            console.error("Error al cargar los tags:", error);
        });
    }

    // Event listener para el botón de obtener coordenadas
    getCoordinatesBtn.addEventListener("click", function() {
        getCoordinatesFromAddress(placeAddressInput.value);
    });

    function loadPlaces(searchTerm = '') {
        let url = "/places";
        if (searchTerm) {
            url += `?search=${encodeURIComponent(searchTerm)}`;
        }

        fetch(url, {
            headers: {
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al cargar los lugares');
            }
            return response.json();
        })
        .then(data => {
            placesTableBody.innerHTML = "";
            
            if (data.places && data.places.length === 0) {
                let noResultsRow = `<tr><td colspan="6" class="text-center">No se encontraron resultados</td></tr>`;
                placesTableBody.innerHTML = noResultsRow;
            } else if (data.places) {
                data.places.forEach(place => {
                    const tags = place.tags ? place.tags.map(tag => tag.name).join(', ') : '';
                    const imageCell = place.img ? 
                        `<img src="/${place.img}" alt="${place.name}" style="max-width: 50px; max-height: 50px; border-radius: 4px;">` : 
                        '<span class="text-muted">Sin imagen</span>';
                    
                    let row = `<tr>
                        <td>${place.name}</td>
                        <td>${place.address}</td>
                        <td>${place.description}</td>
                        <td>${tags}</td>
                        <td>${imageCell}</td>
                        <td>
                            <button class="btn btn-warning btn-sm places-edit-btn" 
                                data-id="${place.id}" 
                                data-name="${place.name}" 
                                data-address="${place.address}" 
                                data-latitude="${place.latitude}" 
                                data-longitude="${place.longitude}" 
                                data-description="${place.description}"
                                data-img="${place.img || ''}"
                                data-tags="${place.tags ? place.tags.map(tag => tag.id).join(',') : ''}">
                                Editar
                            </button>
                            <button class="btn btn-danger btn-sm places-delete-btn" data-id="${place.id}">Eliminar</button>
                        </td>
                    </tr>`;
                    placesTableBody.innerHTML += row;
                });

                document.querySelectorAll(".places-edit-btn").forEach(button => {
                    button.addEventListener("click", function() {
                        const placeId = this.getAttribute("data-id");
                        const placeName = this.getAttribute("data-name");
                        const placeAddress = this.getAttribute("data-address");
                        const placeLatitude = this.getAttribute("data-latitude");
                        const placeLongitude = this.getAttribute("data-longitude");
                        const placeDescription = this.getAttribute("data-description");
                        const placeImg = this.getAttribute("data-img");
                        const placeTags = this.getAttribute("data-tags");
                        
                        openEditPlaceModal(
                            placeId, 
                            placeName, 
                            placeAddress, 
                            placeLatitude, 
                            placeLongitude, 
                            placeDescription,
                            placeImg,
                            placeTags
                        );
                    });
                });

                document.querySelectorAll(".places-delete-btn").forEach(button => {
                    button.addEventListener("click", function() {
                        const placeId = this.getAttribute("data-id");
                        deletePlace(placeId);
                    });
                });
            }
        })
        .catch(error => {
            console.error("Error al cargar los places:", error);
            placesTableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Error al cargar los datos</td></tr>`;
        });
    }

    // Event listener para el input de búsqueda
    placeSearchInput.addEventListener("input", function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadPlaces(this.value.trim());
        }, 300);
    });

    // Event listener para el botón de limpiar búsqueda
    clearPlaceSearch.addEventListener("click", function() {
        placeSearchInput.value = "";
        loadPlaces();
    });

    function openEditPlaceModal(placeId, placeName, placeAddress, placeLatitude, placeLongitude, placeDescription, placeImg, placeTags) {
        document.getElementById("editPlaceId").value = placeId;
        document.getElementById("editPlaceName").value = placeName;
        document.getElementById("editPlaceAddress").value = placeAddress;
        document.getElementById("editPlaceLatitude").value = placeLatitude;
        document.getElementById("editPlaceLongitude").value = placeLongitude;
        document.getElementById("editPlaceDescription").value = placeDescription;
        
        const imageContainer = document.getElementById("currentPlaceImage");
        imageContainer.innerHTML = '';
        
        if (placeImg) {
            imageContainer.innerHTML = `
                <p class="mb-1">Imagen actual:</p>
                <img src="/${placeImg}" alt="${placeName}" style="max-width: 100px; max-height: 100px; border-radius: 4px;" class="mb-2">
            `;
        }

        // Seleccionar los tags actuales
        const tagIds = placeTags ? placeTags.split(',') : [];
        Array.from(editPlaceTagsSelect.options).forEach(option => {
            option.selected = tagIds.includes(option.value);
        });
        
        editPlaceModal.show();
    }

    createPlaceForm.addEventListener("submit", function (event) {
        event.preventDefault();

        // Validar que las coordenadas no estén vacías
        if (!placeLatitudeInput.value || !placeLongitudeInput.value) {
            showSweetAlert('error', 'Por favor obtén las coordenadas antes de guardar');
            return;
        }

        const formData = new FormData(this);
        fetch("/places", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                "Accept": "application/json"
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            showSweetAlert('success', data.message || 'Lugar creado correctamente');
            createPlaceForm.reset();
            createPlaceModal.hide();
            loadPlaces();
        })
        .catch(error => {
            showSweetAlert('error', error.error || error.message || 'Error al crear el lugar');
        });
    });

    editPlaceForm.addEventListener("submit", function (event) {
        event.preventDefault();
    
        const formData = new FormData(this);
        const placeId = document.getElementById("editPlaceId").value;
        
        fetch(`/places/${placeId}`, {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                "Accept": "application/json",
                "X-HTTP-Method-Override": "PUT"
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            showSweetAlert('success', data.message || 'Lugar actualizado correctamente');
            editPlaceModal.hide();
            loadPlaces();
        })
        .catch(error => {
            showSweetAlert('error', error.error || error.message || 'Error al actualizar el lugar');
        });
    });

    function deletePlace(placeId) {
        Swal.fire({
            title: '¿Seguro que deseas eliminar este lugar?',
            text: "¡Esta acción no se puede deshacer!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/places/${placeId}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        "Accept": "application/json"
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    showSweetAlert('success', data.message || 'Lugar eliminado correctamente');
                    loadPlaces();
                })
                .catch(error => {
                    showSweetAlert('error', error.error || error.message || 'Error al eliminar el lugar');
                });
            }
        });
    }

    togglePlacesButton.addEventListener("click", function() {
        tagsTableContainer.style.display = "none";
        usersTableContainer.style.display = "none";
        placesTableContainer.style.display = "block";
        loadPlaces();
    });

    // Cargar datos iniciales
    loadAllTags();
    loadPlaces();
});

function showSweetAlert(icon, title, text = '') {
    Swal.fire({
        icon: icon,
        title: title,
        text: text,
        showConfirmButton: false,
        timer: 1500
    });
}
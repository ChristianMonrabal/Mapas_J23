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

    tagsTableContainer.style.display = "none";
    placesTableContainer.style.display = "none";
    usersTableContainer.style.display = "block";

    let searchTimeout;

    function loadPlaces(searchTerm = '') {
        let url = "/places/list";
        if (searchTerm) {
            url += `?search=${encodeURIComponent(searchTerm)}`;
        }

        fetch(url)
            .then(response => response.json())
            .then(data => {
                placesTableBody.innerHTML = "";
                
                if (data.places.length === 0) {
                    let noResultsRow = `<tr><td colspan="6" class="text-center">No se encontraron resultados</td></tr>`;
                    placesTableBody.innerHTML = noResultsRow;
                } else {
                    data.places.forEach(place => {
                        let row = `<tr>
                            <td>${place.name}</td>
                            <td>${place.address}</td>
                            <td>${place.description}</td>
                            <td>
                                <button class="btn btn-warning btn-sm places-edit-btn" data-id="${place.id}" data-name="${place.name}" data-address="${place.address}" data-latitude="${place.latitude}" data-longitude="${place.longitude}" data-description="${place.description}">Editar</button>
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
                            openEditPlaceModal(placeId, placeName, placeAddress, placeLatitude, placeLongitude, placeDescription);
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
            .catch(error => console.error("Error al cargar los places:", error));
    }

    // Event listener para el input de búsqueda
    placeSearchInput.addEventListener("input", function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadPlaces(this.value);
        }, 300);
    });

    // Event listener para el botón de limpiar búsqueda
    clearPlaceSearch.addEventListener("click", function() {
        placeSearchInput.value = "";
        loadPlaces();
    });

    function openEditPlaceModal(placeId, placeName, placeAddress, placeLatitude, placeLongitude, placeDescription) {
        document.getElementById("editPlaceId").value = placeId;
        document.getElementById("editPlaceName").value = placeName;
        document.getElementById("editPlaceAddress").value = placeAddress;
        document.getElementById("editPlaceLatitude").value = placeLatitude;
        document.getElementById("editPlaceLongitude").value = placeLongitude;
        document.getElementById("editPlaceDescription").value = placeDescription;
        editPlaceModal.show();
    }

    createPlaceForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const formData = new FormData(this);
        fetch("/places", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                "Accept": "application/json"
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            showSweetAlert('success', data.message);
            createPlaceForm.reset();
            createPlaceModal.hide();
            loadPlaces();
        })
        .catch(error => {
            showSweetAlert('error', error.message);
        });
    });

    editPlaceForm.addEventListener("submit", function (event) {
        event.preventDefault();
    
        const placeId = document.getElementById("editPlaceId").value;
        const placeName = document.getElementById("editPlaceName").value;
        const placeAddress = document.getElementById("editPlaceAddress").value;
        const placeLatitude = document.getElementById("editPlaceLatitude").value;
        const placeLongitude = document.getElementById("editPlaceLongitude").value;
        const placeDescription = document.getElementById("editPlaceDescription").value;
    
        const data = {
            id: placeId,
            name: placeName,
            address: placeAddress,
            latitude: placeLatitude,
            longitude: placeLongitude,
            description: placeDescription
        };
    
        fetch(`/places/${placeId}`, {
            method: "PUT",
            body: JSON.stringify(data),
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                "Accept": "application/json",
                "Content-Type": "application/json"
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            showSweetAlert('success', data.message);
            editPlaceForm.reset();
            editPlaceModal.hide();
            loadPlaces();
        })
        .catch(error => {
            showSweetAlert('error', error.message);
        });
    });

    function deletePlace(placeId) {
        Swal.fire({
            title: '¿Seguro que deseas eliminar este place?',
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
                .then(response => response.json())
                .then(data => {
                    showSweetAlert('success', data.message);
                    loadPlaces();
                })
                .catch(error => {
                    showSweetAlert('error', error.message);
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

    loadPlaces();
});
document.addEventListener("DOMContentLoaded", function () {
    createPlaceForm = document.getElementById("placeForm");
    editPlaceForm = document.getElementById("editPlaceForm");
    createPlaceModal = new bootstrap.Modal(document.getElementById("placeModal"));
    editPlaceModal = new bootstrap.Modal(document.getElementById("editPlaceModal"));
    placesTableBody = document.getElementById("placesTableBody");
    togglePlacesButton = document.getElementById("placesButton");
    placesTableContainer = document.getElementById("placesTableContainer");

    function loadPlaces() {
        fetch("/places/list")
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
                            <td>${place.latitude}</td>
                            <td>${place.longitude}</td>
                            <td>${place.description}</td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-btn" data-id="${place.id}" data-name="${place.name}" data-address="${place.address}" data-latitude="${place.latitude}" data-longitude="${place.longitude}" data-description="${place.description}">Editar</button>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${place.id}">Eliminar</button>
                            </td>
                        </tr>`;
                        placesTableBody.innerHTML += row;
                    });
    
                    document.querySelectorAll(".edit-btn").forEach(button => {
                        button.addEventListener("click", function() {
                            placeId = this.getAttribute("data-id");
                            placeName = this.getAttribute("data-name");
                            placeAddress = this.getAttribute("data-address");
                            placeLatitude = this.getAttribute("data-latitude");
                            placeLongitude = this.getAttribute("data-longitude");
                            placeDescription = this.getAttribute("data-description");
                            openEditPlaceModal(placeId, placeName, placeAddress, placeLatitude, placeLongitude, placeDescription);
                        });
                    });
    
                    document.querySelectorAll(".delete-btn").forEach(button => {
                        button.addEventListener("click", function() {
                            placeId = this.getAttribute("data-id");
                            deletePlace(placeId);
                        });
                    });
                }
            })
            .catch(error => console.error("Error al cargar los places:", error));
    }
    setInterval(loadPlaces, 5000);
    
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

        let formData = new FormData(this);
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
    
        placeId = document.getElementById("editPlaceId").value;
        placeName = document.getElementById("editPlaceName").value;
        placeAddress = document.getElementById("editPlaceAddress").value;
        placeLatitude = document.getElementById("editPlaceLatitude").value;
        placeLongitude = document.getElementById("editPlaceLongitude").value;
        placeDescription = document.getElementById("editPlaceDescription").value;
    
        data = {
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
        if (placesTableContainer.style.display === "none") {
            placesTableContainer.style.display = "block";
            loadPlaces();
        } else {
            placesTableContainer.style.display = "none";
        }
    });

    loadPlaces();
});
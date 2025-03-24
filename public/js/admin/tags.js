document.addEventListener("DOMContentLoaded", function () {
    const createTagForm = document.getElementById("tagForm");
    const editTagForm = document.getElementById("editTagForm");
    const createTagModal = new bootstrap.Modal(document.getElementById("tagModal"));
    const editTagModal = new bootstrap.Modal(document.getElementById("editTagModal"));
    const tagsTableBody = document.getElementById("tagsTableBody");
    const toggleTagsButton = document.getElementById("toggleTags");
    const tagsTableContainer = document.getElementById("tagsTableContainer");
    const placesTableContainer = document.getElementById("placesTableContainer");
    const usersTableContainer = document.getElementById("usersTableContainer");
    const tagSearchInput = document.getElementById("tagSearchInput");
    const clearTagSearch = document.getElementById("clearTagSearch");

    tagsTableContainer.style.display = "none";
    placesTableContainer.style.display = "none";
    usersTableContainer.style.display = "block";

    let searchTimeout;

    function loadTags(searchTerm = '') {
        let url = "/tags";
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
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            tagsTableBody.innerHTML = "";
            
            if (data.tags && data.tags.length === 0) {
                let noResultsRow = `<tr><td colspan="2" class="text-center">No se encontraron resultados</td></tr>`;
                tagsTableBody.innerHTML = noResultsRow;
            } else if (data.tags) {
                data.tags.forEach(tag => {
                    let row = `<tr>
                        <td>${tag.name}</td>
                        <td>
                            <button class="btn btn-warning btn-sm tags-edit-btn" data-id="${tag.id}" data-name="${tag.name}">Editar</button>
                            <button class="btn btn-danger btn-sm tags-delete-btn" data-id="${tag.id}">Eliminar</button>
                        </td>
                    </tr>`;
                    tagsTableBody.innerHTML += row;
                });

                document.querySelectorAll(".tags-edit-btn").forEach(button => {
                    button.addEventListener("click", function() {
                        const tagId = this.getAttribute("data-id");
                        const tagName = this.getAttribute("data-name");
                        openEditModal(tagId, tagName);
                    });
                });

                document.querySelectorAll(".tags-delete-btn").forEach(button => {
                    button.addEventListener("click", function() {
                        const tagId = this.getAttribute("data-id");
                        deleteTag(tagId);
                    });
                });
            }
        })
        .catch(error => {
            console.error("Error al cargar los tags:", error);
            tagsTableBody.innerHTML = `<tr><td colspan="2" class="text-center text-danger">Error al cargar los datos</td></tr>`;
        });
    }

    tagSearchInput.addEventListener("input", function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadTags(this.value.trim());
        }, 300);
    });

    clearTagSearch.addEventListener("click", function() {
        tagSearchInput.value = "";
        loadTags();
    });

    function openEditModal(tagId, tagName) {
        document.getElementById("editTagId").value = tagId;
        document.getElementById("editTagName").value = tagName;
        editTagModal.show();
    }

    createTagForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const formData = new FormData(this);
        fetch("/tags", {
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
            showSweetAlert('success', data.message || 'Tag creado correctamente');
            createTagForm.reset();
            createTagModal.hide();
            loadTags();
        })
        .catch(error => {
            showSweetAlert('error', error.error || error.message || 'Error al crear el tag');
        });
    });

    editTagForm.addEventListener("submit", function (event) {
        event.preventDefault();
    
        const tagId = document.getElementById("editTagId").value;
        const tagName = document.getElementById("editTagName").value;
    
        const data = {
            id: tagId,
            name: tagName
        };
    
        fetch(`/tags/${tagId}`, {
            method: "PUT",
            body: JSON.stringify(data),
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                "Accept": "application/json",
                "Content-Type": "application/json"
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            showSweetAlert('success', data.message || 'Tag actualizado correctamente');
            editTagForm.reset();
            editTagModal.hide();
            loadTags();
        })
        .catch(error => {
            showSweetAlert('error', error.error || error.message || 'Error al actualizar el tag');
        });
    });

    function deleteTag(tagId) {
        Swal.fire({
            title: '¿Seguro que deseas eliminar este tag?',
            text: "¡Esta acción no se puede deshacer!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/tags/${tagId}`, {
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
                    showSweetAlert('success', data.message || 'Tag eliminado correctamente');
                    loadTags();
                })
                .catch(error => {
                    showSweetAlert('error', error.error || error.message || 'Error al eliminar el tag');
                });
            }
        });
    }

    toggleTagsButton.addEventListener("click", function() {
        placesTableContainer.style.display = "none";
        usersTableContainer.style.display = "none";
        tagsTableContainer.style.display = "block";
        loadTags();
    });

    loadTags();
});
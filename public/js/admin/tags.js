document.addEventListener("DOMContentLoaded", function () {
    createTagForm = document.getElementById("tagForm");
    editTagForm = document.getElementById("editTagForm");
    createTagModal = new bootstrap.Modal(document.getElementById("tagModal"));
    editTagModal = new bootstrap.Modal(document.getElementById("editTagModal"));
    tagsTableBody = document.getElementById("tagsTableBody");
    toggleTagsButton = document.getElementById("toggleTags");
    tagsTableContainer = document.getElementById("tagsTableContainer");

    function loadTags() {
        fetch("/tags/list")
            .then(response => response.json())
            .then(data => {
                tagsTableBody.innerHTML = "";
                
                if (data.tags.length === 0) {
                    let noResultsRow = `<tr><td colspan="2" class="text-center">No se encontraron resultados</td></tr>`;
                    tagsTableBody.innerHTML = noResultsRow;
                } else {
                    data.tags.forEach(tag => {
                        let row = `<tr>
                            <td>${tag.name}</td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-btn" data-id="${tag.id}" data-name="${tag.name}">Editar</button>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${tag.id}">Eliminar</button>
                            </td>
                        </tr>`;
                        tagsTableBody.innerHTML += row;
                    });
    
                    document.querySelectorAll(".edit-btn").forEach(button => {
                        button.addEventListener("click", function() {
                            tagId = this.getAttribute("data-id");
                            tagName = this.getAttribute("data-name");
                            openEditModal(tagId, tagName);
                        });
                    });
    
                    document.querySelectorAll(".delete-btn").forEach(button => {
                        button.addEventListener("click", function() {
                            tagId = this.getAttribute("data-id");
                            deleteTag(tagId);
                        });
                    });
                }
            })
            .catch(error => console.error("Error al cargar los tags:", error));
    }
    
    setInterval(loadTags, 5000);
    
    function openEditModal(tagId, tagName) {
        document.getElementById("editTagId").value = tagId;
        document.getElementById("editTagName").value = tagName;
        editTagModal.show();
    }

    createTagForm.addEventListener("submit", function (event) {
        event.preventDefault();

        let formData = new FormData(this);
        fetch("/tags", {
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
            createTagForm.reset();
            createTagModal.hide();
            loadTags();
        })
        .catch(error => {
            showSweetAlert('error', error.message);
        });
    });

    editTagForm.addEventListener("submit", function (event) {
        event.preventDefault();
    
        tagId = document.getElementById("editTagId").value;
        tagName = document.getElementById("editTagName").value;
    
        data = {
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
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            showSweetAlert('success', data.message);
            editTagForm.reset();
            editTagModal.hide();
            loadTags();
        })
        .catch(error => {
            showSweetAlert('error', error.message);
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
                .then(response => response.json())
                .then(data => {
                    showSweetAlert('success', data.message);
                    loadTags();
                })
                .catch(error => {
                    showSweetAlert('error', error.message);
                });
            }
        });
    }

    toggleTagsButton.addEventListener("click", function() {
        if (tagsTableContainer.style.display === "none") {
            tagsTableContainer.style.display = "block";
            loadTags();
        } else {
            tagsTableContainer.style.display = "none";
        }
    });

    loadTags();
});

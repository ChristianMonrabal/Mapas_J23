document.addEventListener("DOMContentLoaded", function () {
    const createUserForm = document.getElementById("userForm");
    const editUserForm = document.getElementById("editUserForm");
    const createUserModal = new bootstrap.Modal(document.getElementById("userModal"));
    const editUserModal = new bootstrap.Modal(document.getElementById("editUserModal"));
    const usersTableBody = document.getElementById("usersTableBody");
    const toggleUsersButton = document.getElementById("usersButton");
    const usersTableContainer = document.getElementById("usersTableContainer");
    const tagsTableContainer = document.getElementById("tagsTableContainer");
    const placesTableContainer = document.getElementById("placesTableContainer");

    tagsTableContainer.style.display = "none";
    placesTableContainer.style.display = "none";
    usersTableContainer.style.display = "none";

    function loadUsers() {
        fetch("/users/list")
            .then(response => response.json())
            .then(data => {
                usersTableBody.innerHTML = "";
                
                if (data.users.length === 0) {
                    let noResultsRow = `<tr><td colspan="5" class="text-center">No se encontraron resultados</td></tr>`;
                    usersTableBody.innerHTML = noResultsRow;
                } else {
                    data.users.forEach(user => {
                        let row = `<tr>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td>${user.role_id}</td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-btn" data-id="${user.id}" data-name="${user.name}" data-email="${user.email}" data-role-id="${user.role_id}">Editar</button>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${user.id}">Eliminar</button>
                            </td>
                        </tr>`;
                        usersTableBody.innerHTML += row;
                    });
    
                    document.querySelectorAll(".edit-btn").forEach(button => {
                        button.addEventListener("click", function() {
                            const userId = this.getAttribute("data-id");
                            const userName = this.getAttribute("data-name");
                            const userEmail = this.getAttribute("data-email");
                            const userRoleId = this.getAttribute("data-role-id");
                            openEditUserModal(userId, userName, userEmail, userRoleId);
                        });
                    });
    
                    document.querySelectorAll(".delete-btn").forEach(button => {
                        button.addEventListener("click", function() {
                            const userId = this.getAttribute("data-id");
                            deleteUser(userId);
                        });
                    });
                }
            })
            .catch(error => console.error("Error al cargar los usuarios:", error));
    }

    function openEditUserModal(userId, userName, userEmail, userRoleId) {
        document.getElementById("editUserId").value = userId;
        document.getElementById("editUserName").value = userName;
        document.getElementById("editUserEmail").value = userEmail;
        document.getElementById("editUserRoleId").value = userRoleId;
        editUserModal.show();
    }

    createUserForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const formData = new FormData(this);
        fetch("/users", {
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
            createUserForm.reset();
            createUserModal.hide();
            loadUsers();
        })
        .catch(error => {
            showSweetAlert('error', error.message);
        });
    });

    editUserForm.addEventListener("submit", function (event) {
        event.preventDefault();
    
        const userId = document.getElementById("editUserId").value;
        const userName = document.getElementById("editUserName").value;
        const userEmail = document.getElementById("editUserEmail").value;
        const userRoleId = document.getElementById("editUserRoleId").value;
    
        const data = {
            id: userId,
            name: userName,
            email: userEmail,
            role_id: userRoleId,
        };
    
        fetch(`/users/${userId}`, {
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
            editUserForm.reset();
            editUserModal.hide();
            loadUsers();
        })
        .catch(error => {
            showSweetAlert('error', error.message);
        });
    });

    function deleteUser(userId) {
        Swal.fire({
            title: '¿Seguro que deseas eliminar este usuario?',
            text: "¡Esta acción no se puede deshacer!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/users/${userId}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        "Accept": "application/json"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    showSweetAlert('success', data.message);
                    loadUsers();
                })
                .catch(error => {
                    showSweetAlert('error', error.message);
                });
            }
        });
    }

    toggleUsersButton.addEventListener("click", function() {
        tagsTableContainer.style.display = "none";
        placesTableContainer.style.display = "none";

        if (usersTableContainer.style.display === "none") {
            usersTableContainer.style.display = "block";
            loadUsers();
        } else {
            usersTableContainer.style.display = "none";
        }
    });

    loadUsers();
});
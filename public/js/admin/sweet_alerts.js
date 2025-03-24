function showSweetAlert(type, message) {
    if (type === 'success') {
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: message,
            confirmButtonText: 'Aceptar'
        });
    } else if (type === 'error') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
            confirmButtonText: 'Aceptar'
        });
    }
}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Panel</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    @if(Auth::check() && Auth::user()->role_id == 2)
        <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #006400;">
            <div class="container-fluid">
                <a class="navbar-brand" href="#"><img src="{{ asset('img/icon.png') }}" alt="Logo" height="30"></a>
                <div class="d-flex">
                    <button id="toggleTags" class="btn btn-outline-light">Tags</button>
                    <button class="btn btn-outline-light" type="button" id="placesButton">Places</button>
                    <button class="btn btn-outline-light" type="button" id="usersButton">Users</button>
                    <form action="{{ route('auth.logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger ms-2">Cerrar sesión</button>
                    </form>
                </div>
            </div>
        </nav>

        <div class="modal fade" id="tagModal" tabindex="-1" aria-labelledby="tagModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tagModalLabel">Nuevo Tag</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="tagForm">
                            @csrf
                            <div class="mb-3">
                                <label for="tagName" class="form-label">Nombre del Tag</label>
                                <input type="text" class="form-control" id="tagName" name="name" maxlength="20">
                            </div>
                            <button type="submit" class="btn btn-success">Guardar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editTagModal" tabindex="-1" aria-labelledby="editTagModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editTagModalLabel">Editar Tag</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editTagForm">
                            @csrf
                            <input type="hidden" id="editTagId" name="id">
                            <div class="mb-3">
                                <label for="editTagName" class="form-label">Nombre del Tag</label>
                                <input type="text" class="form-control" id="editTagName" name="name" maxlength="20">
                            </div>
                            <button type="submit" class="btn btn-warning">Actualizar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4" id="tagsTableContainer" style="display: none;">
            <h1 class="text-center">Tags</h1>
            <div class="d-flex justify-content-between align-items-center mb-3 mx-auto" style="max-width: 80%;">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tagModal">
                    <i class="bi bi-plus-circle"></i> Agregar Tag
                </button>
            </div>
        
            <div class="table-responsive mx-auto" style="max-width: 80%;">
                <table class="table table-bordered table-striped table-hover text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tagsTableBody">
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="modal fade" id="placeModal" tabindex="-1" aria-labelledby="placeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="placeModalLabel">Nuevo Place</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="placeForm">
                            @csrf
                            <div class="mb-3">
                                <label for="placeName" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="placeName" name="name" maxlength="20">
                            </div>
                            <div class="mb-3">
                                <label for="placeAddress" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="placeAddress" name="address">
                            </div>
                            <div class="mb-3">
                                <label for="placeLatitude" class="form-label">Latitud</label>
                                <input type="text" class="form-control" id="placeLatitude" name="latitude">
                            </div>
                            <div class="mb-3">
                                <label for="placeLongitude" class="form-label">Longitud</label>
                                <input type="text" class="form-control" id="placeLongitude" name="longitude">
                            </div>
                            <div class="mb-3">
                                <label for="placeDescription" class="form-label">Descripción</label>
                                <textarea class="form-control" id="placeDescription" name="description"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Guardar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editPlaceModal" tabindex="-1" aria-labelledby="editPlaceModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPlaceModalLabel">Editar Place</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editPlaceForm">
                            @csrf
                            <input type="hidden" id="editPlaceId" name="id">
                            <div class="mb-3">
                                <label for="editPlaceName" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="editPlaceName" name="name" maxlength="20">
                            </div>
                            <div class="mb-3">
                                <label for="editPlaceAddress" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="editPlaceAddress" name="address">
                            </div>
                            <div class="mb-3">
                                <label for="editPlaceLatitude" class="form-label">Latitud</label>
                                <input type="text" class="form-control" id="editPlaceLatitude" name="latitude">
                            </div>
                            <div class="mb-3">
                                <label for="editPlaceLongitude" class="form-label">Longitud</label>
                                <input type="text" class="form-control" id="editPlaceLongitude" name="longitude">
                            </div>
                            <div class="mb-3">
                                <label for="editPlaceDescription" class="form-label">Descripción</label>
                                <textarea class="form-control" id="editPlaceDescription" name="description"></textarea>
                            </div>
                            <button type="submit" class="btn btn-warning">Actualizar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4" id="placesTableContainer" style="display: none;">
            <h1 class="text-center">Places</h1>
            <div class="d-flex justify-content-between align-items-center mb-3 mx-auto" style="max-width: 80%;">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#placeModal">
                    <i class="bi bi-plus-circle"></i> Agregar Place
                </button>
            </div>

        <div class="table-responsive mx-auto" style="max-width: 80%;">
            <table class="table table-bordered table-striped table-hover text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Latitud</th>
                        <th>Longitud</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="placesTableBody">
                </tbody>
            </table>
        </div>
    </div>

    @else
        <?php
        return redirect()->route('auth.login');
        ?>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/admin/tags.js') }}"></script>
    <script src="{{ asset('js/admin/places.js') }}"></script>
    <script src="{{ asset('js/admin/sweet_alerts.js') }}"></script>
    <script src="{{ asset('js/admin/validation_forms.js') }}"></script>
</body>
</html>

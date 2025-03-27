<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Panel</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    @if(Auth::check() && Auth::user()->role_id == 2)
        <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #006400;">
            <div class="container-fluid">
                <a class="navbar-brand" href="#"><img src="{{ asset('img/icon.png') }}" height="30"></a>
                <div class="d-flex">
                    <button id="gymkhanaButton" class="btn btn-outline-light">Gymkhana</button>
                    <button id="checkpointButton" class="btn btn-outline-light">Checkpoints</button>
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
                        <form id="tagForm" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="tagName" class="form-label">Nombre del Tag</label>
                                <input type="text" class="form-control" id="tagName" name="name" maxlength="20" required>
                            </div>
                            <div class="mb-3">
                                <label for="tagImage" class="form-label">Imagen del Tag</label>
                                <input type="file" class="form-control" id="tagImage" name="image" accept="image/*">
                                <small class="text-muted">Formatos aceptados: JPEG, PNG, JPG, GIF. Tamaño máximo: 2MB</small>
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
                        <form id="editTagForm" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="editTagId" name="id">
                            <div class="mb-3">
                                <label for="editTagName" class="form-label">Nombre del Tag</label>
                                <input type="text" class="form-control" id="editTagName" name="name" maxlength="20" required>
                            </div>
                            <div class="mb-3">
                                <label for="editTagImage" class="form-label">Nueva Imagen (opcional)</label>
                                <input type="file" class="form-control" id="editTagImage" name="image" accept="image/*">
                                <small class="text-muted">Dejar en blanco para mantener la imagen actual</small>
                                <div id="currentImageContainer" class="mt-2"></div>
                            </div>
                            <button type="submit" class="btn btn-primary">Actualizar</button>
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
                <div class="input-group" style="max-width: 300px;">
                    <input type="text" id="tagSearchInput" class="form-control" placeholder="Buscar por nombre...">
                    <span class="input-group-text bg-white" id="clearTagSearch" style="cursor: pointer;">
                        <i class="bi bi-x-lg"></i>
                    </span>
                </div>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tagModal">
                    <i class="bi bi-plus-circle"></i> 
                </button>
            </div>
        
            <div class="table-responsive mx-auto" style="max-width: 80%;">
                <table class="table table-bordered table-striped table-hover text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Imagen</th>
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
                        <form id="placeForm" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="placeName" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="placeName" name="name" maxlength="20" required>
                            </div>
                            <div class="mb-3">
                                <label for="placeAddress" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="placeAddress" name="address" required>
                                <div class="mt-2">
                                    <a href="#" id="getCoordinatesBtn" class="text-decoration-none" title="Obtener coordenadas">
                                        <i class="bi bi-geo-alt-fill text-primary"></i> 
                                        <small class="text-muted">Obtener coordenadas</small>
                                    </a>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="placeLatitude" class="form-label">Latitud</label>
                                <input type="text" class="form-control" id="placeLatitude" name="latitude" required>
                            </div>
                            <div class="mb-3">
                                <label for="placeLongitude" class="form-label">Longitud</label>
                                <input type="text" class="form-control" id="placeLongitude" name="longitude" required>
                            </div>
                            <div class="mb-3">
                                <label for="placeDescription" class="form-label">Descripción</label>
                                <textarea class="form-control" id="placeDescription" name="description" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="placeImage" class="form-label">Imagen</label>
                                <input type="file" class="form-control" id="placeImage" name="image" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label for="placeTags" class="form-label">Tags</label>
                                <select class="form-select" id="placeTags" name="tags[]" multiple>
                                </select>
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
                        <form id="editPlaceForm" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="editPlaceId" name="id">
                            <div class="mb-3">
                                <label for="editPlaceName" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="editPlaceName" name="name" maxlength="20" required>
                            </div>
                            <div class="mb-3">
                                <label for="editPlaceAddress" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="editPlaceAddress" name="address" required>
                            </div>
                            <div class="mb-3">
                                <label for="editPlaceLatitude" class="form-label">Latitud</label>
                                <input type="text" class="form-control" id="editPlaceLatitude" name="latitude" required>
                            </div>
                            <div class="mb-3">
                                <label for="editPlaceLongitude" class="form-label">Longitud</label>
                                <input type="text" class="form-control" id="editPlaceLongitude" name="longitude" required>
                            </div>
                            <div class="mb-3">
                                <label for="editPlaceDescription" class="form-label">Descripción</label>
                                <textarea class="form-control" id="editPlaceDescription" name="description" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="editPlaceImage" class="form-label">Nueva Imagen (opcional)</label>
                                <input type="file" class="form-control" id="editPlaceImage" name="image" accept="image/*">
                                <div id="currentPlaceImage" class="mt-2"></div>
                            </div>
                            <div class="mb-3">
                                <label for="editPlaceTags" class="form-label">Tags</label>
                                <select class="form-select" id="editPlaceTags" name="tags[]" multiple>
                                </select>
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
                <div class="input-group" style="max-width: 300px;">
                    <input type="text" id="placeSearchInput" class="form-control" placeholder="Buscar por nombre...">
                    <span class="input-group-text bg-white" id="clearPlaceSearch" style="cursor: pointer;">
                        <i class="bi bi-x-lg"></i>
                    </span>
                </div>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#placeModal">
                    <i class="bi bi-plus-circle"></i>
                </button>
            </div>
        
            <div class="table-responsive mx-auto" style="max-width: 80%;">
                <table class="table table-bordered table-striped table-hover text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Dirección</th>
                            <th>Descripción</th>
                            <th>Tags</th>
                            <th>Imagen</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="placesTableBody">
                    </tbody>
                </table>
            </div>
        </div>

    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="userForm">
                        @csrf
                        <div class="mb-3">
                            <label for="userName" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="userName" name="name" maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="userEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="userEmail" name="email">
                        </div>
                        <div class="mb-3">
                            <label for="userPassword" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="userPassword" name="password">
                        </div>
                        <div class="mb-3">
                            <label for="userRoleId" class="form-label">Rol</label>
                            <select class="form-control" id="userRoleId" name="role_id">
                                <option value="1">Usuario</option>
                                <option value="2">Administrador</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        @csrf
                        <input type="hidden" id="editUserId" name="id">
                        <div class="mb-3">
                            <label for="editUserName" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="editUserName" name="name" maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="editUserEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editUserEmail" name="email">
                        </div>
                        <div class="mb-3">
                            <label for="editUserPassword" class="form-label">Contraseña (dejar en blanco para no cambiar)</label>
                            <input type="password" class="form-control" id="editUserPassword" name="password">
                        </div>
                        <div class="mb-3">
                            <label for="editUserRoleId" class="form-label">Rol</label>
                            <select class="form-control" id="editUserRoleId" name="role_id">
                                <option value="1">Usuario</option>
                                <option value="2">Administrador</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4" id="usersTableContainer" style="display: none;">
        <h1 class="text-center">Usuarios</h1>
        <div class="d-flex justify-content-between align-items-center mb-3 mx-auto" style="max-width: 80%;">
            <div class="input-group" style="max-width: 300px;">
                <input type="text" id="userSearchInput" class="form-control" placeholder="Buscar por nombre...">
                <span class="input-group-text bg-white" id="clearSearch" style="cursor: pointer;">
                    <i class="bi bi-x-lg"></i>
                </span>
            </div>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#userModal">
                <i class="bi bi-plus-circle"></i>
            </button>
        </div>
    
        <div class="table-responsive mx-auto" style="max-width: 80%;">
            <table class="table table-bordered table-striped table-hover text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                </tbody>
            </table>
        </div>
    </div>

    <div class="container mt-4" id="gymkhanaSection">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h1 class="text-center">Gymkhanas</h1>
          <!-- Botón para abrir el modal de creación de Gymkhana -->
          <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#gymkhanaModal">
            <i class="bi bi-plus-circle"></i> Agregar Gymkhana
          </button>
        </div>
        <!-- Tabla de Gymkhanas siempre visible -->
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-hover text-center">
            <thead class="table-dark">
              <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody id="gymkhanaTableBody">
              <!-- Los datos se cargarán vía JS -->
            </tbody>
          </table>
        </div>
      </div>
  
      <!-- Modal para crear/editar Gymkhana -->
      <div class="modal fade" id="gymkhanaModal" tabindex="-1" aria-labelledby="gymkhanaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="gymkhanaModalLabel">Nuevo Gymkhana</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
              <form id="gymkhanaForm">
                @csrf
                <!-- Campo oculto para edición, se usará si se edita un registro -->
                <input type="hidden" id="editGymkhanaId" name="id">
                <div class="mb-3">
                  <label for="gymkhanaName" class="form-label">Nombre de la Gymkhana</label>
                  <input type="text" class="form-control" id="gymkhanaName" name="name" required>
                </div>
                <div class="mb-3">
                  <label for="gymkhanaDescription" class="form-label">Descripción</label>
                  <textarea class="form-control" id="gymkhanaDescription" name="description" required></textarea>
                </div>
                <button type="submit" class="btn btn-success">Guardar</button>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal para editar Gymkhana -->
<!-- Modal para editar Gymkhana -->
<div class="modal fade" id="editGymkhanaModal" tabindex="-1" aria-labelledby="editGymkhanaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editGymkhanaModalLabel">Editar Gymkhana</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <form id="editGymkhanaForm">
            @csrf
            @method('PUT')
            <input type="hidden" id="editGymkhanaId" name="id">
            <div class="mb-3">
              <label for="editGymkhanaName" class="form-label">Nombre de la Gymkhana</label>
              <input type="text" class="form-control" id="editGymkhanaName" name="name" required>
            </div>
            <div class="mb-3">
              <label for="editGymkhanaDescription" class="form-label">Descripción</label>
              <textarea class="form-control" id="editGymkhanaDescription" name="description" required></textarea>
            </div>
            <button type="submit" class="btn btn-warning">Actualizar</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  
  
  
      <!-- ========================= -->
      <!-- Sección de Checkpoints -->
      <!-- ========================= -->
<!-- Sección de Checkpoints -->
<div class="container mt-4" id="checkpointSection" style="display: none;">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="text-center">Checkpoints</h1>
      <!-- Botón para abrir el modal de creación de Checkpoint -->
      <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#checkpointModal">
        <i class="bi bi-plus-circle"></i> Agregar Checkpoint
      </button>
    </div>
    <!-- Tabla de Checkpoints -->
    <div class="table-responsive">
      <table class="table table-bordered table-striped table-hover text-center">
        <thead class="table-dark">
          <tr>
            <th>Pista</th>
            <th>Gymkhana</th>
            <th>Lugar</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody id="checkpointTableBody">
          <!-- Se cargarán los datos vía JavaScript -->
        </tbody>
      </table>
    </div>
  </div>
  
  <!-- Modal para crear Checkpoint -->
  <div class="modal fade" id="checkpointModal" tabindex="-1" aria-labelledby="checkpointModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="checkpointModalLabel">Nuevo Checkpoint</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <form id="checkpointForm">
            @csrf
            <input type="hidden" id="editCheckpointId" name="id">
            <div class="mb-3">
              <label for="checkpointPista" class="form-label">Pista</label>
              <input type="text" class="form-control" id="checkpointPista" name="pista" required>
            </div>
            <div class="mb-3">
              <label for="gymkhanaId" class="form-label">Gymkhana</label>
              <select class="form-select" id="gymkhanaId" name="gymkhana_id" required>
                <!-- Se llenará dinámicamente -->
              </select>
            </div>
            <div class="mb-3">
              <label for="placeId" class="form-label">Place</label>
              <select class="form-select" id="placeId" name="place_id" required>
                <!-- Se llenará dinámicamente -->
              </select>
            </div>
            <button type="submit" class="btn btn-success">Guardar</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Modal para editar Checkpoint -->
  <div class="modal fade" id="editCheckpointModal" tabindex="-1" aria-labelledby="editCheckpointModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editCheckpointModalLabel">Editar Checkpoint</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <form id="editCheckpointForm">
            @csrf
            @method('PUT')
            <input type="hidden" id="editCheckpointId" name="id">
            <div class="mb-3">
              <label for="editCheckpointPista" class="form-label">Pista</label>
              <input type="text" class="form-control" id="editCheckpointPista" name="pista" required>
            </div>
            <div class="mb-3">
              <label for="editGymkhanaId" class="form-label">Gymkhana</label>
              <select class="form-select" id="editGymkhanaId" name="gymkhana_id" required>
                <!-- Opciones dinámicas -->
              </select>
            </div>
            <div class="mb-3">
              <label for="editPlaceId" class="form-label">Place</label>
              <select class="form-select" id="editPlaceId" name="place_id" required>
                <!-- Opciones dinámicas -->
              </select>
            </div>
            <button type="submit" class="btn btn-warning">Actualizar</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  
 

    @else
        <?php
            return redirect()->route('auth.login');
        ?>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/admin/tags.js') }}"></script>
    <script src="{{ asset('js/admin/places.js') }}"></script>
    <script src="{{ asset('js/admin/users.js') }}"></script>
    <script src="{{ asset('js/admin/sweet_alerts.js') }}"></script>
    <script src="{{ asset('js/admin/validation_forms.js') }}"></script>
    <script src="{{ asset('js/admin/gymkhana.js') }}"></script>
    <script src="{{ asset('js/admin/checkpoints.js') }}"></script>
</body>
</html>

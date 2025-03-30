@extends('layout.layout')

@section('content')
<div class="container my-4">
    <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center mb-4">
        <h1 class="h2 mb-3 mb-md-0 text-center text-md-start w-100">Gestión de Grupos</h1>
        <div class="d-flex justify-content-start justify-content-md-end">
            <a href="/dashboard/mapa" class="btn btn-outline-primary w-100 w-md-auto" style="min-width: 150px;">
                <i class="bi bi-map me-2"></i> Volver al Mapa
            </a>
        </div>
    </div>

    {{-- Buscador --}}
    <div class="row g-3 mb-4">
      <div class="col-12 col-sm-6 col-md-3">
          <input type="text" id="searchName" class="form-control" placeholder="Buscar por nombre...">
      </div>
      <div class="col-12 col-sm-6 col-md-3">
          <input type="text" id="searchCode" class="form-control" placeholder="Buscar por código...">
      </div>
      <div class="col-6 col-sm-6 col-md-2">
          <button id="btnBuscar" class="btn btn-primary w-100">Buscar</button>
      </div>
      <div class="col-6 col-sm-6 col-md-2">
          <button id="btnClearFilters" class="btn btn-secondary w-100">Limpiar</button>
      </div>
      <div class="col-12 col-md-2 text-center text-md-end">
          <button class="btn btn-success w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#createGroupModal">
              Crear Grupo
          </button>
      </div>
    </div>

    <hr>

    {{-- Listado de grupo (único, ya que el usuario solo puede pertenecer a uno) --}}
    <div id="listaGrupos">
        <p>Cargando grupo...</p>
    </div>
</div>

{{-- Modal para Crear Grupo --}}
<div class="modal fade" id="createGroupModal" tabindex="-1" aria-labelledby="createGroupModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="createGroupModalLabel">Crear Nuevo Grupo</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form id="formCrearGrupo">
            @csrf
            <div class="mb-3">
                <label for="nombreGrupo" class="form-label">Nombre del Grupo</label>
                <input type="text" class="form-control" id="nombreGrupo" name="name">
                <!-- Aquí se puede insertar un mensaje de error mediante JS en el onblur (group-validate.js) -->
            </div>
            <div class="mb-3">
                <label for="capacidadGrupo" class="form-label">Capacidad (2-4)</label>
                <input type="number" class="form-control" id="capacidadGrupo" name="max_miembros">
            </div>
            <!-- Seleccionar Gymkhana vía fetch -->
            <div class="mb-3">
              <label for="gymkhanaId" class="form-label">Gymkhana</label>
              <select class="form-select" id="gymkhanaId" name="gymkhana_id">
                  <!-- Se llenará dinámicamente al abrir el modal -->
              </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Crear</button>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Modal para Detalle de Grupo --}}
<div class="modal fade" id="detalleGroupModal" tabindex="-1" aria-labelledby="detalleGroupModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body" id="detalleGroupBody">
        <p>Cargando detalle...</p>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
    <!-- SweetAlert (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      window.currentUserId = {{ Auth::id() }};
    </script>
    <!-- JS principal -->
    <script src="{{ asset('js/groups.js') }}"></script>
    <script src="{{ asset('js/group-validate.js') }}"></script>
    <script src="{{ asset('js/validar-integrantes.js') }}"></script>
@endsection

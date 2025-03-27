@extends('layout.layout')

@section('content')
<div class="container my-4">
    <h1 class="mb-3">Gestión de Grupos</h1>

    {{-- Buscador --}}
    <div class="row mb-4">
      <div class="col-md-3">
          <input type="text" id="searchName" class="form-control" placeholder="Buscar por nombre...">
      </div>
      <div class="col-md-3">
          <input type="text" id="searchCode" class="form-control" placeholder="Buscar por código...">
      </div>
      <div class="col-md-2">
          <button id="btnBuscar" class="btn btn-primary w-100">Buscar</button>
      </div>
      <div class="col-md-2">
          <button id="btnClearFilters" class="btn btn-secondary w-100">Limpiar Filtros</button>
      </div>
      <div class="col-md-2 text-end">
          <!-- Botón Crear Grupo -->
          <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createGroupModal">
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
  <div class="modal-dialog">
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
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="detalleGroupModalLabel">Detalle del Grupo</h1>
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
@endsection

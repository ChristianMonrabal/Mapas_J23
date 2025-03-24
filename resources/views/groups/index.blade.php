@extends('layout.layout')

@section('content')
<div class="container my-4">
    <h1 class="mb-3">Gestión de Grupos</h1>

    {{-- Buscador --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <input type="text" id="searchQuery" class="form-control" placeholder="Buscar por nombre o código...">
        </div>
        <div class="col-md-2">
            <button id="btnBuscar" class="btn btn-primary w-100">Buscar</button>
        </div>
        <div class="col-md-6 text-end">
            <!-- Botón Crear Grupo -->
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createGroupModal">
                Crear Grupo
            </button>
        </div>
    </div>

    <hr>

    {{-- Listado de grupos --}}
    <div id="listaGrupos">
        <p>Cargando grupos...</p>
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
                <input type="text" class="form-control" id="nombreGrupo" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="gymkhanaId" class="form-label">Gimkhana</label>
                <select class="form-select" id="gymkhanaId" name="gymkhana_id" required>
                    <option value="">-- Seleccione Gimkhana --</option>
                    <option value="1">Gimkhana 1</option>
                    <option value="2">Gimkhana 2</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="capacidadGrupo" class="form-label">Capacidad (2-4)</label>
                <input type="number" class="form-control" id="capacidadGrupo" name="max_miembros" min="2" max="4" required>
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
    {{-- Injecta el ID del usuario autenticado (si lo deseas) --}}
    <script>
        window.currentUserId = {{ Auth::id() ?? 'null' }};
    </script>

    {{-- Carga nuestro archivo JS para manejar todo vía fetch --}}
    <script src="{{ asset('js/groups.js') }}"></script>
@endsection

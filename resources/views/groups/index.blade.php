@extends('layout.layout')

@section('content')
<div class="container">
    <h2 class="text-center my-3">Iniciar Juego - Grupos</h2>
    <div class="row justify-content-center">
        <div class="col-12 col-md-6">
            <!-- Botones para abrir los modales -->
            <div class="text-center mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createGroupModal">
                    Crear Grupo
                </button>
            </div>
            <div class="text-center">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#joinGroupModal">
                    Unirse a un Grupo
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Crear Grupo -->
<div class="modal fade" id="createGroupModal" tabindex="-1" aria-labelledby="createGroupModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="createGroupModalLabel">Crear Grupo</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form id="form-crear-grupo" action="{{ url('/groups/crear') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="groupName" class="form-label">Nombre del Grupo</label>
                <input type="text" class="form-control" id="groupName" name="name" placeholder="Ingrese el nombre del grupo" required>
            </div>
            <!-- Campo opcional: Descripción -->
            <div class="mb-3">
                <label for="groupDescription" class="form-label">Descripción del Grupo</label>
                <textarea class="form-control" id="groupDescription" name="descripcion" placeholder="Ingrese una descripción (opcional)"></textarea>
            </div>
            <!-- Campo: Seleccionar Gimkhana -->
            <div class="mb-3">
                <label for="gymkhanaSelect" class="form-label">Seleccionar Gimkhana</label>
                <select class="form-select" id="gymkhanaSelect" name="gymkhana_id" required>
                    <option value="">Seleccione una Gimkhana</option>
                    <!-- Opciones de ejemplo; se pueden llenar dinámicamente -->
                    <option value="1">Gimkhana 1</option>
                    <option value="2">Gimkhana 2</option>
                </select>
            </div>
            <!-- Campo: Capacidad del Grupo -->
            <div class="mb-3">
                <label for="groupCapacity" class="form-label">Capacidad del Grupo (2-4)</label>
                <input type="number" class="form-control" id="groupCapacity" name="max_miembros" placeholder="Ingrese la capacidad (2-4)" required min="2" max="4">
            </div>
            <button type="submit" class="btn btn-primary w-100">Crear Grupo</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Unirse a un Grupo -->
<div class="modal fade" id="joinGroupModal" tabindex="-1" aria-labelledby="joinGroupModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="joinGroupModalLabel">Unirse a un Grupo</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <!-- Contenedor para la lista de grupos disponibles para unirse -->
        <div id="listaGrupos">
            <p class="text-center">Cargando grupos...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('js/groups.js') }}"></script>
@endsection

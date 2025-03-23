@extends('layout.layout')

@section('content')
<div class="container">
    <h2 class="text-center my-3">Iniciar Juego - Grupos</h2>
    <div class="row justify-content-center">
        <div class="col-12 col-md-6">
            <!-- Sección para Crear Grupo -->
            <div class="card mb-4">
                <div class="card-header text-center">
                    Crear Grupo
                </div>
                <div class="card-body">
                    <form action="{{ route('groups.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="groupName" class="form-label">Nombre del Grupo</label>
                            <input type="text" class="form-control" id="groupName" name="nombre" placeholder="Ingrese el nombre del grupo" required>
                        </div>
                        <!-- Nuevo campo: Descripción -->
                        <div class="mb-3">
                            <label for="groupDescription" class="form-label">Descripción del Grupo</label>
                            <textarea class="form-control" id="groupDescription" name="descripcion" placeholder="Ingrese una descripción (opcional)"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Crear Grupo</button>
                    </form>
                </div>
            </div>

            <!-- Sección visual de Agregar Grupo (Solo Visual) -->
            <div class="card mb-4">
                <div class="card-header text-center">
                    Agregar Grupo
                </div>
                <div class="card-body">
                    <p class="text-center">Esta sección es solo visual. Aquí se mostrará el formulario para agregar un grupo en el futuro.</p>
                    <div class="text-center">
                        <button class="btn btn-secondary" disabled>Agregar Grupo</button>
                    </div>
                </div>
            </div>

            <!-- Botón para abrir el Modal de Unirse a un Grupo -->
            <div class="text-center">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#joinGroupModal">
                    Unirse a un Grupo
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Unirse a un Grupo -->
<div class="modal fade" id="joinGroupModal" tabindex="-1" aria-labelledby="joinGroupModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="joinGroupModalLabel">Unirse a un Grupo</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <!-- Contenedor que se llenará mediante fetch -->
        <div id="groupsList">
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

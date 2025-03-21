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
                    <form action="{{ route('groups.index') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="groupName" class="form-label">Nombre del Grupo</label>
                            <input type="text" class="form-control" id="groupName" name="nombre" placeholder="Ingrese el nombre del grupo" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Crear Grupo</button>
                    </form>
                </div>
            </div>

            <!-- Sección para Unirse a un Grupo -->
            <div class="card">
                <div class="card-header text-center">
                    Unirse a un Grupo
                </div>
                <div class="card-body">
                    @if($groups->count())
                        <ul class="list-group">
                            @foreach($groups as $group)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $group->nombre }}
                                    <form action="" method="POST" class="mb-0">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Unirse</button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mb-0 text-center">No hay grupos disponibles.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

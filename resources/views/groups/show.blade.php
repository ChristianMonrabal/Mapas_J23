@extends('layout.layout')

@section('content')
<div class="container mt-4" id="group-info" data-group-id="{{ $group->id }}">
    <h1>Detalle del Grupo: {{ $group->nombre }}</h1>

    <p><strong>Código:</strong> {{ $group->codigo }}</p>
    <p><strong>Capacidad:</strong> {{ $group->max_miembros }}</p>
    <p><strong>Miembros Actuales:</strong> {{ $group->users->count() }}</p>

    @if($group->gymkhana)
        <p><strong>Gimkhana:</strong> {{ $group->gymkhana->nombre }}</p>
    @else
        <p><strong>Gimkhana:</strong> No asignada</p>
    @endif

    <hr>

    <h3>Miembros del Grupo</h3>
    <ul id="group-members-list">
        @foreach($group->users as $user)
            <li>
                {{ $user->nombre }} ({{ $user->email }})
                {{-- Solo el creador puede expulsar a otros (no a sí mismo) --}}
                @if($group->creador === Auth::id() && $user->id !== Auth::id())
                    <button class="btn btn-sm btn-danger ms-2"
                            onclick="expulsarMiembro({{ $user->id }})">
                        Expulsar
                    </button>
                @endif
            </li>
        @endforeach
    </ul>

    <div class="mt-4">
        {{-- Solo el creador ve estos botones --}}
        @if($group->creador === Auth::id())
            <button class="btn btn-warning" onclick="eliminarGrupo()">
                Eliminar Grupo
            </button>

            {{-- Si está lleno, permitir iniciar el juego --}}
            @if($group->users->count() == $group->max_miembros)
                <button class="btn btn-success ms-2" onclick="iniciarJuego()">
                    Iniciar Juego
                </button>
            @endif
        @endif
    </div>
</div>
@endsection

@section('scripts')
    {{-- Importar el JS de la vista (o usar el mismo groups.js si prefieres) --}}
    <script src="{{ asset('js/groupShow.js') }}"></script>
@endsection

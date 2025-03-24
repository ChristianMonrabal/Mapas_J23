<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    // Listar todos los grupos con la información relacionada (gimkhana y usuarios)
    public function listarGrupos(Request $request)
    {
        $grupos = Group::with('gymkhana', 'users')->get();

        // Si la petición espera JSON (fetch, etc.)
        if ($request->expectsJson()) {
            return response()->json(['groups' => $grupos]);
        }
        // Si se accede directamente por el navegador, se devuelve la vista
        return view('groups.index', compact('grupos'));
    }

    // Crear un grupo
    public function crearGrupo(Request $request)
{
    $datos = $request->validate([
        'name'         => 'required|string|max:100',
        'gymkhana_id'  => 'required|exists:gymkhanas,id',
        'max_miembros' => 'required|integer|between:2,4',
    ]);

    $datos['codigo'] = strtoupper(Str::random(6));
    $datos['creador'] = Auth::id();

    $grupo = Group::create($datos);
    $grupo->users()->attach(Auth::id());

    if ($request->expectsJson()) {
        return response()->json(['grupo' => $grupo], 201);
    }
    
    return redirect()->route('groups.index')
                     ->with('success', '¡Grupo creado correctamente!');
    
}


    // Unirse a un grupo mediante código
    public function unirseGrupo(Request $request)
    {
        $datos = $request->validate([
            'codigo' => 'required|string'
        ]);

        $grupo = Group::where('codigo', $datos['codigo'])->firstOrFail();

        // Verifica que el grupo no esté lleno
        if ($grupo->users()->count() >= $grupo->max_miembros) {
            return response()->json(['mensaje' => 'El grupo ya está lleno.'], 400);
        }

        // Verifica que el usuario no esté ya en el grupo
        if ($grupo->users()->where('user_id', Auth::id())->exists()) {
            return response()->json(['mensaje' => 'Ya formas parte de este grupo.'], 400);
        }

        $grupo->users()->attach(Auth::id());

        return response()->json([
            'mensaje' => 'Te has unido al grupo exitosamente.',
            'grupo'   => $grupo
        ]);
    }

    // Expulsar a un miembro (solo el creador puede expulsar)
    public function expulsarMiembro(Request $request, $grupoId, $miembroId)
    {
        $grupo = Group::findOrFail($grupoId);

        // Verificar permisos: solo el creador puede expulsar
        if (Auth::id() !== $grupo->creador) {
            return response()->json(['mensaje' => 'No tienes permiso para expulsar miembros.'], 403);
        }

        // Evitar expulsar al creador
        if ($miembroId == $grupo->creador) {
            return response()->json(['mensaje' => 'No puedes expulsar al creador.'], 400);
        }

        $grupo->users()->detach($miembroId);

        return response()->json(['mensaje' => 'Miembro expulsado del grupo.']);
    }

    // Iniciar el juego cuando el grupo esté completo
    public function iniciarJuego(Request $request, $grupoId)
    {
        $grupo = Group::withCount('users')->findOrFail($grupoId);

        if ($grupo->users_count < $grupo->max_miembros) {
            return response()->json(['mensaje' => 'El grupo aún no está completo.'], 400);
        }

        // Aquí podrías actualizar un campo de estado para indicar que el juego inició.
        // Ejemplo: $grupo->estado = 'en_juego'; $grupo->save();

        return response()->json(['mensaje' => 'El juego ha comenzado.']);
    }
}
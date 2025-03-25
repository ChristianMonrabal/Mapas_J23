<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Gymkhana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    /**
     * Devuelve la vista principal (sin datos).
     * Los datos se cargarán vía fetch.
     */
    public function index()
    {
        return view('groups.index');
    }

    /**
     * Retorna la lista de grupos en JSON.
     */
    public function list()
    {
        // Cargamos grupos con count de usuarios
        $grupos = Group::with('users', 'gymkhana')
            ->withCount('users')
            ->get();

        return response()->json($grupos);
    }

    /**
     * Busca grupos por nombre o código (JSON).
     */
    public function search(Request $request)
    {
        $query = $request->get('query', '');

        $grupos = Group::with('users', 'gymkhana')
            ->withCount('users')
            ->where('nombre', 'LIKE', "%{$query}%")
            ->orWhere('codigo', 'LIKE', "%{$query}%")
            ->get();

        return response()->json($grupos);
    }

    /**
     * Muestra detalles de un grupo (y sus usuarios) en JSON.
     */
    public function show(Group $group)
    {
        // Cargamos sus relaciones
        $group->load(['users', 'gymkhana']);
        // Retornamos un flag "is_creator" o "is_member" para el usuario actual
        $is_creator = (Auth::id() === $group->creador);
        $is_member = $group->users->contains(Auth::id());

        return response()->json([
            'group' => $group,
            'is_creator' => $is_creator,
            'is_member'  => $is_member
        ]);
    }

    /**
     * Crea un nuevo grupo con validaciones y transacción.
     */
    public function store(Request $request)
    {
        // Verificar si ya eres creador de un grupo
        $grupoExistente = Group::where('creador', Auth::id())->first();
        if ($grupoExistente) {
            return response()->json([
                'message' => 'Ya eres creador de un grupo. Elimínalo antes de crear otro.'
            ], 400);
        }

        $data = $request->validate([
            'nombre'       => 'required|string|max:100',
            'gymkhana_id'  => 'required|exists:gymkhanas,id',
            'max_miembros' => 'required|integer|between:2,4'
        ]);

        $data['codigo']  = strtoupper(Str::random(6));
        $data['creador'] = Auth::id();

        DB::beginTransaction();
        try {
            // Crear grupo
            $grupo = Group::create($data);

            // Asociar al creador
            $grupo->users()->attach(Auth::id());

            DB::commit();
            return response()->json([
                'message' => 'Grupo creado correctamente.',
                'group'   => $grupo
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear el grupo.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unirse a un grupo (si no eres creador de otro).
     */
    public function unirseGrupo(Group $group)
    {
        // Verificar si usuario es creador de un grupo
        $miGrupo = Group::where('creador', Auth::id())->first();
        if ($miGrupo) {
            return response()->json([
                'message' => 'No puedes unirte a otro grupo. Eres creador de uno.'
            ], 400);
        }

        // Verificar capacidad
        if ($group->users()->count() >= $group->max_miembros) {
            return response()->json([
                'message' => 'El grupo está lleno.'
            ], 400);
        }

        // Verificar si ya es miembro
        if ($group->users->contains(Auth::id())) {
            return response()->json([
                'message' => 'Ya formas parte de este grupo.'
            ], 400);
        }

        $group->users()->attach(Auth::id());

        return response()->json([
            'message' => 'Te has unido al grupo.',
            'group'   => $group
        ]);
    }

    /**
     * Iniciar el juego (sólo creador y si está lleno).
     */
    public function iniciarJuego(Group $group)
    {
        if ($group->creador !== Auth::id()) {
            return response()->json([
                'message' => 'No eres el creador; no puedes iniciar el juego.'
            ], 403);
        }

        if ($group->users()->count() < $group->max_miembros) {
            return response()->json([
                'message' => 'El grupo no está completo aún.'
            ], 400);
        }

        // Cambiar estado o lo que necesites
        // $group->estado = 'en_juego';
        // $group->save();

        return response()->json([
            'message' => 'El juego ha comenzado.'
        ]);
    }

    /**
     * Expulsar a un miembro (solo creador).
     */
    public function expulsarMiembro(Group $group, $userId)
    {
        if ($group->creador !== Auth::id()) {
            return response()->json([
                'mensaje' => 'No tienes permiso para expulsar miembros.'
            ], 403);
        }

        if ($userId == $group->creador) {
            return response()->json([
                'mensaje' => 'No puedes expulsar al creador.'
            ], 400);
        }

        $group->users()->detach($userId);

        return response()->json([
            'mensaje' => 'Miembro expulsado del grupo.'
        ]);
    }

    /**
     * Salir de un grupo (miembro normal). El creador no puede "salir", debe eliminar el grupo.
     */
    public function salirDelGrupo(Group $group)
    {
        // Si eres creador no puedes "salir" (a menos que cambies la lógica).
        if ($group->creador == Auth::id()) {
            return response()->json([
                'message' => 'Eres el creador del grupo, no puedes simplemente salir. Elimínalo.'
            ], 400);
        }

        // Verificar si realmente eres miembro
        if (!$group->users->contains(Auth::id())) {
            return response()->json([
                'message' => 'No perteneces a este grupo.'
            ], 400);
        }

        $group->users()->detach(Auth::id());

        return response()->json([
            'message' => 'Has salido del grupo.'
        ]);
    }

    /**
     * Eliminar un grupo (solo creador). Usamos transacción.
     */
    public function destroy(Group $group)
    {
        if ($group->creador !== Auth::id()) {
            return response()->json([
                'message' => 'No tienes permiso para eliminar este grupo.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Desvincular usuarios
            $group->users()->detach();

            // Eliminar el grupo
            $group->delete();

            DB::commit();
            return response()->json([
                'message' => 'Grupo eliminado correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al eliminar el grupo.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}

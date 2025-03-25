<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Gymkhana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

class GroupController extends Controller
{
    /**
     * Vista principal (sin datos directos). Se cargan vía fetch.
     */
    public function index()
    {
        return view('groups.index');
    }

    /**
     * Lista de grupos en JSON.
     */
    public function list()
    {
        $grupos = Group::with('users', 'gymkhana')
            ->withCount('users')
            ->get();

        return response()->json($grupos);
    }

    /**
     * Búsqueda de grupos por nombre o código en JSON.
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
     * Detalle de un grupo (JSON).
     */
    public function show(Group $group)
    {
        // Cargamos usuarios y gymkhana
        $group->load(['users', 'gymkhana']);

        $is_creator = (Auth::id() === $group->creador);
        $is_member  = $group->users->contains(Auth::id());

        return response()->json([
            'group'      => $group,
            'is_creator' => $is_creator,
            'is_member'  => $is_member
        ]);
    }

    /**
     * Crear grupo.
     * - Solo si no estás en ningún grupo (como creador o miembro).
     */
    public function store(Request $request)
    {
        // Verificar si usuario ya está en algún grupo
        if ($this->userIsInAnyGroup()) {
            return response()->json([
                'message' => 'Ya perteneces a un grupo. Debes salir/eliminar ese grupo antes de crear otro.'
            ], 400);
        }

        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'gymkhana_id'  => 'required|exists:gymkhanas,id',
            'max_miembros' => 'required|integer|between:2,4',
        ]);

        $data['codigo']  = strtoupper(Str::random(6));
        $data['creador'] = Auth::id();

        DB::beginTransaction();
        try {
            $grupo = Group::create($data);
            // El creador también pasa a ser "miembro"
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
     * Unirse a un grupo:
     * - Solo si no estás en ningún grupo (como creador o miembro).
     * - Verifica si está lleno.
     */
    public function unirseGrupo(Group $group)
    {
        // Verificar si usuario ya está en un grupo (como creador o miembro)
        if ($this->userIsInAnyGroup()) {
            return response()->json([
                'message' => 'No puedes unirte; ya perteneces a un grupo o eres creador de uno.'
            ], 400);
        }

        // Verificar capacidad
        if ($group->users()->count() >= $group->max_miembros) {
            return response()->json([
                'message' => 'El grupo está lleno.'
            ], 400);
        }

        // Verificar si ya es miembro (caso extraño, pero por seguridad)
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
     * Iniciar el juego (solo si eres creador y el grupo está lleno).
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

        // Dashboard.Gymkhana

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
                'mensaje' => 'No puedes expulsarte a ti mismo (creador).'
            ], 400);
        }

        $group->users()->detach($userId);

        return response()->json([
            'mensaje' => 'Miembro expulsado del grupo.'
        ]);
    }

    /**
     * Salir de un grupo:
     * - NO se permite si eres el creador (debes eliminar el grupo).
     */
    public function salirDelGrupo(Group $group)
    {
        // Recargamos la relación con users para asegurarnos
        $group->load('users');

        if ($group->creador == Auth::id()) {
            return response()->json([
                'message' => 'Eres el creador. Debes eliminar el grupo si quieres salir.'
            ], 400);
        }

        // Verificar si realmente eres miembro
        if (!$group->users->contains(Auth::id())) {
            return response()->json([
                'message' => 'No perteneces a este grupo.'
            ], 400);
        }

        // Salir
        $group->users()->detach(Auth::id());

        return response()->json([
            'message' => 'Has salido del grupo.'
        ]);
    }

    /**
     * Eliminar un grupo (solo creador).
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
            $group->users()->detach();
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

    /**
     * Chequea si el usuario actual está en algún grupo:
     *   - Como creador
     *   - O como miembro en la pivot 'group_users'
     */
    private function userIsInAnyGroup()
    {
        $userId = Auth::id();
        if (!$userId) {
            return false; // O forzar error si no hay sesión
        }

        // 1) ¿Es creador de algún grupo?
        $esCreador = Group::where('creador', $userId)->exists();
        if ($esCreador) {
            return true;
        }

        // 2) ¿Está en algún grupo como miembro?
        //   Revisa la tabla pivot group_users
        $estaEnAlguno = DB::table('group_users')
            ->where('user_id', $userId)
            ->exists();

        return $estaEnAlguno;
    }
}

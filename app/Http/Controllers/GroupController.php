<?php
namespace App\Http\Controllers;

use App\Models\Checkpoint;
use App\Models\Group;
use App\Models\GroupUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Gymkhana;
use App\Models\GymkhanaProgress;

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
    $userId = Auth::id();
    // Asegurarse de que el usuario está autenticado:
    if (!$userId) {
        return response()->json(['message' => 'No autenticado'], 401);
    }

    // Puedes hacer un dd para depurar:
    // dd($userId);

    $group = Group::with(['users', 'gymkhana'])
            ->withCount('users')
            ->where('creador', $userId)
            ->orWhereHas('users', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->first();

    // dd($group);

    if ($group) {
        return response()->json([$group]);
    }

    return response()->json([]);
}
public function available(Request $request)
{
    $userId = Auth::id();
    
    // Devuelve grupos en los que el usuario NO está
    $grupos = Group::with(['users', 'gymkhana'])
        ->withCount('users')
        ->whereDoesntHave('users', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })
        ->get();

    return response()->json($grupos);
}


    

    /**
     * Búsqueda de grupos por nombre o código en JSON.
     */
    public function search(Request $request)
    {
        $name   = $request->get('name', '');
        $codigo = $request->get('codigo', '');
    
        $grupos = Group::with('users')
            ->withCount('users')
            ->when($name, function($query, $name) {
                return $query->where('name', 'LIKE', "%{$name}%");
            })
            ->when($codigo, function($query, $codigo) {
                return $query->where('codigo', 'LIKE', "%{$codigo}%");
            })
            ->get();
    
        return response()->json($grupos);
    }
    


    /**
     * Detalle de un grupo (JSON).
     */
    public function show(Group $group)
{
    // Cargamos la relación 'users' y 'gymkhana' en el grupo
    $group->load(['users']);

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
    public function listarGymkhanas()
    {
        $gymkhanas = Gymkhana::all();
        return response()->json($gymkhanas);
    }
    


    /**
     * Crea un nuevo Grupo y lo asocia a la Gymkhana en gymkhana_progress.
     */
    public function store(Request $request)
    {
        if ($this->userIsInAnyGroup()) {
            return response()->json([
                'message' => 'Ya perteneces a un grupo. Debes salir o eliminar ese grupo antes de crear otro.'
            ], 400);
        }
    
        // Validar que gymkhana_id esté presente y sea válido.
        $request->validate([
            'gymkhana_id' => 'required|exists:gymkhanas,id',
        ]);
    
        // Validar los datos para el grupo (sin incluir gymkhana_id)
        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'max_miembros' => 'required|integer|between:2,4'
        ]);
    
        $data['codigo']  = strtoupper(Str::random(6));
        $data['creador'] = Auth::id();
    
        DB::beginTransaction();
        try {
            // 1. Crear el grupo en la tabla 'groups'
            $group = Group::create($data);
    
            // 2. Insertar manualmente en la tabla pivot 'group_users'
            $groupUsersId = DB::table('group_users')->insertGetId([
                'group_id'   => $group->id,
                'user_id'    => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    
            // 3. Obtener el checkpoint_id:
            // Si no se envía en el request, obtener el primer checkpoint para la gymkhana
            $checkpointId = $request->input('checkpoint_id');
            if (!$checkpointId) {
                $checkpointId = \App\Models\Checkpoint::where('gymkhana_id', $request->gymkhana_id)
                    ->orderBy('id')
                    ->value('id');
                if (!$checkpointId) {
                    throw new \Exception("No hay checkpoints definidos para la gymkhana seleccionada.");
                }
            }
    
            // 4. Crear el registro en gymkhana_progress usando el ID obtenido de la tabla pivot
            \App\Models\GymkhanaProgress::create([
                'group_users_id' => $groupUsersId,
                'gymkhana_id'    => $request->gymkhana_id,
                'checkpoint_id'  => $checkpointId,
                'completed'      => false,
            ]);
    
            DB::commit();
            return response()->json([
                'message' => 'Grupo creado correctamente',
                'group'   => $group
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear el grupo',
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
        if ($this->userIsInAnyGroup()) {
            return response()->json([
                'message' => 'No puedes unirte; ya perteneces a un grupo o eres creador de uno.'
            ], 400);
        }

        if ($group->users()->count() >= $group->max_miembros) {
            return response()->json([
                'message' => 'El grupo está lleno.'
            ], 400);
        }

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
    public function iniciarJuego(Group $group) {
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

        $usuariosDelGrupo = GroupUser::where('group_id', $group->id)->pluck('id');

        // Buscar registros en gymkhana_progress que coincidan con estos usuarios
        $progreso = GymkhanaProgress::whereIn('group_users_id', $usuariosDelGrupo)
            ->pluck('checkpoint_id');

        // Obtener la gymkhana a la que pertenece el primer checkpoint encontrado
        $gymkhanaId = Checkpoint::whereIn('id', $progreso)
            ->value('gymkhana_id'); // Tomamos la primera coincidencia

        // Actualizamos el campo 'game_started' a true para indicar que el juego ha comenzado.
        $group->update(['game_started' => true]);

        return response()->json([
            'message' => 'El juego ha comenzado.',
            'group'   => $group,
            'gymkhana_id' => $gymkhanaId
        ]);
    }

public function estadoJuego()
{
    $userId = Auth::id();
    if (!$userId) {
        return response()->json(['message' => 'No autenticado'], 401);
    }

    $group = Group::with('users')
        ->where(function ($query) use ($userId) {
            $query->where('creador', $userId)
                  ->orWhereHas('users', function ($q) use ($userId) {
                      $q->where('user_id', $userId);
                  });
        })
        ->first();

    if ($group) {
        return response()->json([
            'game_started' => $group->game_started,
            'group'        => $group,
        ]);
    }

    return response()->json(['message' => 'Grupo no encontrado'], 404);
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
        $group->load('users');

        if ($group->creador == Auth::id()) {
            return response()->json([
                'message' => 'Eres el creador. Debes eliminar el grupo si quieres salir.'
            ], 400);
        }

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
     * Eliminar un grupo (solo creador).
     */
    public function destroy($id)
{
    // Buscar el grupo manualmente
    $group = Group::findOrFail($id);

    // Verificar que el usuario autenticado sea el creador del grupo
    if ($group->creador !== Auth::id()) {
        return response()->json([
            'message' => 'No tienes permiso para eliminar este grupo.'
        ], 403);
    }

    DB::beginTransaction();
    try {
        // 1. Eliminar los registros en gymkhana_progress que dependan de este grupo
        DB::table('gymkhana_progress')
            ->whereIn('group_users_id', function($query) use ($group) {
                $query->select('id')
                      ->from('group_users')
                      ->where('group_id', $group->id);
            })
            ->delete();

        // 2. Eliminar las relaciones en la tabla pivot group_users
        $group->users()->detach();

        // 3. Eliminar el grupo
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
     *   - O como miembro en la tabla pivote 'group_users'
     */
    private function userIsInAnyGroup()
    {
        $userId = Auth::id();
        if (!$userId) {
            return false;
        }

        $esCreador = Group::where('creador', $userId)->exists();
        if ($esCreador) {
            return true;
        }

        $estaEnAlguno = DB::table('group_users')
            ->where('user_id', $userId)
            ->exists();

        return $estaEnAlguno;
    }
}

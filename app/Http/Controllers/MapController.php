<?php

namespace App\Http\Controllers;

use App\Models\Checkpoint;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Gymkhana;
use App\Models\GymkhanaProgress;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MapController extends Controller
{
    // Función para obtener los datos de la gymkhana (checkpoints, progreso, etc.)
    public function obtenerDatosGymkhana($gymkhanaId, $grupoId) {
        // 1️⃣ Obtener el grupo
        $grupo = Group::findOrFail($grupoId);
    
        // 2️⃣ Obtener los IDs de los usuarios relacionados con ese grupo (usando la relación many-to-many)
        $usuariosDelGrupo = GroupUser::where('group_id', $grupoId)->pluck('id');

        $todosLosUsuariosDeUnGrupo = GroupUser::where('group_id', $grupoId)->pluck('user_id');
    
        // 3️⃣ Obtener la gymkhana
        $gymkhana = Gymkhana::findOrFail($gymkhanaId);
    
        // 4️⃣ Obtener los checkpoints de la gymkhana
        $checkpoints = Checkpoint::where('gymkhana_id', $gymkhana->id)->get();
    
        // 5️⃣ Obtener los IDs de los checkpoints
        $checkpointsIds = $checkpoints->pluck('id');
    
        // 6️⃣ Obtener la relación entre "usuarios del grupo" y "checkpoints" en la tabla `gymkhana_progress`
        $progreso = GymkhanaProgress::whereIn('group_users_id', $usuariosDelGrupo)
        ->whereIn('checkpoint_id', $checkpointsIds)
        ->get();
    
        // 7️⃣ Obtener los lugares asociados a los checkpoints
        $sitios = [];
    
        foreach ($checkpoints as $checkpoint) {
            
            $lugar = Place::find($checkpoint->place_id);
    
            if ($lugar) {
                // Obtener etiquetas del lugar
                $etiquetas = $lugar->tags()->get(['name', 'img']);
                Log::info("Lugar encontrado: " . $lugar->name);
    
                // Determinar el icono basado en la primera etiqueta (si existe)
                $icono = $etiquetas->isNotEmpty() ? $etiquetas->first()->img : null;
    
                // Construir la información del sitio
                $sitios[] = [
                    'id' => $lugar->id,
                    'name' => $lugar->name,
                    'description' => $lugar->description,
                    'latitude' => $lugar->latitude,
                    'longitude' => $lugar->longitude,
                    'etiquetas' => $etiquetas->toArray(),
                    'icono' => $icono,
                    'pista' => $checkpoint->pista,
                    'completed' => $checkpoint->completed,
                    'is_gymkhana' => true,
                ];
            } else {
                Log::info("No se encontró lugar para el checkpoint: " . $checkpoint->id);
            }
        }

        // // También puedes incluir otros lugares no relacionados con la gymkhana si lo deseas
        $otrosSitios = Place::all()->where('gymkhana_id', null);

        foreach ($otrosSitios as $lugar) {

            $etiquetas = $lugar->tags()->get(['name', 'img']);
            $icono = $etiquetas->isNotEmpty() ? $etiquetas->first()->img : null;

            $sitios[] = [
                'id' => $lugar->id,
                'name' => $lugar->name,
                'description' => $lugar->description,
                'latitude' => $lugar->latitude,
                'longitude' => $lugar->longitude,
                'etiquetas' => $etiquetas->toArray(),
                'icono' => $icono,
                'pista' => null,  // No tiene pista ya que no está en la gymkhana
                'completed' => null,  // Como no está en la gymkhana, lo damos por nulo
                'is_gymkhana' => false,  // Indicamos que no es parte de la gymkhana
            ];
        }

        $idUsuarioActual = Auth::id();
    
        // 8️⃣ Devolver la respuesta en formato JSON
        return response()->json([
            'sitios' => $sitios,
            'grupo' => $grupo,
            'progreso' => $progreso,
            'usuariosDelGrupo' => $usuariosDelGrupo,
            'todosLosUsuariosDeUnGrupo' => $todosLosUsuariosDeUnGrupo,
            'idUsuarioActual' => $idUsuarioActual,
        ]);
    }

    // Verifica que usuarios han completado un sitio
    // (Comprueba si la columna "completed" de la tabla group_users (de los usuarios del grupo en el que estemos) es 1 (completado) )
    public function verificarUsuariosCompletados($grupoId)
    {
        // Contar el total de usuarios en el grupo
        $totalUsuarios = GroupUser::where('group_id', $grupoId)->count();

        // Contar los usuarios que ya completaron el checkpoint
        $usuariosCompletados = GroupUser::where('group_id', $grupoId)
            ->where('completed', 1)
            ->count();

        return response()->json(['todosCompletados' => $usuariosCompletados === $totalUsuarios]);
    }

    // Actualiza el progreso del usuario que ha completado el sitio
    // (Actualiza la columna "completed" de la tabla group_users (del grupo en el que estemos) a 1 (completado) )
    public function actualizarProgresoUsuario($usuarioId) {
        // Buscar el usuario en la tabla pivot group_users
        $usuarioGrupo = GroupUser::where('user_id', $usuarioId)->first();
    
        // Si el usuario pertenece al grupo y el checkpoint pertenece a su gymkhana
        if ($usuarioGrupo) {
            $usuarioGrupo->completed = 1;
            $usuarioGrupo->save();
    
            return response()->json(['success' => true, 'message' => 'Progreso actualizado']);
        }
    
        return response()->json(['success' => false, 'message' => 'Usuario no válido'], 400);
    }
    

    // Cuando han completado el sitio todos los del grupo
    // (cuando todos los usuarios del grupo que estén relacionados con la gymkhana (se relacionan en la tabla "gymkhana_progress") tengan la columna "completed" de la tabla "group_users" a 1),
    // se actualiza el progreso del sitio (cambia el valor de la columna "completed" de la tabla "checkpoints" a 1 )
    public function actualizarCheckpointCompletado(Request $request, $placeId)
    {
        // Buscar el lugar por su ID
        $place = Place::find($placeId);
    
        // Buscar el checkpoint que tiene ese place_id
        $checkpoint = Checkpoint::where('place_id', $place->id)->first();
    
        // Actualizar el checkpoint
        $checkpoint->completed = $request->completed;
        $checkpoint->save();
    
        return response()->json(['success' => true, 'checkpoint' => $checkpoint]);
    }
    

    // Verifica que sitios han sido completados
    public function verificarGymkhanaCompletada($gymkhanaId)
    {
        // Contar los checkpoints incompletos de la gymkhana
        $checkpointsIncompletos = Checkpoint::where('gymkhana_id', $gymkhanaId)
        ->where('completed', 0)
        ->count();

        Log::info("Checkpoints incompletos para gymkhana {$gymkhanaId}: {$checkpointsIncompletos}");

        // Retornar el estado de la gymkhana
        return response()->json(['gymkhanaCompletada' => $checkpointsIncompletos === 0]);
    }

    // Cuando nuestro grupo ha completado todos los sitios de nuestra gymkhana (que teníamos relacionada en la tabla "gymkhana_progress")
    // (cuando todos los sitios de la gymkhana (se relacionan en la tabla "checkpoints") tengan la columna "completed" de la tabla "checkpoints" a 1),
    // se actualiza el progreso de la gymhana, a acabada (cambia el valor de la columna "completed" de la tabla "gymkhana_progress" a 1 )
    public function actualizarProgresoGimcana(Request $request, $grupoId)
    {
        \Log::info("🔄 Iniciando actualización del progreso para el grupo {$grupoId} en el sitio {$request->sitioId}.");
    
        // Obtener el grupo correspondiente
        $grupo = Group::find($grupoId);
        
        if (!$grupo) {
            \Log::error("❌ Grupo {$grupoId} no encontrado.");
            return response()->json(['error' => 'Grupo no encontrado'], 404);
        }
    
        \Log::info("✅ Grupo encontrado: " . json_encode($grupo));
    
        // Obtener los usuarios del grupo
        $usuariosDelGrupo = GroupUser::where('group_id', $grupoId)->pluck('id');
    
        if ($usuariosDelGrupo->isEmpty()) {
            \Log::warning("⚠️ No se encontraron usuarios en el grupo {$grupoId}.");
            return response()->json(['error' => 'No hay usuarios en el grupo'], 404);
        }
    
        \Log::info("👥 Usuarios del grupo: " . json_encode($usuariosDelGrupo));
    
        // Obtener el checkpoint correspondiente al sitio que se está desbloqueando
        $checkpoint = Checkpoint::where('place_id', $request->sitioId)->first();
    
        if (!$checkpoint) {
            \Log::error("❌ Checkpoint con sitioId {$request->sitioId} no encontrado.");
            return response()->json(['error' => 'Checkpoint no encontrado'], 404);
        }
    
        \Log::info("📍 Checkpoint encontrado: " . json_encode($checkpoint));
    
        // Buscar el progreso de un usuario específico en `gymkhana_progress`
        $progreso = GymkhanaProgress::where('group_users_id', $usuariosDelGrupo->first())
            ->where('checkpoint_id', $checkpoint->id)
            ->first();
    
        if (!$progreso) {
            \Log::warning("⚠️ No se encontró progreso para el usuario {$usuariosDelGrupo->first()} en el checkpoint {$checkpoint->id}.");
            return response()->json(['error' => 'Progreso no encontrado'], 404);
        }
    
        \Log::info("📊 Progreso encontrado antes de actualizar: " . json_encode($progreso));
    
        // Si se encuentra el progreso, actualizar "completed" a 1
        $progreso->completed = 1;
        $progreso->save();
    
        \Log::info("✅ Progreso actualizado: " . json_encode($progreso));
    
        // Devolver respuesta
        return response()->json(['success' => true, 'progreso' => $progreso]);
    }
    

    public function verificarGymkhanaFinalizada($gymkhanaId) 
    {
        \Log::info("🔍 Verificando si la gymkhana {$gymkhanaId} ha sido finalizada.");
    
        // Obtener los IDs de los checkpoints de esta gymkhana
        $checkpoints = Checkpoint::where('gymkhana_id', $gymkhanaId)->pluck('id');
    
        if ($checkpoints->isEmpty()) { // Ahora sí es una colección
            \Log::warning("⚠️ No se encontraron checkpoints para la gymkhana {$gymkhanaId}.");
            return response()->json(['error' => 'No se encontraron checkpoints'], 404);
        }
    
        \Log::info("✅ Checkpoints encontrados: " . json_encode($checkpoints));
    
        // Buscar el progreso asociado a estos checkpoints
        $progress = GymkhanaProgress::whereIn('checkpoint_id', $checkpoints)
            ->where('completed', 1)
            ->exists(); // Mejor usar exists() para verificar si hay registros
    
        if ($progress) {
            \Log::info("🏆 Gymkhana {$gymkhanaId} ha sido completada.");
            return response()->json(['gymkhanaCompletada' => true]);
        }
    
        \Log::info("❌ Gymkhana {$gymkhanaId} aún no ha sido completada.");
        return response()->json(['gymkhanaCompletada' => false]);
    }
    
    

    // Función para reiniciar el progreso de los usuarios
    public function reiniciarProgresoUsuarios($grupoId)
    {
        // Obtener todos los usuarios del grupo usando el modelo GroupUser
        $usuariosDelGrupo = GroupUser::where('group_id', $grupoId)->get();

        // Reiniciar la columna `completed` de todos los usuarios a 0
        foreach ($usuariosDelGrupo as $usuario) {
            $usuario->completed = 0;
            $usuario->save();
        }

        // Retornar una respuesta exitosa
        return response()->json(['success' => true]);
    }
}

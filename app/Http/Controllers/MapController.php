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
    public function actualizarCheckpointCompletado(Request $request, $checkpointId)
    {
        Log::info('🔹 Entrando en actualizarCheckpointCompletado');
        Log::info('📌 Checkpoint ID recibido: ' . $checkpointId);
        Log::info('📩 Datos recibidos en la request:', $request->all());
    
        // Buscar el checkpoint
        $checkpoint = Checkpoint::find($checkpointId);
    
        if (!$checkpoint) {
            Log::error('❌ Checkpoint no encontrado: ' . $checkpointId);
            return response()->json(['error' => 'Checkpoint no encontrado'], 404);
        }
    
        Log::info('✅ Checkpoint encontrado:', ['checkpoint' => $checkpoint]);
    
        // Actualizar el estado de "completed"
        $checkpoint->completed = $request->completed;
        $checkpoint->save();
    
        Log::info('✅ Checkpoint actualizado correctamente', ['checkpoint' => $checkpoint]);
    
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
        // Obtener el grupo correspondiente
        $grupo = Group::findOrFail($grupoId);

        $usuariosDelGrupo = GroupUser::where('group_id', $grupoId)->pluck('id');

        // Obtener el checkpoint correspondiente al sitio que se está desbloqueando
        $checkpoint = Checkpoint::findOrFail($request->sitioId);

        // Buscar el progreso de un usuario específico en la tabla `gymkhana_progress` para el checkpoint actual
        $progreso = GymkhanaProgress::where('group_users_id', $usuariosDelGrupo->first()) // Asegúrate de que sea un usuario del grupo, puedes recorrer o filtrar si es necesario
            ->where('checkpoint_id', $checkpoint->id)
            ->first();

        // Si se encuentra el progreso, actualizamos la columna "completed" de 0 a 1
        if ($progreso) {
            $progreso->completed = 1;
            $progreso->save();
        }

        // Devolver la respuesta indicando que el progreso se ha actualizado
        return response()->json(['success' => true, 'progreso' => $progreso]);
    }

    public function verificarGymkhanaFinalizada($gymkhanaId) {

        // Obtener el checkpoint correspondiente al sitio que se está desbloqueando
        $checkpoint = Checkpoint::where('gymkhana_id', $gymkhanaId)->pluck('id');

        $progress = GymkhanaProgress::where('checkpoint_id', $checkpoint)->first();

        // Verifica si el progreso de la gymkhana está completado (completed = 1)
        if ($progress && $progress->completed === 1) {
            return response()->json(['gymkhanaCompletada' => true]);
        }

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

<?php

namespace App\Http\Controllers;

use App\Models\Checkpoint;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Gymkhana;
use App\Models\GymkhanaProgress;
use App\Models\Place;
use Illuminate\Http\Request;

class MapController extends Controller
{
    // Función para obtener los datos de la gymkhana (checkpoints, progreso, etc.)
    public function obtenerDatosGymkhana($gymkhanaId, $grupoId) {
        // 1️⃣ Obtener el grupo
        $grupo = Group::findOrFail($grupoId);
    
        // 2️⃣ Obtener los IDs de los usuarios relacionados con ese grupo (usando la relación many-to-many)
        $usuariosDelGrupo = GroupUser::where('group_id', $grupoId)->pluck('id');
    
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
    
                // Determinar el icono basado en la primera etiqueta (si existe)
                $icono = $etiquetas->isNotEmpty() ? $etiquetas->first()->img : null;
    
                // Construir la información del sitio
                $sitios[] = [
                    'name' => $lugar->name,
                    'latitude' => $lugar->latitude,
                    'longitude' => $lugar->longitude,
                    'etiquetas' => $etiquetas->toArray(),
                    'icono' => $icono,
                    'pista' => $checkpoint->pista,
                    'completed' => $checkpoint->completed,
                ];
            }
        }
    
        // 8️⃣ Devolver la respuesta en formato JSON
        return response()->json([
            'sitios' => $sitios,
            'grupo' => $grupo,
            'progreso' => $progreso,
        ]);
    }

    // Función para actualizar el campo completed de un checkpoint
    public function actualizarCheckpointCompletado(Request $request, $checkpointId)
    {
        $checkpoint = Checkpoint::findOrFail($checkpointId);
        $checkpoint->completed = $request->completed;  // Marcamos el checkpoint como completado
        $checkpoint->save();

        return response()->json(['success' => true, 'checkpoint' => $checkpoint]);
    }

    // Función para actualizar el progreso de un grupo en los checkpoints
    public function actualizarProgreso(Request $request, $grupoId)
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
            $progreso->completed = 1;  // Marcamos el progreso como completado
            $progreso->save();
        }

        // Devolver la respuesta indicando que el progreso se ha actualizado
        return response()->json(['success' => true, 'progreso' => $progreso]);
    }
}

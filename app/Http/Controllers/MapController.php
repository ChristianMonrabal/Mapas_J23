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
                $etiquetas = $lugar->tags()->pluck('tags.name')->toArray();
    
                // Determinar el icono basado en la primera etiqueta (si existe)
                $icono = !empty($etiquetas) ? $this->obtenerIconoPorEtiqueta($etiquetas[0]) : null;
    
                // Construir la información del sitio
                $sitios[] = [
                    'name' => $lugar->name,
                    'latitude' => $lugar->latitude,
                    'longitude' => $lugar->longitude,
                    'etiquetas' => $etiquetas,
                    'icono' => $icono,
                    'pista' => $checkpoint->pista,
                ];
            }
        }
    
        // 8️⃣ Devolver la respuesta en formato JSON
        return response()->json([
            'sitios' => $sitios,
            'grupo' => $grupo,
            'progreso' => $progreso
        ]);
    }   
    

    // Función para obtener el icono según la etiqueta
    public function obtenerIconoPorEtiqueta($etiqueta) {
        $iconos = [
            'lugar' => 'https://cdn-icons-png.flaticon.com/128/367/367393.png',
            'parque' => 'https://cdn-icons-png.flaticon.com/128/367/367393.png',
            'restaurante' => 'https://cdn-icons-png.flaticon.com/128/367/367393.png',
        ];

        return $iconos[$etiqueta] ?? 'https://cdn-icons-png.flaticon.com/128/367/367393.png';
    }

    // Función para unirse a un grupo mediante el código
    // public function unirseAGrupo($codigoGrupo)
    // {
    //     // Buscar el grupo por el código
    //     $grupo = Group::where('codigo', $codigoGrupo)->first();
        
    //     // Si el grupo existe, se devuelve la información del grupo
    //     if ($grupo) {
    //         return response()->json(['success' => true, 'grupo' => $grupo]);
    //     }

    //     // Si no se encuentra el grupo, devolver un mensaje de error
    //     return response()->json(['success' => false, 'message' => 'Código incorrecto.']);
    // }

    // Función para actualizar el progreso de un grupo en los checkpoints
    public function actualizarProgreso(Request $request, $grupoId)
    {
        // Obtener el grupo correspondiente
        $grupo = Group::findOrFail($grupoId);

        // Crear un nuevo progreso en la gymkhana
        $progreso = GymkhanaProgress::create([
            'group_id' => $grupo->id,
            'checkpoint_id' => $request->sitioId
        ]);

        // Devolver la respuesta indicando que el progreso se ha actualizado
        return response()->json(['success' => true, 'progreso' => $progreso]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Checkpoint;
use App\Models\Group;
use App\Models\GymkhanaProgress;
use App\Models\Place;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function obtenerDatosGymkhana($gymkhanaId, $grupoId)
    {
        // Buscar el grupo relacionado con la gymkhana
        $grupo = Group::findOrFail($grupoId);

        // Obtener los progresos del grupo en la gymkhana
        $progreso = GymkhanaProgress::where('group_id', $grupo->id)->get();

        // Obtener los checkpoints de la gymkhana
        $checkpoints = Checkpoint::where('gymkhana_id', $gymkhanaId)->get();

        $sitios = [];
        
        foreach ($checkpoints as $checkpoint) {
            // Obtener el lugar (place) asociado al checkpoint
            $lugar = Place::find($checkpoint->place_id);
            
            // Obtener las etiquetas del lugar (place)
            $etiquetas = $lugar->tags()->pluck('tags.name')->toArray();
            // Si tiene etiquetas, usamos la primera para el icono
            $icono = $etiquetas ? $this->obtenerIconoPorEtiqueta($etiquetas[0]) : null;
            
            // Obtener la pista del checkpoint
            $pista = $checkpoint->pista;

            // Agregar los datos al array de sitios
            $sitios[] = [
                'name' => $lugar->name,
                'latitude' => $lugar->latitude,
                'longitude' => $lugar->longitude,
                'etiquetas' => $etiquetas,
                'icono' => $icono,
                'pista' => $pista,
            ];
        }

        // Devolver los datos al frontend (en formato JSON)
        return response()->json([
            'sitios' => $sitios,
            'grupo' => $grupo,
            'progreso' => $progreso
        ]);
    }

    // Función para obtener el icono según la etiqueta (puedes ampliar esto si necesitas más iconos)
    public function obtenerIconoPorEtiqueta($etiqueta)
    {
        $iconos = [
            'lugar' => 'https://cdn-icons-png.flaticon.com/128/367/367393.png',
            'parque' => 'https://cdn-icons-png.flaticon.com/128/367/367393.png',
            'restaurante' => 'https://cdn-icons-png.flaticon.com/128/367/367393.png',
        ];

        return $iconos[$etiqueta] ?? 'https://cdn-icons-png.flaticon.com/128/367/367393.png';
    }
}

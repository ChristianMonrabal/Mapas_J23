<?php

namespace App\Http\Controllers;

use App\Models\Checkpoint;
use App\Models\Gymkhana;
use App\Models\Place;
use Illuminate\Http\Request;

class CheckpointController extends Controller
{
    // Mostrar todos los checkpoints
    public function index()
    {
        $checkpoints = Checkpoint::with(['gymkhana', 'place'])->get();
        return response()->json($checkpoints);
    }

    // Crear un nuevo checkpoint
    public function store(Request $request)
    {
        $request->validate([
            'pista' => 'required|max:255',
            'gymkhana_id' => 'required|exists:gymkhanas,id',
            'place_id' => 'required|exists:places,id',
        ]);

        $checkpoint = new Checkpoint();
        $checkpoint->pista = $request->pista;
        $checkpoint->gymkhana_id = $request->gymkhana_id;
        $checkpoint->place_id = $request->place_id;
        $checkpoint->save();

        return response()->json([
            'message' => 'Checkpoint creado con éxito!',
            'checkpoint' => $checkpoint
        ]);
    }

    // Ver detalles de un checkpoint específico
    public function show($id)
{
    $checkpoint = Checkpoint::with(['gymkhana', 'place'])->find($id);
    if ($checkpoint) {
        return response()->json($checkpoint);
    }
    return response()->json(['message' => 'Checkpoint no encontrado'], 404);
}


    // Actualizar un checkpoint
    public function update(Request $request, $id)
    {
        $checkpoint = Checkpoint::find($id);

        if (!$checkpoint) {
            return response()->json(['message' => 'Checkpoint no encontrado'], 404);
        }

        $request->validate([
            'pista' => 'required|max:255',
            'gymkhana_id' => 'required|exists:gymkhanas,id',
            'place_id' => 'required|exists:places,id',
        ]);

        $checkpoint->pista = $request->pista;
        $checkpoint->gymkhana_id = $request->gymkhana_id;
        $checkpoint->place_id = $request->place_id;
        $checkpoint->save();

        return response()->json([
            'message' => 'Checkpoint actualizado con éxito!',
            'checkpoint' => $checkpoint
        ]);
    }

    // Eliminar un checkpoint
    public function destroy($id)
    {
        $checkpoint = Checkpoint::find($id);

        if (!$checkpoint) {
            return response()->json(['message' => 'Checkpoint no encontrado'], 404);
        }

        $checkpoint->delete();

        return response()->json(['message' => 'Checkpoint eliminado con éxito']);
    }

    // Obtener todas las gymkhanas disponibles para los checkpoints
    public function getGymkhanas()
    {
        $gymkhanas = Gymkhana::all();
        return response()->json($gymkhanas);
    }

    // Obtener todos los lugares disponibles para los checkpoints
    public function getPlaces()
    {
        $places = Place::all();
        return response()->json($places);
    }
}

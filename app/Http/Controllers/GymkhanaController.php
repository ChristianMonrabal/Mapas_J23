<?php

namespace App\Http\Controllers;

use App\Models\Gymkhana;
use Illuminate\Http\Request;

class GymkhanaController extends Controller
{
    // Mostrar todas las gymkhanas
    public function index()
    {
        $gymkhanas = Gymkhana::all();
        return response()->json($gymkhanas);
    }

    // Crear una nueva gymkhana
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
        ]);

        $gymkhana = new Gymkhana();
        $gymkhana->name = $request->name;
        $gymkhana->description = $request->description;
        $gymkhana->save();

        return response()->json([
            'message' => 'Gymkhana creada con éxito!',
            'gymkhana' => $gymkhana
        ]);
    }

    // Ver detalles de una gymkhana específica
    public function show($id)
    {
        $gymkhana = Gymkhana::find($id);

        if ($gymkhana) {
            return response()->json($gymkhana);
        }

        return response()->json(['message' => 'Gymkhana no encontrada'], 404);
    }

    // Actualizar una gymkhana
    public function update(Request $request, $id)
    {
        $gymkhana = Gymkhana::find($id);

        if (!$gymkhana) {
            return response()->json(['message' => 'Gymkhana no encontrada'], 404);
        }

        $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
        ]);

        $gymkhana->name = $request->name;
        $gymkhana->description = $request->description;
        $gymkhana->save();

        return response()->json([
            'message' => 'Gymkhana actualizada con éxito!',
            'gymkhana' => $gymkhana
        ]);
    }

    // Eliminar una gymkhana
    public function destroy($id)
    {
        $gymkhana = Gymkhana::find($id);

        if (!$gymkhana) {
            return response()->json(['message' => 'Gymkhana no encontrada'], 404);
        }

        $gymkhana->delete();

        return response()->json(['message' => 'Gymkhana eliminada con éxito']);
    }
}

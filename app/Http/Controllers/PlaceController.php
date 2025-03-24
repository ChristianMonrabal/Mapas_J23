<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;

class PlaceController extends Controller
{
    public function mostrar_lugares()
    {
        try {
            $places = Place::with('tags')->get();
            return response()->json([
                'success' => true,
                'places' => $places
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al cargar los lugares: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:20',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'description' => 'required|string',
        ]);

        $place = Place::create($request->all());
        return response()->json(['message' => 'Place created successfully', 'place' => $place], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:20',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'description' => 'required|string',
        ]);

        $place = Place::findOrFail($id);
        $place->update($request->all());
        return response()->json(['message' => 'Place updated successfully', 'place' => $place]);
    }

    public function destroy($id)
    {
        $place = Place::findOrFail($id);
        $place->delete();
        return response()->json(['message' => 'Place deleted successfully']);
    }

    public function show($id)
    {
        $place = Place::with('tags')->find($id);
        
        if (!$place) {
            return response()->json([
                'success' => false,
                'message' => 'Lugar no encontrado'
            ], 404);
        }

        // Verificar si hay imagen y asegurarnos de que el campo se llama 'image'
        $placeData = $place->toArray();

        return response()->json([
            'success' => true,
            'place' => $placeData
        ]);
    }

    public function search($query)
    {
        try {
            $places = Place::with('tags')
                ->where(function($q) use ($query) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($query) . '%'])
                      ->orWhereRaw('LOWER(address) LIKE ?', ['%' . strtolower($query) . '%'])
                      ->orWhereHas('tags', function($tagQuery) use ($query) {
                          $tagQuery->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($query) . '%']);
                      });
                })
                ->get();

            return response()->json([
                'success' => true,
                'places' => $places
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error en la búsqueda: ' . $e->getMessage()
            ], 500);
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;

class PlaceController extends Controller
{
    public function index()
    {
        try {
            $places = Place::all();
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
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;
use App\Models\Tag;
use Illuminate\Support\Facades\File;

class PlaceController extends Controller
{
    public function index(Request $request)
    {
        $query = Place::with('tags');
        
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }

        $places = $query->get();
        
        return response()->json(['places' => $places]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:20',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            
            if (!File::exists(public_path('img/places'))) {
                File::makeDirectory(public_path('img/places'), 0755, true);
            }
            
            $image->move(public_path('img/places'), $imageName);
            $imagePath = '/img/places/'.$imageName;
        }

        $place = Place::create([
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'description' => $request->input('description'),
            'img' => $imagePath
        ]);

        if ($request->has('tags')) {
            $place->tags()->sync($request->input('tags'));
        }

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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id'
        ]);

        $place = Place::findOrFail($id);

        if ($request->hasFile('image')) {
            if ($place->img && File::exists(public_path($place->img))) {
                File::delete(public_path($place->img));
            }
            
            $image = $request->file('image');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('img/places'), $imageName);
            $place->img = '/img/places/'.$imageName;
        }

        $place->update([
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'description' => $request->input('description')
        ]);

        if ($request->has('tags')) {
            $place->tags()->sync($request->input('tags'));
        }

        return response()->json(['message' => 'Place updated successfully', 'place' => $place]);
    }

    public function destroy($id)
    {
        $place = Place::findOrFail($id);
        
        $place->tags()->detach();
        
        if ($place->img && File::exists(public_path($place->img))) {
            File::delete(public_path($place->img));
        }
        
        $place->delete();
        
        return response()->json(['message' => 'Place deleted successfully']);
    }

    public function show($id)
    {
        $place = Place::with('tags')->findOrFail($id);
        return response()->json([
            'success' => true,
            'place' => $place
        ]);
    }

    public function favorites()
    {
        $favorites = Place::where('is_favorite', true)->get();
        return response()->json(['favorites' => $favorites]);
    }
}
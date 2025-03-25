<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

class TagController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Tag::query();
            
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where('name', 'like', '%' . $searchTerm . '%');
            }

            $tags = $query->get();
            
            return response()->json([
                'tags' => $tags
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:20|unique:tags,name',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $imageName = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time().'.'.$image->getClientOriginalExtension();
                
                // Crear directorio si no existe
                if (!File::exists(public_path('img/tags'))) {
                    File::makeDirectory(public_path('img/tags'), 0755, true);
                }
                
                $image->move(public_path('img/tags'), $imageName);
            }

            $tag = Tag::create([
                'name' => $request->input('name'),
                'img' => $imageName ? 'img/tags/'.$imageName : null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            return response()->json([
                'message' => 'Tag agregado correctamente',
                'tag' => $tag
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:20|unique:tags,name,' . $id,
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
    
            $tag = Tag::findOrFail($id);
            $tag->name = $request->input('name');

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($tag->img && File::exists(public_path($tag->img))) {
                    File::delete(public_path($tag->img));
                }
                
                $image = $request->file('image');
                $imageName = time().'.'.$image->getClientOriginalExtension();
                $image->move(public_path('img/tags'), $imageName);
                $tag->img = 'img/tags/'.$imageName;
            }
            
            $tag->save();
    
            return response()->json([
                'message' => 'Tag actualizado correctamente',
                'tag' => $tag
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $tag = Tag::findOrFail($id);
            
            // Delete associated image
            if ($tag->img && File::exists(public_path($tag->img))) {
                File::delete(public_path($tag->img));
            }
            
            $tag->delete();

            return response()->json([
                'message' => 'Tag eliminado correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
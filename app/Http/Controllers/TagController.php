<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use Exception;
use Illuminate\Support\Carbon;

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
                'name' => 'required|string|max:20|unique:tags,name'
            ]);

            $tag = Tag::create([
                'name' => $request->input('name'),
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
                'name' => 'required|string|max:20|unique:tags,name,' . $id
            ]);
    
            $tag = Tag::findOrFail($id);
            $tag->name = $request->input('name');
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
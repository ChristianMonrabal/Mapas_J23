<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggle(Request $request, $placeId)
    {
        $userId = auth()->id();
        $favorite = Favorite::where('user_id', $userId)
                          ->where('place_id', $placeId)
                          ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['isFavorite' => false]);
        }

        Favorite::create([
            'user_id' => $userId,
            'place_id' => $placeId
        ]);

        return response()->json(['isFavorite' => true]);
    }

    public function check($placeId)
    {
        $userId = auth()->id();
        $isFavorite = Favorite::where('user_id', $userId)
                            ->where('place_id', $placeId)
                            ->exists();

        return response()->json(['isFavorite' => $isFavorite]);
    }
} 
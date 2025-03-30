<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GymkhanaProgress;
use App\Models\GroupUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }

        $users = $query->get();
        
        return response()->json(['users' => $users]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return response()->json(['message' => 'Usuario creado con éxito', 'user' => $user], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->role_id = $request->role_id;
        $user->save();

        return response()->json(['message' => 'Usuario actualizado con éxito', 'user' => $user]);
    }

    public function destroy($id) {

        // Iniciar una transacción
        DB::beginTransaction();
    
        try {

            $user = User::findOrFail($id);
    
            // Obtener los IDs de las relaciones en group_users
            $group_users_ids = GroupUser::where('user_id', $user->id)->pluck('id');
        
            $gymkhana_progress = GymkhanaProgress::whereIn('group_users_id', $group_users_ids);
    
            $gymkhana_progress->delete();
    
            $group_users = GroupUser::where('user_id', $user->id);
    
            $group_users->delete();
        
            $user->delete();
    
            // Confirmar la transacción
            DB::commit();
    
            return response()->json(['message' => 'Usuario eliminado con éxito']);
    
        } catch (\Exception $e) {

            // Si hay error, deshacer cambios
            DB::rollBack();
            return response()->json(['error' => 'Error al eliminar el usuario', 'details' => $e->getMessage()], 500);

        }
    }
    
    
}
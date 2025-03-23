<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;


class GroupController 
{
    public function index()
    {
        $groups = Group::all(); // Obtén tus datos
        return view('groups.index', compact('groups'));
    }
    public function store(Request $request)
{
    // Valida los campos, aquí 'descripcion' es opcional
    $validated = $request->validate([
        'nombre'      => 'required|string|max:100',
        'descripcion' => 'nullable|string'
    ]);

    // Crea el grupo utilizando asignación masiva
    Group::create($validated);

    return redirect()->back()->with('success', 'Grupo creado con éxito.');
}


}

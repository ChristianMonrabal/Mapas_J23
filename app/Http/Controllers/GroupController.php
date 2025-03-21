<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;


class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::all(); // Obtén tus datos
        return view('groups.index', compact('groups'));
    }
    

}

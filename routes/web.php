<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\UserController;
use App\Models\Tag;


Route::get('signin', [AuthController::class, 'showSigninForm'])->name('auth.signin');
Route::post('signin', [AuthController::class, 'signin'])->name('auth.signin.submit');
Route::get('signup', [AuthController::class, 'showSignupForm'])->name('auth.signup');
Route::post('signup', [AuthController::class, 'signup'])->name('auth.signup.submit');
Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
Route::put('/tags/{id}', [TagController::class, 'update']);
Route::delete('/tags/{id}', [TagController::class, 'destroy']);

Route::get('/places/list', [PlaceController::class, 'index'])->middleware('auth');
Route::post('/places', [PlaceController::class, 'store'])->name('places.store');
Route::put('/places/{id}', [PlaceController::class, 'update']);
Route::delete('/places/{id}', [PlaceController::class, 'destroy']);

Route::get('/users/list', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);


//groups


    // Ruta para mostrar la vista visual
    // Route::get('/groups/index', function () {
    //      return view('groups.index');
    // })->name('groups.index');

    // // Endpoint que retorna JSON con los datos de los grupos
    // Route::get('/groups', [GroupController::class, 'listarGrupos'])->name('groups.listar');

    // // Endpoint para crear un grupo (devuelve JSON)
    // Route::post('/groups/crear', [GroupController::class, 'crearGrupo'])->name('groups.crear');

    // // Otros endpoints (unirse, expulsar, iniciar, etc.)
    // Route::post('/groups/unirse', [GroupController::class, 'unirseGrupo'])->name('groups.unirse');
    // Route::delete('/groups/{groupsId}/miembros/{miembroId}', [GroupController::class, 'expulsarMiembro'])->name('groups.expelir');
    // Route::post('/groups/{groupsId}/iniciar', [GroupController::class, 'iniciarJuego'])->name('groups.iniciar');
    // // Sólo grupos disponibles para unirse
    // Route::get('/groups/disponibles', [GroupController::class, 'listarGruposDisponibles'])
    // ->name('groups.disponibles');

    // // Mis grupos (todos en los que participo)
    // Route::get('/groups/mis-grupos', [GroupController::class, 'listarMisGrupos'])
    // ->name('groups.mis');


// Ejemplo de rutas para grupos:




// Vista principal con la lista de grupos (pero sin datos en Blade).
Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');

// Para cargar la lista (JSON).
Route::get('/groups/list', [GroupController::class, 'list'])->name('groups.list');

// Para buscar grupos por nombre o código (JSON).
Route::get('/groups/search', [GroupController::class, 'search'])->name('groups.search');

// Para traer detalles concretos de un grupo (JSON).
Route::get('/groups/{group}', [GroupController::class, 'show'])->name('groups.show');

// Crear grupo.
Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');

// Unirse a un grupo.
Route::post('/groups/{group}/join', [GroupController::class, 'unirseGrupo'])->name('groups.join');

// Iniciar juego.
Route::post('/groups/{group}/start', [GroupController::class, 'iniciarJuego'])->name('groups.start');

// Expulsar a un miembro (sólo creador).
Route::delete('/groups/{group}/kick/{user}', [GroupController::class, 'expulsarMiembro'])->name('groups.kick');

// Salir de un grupo (miembro normal).
Route::delete('/groups/{group}/leave', [GroupController::class, 'salirDelGrupo'])->name('groups.leave');

// Eliminar el grupo (sólo creador).
Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');










Route::get('/tags/list', function () {
    $tags = Tag::all();
    return response()->json(['tags' => $tags]);
});
Route::get('/admin', function () {
    if (Auth::check() && Auth::user()->role_id == 2) {
        return view('admin.admin');
    }
    return redirect()->route('index');
})->name('admin');

Route::get('/', function () {
    return view('index');
})->name('index');

Route::get('/dashboard/mapa', function () {
    if (Auth::check() && Auth::user()->role_id == 1) {
        return view('dashboard.mapa');
    }
    return redirect()->route('index');
})->name('dashboard.mapa');

<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GymkhanaController;
use App\Http\Controllers\CheckpointController;

use App\Models\Tag;


Route::get('signin', [AuthController::class, 'showSigninForm'])->name('auth.signin');
Route::post('signin', [AuthController::class, 'signin'])->name('auth.signin.submit');
Route::get('signup', [AuthController::class, 'showSignupForm'])->name('auth.signup');
Route::post('signup', [AuthController::class, 'signup'])->name('auth.signup.submit');
Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::get('/tags', [TagController::class, 'index']);
Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
Route::put('/tags/{id}', [TagController::class, 'update']);
Route::delete('/tags/{id}', [TagController::class, 'destroy']);

Route::get('/places/list', [PlaceController::class, 'index']);
Route::get('/places', [PlaceController::class, 'index']);
Route::post('/places', [PlaceController::class, 'store'])->name('places.store');
Route::put('/places/{id}', [PlaceController::class, 'update']);
Route::delete('/places/{id}', [PlaceController::class, 'destroy']);
Route::get('/places/{id}', [PlaceController::class, 'show']);

Route::get('/users/list', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);


//groups

// Vista principal
Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
// Listar el grupo actual del usuario
Route::get('/groups/list', [GroupController::class, 'list'])->name('groups.list');
// Listar grupos disponibles (a los que el usuario no está unido)
Route::get('/groups/available', [GroupController::class, 'available'])->name('groups.available');
// Búsqueda (puedes dejarlo como ya lo tienes)
Route::get('/groups/search', [GroupController::class, 'search'])->name('groups.search');
Route::get('/grupo/estado', [GroupController::class, 'estadoJuego'])->name('grupo.estado');

Route::get('/groups/gymkhanas', [GroupController::class, 'listarGymkhanas'])->name('groups.gymkhanas');
// Detalle de un grupo
Route::get('/groups/{group}', [GroupController::class, 'show'])->name('groups.show');
// Resto de rutas (crear, unirse, salir, expulsar, etc.)
Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
Route::post('/groups/{group}/join', [GroupController::class, 'unirseGrupo'])->name('groups.join');
Route::post('/groups/{group}/start', [GroupController::class, 'iniciarJuego'])->name('groups.start');
Route::delete('/groups/{group}/kick/{user}', [GroupController::class, 'expulsarMiembro'])->name('groups.kick');
Route::delete('/groups/{group}/leave', [GroupController::class, 'salirDelGrupo'])->name('groups.leave');
Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');





// Mostrar todas las Gymkhanas
Route::get('/gymkhanas', [GymkhanaController::class, 'index'])->name('gymkhanas.index');

// Crear una nueva Gymkhana
Route::post('/gymkhanas', [GymkhanaController::class, 'store'])->name('gymkhanas.store');

// Ver una Gymkhana específica
Route::get('/gymkhanas/{id}', [GymkhanaController::class, 'show'])->name('gymkhanas.show');

// Actualizar una Gymkhana
Route::put('/gymkhanas/{id}', [GymkhanaController::class, 'update'])->name('gymkhanas.update');

// Eliminar una Gymkhana
Route::delete('/gymkhanas/{id}', [GymkhanaController::class, 'destroy'])->name('gymkhanas.destroy');




// Mostrar todos los Checkpoints
Route::get('/checkpoints', [CheckpointController::class, 'index'])->name('checkpoints.index');

// Crear un Checkpoint
Route::post('/checkpoints', [CheckpointController::class, 'store'])->name('checkpoints.store');

// Ver un Checkpoint específico
Route::get('/checkpoints/{id}', [CheckpointController::class, 'show'])->name('checkpoints.show');

// Actualizar un Checkpoint
Route::put('/checkpoints/{id}', [CheckpointController::class, 'update'])->name('checkpoints.update');

// Eliminar un Checkpoint
Route::delete('/checkpoints/{id}', [CheckpointController::class, 'destroy'])->name('checkpoints.destroy');

// Obtener todas las Gymkhanas para los Checkpoints
Route::get('/checkpoints/gymkhanas', [CheckpointController::class, 'getGymkhanas'])->name('checkpoints.gymkhanas');

// Obtener todos los lugares para los Checkpoints
Route::get('/checkpoints/places', [CheckpointController::class, 'getPlaces'])->name('checkpoints.places');



Route::get('/tags/list', function () {
    $tags = Tag::all();
    return response()->json(['tags' => $tags]);
});
Route::get('/admin', function () {
    if (Auth::check() && Auth::user()->role_id == 2) {
        return view('admin.admin');
    }
    return redirect()->route('signin');
})->name('admin');

Route::get('/', function () {
    return view('auth.signin');
})->name('signin');

Route::get('/dashboard/mapa', function () {
    if (Auth::check() && Auth::user()->role_id == 1) {
        return view('dashboard.mapa');
    }
    return redirect()->route('signin');
})->name('dashboard.mapa');

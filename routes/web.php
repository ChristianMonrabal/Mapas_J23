<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\UserController;
use App\Models\Tag;
use App\Http\Controllers\FavoriteController;


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

Route::post('/favorites/{placeId}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
Route::get('/favorites/{placeId}/check', [FavoriteController::class, 'check'])->name('favorites.check');

Route::get('/dashboard/gimcana', function () {
    if (Auth::check() && Auth::user()->role_id == 1) {
        return view('dashboard.gimcana');
    }
    return redirect()->route('index');
})->name('dashboard.gimcana');

// Route::get('/api/unirse-grupo/{codigoGrupo}', [MapController::class, 'unirseAGrupo']);
Route::get('/buscarGymkhana/{gymkhanaId}/{grupoId}', [MapController::class, 'obtenerDatosGymkhana']);
Route::get('/verificarUsuariosCompletados/{grupoId}', [MapController::class, 'verificarUsuariosCompletados']);
Route::post('/actualizarProgresoUsuario/{usuarioId}', [MapController::class, 'actualizarProgresoUsuario']);
Route::post('/actualizarCheckpointCompletado/{checkpointId}', [MapController::class, 'actualizarCheckpointCompletado']);
Route::get('/verificarGymkhanaCompletada/{gymkhanaId}', [MapController::class, 'verificarGymkhanaCompletada']);
Route::post('/actualizarProgresoGimcana/{grupoId}', [MapController::class, 'actualizarProgresoGimcana']);
Route::post('/reiniciarProgresoUsuarios/{grupoId}', [MapController::class, 'reiniciarProgresoUsuarios']);

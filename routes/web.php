<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapController;
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
Route::get('/places/{id}', [PlaceController::class, 'show']);
Route::get('/places/search/{query}', [PlaceController::class, 'search'])->middleware('auth');

Route::get('/users/list', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

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

Route::get('/dashboard/gimcana', function () {
    if (Auth::check() && Auth::user()->role_id == 1) {
        return view('dashboard.gimcana');
    }
    return redirect()->route('index');
})->name('dashboard.gimcana');

Route::get('/api/unirse-grupo/{codigoGrupo}', [MapController::class, 'unirseAGrupo']);
Route::get('/api/gymkhana-datos/{gymkhanaId}/{grupoId}', [MapController::class, 'obtenerDatosGymkhana']);
Route::post('/api/actualizar-progreso/{grupoId}', [MapController::class, 'actualizarProgreso']);
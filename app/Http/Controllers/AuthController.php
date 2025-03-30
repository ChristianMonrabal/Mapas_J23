<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showSigninForm()
    {
        return view('auth.signin'); 
    }

    public function signin(Request $request)
    {
        if (empty($request->email) || empty($request->password)) {
            return back()->withErrors(['error' => 'Todos los campos son obligatorios.'])->withInput();
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            if ($user->role_id == 2) {
                return redirect()->route('admin');
            } elseif ($user->role_id == 1) {
                return redirect()->route('dashboard.mapa');
            }
            
            return redirect()->route('auth.signin');
        }

        return back()->withErrors(['error' => 'Credenciales incorrectas.'])->withInput();
    }

    public function showSignupForm()
    {
        return view('auth.signup');
    }

    public function signup(Request $request)
    {
        $errors = [];
    
        if (empty($request->name)) {
            $errors['name'] = 'El nombre de usuario es obligatorio.';
        }
    
        if (empty($request->email)) {
            $errors['email'] = 'El correo electrónico es obligatorio.';
        } elseif (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El correo electrónico no es válido.';
        }
    
        if (empty($request->password)) {
            $errors['password'] = 'La contraseña es obligatoria.';
        } 
    
        if (empty($request->password_confirmation)) {
            $errors['password_confirmation'] = 'La confirmación de contraseña es obligatoria.';
        } 
    
        if (!empty($request->password) && !empty($request->password_confirmation) && $request->password !== $request->password_confirmation) {
            $errors['password_confirmation'] = 'Las contraseñas no coinciden.';
        } elseif (!empty($request->password) && strlen($request->password) < 8) {
            $errors['password'] = 'La contraseña debe tener al menos 8 caracteres.';
        }
    
        if (User::where('name', $request->name)->exists()) {
            $errors['name'] = 'El nombre de usuario ya existe.';
        }
    
        if (User::where('email', $request->email)->exists()) {
            $errors['email'] = 'El correo electrónico ya está en uso.';
        }
    
        if (!empty($errors)) {
            return redirect()->route('auth.signup')
                ->withInput()
                ->withErrors($errors);
        }
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 1,
        ]);
    
        Auth::login($user);
    
        if ($user->role_id == 2) {
            return redirect()->route('admin');
        } else {
            return redirect()->route('dashboard.mapa');
        }
    }
    
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        return redirect()->route('auth.signin');
    }
    
}


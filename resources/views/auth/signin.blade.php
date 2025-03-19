<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="shortcut icon" href="{{ asset('img/icon.png') }}" type="image/x-icon">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
</head>
<body>
    <div class="form-container">
        <img src="{{ asset('img/icon.png') }}">
        <h1>OnlyMaps</h1>
        <form action="{{ route('auth.signin.submit') }}" method="POST">
            @csrf
            <div class="input-group">
                <label for="email"></label>
                <input type="email" id="email" name="email" placeholder="Introduce tu correo electrónico" value="{{ old('email') }}">
            </div>
            <div class="input-group">
                <label for="password"></label>
                <input type="password" id="password" name="password" placeholder="Introduce tu contraseña">
                @error('error')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <button type="submit">Iniciar sesión</button>
            </div>
        </form>
        <p>No tienes cuenta? Regístrate <a href="{{ route('auth.signup') }}">aquí</a></p>
    </div>

    <script src="{{ asset('js/login.js') }}"></script>
</body>
</html>

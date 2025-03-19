<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrate</title>
    <link rel="shortcut icon" href="{{ asset('img/icon.png') }}" type="image/x-icon">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
</head>
<body>
    <div class="form-container">
        <img src="{{ asset('img/icon.png') }}">
        <h1>OnlyMaps</h1>
        <form action="{{ route('auth.signup.submit') }}" method="POST">
            @csrf
            <div class="input-group">
                <input type="text" id="name" name="name" placeholder="Introduce tu nombre de usuario" value="{{ old('name') }}">
                @error('name')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div class="input-group">
                <input type="email" id="email" name="email" placeholder="Introduce tu email" value="{{ old('email') }}">
                @error('email')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div class="input-group">
                <input type="password" id="password" name="password" placeholder="Introduce tu contraseña">
                @error('password')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div class="input-group">
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repite tu contraseña">
                @error('password_confirmation')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <button type="submit">Registrate</button>
            </div>

            @if($errors->has('error'))
                <div class="error">
                    <p>{{ $errors->first('error') }}</p>
                </div>
            @endif
        </form>
        <p>Ya tienes cuenta? Inicia sesión <a href="{{ route('auth.signin') }}">aquí</a></p>
    </div>

    <script src="{{ asset('js/login.js') }}"></script>
</body>
</html>
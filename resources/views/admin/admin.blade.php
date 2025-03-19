<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Panel</title>
</head>
<body>

    @if(Auth::check() && Auth::user()->role_id == 2)
        <p>Usuario autenticado: {{ Auth::user()->name }}</p>
        <form action="{{ route('auth.logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit">Cerrar sesión</button>
        </form>
    @else
        <script>
            window.location.href = "{{ route('index') }}";
        </script>
    @endif

</body>
</html>

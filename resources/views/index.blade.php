<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <p>Usuario autenticado:  
        <?php echo e(Auth::user()->name ?? 'Invitado'); ?>
    </p>
    <form action="{{ route('auth.logout') }}" method="POST" style="display: inline;">
        @csrf
        <button type="submit">Cerrar sesión</button>
    </form>
    

</body>
</html>

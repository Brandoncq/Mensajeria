<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle Usuario</title>
    <link rel="icon" type="image/png" href="/img/logo.png">
    @vite('resources/css/app.css')
</head>
<body class="font-sans bg-gray-50">

<div class="w-full mx-auto">
    <!-- Header -->
    <div class="w-full mx-auto p-4 bg-[#c1392b] flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-extralight text-gray-100 mb-2">Panel de Administrador</h1>
            <h2 class="text-2xl font-semibold text-gray-100">Detalle Usuario #{{ $usuario->id_usuario }}</h2>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-gray-100 text-[#c1392b] font-bold px-4 py-2 rounded-lg shadow hover:bg-gray-200 transition">
                <i class="fa fa-sign-out-alt"></i> Cerrar sesión
            </button>
        </form>
    </div>

    <!-- Detalles -->
    <div class="max-w-3xl mx-auto mt-8 bg-white shadow-md rounded-lg p-6 space-y-4">
        <p><strong>Nombre:</strong> {{ $usuario->nombre }}</p>
        <p><strong>Email:</strong> {{ $usuario->email }}</p>
        <p><strong>Teléfono:</strong> {{ $usuario->telefono }}</p>
        <p><strong>DNI:</strong> {{ $usuario->dni }}</p>
        <p><strong>Username:</strong> {{ $usuario->username }}</p>
        <p><strong>Rol:</strong> {{ ucfirst($usuario->rol) }}</p>
        <p><strong>Activo:</strong> {{ $usuario->activo ? 'Sí' : 'No' }}</p>

        <div class="flex justify-end space-x-4 mt-4">
            <a href="{{ url('dashboardAdministrador') }}"
                   class="px-4 py-2 text-gray-600 border border-gray-400 rounded hover:bg-gray-100 transition">
                   Volver
            </a>
            <a href="{{ route('admin.usuarios.edit', $usuario->id_usuario) }}"
                class="px-4 py-2 bg-[#c1392b] text-white rounded hover:bg-red-700 transition">Editar</a>
        </div>
    </div>
</div>

</body>
</html>

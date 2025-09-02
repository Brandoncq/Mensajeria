<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <link rel="icon" type="image/png" href="/img/logo.png">
    @vite('resources/css/app.css')
</head>
<body class="font-sans bg-gray-50">

<div class="w-full mx-auto">
    <!-- Header -->
    <div class="w-full mx-auto p-4 bg-[#c1392b] flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-extralight text-gray-100 mb-2">Panel de Administrador</h1>
            <h2 class="text-2xl font-semibold text-gray-100">Crear Nuevo Usuario</h2>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-gray-100 text-[#c1392b] font-bold px-4 py-2 rounded-lg shadow hover:bg-gray-200 transition">
                <i class="fa fa-sign-out-alt"></i> Cerrar sesión
            </button>
        </form>
    </div>

    <!-- Formulario -->
    <div class="max-w-3xl mx-auto mt-8 bg-white shadow-md rounded-lg p-6">
        <!-- Mensajes de éxito -->
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Mensajes de error -->
        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Errores de validación -->
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.usuarios.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Nombre -->
            <div>
                <label for="nombre" class="block text-lg font-medium text-gray-700">Nombre *</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                    class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                @error('nombre')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-lg font-medium text-gray-700">Email *</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Teléfono -->
            <div>
                <label for="telefono" class="block text-lg font-medium text-gray-700">Teléfono * (será la contraseña)</label>
                  <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}" required 
                    maxlength="9" pattern="[0-9]{9}" inputmode="numeric"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                @error('telefono')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- DNI -->
            <div>
                <label for="dni" class="block text-lg font-medium text-gray-700">DNI *</label>
                <input type="text" name="dni" id="dni" value="{{ old('dni') }}" required 
                    maxlength="8" pattern="[0-9]{8}" inputmode="numeric"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                @error('dni')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Rol -->
            <div>
                <label for="rol" class="block text-lg font-medium text-gray-700">Rol *</label>
                <select name="rol" id="rol" required
                    class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                    <option value="">Seleccione un rol</option>
                    <option value="monitor" {{ old('rol') == 'monitor' ? 'selected' : '' }}>Monitor</option>
                    <option value="administrador" {{ old('rol') == 'administrador' ? 'selected' : '' }}>Administrador</option>
                    <option value="asociado" {{ old('rol') == 'asociado' ? 'selected' : '' }}>Asociado</option>
                </select>
                @error('rol')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Activo -->
            <div>
                <label for="activo" class="block text-lg font-medium text-gray-700">Estado *</label>
                <select name="activo" id="activo" required
                    class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                    <option value="1" {{ old('activo', '1') == '1' ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ old('activo') == '0' ? 'selected' : '' }}>Inactivo</option>
                </select>
                @error('activo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4">
                <a href="{{ url('dashboardAdministrador') }}"
                    class="px-4 py-2 text-gray-600 border border-gray-400 rounded hover:bg-gray-100 transition">Cancelar</a>
                <button type="submit"
                    class="px-4 py-2 bg-[#c1392b] text-white rounded hover:bg-red-700 transition">Crear Usuario</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
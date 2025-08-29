@vite('resources/css/app.css')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md bg-white shadow-lg rounded-2xl p-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Iniciar Sesión</h2>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                <input 
                    id="email" 
                    type="email" 
                    name="email" 
                    placeholder="ejemplo@correo.com"
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3"
                    required 
                    autofocus
                >
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                <input 
                    id="password" 
                    type="password" 
                    name="password" 
                    placeholder="********"
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3"
                    required
                >
            </div>

            <!-- Botón -->
            <div>
                <button 
                    type="submit" 
                    class="w-full bg-indigo-600 text-white py-3 rounded-xl shadow-md hover:bg-indigo-700 transition"
                >
                    Ingresar
                </button>
            </div>
        </form>

        <!-- Errores -->
        @if ($errors->any())
            <div class="mt-4 text-red-600 text-sm text-center">
                {{ $errors->first('email') }}
            </div>
        @endif
    </div>
</div>

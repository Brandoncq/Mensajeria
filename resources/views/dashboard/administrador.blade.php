<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel="icon" type="image/png" href="/img/logo.png">
    @vite('resources/css/app.css')
</head>
<body class="font-sans">

    <div class="w-full mx-auto">
        <div class="w-full mx-auto p-4 bg-[#c1392b] flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-extralight text-gray-100 mb-2">Panel de Administrador</h1>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-gray-100 text-[#c1392b] font-bold px-4 py-2 rounded-lg shadow hover:bg-gray-200 transition">
                    <i class="fa fa-sign-out-alt"></i> Cerrar sesión
                </button>
            </form>
        </div>
        
        <div class="flex flex-col lg:flex-row gap-6 mt-2">
            <!-- Columna 1: Listado de Reportes -->
            <div class="w-full lg:w-1/2 px-6 py-3">
                <h2 class="text-2xl font-light text-gray-700 mb-4">Listado de Reportes</h2>
                <div class="overflow-x-auto bg-white flex justify-center">
                    <table class="w-full border border-gray-400">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-2 py-2 text-left border-b font-normal">ID</th>
                                <th class="px-2 py-2 text-left border-b font-normal">Categoría</th>
                                <th class="px-2 py-2 text-left border-b font-normal">Fecha Evento</th>
                                <th class="px-2 py-2 text-left border-b font-normal">Estado</th>
                                <th class="px-2 py-2 text-left border-b font-normal">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600">
                            @foreach($reportes as $r)
                            <tr class="hover:bg-gray-50">
                                <td class="px-2 py-2 border-b">{{ $r->id_reporte }}</td>
                                <td class="px-2 py-2 border-b">{{ $r->categoria->nombre ?? 'Sin categoría' }}</td>
                                <td class="px-4 py-2 border-b">{{ $r->fecha_evento }}</td>
                                <td class="px-2 py-2 border-b">
                                    @if($r->estado == 'aprobado')
                                        <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded">Aprobado</span>
                                    @elseif($r->estado == 'revisado')
                                        <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-700 rounded">Revisado</span>
                                    @elseif($r->estado == 'rechazado')
                                        <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-700 rounded">Rechazado</span>
                                    @elseif($r->estado == 'pendiente')
                                        <span class="px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-700 rounded">Pendiente</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-700 rounded">Borrador</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2 border-b text-center space-x-1 space-y-1">
                                    <a href="{{ route('admin.reportes.show',$r->id_reporte) }}"
                                    class="inline-block px-3 py-1 text-sm text-blue-600 border border-blue-600 rounded hover:bg-blue-600 hover:text-white transition">
                                    Ver
                                    </a>
                                    <a href="{{ route('admin.reportes.edit',$r->id_reporte) }}"
                                    class="inline-block px-3 py-1 text-sm text-yellow-600 border border-yellow-600 rounded hover:bg-yellow-600 hover:text-white transition">
                                    Editar
                                    </a>
                                    <a href="{{ route('admin.reportes.imprimir',$r->id_reporte) }}" target="_blank"
                                    class="inline-block px-3 py-1 text-sm text-purple-600 border border-purple-600 rounded hover:bg-purple-600 hover:text-white transition">
                                    Imprimir
                                    </a>
                                    <!-- Botón que abre el modal -->
                                    <button type="button" 
                                        onclick="openModal({{ $r->id_reporte }})"
                                        class="px-3 py-1 text-sm text-red-600 border border-red-600 rounded hover:bg-red-600 hover:text-white transition">
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Columna 2: Listado de Usuarios -->
            <div class="w-full lg:w-1/2 px-6 py-3">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-2xl font-light text-gray-700">Listado de Usuarios</h2>
                    <a href="{{ route('admin.usuarios.create') }}" 
                       class="px-4 py-1.5 bg-green-600 text-white rounded hover:bg-green-700 transition">
                        + Nuevo Usuario
                    </a>
                </div>
                <div class="overflow-x-auto bg-white">
                    <table class="w-full border border-gray-400">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-2 py-2 text-left border-b font-normal">Nombre</th>
                                <th class="px-2 py-2 text-left border-b font-normal">Email</th>
                                <th class="px-2 py-2 text-left border-b font-normal">Rol</th>
                                <th class="px-2 py-2 text-left border-b font-normal">Estado</th>
                                <th class="px-2 py-2 text-left border-b font-normal">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600">
                            @foreach($usuarios as $usuario)
                            <tr class="hover:bg-gray-50">
                                <td class="px-2 py-2 border-b">{{ $usuario->nombre }}</td>
                                <td class="px-2 py-2 border-b">{{ $usuario->email }}</td>
                                <td class="px-2 py-2 border-b">
                                    @if($usuario->rol == 'administrador')
                                        <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-700 rounded">Administrador</span>
                                    @elseif($usuario->rol == 'monitor')
                                        <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded">Monitor</span>
                                    @elseif($usuario->rol == 'asociado')
                                        <span class="px-2 py-1 text-xs font-semibold bg-purple-100 text-purple-700 rounded">Asociado</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-700 rounded">{{ $usuario->rol }}</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2 border-b">
                                    @if($usuario->activo)
                                        <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded">Activo</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-700 rounded">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2 border-b text-center space-x-1 space-y-1">
                                    <a href="{{ route('admin.usuarios.show', $usuario->id_usuario) }}"
                                       class="inline-block px-3 py-1 text-sm text-blue-600 border border-blue-600 rounded hover:bg-blue-600 hover:text-white transition">
                                        Ver
                                    </a>
                                    <a href="{{ route('admin.usuarios.edit', $usuario->id_usuario) }}"
                                       class="inline-block px-3 py-1 text-sm text-yellow-600 border border-yellow-600 rounded hover:bg-yellow-600 hover:text-white transition">
                                        Editar
                                    </a>
                                    <!-- Botón que abre el modal de eliminación de usuario -->
                                    <button type="button" 
                                            onclick="openUserModal({{ $usuario->id_usuario }})"
                                            class="px-3 py-1 text-sm text-red-600 border border-red-600 rounded hover:bg-red-600 hover:text-white transition">
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para reportes -->
    <div id="confirmModal" class="hidden fixed inset-0 items-center justify-center bg-black/60 z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Confirmar Eliminación</h3>
            <p class="text-gray-600 mb-6">¿Estás seguro de que deseas eliminar este reporte? Esta acción no se puede deshacer.</p>
            
            <div class="flex justify-end space-x-4">
                <button onclick="closeModal()" 
                    class="px-4 py-2 text-gray-600 border border-gray-400 rounded hover:bg-gray-100 transition">
                    Cancelar
                </button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" 
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para usuarios -->
    <div id="confirmUserModal" class="hidden fixed inset-0 items-center justify-center bg-black/60 z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Confirmar Eliminación</h3>
            <p class="text-gray-600 mb-6">¿Estás seguro de que deseas eliminar este usuario? Se eliminarán todos sus datos relacionados.</p>
            
            <div class="flex justify-end space-x-4">
                <button onclick="closeUserModal()" 
                    class="px-4 py-2 text-gray-600 border border-gray-400 rounded hover:bg-gray-100 transition">
                    Cancelar
                </button>
                <form id="deleteUserForm" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" 
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Funciones para modales de reportes
        function openModal(reporteId) {
            const modal = document.getElementById('confirmModal');
            const form = document.getElementById('deleteForm');
            form.action = `/admin/reportes/${reporteId}`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            document.getElementById('confirmModal').classList.add('hidden');
            document.getElementById('confirmModal').classList.remove('flex');
        }

        // Funciones para modales de usuarios
        function openUserModal(usuarioId) {
            const modal = document.getElementById('confirmUserModal');
            const form = document.getElementById('deleteUserForm');
            form.action = `/admin/usuarios/${usuarioId}`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeUserModal() {
            document.getElementById('confirmUserModal').classList.add('hidden');
            document.getElementById('confirmUserModal').classList.remove('flex');
        }

        // Cerrar modales al hacer clic fuera
        document.addEventListener('click', function(event) {
            const reportModal = document.getElementById('confirmModal');
            const userModal = document.getElementById('confirmUserModal');
            
            if (event.target === reportModal) {
                closeModal();
            }
            if (event.target === userModal) {
                closeUserModal();
            }
        });
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    @vite('resources/css/app.css')
</head>
<body class="font-sans">

    <div class="w-full mx-auto">
        <div class="w-full mx-auto p-4 bg-[#c1392b]">
            <h1 class="text-4xl font-extralight text-gray-100 mb-4">Panel de Administrador</h1>
            <h2 class="text-2xl font-semibold text-gray-100 mb-2">Listado de Reportes</h2>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg p-6 flex justify-center">
            <table class="w-full lg:w-3/4 border border-gray-400 rounded-xl">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left border-b">ID</th>
                        <th class="px-4 py-2 text-left border-b">Categoría</th>
                        <th class="px-4 py-2 text-left border-b">Estado</th>
                        <th class="px-4 py-2 text-center border-b">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600">
                    @foreach($reportes as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border-b">{{ $r->id_reporte }}</td>
                        <td class="px-4 py-2 border-b">{{ $r->categoria->nombre ?? 'Sin categoría' }}</td>
                        <td class="px-4 py-2 border-b">
                            @if($r->estado == 'aprobado')
                                <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded">Aprobado</span>
                            @elseif($r->estado == 'rechazado')
                                <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-700 rounded">Rechazado</span>
                            @elseif($r->estado == 'pendiente')
                                <span class="px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-700 rounded">Pendiente</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-700 rounded">Borrador</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 border-b text-center space-x-2">
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

    <!-- Modal de confirmación -->
    <div id="confirmModal" class="hidden fixed inset-0 flex items-center justify-center bg-black/60 z-50">
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

    <script>
        function openModal(reporteId) {
            const modal = document.getElementById('confirmModal');
            const form = document.getElementById('deleteForm');
            form.action = `/dashboard/admin/reportes/${reporteId}`;
            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('confirmModal').classList.add('hidden');
        }
    </script>

</body>
</html>

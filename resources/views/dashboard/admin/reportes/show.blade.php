<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Reporte</title>
    <link rel="icon" type="image/png" href="/img/logo.png">
    @vite('resources/css/app.css')
</head>
<body class="font-sans bg-gray-50">

    <div class="w-full mx-auto">
        <!-- Header -->
        <div class="w-full mx-auto p-4 bg-[#c1392b] flex items-center justify-between">
            <!-- Panel e info -->
            <div>
                <h1 class="text-4xl font-extralight text-gray-100 mb-2">Panel de Administrador</h1>
                <h2 class="text-2xl font-semibold text-gray-100">Detalle del Reporte #{{ $reporte->id_reporte }}</h2>
            </div>

            <!-- Botón logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-gray-100 text-[#c1392b] font-bold px-4 py-2 rounded-lg shadow hover:bg-gray-200 transition">
                    <i class="fa fa-sign-out-alt"></i> Cerrar sesión
                </button>
            </form>
        </div>

        <!-- Contenido -->
        <div class="max-w-3xl mx-auto mt-8 bg-white shadow-md rounded-lg p-6 space-y-4">
            <p><span class="font-semibold text-gray-700">Categoría:</span> {{ $reporte->categoria->nombre ?? 'Sin categoría' }}</p>
            <p><span class="font-semibold text-gray-700">Lugar:</span> {{ $reporte->lugar }}</p>
            <p><span class="font-semibold text-gray-700">Descripción:</span> {{ $reporte->descripcion }}</p>
            <p><span class="font-semibold text-gray-700">Estado:</span> 
                @if($reporte->estado == 'aprobado')
                    <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded">Aprobado</span>
                @elseif($reporte->estado == 'revisado')
                    <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-700 rounded">Revisado</span>
                @elseif($reporte->estado == 'rechazado')
                    <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-700 rounded">Rechazado</span>
                @elseif($reporte->estado == 'pendiente')
                    <span class="px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-700 rounded">Pendiente</span>
                @else
                    <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-700 rounded">Borrador</span>
                @endif
            </p>
            <p><span class="font-semibold text-gray-700">Fecha Evento:</span> {{ $reporte->fecha_evento }}</p>
            <p><span class="font-semibold text-gray-700">Fecha Aprobación:</span> {{ $reporte->fecha_aprobacion ?? 'No aprobada aún' }}</p>

            <p><span class="font-semibold text-gray-700">Número de Personas:</span> {{ $reporte->numero_personas ?? 'Sin dato' }}</p>
            <p><span class="font-semibold text-gray-700">Actores Identificados:</span> {{ $reporte->actores_identificados ?? 'No especificado' }}</p>
            <p><span class="font-semibold text-gray-700">Presencia de Autoridades:</span> {{ $reporte->presencia_autoridades ?? 'No especificado' }}</p>
            <p><span class="font-semibold text-gray-700">Intervención Serenazgo:</span> {{ $reporte->intervencion_serenazgo ?? 'No especificado' }}</p>
            <p><span class="font-semibold text-gray-700">Tema Tratado:</span> {{ $reporte->tema_tratado ?? 'No especificado' }}</p>
            <p><span class="font-semibold text-gray-700">Acuerdos y Compromisos:</span> {{ $reporte->acuerdos_compromisos ?? 'No especificado' }}</p>
            <p><span class="font-semibold text-gray-700">Recomendación Preliminar:</span> {{ $reporte->recomendacion_preliminar ?? 'No especificado' }}</p>

            <h3 class="text-lg font-semibold text-gray-700 border-b border-gray-400 pb-2 mb-4">Archivos Adjuntos</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @forelse($reporte->archivos as $archivo)
                    @if($archivo->tipo === 'imagen')
                        <div class="border border-gray-400 rounded-lg overflow-hidden shadow-sm bg-gray-50 hover:shadow-md transition">
                            <img src="{{ asset('storage/archivos/' . $archivo->nombre_archivo) }}" 
                                alt="Imagen" class="w-full h-40 object-cover">
                            <div class="p-2 text-center text-sm text-gray-600">
                                Imagen adjunta
                            </div>
                        </div>
                    @elseif($archivo->tipo === 'enlace')
                        <div class="flex items-center justify-between border border-gray-400 rounded-lg p-3 shadow-sm bg-white hover:bg-gray-50 transition">
                            <span class="text-sm font-medium text-gray-700 truncate">{{ $archivo->url }}</span>
                            <a href="{{ $archivo->url }}" target="_blank" 
                            class="ml-2 px-3 py-1 text-xs font-semibold bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                            Ver
                            </a>
                        </div>
                    @endif
                @empty
                    <p class="text-gray-500 italic">No hay archivos adjuntos para este reporte.</p>
                @endforelse
            </div>

            <!-- Acciones -->
            <div class="flex justify-end space-x-3 mt-6">
                <form action="{{ route('admin.reportes.aprobar',$reporte->id_reporte) }}" method="POST" class="inline">
                    @csrf
                    <button type="button"
                        onclick="toggleModal('modal-aprobar', true)"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                        Revisado
                    </button>
                </form>

                <form action="{{ route('admin.reportes.rechazar',$reporte->id_reporte) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                        Rechazar
                    </button>
                </form>

                <a href="{{ route('admin.reportes.imprimir',$reporte->id_reporte) }}" target="_blank"
                   class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    Imprimir
                </a>

                <a href="{{ url('dashboardAdministrador') }}"
                   class="px-4 py-2 text-gray-600 border border-gray-400 rounded hover:bg-gray-100 transition">
                   Volver
                </a>
            </div>
            
        </div>
    </div>
    <!-- Modal -->
<div id="modal-aprobar" class="hidden fixed inset-0 items-center justify-center bg-black/60">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 class="text-xl font-semibold mb-4">Seleccionar área de interés</h2>
        <form action="{{ route('admin.reportes.aprobar', $reporte->id_reporte) }}" method="POST">
            @csrf
            <label for="area_interes" class="block text-sm font-medium text-gray-700">Área de Interés</label>
            <select name="id_area_interes" id="area_interes" required
                class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                @foreach($areas as $area)
                    <option value="{{ $area->id_area_interes }}">{{ $area->nombre }}</option>
                @endforeach
            </select>

            <div class="flex justify-end mt-4 space-x-2">
                <button type="button" onclick="toggleModal('modal-aprobar', false)"
                    class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-100">Cancelar</button>
                <button type="submit"
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Enviar</button>
            </div>
        </form>
    </div>
</div>
<script>
    function toggleModal(modalId, show = true) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        if (show) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        } else {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
</script>
</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Reporte</title>
    <link rel="icon" type="image/png" href="/img/logo.png">
    @vite('resources/css/app.css')
</head>
<body class="font-sans bg-gray-50">

    <div class="w-full mx-auto">
        <!-- Header -->
        <div class="w-full mx-auto p-4 bg-[#c1392b] flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-extralight text-gray-100 mb-2">Panel de Administrador</h1>
                <h2 class="text-2xl font-semibold text-gray-100">Editar Reporte #{{ $reporte->id_reporte }}</h2>
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
            <form action="{{ route('admin.reportes.update',$reporte->id_reporte) }}" method="POST" class="space-y-6">
                @csrf @method('PUT')

                <!-- Lugar -->
                <div>
                    <label for="lugar" class="block text-lg font-medium text-gray-700">Lugar</label>
                    <input type="text" name="lugar" id="lugar" value="{{ $reporte->lugar }}" required
                        class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                </div>

                <!-- Descripción -->
                <div>
                    <label for="descripcion" class="block text-lg font-medium text-gray-700">Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="4" required
                        class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">{{ $reporte->descripcion }}</textarea>
                </div>
                <h3 class="text-lg font-semibold text-gray-700 border-b border-gray-400 pb-2 mb-4">Archivos Adjuntos</h3>

               
                <!-- Estado -->
                <div>
                    <label for="estado" class="block text-lg font-medium text-gray-700">Estado</label>
                    <select name="estado" id="estado"
                        class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                        <option value="borrador" @if($reporte->estado=='borrador') selected @endif>Borrador</option>
                        <option value="pendiente" @if($reporte->estado=='pendiente') selected @endif>Pendiente</option>
                        <option value="aprobado" @if($reporte->estado=='aprobado') selected @endif>Aprobado</option>
                        <option value="rechazado" @if($reporte->estado=='rechazado') selected @endif>Rechazado</option>
                        <option value="revisado" @if($reporte->estado=='revisado') selected @endif>Revisado</option>
                    </select>
                </div>
                @if(!empty($reporte->numero_personas))
                    <div>
                        <label for="numero_personas" class="block text-lg font-medium text-gray-700">Número de Personas</label>
                        <input type="number" name="numero_personas" id="numero_personas" min="0" oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                            value="{{ old('numero_personas', $reporte->numero_personas) }}"
                            class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                    </div>
                @endif

                <!-- Actores Identificados -->
                @if(!empty($reporte->actores_identificados))
                    <div>
                        <label for="actores_identificados" class="block text-lg font-medium text-gray-700">Actores Identificados</label>
                        <textarea name="actores_identificados" id="actores_identificados" rows="3"
                            class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">{{ $reporte->actores_identificados }}</textarea>
                    </div>
                @endif

                <!-- Presencia Autoridades -->
                @if(!empty($reporte->presencia_autoridades))
                    <div>
                        <label for="presencia_autoridades" class="block text-lg font-medium text-gray-700">Presencia de Autoridades</label>
                        <textarea name="presencia_autoridades" id="presencia_autoridades" rows="3"
                            class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">{{ $reporte->presencia_autoridades }}</textarea>
                    </div>
                @endif

                <!-- Intervención Serenazgo -->
                @if(!empty($reporte->intervencion_serenazgo))
                    <div>
                        <label for="intervencion_serenazgo" class="block text-lg font-medium text-gray-700">Intervención de Serenazgo</label>
                        <textarea name="intervencion_serenazgo" id="intervencion_serenazgo" rows="3"
                            class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">{{ $reporte->intervencion_serenazgo }}</textarea>
                    </div>
                @endif

                <!-- Tema Tratado -->
                @if(!empty($reporte->tema_tratado))
                    <div>
                        <label for="tema_tratado" class="block text-lg font-medium text-gray-700">Tema Tratado</label>
                        <textarea name="tema_tratado" id="tema_tratado" rows="3"
                            class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">{{ $reporte->tema_tratado }}</textarea>
                    </div>
                @endif

                <!-- Acuerdos y Compromisos -->
                @if(!empty($reporte->acuerdos_compromisos))
                    <div>
                        <label for="acuerdos_compromisos" class="block text-lg font-medium text-gray-700">Acuerdos y Compromisos</label>
                        <textarea name="acuerdos_compromisos" id="acuerdos_compromisos" rows="3"
                            class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">{{ $reporte->acuerdos_compromisos }}</textarea>
                    </div>
                @endif

                <!-- Recomendación Preliminar -->
                @if(!empty($reporte->recomendacion_preliminar))
                    <div>
                        <label for="recomendacion_preliminar" class="block text-lg font-medium text-gray-700">Recomendación Preliminar</label>
                        <textarea name="recomendacion_preliminar" id="recomendacion_preliminar" rows="3"
                            class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">{{ $reporte->recomendacion_preliminar }}</textarea>
                    </div>
                @endif

                <!-- Latitud -->
                @if(!empty($reporte->latitud))
                    <div>
                        <label for="latitud" class="block text-lg font-medium text-gray-700">Latitud</label>
                        <input type="number" name="latitud" id="latitud" step="any"
                            value="{{ old('latitud', $reporte->latitud) }}"
                            class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                    </div>
                @endif

                <!-- Longitud -->
                @if(!empty($reporte->longitud))
                    <div>
                        <label for="longitud" class="block text-lg font-medium text-gray-700">Longitud</label>
                        <input type="number" name="longitud" id="longitud" step="any"
                            value="{{ old('longitud', $reporte->longitud) }}"
                            class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                    </div>
                @endif

                <!-- Fecha del Evento -->
                @if(!empty($reporte->fecha_evento))
                    <div>
                        <label for="fecha_evento" class="block text-lg font-medium text-gray-700">Fecha del Evento</label>
                        <input type="datetime-local" name="fecha_evento" id="fecha_evento"
                            value="{{ $reporte->fecha_evento }}"
                            class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                    </div>
                @endif

                <!-- Fecha Aprobación -->
                @if(!empty($reporte->fecha_aprobacion))
                    <div>
                        <label for="fecha_aprobacion" class="block text-lg font-medium text-gray-700">Fecha de Aprobación</label>
                        <input type="datetime-local" name="fecha_aprobacion" id="fecha_aprobacion"
                            value="{{ $reporte->fecha_aprobacion }}"
                            class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                    </div>
                @endif

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

                <!-- Botones -->
                <div class="flex justify-end space-x-4">
                   <a href="{{ url('dashboardAdministrador') }}"
                        class="px-4 py-2 text-gray-600 border border-gray-400 rounded hover:bg-gray-100 transition">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-[#c1392b] text-white rounded hover:bg-red-700 transition">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>

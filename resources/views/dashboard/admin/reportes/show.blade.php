<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Reporte</title>
    <link rel="icon" type="image/png" href="/img/logo.png">
    @vite('resources/css/app.css')
   <style>
        .spinner {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #c1392b;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            visibility: hidden;
            display: inline-block;
            margin-right: 8px;
            vertical-align: middle;
        }
        .spinner-large {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #c1392b;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            max-width: 400px;
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.3s, transform 0.3s;
        }
        .alert.show {
            opacity: 1;
            transform: translateY(0);
        }
        .user-list {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            padding: 0.5rem;
            margin-top: 0.5rem;
            background-color: #f9fafb;
        }
        .user-item {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            border-bottom: 1px solid #e5e7eb;
        }
        .user-item:last-child {
            border-bottom: none;
        }
        .user-info {
            flex: 1;
        }
        .user-name {
            font-weight: 500;
        }
        .user-dni {
            font-size: 0.875rem;
            color: #6b7280;
        }
        .user-telefono {
            font-size: 0.875rem;
            color: #6b7280;
        }
        .no-users {
            padding: 1rem;
            text-align: center;
            color: #6b7280;
            font-style: italic;
        }
    </style>
</head>
<body class="font-sans bg-gray-50">

    <!-- Alertas flotantes -->
    <div id="alert-success" class="alert hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
        <strong class="font-bold">Éxito!</strong>
        <span class="block sm:inline" id="success-message"></span>
        <button onclick="hideAlert('alert-success')" class="absolute top-0 right-0 px-4 py-3">
            <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <title>Cerrar</title>
                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
            </svg>
        </button>
    </div>

    <div id="alert-error" class="alert hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline" id="error-message"></span>
        <button onclick="hideAlert('alert-error')" class="absolute top-0 right-0 px-4 py-3">
            <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <title>Cerrar</title>
                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
            </svg>
        </button>
    </div>
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
            @if($reporte->id_administrador_aprobador)
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <h3 class="font-semibold text-blue-800 mb-2">Información del Administrador</h3>
                <p><span class="font-medium text-gray-700">Administrador que aprobó/editó:</span> 
                    {{ $reporte->administradorAprobador->nombre ?? 'Usuario #' . $reporte->id_administrador_aprobador }}
                </p>
                <p><span class="font-medium text-gray-700">Fecha de aprobación/edición:</span> 
                    {{ $reporte->fecha_aprobacion ? $reporte->fecha_aprobacion : 'No especificada' }}
                </p>
                @if($reporte->administradorAprobador && $reporte->administradorAprobador->email)
                <p><span class="font-medium text-gray-700">Email:</span> 
                    {{ $reporte->administradorAprobador->email }}
                </p>
                @endif
            </div>
            @else
            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <p class="text-yellow-800">Este reporte aún no ha sido aprobado o editado por un administrador.</p>
            </div>
            @endif
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
            <p>
                <span class="font-semibold text-gray-700">Ubicación:</span> 
                @if($reporte->latitud && $reporte->longitud)
                    <a href="https://www.google.com/maps?q={{ $reporte->latitud }},{{ $reporte->longitud }}" 
                    target="_blank" 
                    class="text-blue-600 hover:underline">
                    Ver en Google Maps
                    </a>
                @else
                    <span class="text-gray-500">No especificada</span>
                @endif
            </p>
            <h3 class="text-lg font-semibold text-gray-700 border-b border-gray-400 pb-2 mb-4">Archivos Adjuntos</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @forelse($reporte->archivos as $archivo)
                    @if($archivo->tipo === 'imagen')
                        <div class="border border-gray-400 rounded-lg overflow-hidden shadow-sm bg-gray-50 hover:shadow-md transition">
                            <img src="{{ url(path:'reportes/imagen/' . $archivo->nombre_archivo) }}" 
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
            @if($reporte->respuestasAsociados && $reporte->respuestasAsociados->count() > 0)
                <h3 class="text-lg font-semibold text-gray-700 border-b border-gray-400 pb-2 mb-4">Observaciones de Asociados</h3>
                
                <div class="space-y-4">
                    @foreach($reporte->respuestasAsociados as $respuesta)
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-medium text-gray-800">
                                        {{ $respuesta->usuario->nombre ?? 'Usuario #' . $respuesta->id_usuario }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        @if($respuesta->usuario && $respuesta->usuario->dni)
                                            DNI: {{ $respuesta->usuario->dni }}
                                        @endif
                                    </p>
                                </div>
                                <span class="text-sm text-gray-500">
                                    {{ $respuesta->fecha_respuesta ? \Carbon\Carbon::parse($respuesta->fecha_respuesta)->format('d/m/Y H:i') : 'Sin fecha' }}
                                </span>
                            </div>
                            
                            <p class="text-gray-700 bg-white p-3 rounded border">
                                {{ $respuesta->respuesta }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <h3 class="text-lg font-semibold text-gray-700 border-b border-gray-400 pb-2 mb-4">Observaciones de Asociados</h3>
                <p class="text-gray-500 italic">No hay observaciones para este reporte.</p>
            @endif
            <!-- Acciones -->
            <div class="flex lg:justify-end max-lg:justify-center flex-wrap space-x-3 space-y-3 mt-6">
                <form action="{{ route('admin.reportes.aprobar',$reporte->id_reporte) }}" method="POST" class="inline">
                    @csrf
                    <button type="button"
                        onclick="toggleModal('modal-aprobar', true)"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                        Aprobar
                    </button>
                </form>

                <form action="{{ route('admin.reportes.rechazar',$reporte->id_reporte) }}" method="POST" class="inline">
                    @csrf
                    <button type="button" onclick="toggleModal('modal-rechazar', true)"
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
    </div>  <!-- Modal Aprobar -->
    <div id="modal-aprobar" class="hidden fixed inset-0 items-center justify-center bg-black/60 z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
            <h2 class="text-xl font-semibold mb-4">Seleccionar área de interés</h2>
            <form id="form-aprobar">
                @csrf
                <label for="area_interes" class="block text-sm font-medium text-gray-700">Área de Interés</label>
                <select name="id_area_interes" id="area_interes" required onchange="cargarUsuariosPorArea(this.value)"
                    class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                    @foreach($areas as $area)
                        <option value="{{ $area->id_area_interes }}">{{ $area->nombre }}</option>
                    @endforeach
                </select>

                <!-- Contenedor para mostrar usuarios del área seleccionada -->
                 <div id="usuarios-area" class="mt-4">
                    <h3 class="text-md font-medium text-gray-700 mb-2">Seleccionar usuarios:</h3>
                    <div class="flex items-center mb-2">
                        <input type="checkbox" id="seleccionar-todos" onchange="toggleSeleccionarTodos(this)">
                        <label for="seleccionar-todos" class="ml-2 text-sm font-medium text-gray-700">Seleccionar todos</label>
                    </div>
                    <div id="lista-usuarios" class="user-list">
                        <!-- Los usuarios se cargarán aquí dinámicamente -->
                    </div>
                </div>

                <div class="flex justify-end mt-4 space-x-2">
                    <button type="button" onclick="toggleModal('modal-aprobar', false)"
                        class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-100">Cancelar</button>
                    <button type="button" id="btn-aprobar-confirm" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center">
                        <span id="aprobar-text">Aprobar</span>
                        <span id="aprobar-loading" class="spinner hidden"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal Rechazar -->
    <div id="modal-rechazar" class="hidden fixed inset-0 items-center justify-center bg-black/60 z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
            <h2 class="text-xl font-semibold mb-4">Confirmar rechazo</h2>
            <p class="text-gray-600 mb-4">¿Estás seguro de que deseas rechazar este reporte?</p>
            <form id="form-rechazar">
                @csrf
                <div class="flex justify-end mt-4 space-x-2">
                    <button type="button" onclick="toggleModal('modal-rechazar', false)"
                        class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-100">Cancelar</button>
                    <button type="button" id="btn-rechazar-confirm" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 flex items-center">
                        <span id="rechazar-text">Sí, rechazar</span>
                        <span id="rechazar-loading" class="spinner hidden"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal de carga general -->
    <div id="modal-carga" class="hidden fixed inset-0 items-center justify-center bg-black/60 z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 flex flex-col items-center">
            <div class="spinner-large mb-4"></div>
            <p class="text-gray-700 font-medium" id="carga-mensaje">Procesando solicitud...</p>
            <p class="text-sm text-gray-500 mt-2" id="carga-detalle">Esto puede tomar varios segundos</p>
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
        
        function mostrarCarga(mensaje, detalle = '') {
            document.getElementById('carga-mensaje').textContent = mensaje;
            document.getElementById('carga-detalle').textContent = detalle;
            document.getElementById('modal-carga').classList.remove('hidden');
            document.getElementById('modal-carga').classList.add('flex');
        }
        
        function ocultarCarga() {
            document.getElementById('modal-carga').classList.add('hidden');
            document.getElementById('modal-carga').classList.remove('flex');
        }
        
        function mostrarAlerta(tipo, mensaje) {
            const alert = document.getElementById(`alert-${tipo}`);
            const messageElement = document.getElementById(`${tipo}-message`);
            
            messageElement.textContent = mensaje;
            alert.classList.remove('hidden');
            alert.classList.add('show');
            
            // Auto-ocultar después de 5 segundos
            setTimeout(() => {
                hideAlert(`alert-${tipo}`);
            }, 5000);
        }
        
        function hideAlert(alertId) {
            const alert = document.getElementById(alertId);
            alert.classList.remove('show');
            setTimeout(() => {
                alert.classList.add('hidden');
            }, 300);
        }
        
        // Función para cargar usuarios por área
        function cargarUsuariosPorArea(idArea) {
            const contenedorUsuarios = document.getElementById('usuarios-area');
            const listaUsuarios = document.getElementById('lista-usuarios');
            const seleccionarTodos = document.getElementById('seleccionar-todos');
            
            // Limpiar lista anterior
            listaUsuarios.innerHTML = '';
            seleccionarTodos.checked = false;
            
            // Mostrar spinner de carga
            listaUsuarios.innerHTML = '<div class="no-users">Cargando usuarios...</div>';
            contenedorUsuarios.classList.remove('hidden');
            
            // Hacer petición AJAX para obtener usuarios del área
            fetch(`/admin/areas/${idArea}/usuarios`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                listaUsuarios.innerHTML = '';
                
                if (data.success && data.usuarios.length > 0) {
                    data.usuarios.forEach(usuario => {
                        const userItem = document.createElement('div');
                        userItem.className = 'user-item';
                        userItem.innerHTML = `
                            <div class="flex items-center">
                                <input type="checkbox" name="usuarios_seleccionados[]" value="${usuario.id_usuario}" 
                                    class="usuario-checkbox mr-3" checked>
                                <div class="user-info">
                                    <div class="user-name">${usuario.nombre}</div>
                                    <div class="user-dni">DNI: ${usuario.dni || 'No disponible'}</div>
                                    <div class="user-telefono">Tel: ${usuario.telefono || 'No disponible'}</div>
                                </div>
                            </div>
                        `;
                        listaUsuarios.appendChild(userItem);
                    });
                } else {
                    listaUsuarios.innerHTML = '<div class="no-users">No hay usuarios en esta área</div>';
                }
            })
            .catch(error => {
                console.error('Error al cargar usuarios:', error);
                listaUsuarios.innerHTML = '<div class="no-users">Error al cargar usuarios</div>';
            });
        }
        
        function toggleSeleccionarTodos(checkbox) {
            const checkboxes = document.querySelectorAll('.usuario-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = checkbox.checked;
            });
        }

        // Manejar confirmación de aprobar con AJAX
        document.getElementById('btn-aprobar-confirm').addEventListener('click', function() {
            const areaInteres = document.getElementById('area_interes').value;
            
            // Obtener usuarios seleccionados
            const usuariosSeleccionados = [];
            document.querySelectorAll('.usuario-checkbox:checked').forEach(checkbox => {
                usuariosSeleccionados.push(checkbox.value);
            });
            
            if (usuariosSeleccionados.length === 0) {
                mostrarAlerta('error', 'Debe seleccionar al menos un usuario');
                return;
            }
            
            // Mostrar estado de carga en el botón
            document.getElementById('aprobar-text').classList.add('hidden');
            document.getElementById('aprobar-loading').classList.remove('hidden');
            this.disabled = true;
            
            // Ocultar modal de aprobar y mostrar modal de carga general
            toggleModal('modal-aprobar', false);
            mostrarCarga('Enviando notificaciones...', 'Esta operación puede tomar varios segundos');
            
            // Preparar datos del formulario
            const formData = new FormData();
            formData.append('_token', document.querySelector('input[name="_token"]').value);
            formData.append('id_area_interes', areaInteres);
            formData.append('usuarios_seleccionados', JSON.stringify(usuariosSeleccionados));
            
            // Enviar solicitud AJAX
            fetch("{{ route('admin.reportes.aprobar', $reporte->id_reporte) }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                ocultarCarga();
                
                if (data.success) {
                    mostrarAlerta('success', data.message);
                    // Recargar la página después de 2 segundos para ver los cambios
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    mostrarAlerta('error', data.message || 'Error al aprobar el reporte');
                    // Habilitar botón nuevamente
                    document.getElementById('aprobar-text').classList.remove('hidden');
                    document.getElementById('aprobar-loading').classList.add('hidden');
                    document.getElementById('btn-aprobar-confirm').disabled = false;
                }
            })
            .catch(error => {
                ocultarCarga();
                mostrarAlerta('error', 'Error de conexión: ' + error.message);
                // Habilitar botón nuevamente
                document.getElementById('aprobar-text').classList.remove('hidden');
                document.getElementById('aprobar-loading').classList.add('hidden');
                document.getElementById('btn-aprobar-confirm').disabled = false;
            });
        });
        
        // Manejar confirmación de rechazar con AJAX
        document.getElementById('btn-rechazar-confirm').addEventListener('click', function() {
            // Mostrar estado de carga en el botón
            document.getElementById('rechazar-text').classList.add('hidden');
            document.getElementById('rechazar-loading').classList.remove('hidden');
            this.disabled = true;
            
            // Ocultar modal de rechazar y mostrar modal de carga general
            toggleModal('modal-rechazar', false);
            mostrarCarga('Rechazando reporte...', '');
            
            // Preparar datos del formulario
            const formData = new FormData();
            formData.append('_token', document.querySelector('input[name="_token"]').value);
            
            // Enviar solicitud AJAX
            fetch("{{ route('admin.reportes.rechazar', $reporte->id_reporte) }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                ocultarCarga();
                
                if (data.success) {
                    mostrarAlerta('success', data.message);
                    // Recargar la página después de 2 segundos para ver los cambios
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    mostrarAlerta('error', data.message || 'Error al rechazar el reporte');
                    // Habilitar botón nuevamente
                    document.getElementById('rechazar-text').classList.remove('hidden');
                    document.getElementById('rechazar-loading').classList.add('hidden');
                    document.getElementById('btn-rechazar-confirm').disabled = false;
                }
            })
            .catch(error => {
                ocultarCarga();
                mostrarAlerta('error', 'Error de conexión: ' + error.message);
                // Habilitar botón nuevamente
                document.getElementById('rechazar-text').classList.remove('hidden');
                document.getElementById('rechazar-loading').classList.add('hidden');
                document.getElementById('btn-rechazar-confirm').disabled = false;
            });
        });
        
        // Prevenir el cierre del modal de carga con clic fuera o ESC
        document.getElementById('modal-carga').addEventListener('click', function(e) {
            if (e.target === this) {
                e.stopPropagation();
            }
        });
        
        // También prevenir el cierre con la tecla ESC
        document.addEventListener('keydown', function(e) {
            const modalCarga = document.getElementById('modal-carga');
            if (e.key === 'Escape' && !modalCarga.classList.contains('hidden')) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    </script>
</body>
</html>
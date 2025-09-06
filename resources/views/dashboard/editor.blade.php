<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Asociado</title>
    <link rel="icon" type="image/png" href="/img/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: #f7f7f7;
        }
        .header-red {
            background: #c0392b;
            color: white;
            padding: 15px 0;
        }
        .btn-red {
            background: #c0392b;
            border-color: #c0392b;
            color: white;
        }
        .btn-red:hover {
            background: #a93226;
            border-color: #a93226;
            color: white;
        }
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: #c0392b;">
        <div class="container-fluid">
            <span class="navbar-brand fw-bold">
                <i class="fa fa-user-circle"></i>
                {{ Auth::user()->nombre ?? Auth::user()->username ?? 'Asociado' }}
            </span>
            <div class="d-flex ms-auto">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-light fw-bold">
                        <i class="fa fa-sign-out-alt"></i> Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <div class="header-red text-center">
        <h1 class="h3 mb-0">Panel de Asociado - Reportes Asignados</h1>
    </div>

    <div class="container-fluid mt-4">
        <!-- Sección de Áreas de Interés -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fa fa-star"></i> Mis Áreas de Interés</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('asociado.areas.update') }}">
                            @csrf
                            <div class="row">
                                <!-- Agregar opción "Todos" al inicio -->
                                <div class="col-md-3 col-sm-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="selectAll" 
                                               onchange="toggleAllAreas(this)">
                                        <label class="form-check-label fw-bold" for="selectAll">
                                            <i class="fa fa-check-double"></i> Seleccionar Todos
                                        </label>
                                    </div>
                                </div>
                                <!-- Áreas existentes -->
                                @foreach($areas as $area)
                                <div class="col-md-3 col-sm-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input area-checkbox" type="checkbox" 
                                               name="areas[]" value="{{ $area->id_area_interes }}" 
                                               id="area{{ $area->id_area_interes }}"
                                               {{ in_array($area->id_area_interes, $misAreas) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="area{{ $area->id_area_interes }}">
                                            {{ $area->nombre }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button type="submit" class="btn btn-red mt-3">
                                <i class="fa fa-save"></i> Guardar Áreas de Interés
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Reportes Asignados -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fa fa-file-alt"></i> Reportes Asignados</h5>
                        <span class="badge bg-primary">{{ count($reportes) }} reportes</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Categoría</th>
                                        <th>Fecha Evento</th>
                                        <th>Lugar</th>
                                        <th>Estado</th>
                                        <th>Fecha Asignación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reportes as $reporte)
                                    <tr>
                                        <td>{{ $reporte->id_reporte }}</td>
                                        <td>{{ $reporte->categoria->nombre ?? 'Sin categoría' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($reporte->fecha_evento)->format('d/m/Y H:i') }}</td>
                                        <td>{{ $reporte->lugar }}</td>
                                        <td>
                                            @if($reporte->estado == 'revisado')
                                                <span class="badge bg-success">Revisado</span>
                                            @elseif($reporte->estado == 'aprobado')
                                                <span class="badge bg-warning text-dark">No Revisado</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $reporte->fecha_asignacion ? \Carbon\Carbon::parse($reporte->fecha_asignacion)->format('d/m/Y') : 'Sin asignar' }}
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        onclick="verReporte({{ $reporte->id_reporte }})">
                                                    <i class="fa fa-eye"></i> Ver
                                                </button>
                                                <a href="{{ route('reportes.export.word', $reporte->id_reporte) }}" 
                                                   class="btn btn-sm btn-outline-info">
                                                    <i class="fa fa-file-word"></i> Word
                                                </a>
                                                <a href="{{ route('reportes.export.pdf', $reporte->id_reporte) }}" 
                                                   class="btn btn-sm btn-outline-danger">
                                                    <i class="fa fa-file-pdf"></i> PDF
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No tienes reportes asignados</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Detalle del Reporte -->
    <div class="modal fade" id="reporteModal" tabindex="-1" aria-labelledby="reporteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: #c0392b; color: white;">
                    <h5 class="modal-title" id="reporteModalLabel">
                        <i class="fa fa-file-alt"></i> Detalle del Reporte
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="reporteContent">
                    <!-- Contenido del reporte se carga aquí -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-red" id="marcarRevisado" onclick="marcarComoRevisado()" style="display: none;">
                        <i class="fa fa-check"></i> Marcar como Revisado
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let reporteActual = null;

        function verReporte(id) {
            reporteActual = id;
            fetch(`/asociado/reportes/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('reporteContent').innerHTML = data.html;
                        // Mostrar botón solo si no hay respuesta existente
                        const btnMarcarRevisado = document.getElementById('marcarRevisado');
                        const observacionText = document.getElementById('observacionText');
                        btnMarcarRevisado.style.display = observacionText ? 'block' : 'none';
                        
                        new bootstrap.Modal(document.getElementById('reporteModal')).show();
                    } else {
                        Swal.fire('Error', 'No se pudo cargar el reporte', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'Error al cargar el reporte', 'error');
                });
        }

        function marcarComoRevisado() {
            if (!reporteActual) return;

            const observacion = document.getElementById('observacionText');
            if (!observacion) {
                Swal.fire('Error', 'Este reporte ya fue revisado', 'info');
                return;
            }

            if (!observacion.value.trim()) {
                Swal.fire('Error', 'Debes escribir una observación', 'warning');
                return;
            }

            Swal.fire({
                title: '¿Confirmar revisión?',
                text: '¿Estás seguro de marcar este reporte como revisado con tu observación?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#c0392b',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, marcar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/asociado/reportes/${reporteActual}/revisar`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            observacion: observacion.value
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('¡Éxito!', 'Reporte marcado como revisado', 'success');
                            bootstrap.Modal.getInstance(document.getElementById('reporteModal')).hide();
                            location.reload();
                        } else {
                            Swal.fire('Error', data.message || 'Error al marcar como revisado', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'Error al procesar la solicitud', 'error');
                    });
                }
            });
        }

        // Función para seleccionar/deseleccionar todas las áreas
        function toggleAllAreas(checkbox) {
            const areaCheckboxes = document.querySelectorAll('.area-checkbox');
            areaCheckboxes.forEach(cb => {
                cb.checked = checkbox.checked;
            });
        }

        // Función para verificar si todas las áreas están seleccionadas
        function checkIfAllSelected() {
            const areaCheckboxes = document.querySelectorAll('.area-checkbox');
            const selectAllCheckbox = document.getElementById('selectAll');
            const allChecked = Array.from(areaCheckboxes).every(cb => cb.checked);
            selectAllCheckbox.checked = allChecked;
        }

        // Agregar listener a cada checkbox de área
        document.querySelectorAll('.area-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', checkIfAllSelected);
        });

        // Verificar estado inicial
        checkIfAllSelected();

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#c0392b'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                confirmButtonColor: '#c0392b'
            });
        @endif
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Monitor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            font-family: 'Nunito', sans-serif;
            background: #f7f7f7;
            padding: 0;
        }
        .login-header {
            width: 100%;
            background: #c0392b;
            color: #fff;
            padding: 10px 0;
            text-align: center;
            font-size: 18px;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .container {
            max-width: 100%;
            padding: 15px;
            margin: 0 auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }
        .form-group label {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        .form-control {
            font-size: 14px;
            padding: 8px;
        }
        .btn-primary {
            background: #c0392b;
            border: none;
            font-weight: 600;
            transition: background 0.3s;
            font-size: 14px;
            padding: 10px;
        }
        .btn-primary:hover {
            background: #a93226;
        }
        .alert {
            font-size: 14px;
        }
        .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        .image-preview img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        input[type="file"]::file-selector-button {
            display: none; /* Oculta el botón predeterminado */
        }
        @media (max-width: 768px) {
            .container {
                padding: 10px;
                box-shadow: none;
            }
            .form-group {
                margin-bottom: 15px;
            }
            .btn-primary {
                font-size: 13px;
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar bonito con Bootstrap -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: #c0392b;">
        <div class="container-fluid">
            <span class="navbar-brand fw-bold">
                <i class="fa fa-user-circle"></i>
                {{ Auth::user()->nombre ?? Auth::user()->username ?? 'Monitor' }}
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

    <div class="login-header">
        Grupo Desarrollo y Comunicacion S.A.C
    </div>
    <div class="container">
        <h2 class="text-center mb-4" style="font-size: 18px;">Nuevo Reporte</h2>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ url('monitor/reportar') }}" enctype="multipart/form-data" id="reporteForm">
            @csrf
            <!-- Campo oculto para el ID del monitor -->
            <input type="hidden" name="id_monitor" value="{{ Auth::id() }}">

            <div class="form-group mb-3">
                <label>Categoría del evento</label>
                <select name="id_categoria" class="form-control" required>
                    <option value="">Seleccione...</option>
                    <option value="1">Alerta social (Rumor)</option>
                    <option value="2">Redes sociales</option>
                    <option value="3">Reunión o asamblea local</option>
                    <option value="4">Reclamo por servicios o pagos</option>
                    <option value="5">Actividad política vinculada al conflicto</option>
                    <option value="6">Bloqueo de vías / Protesta / Amenaza o agresión</option>
                    <option value="7">Otro (especificar)</option>
                </select>
            </div>
            <div class="form-group mb-3">
                <label>Fecha y hora del evento</label>
                <input type="datetime-local" name="fecha_evento" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label>Lugar del incidente</label>
                <input type="text" name="lugar" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label>Descripción del hecho</label>
                <textarea name="descripcion" class="form-control" rows="3" required></textarea>
            </div>
            <div class="form-group mb-3">
                <label>Actores identificados (nombres, roles, vínculo):</label>
                <div id="actores-container">
                    <div class="d-flex align-items-center mb-2">
                        <input type="text" name="actores_identificados[]" class="form-control me-2" placeholder="Nombre">
                        <input type="text" name="actores_roles[]" class="form-control me-2" placeholder="Rol">
                        <input type="text" name="actores_vinculos[]" class="form-control me-2" placeholder="Vínculo">
                        <button type="button" class="btn btn-danger remove-actor-btn">Eliminar</button>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary mt-2" id="addActorButton">Añadir otro actor</button>
            </div>
            <div class="form-group mb-3">
                <label>Tema tratado</label>
                <textarea name="tema_tratado" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group mb-3">
                <label>Acuerdos o compromisos asumidos (si los hubiera)</label>
                <textarea name="acuerdos_compromisos" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group mb-3">
                <label>Recomendación preliminar</label>
                <textarea name="recomendacion_preliminar" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group mb-3">
                <label>Adjuntar imágenes (máx. 5):</label>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary" id="cameraButton">Abrir cámara</button>
                    <button type="button" class="btn btn-secondary" id="fileButton">Añadir desde archivos</button>
                </div>
                <input type="file" name="imagenes[]" accept="image/*" id="cameraInput" capture="environment" class="d-none">
                <input type="file" name="imagenes[]" accept="image/*" id="fileInput" class="d-none" multiple>
                <div class="image-preview" id="image-preview"></div>
                <small class="text-muted">Máximo 5 imágenes.</small>
            </div>
            <div class="form-group mb-3">
                <label>Enlace (URL):</label>
                <div id="url-container">
                    <div class="d-flex align-items-center mb-2">
                        <input type="url" name="enlace[]" class="form-control" placeholder="Añadir enlace">
                        <button type="button" class="btn btn-danger ms-2 remove-url-btn">Eliminar</button>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary mt-2" id="addUrlButton">Añadir otro enlace</button>
                <small class="text-muted">Máximo 5 enlaces.</small>
            </div>
            <div id="bloqueos-fields" style="display: none;">
                <div class="form-group mb-3">
                    <label>Número de personas estimado</label>
                    <input type="number" name="numero_personas" id="numero_personas" class="form-control" placeholder="Número de personas" required>
                </div>
                <div class="form-group mb-3">
                    <label>¿Presencia de autoridades o líderes locales?</label>
                    <textarea name="presencia_autoridades" id="presencia_autoridades" class="form-control" rows="3" placeholder="Explica si hubo presencia de autoridades" required></textarea>
                </div>
                <div class="form-group mb-3">
                    <label>Interviene serenazgo / PNP</label>
                    <textarea name="intervencion_serenazgo" id="intervencion_serenazgo" class="form-control" rows="3" placeholder="Explica si hubo intervención de serenazgo o PNP" required></textarea>
                </div>
            </div>
            <div id="ubicacion-fields" style="display: none;">
                <div class="form-group mb-3">
                    <label>Ubicación</label>
                    <div class="input-group">
                        <input type="text" name="latitud" id="latitud" class="form-control" placeholder="Latitud" readonly required>
                        <input type="text" name="longitud" id="longitud" class="form-control" placeholder="Longitud" readonly required>
                        <button type="button" class="btn btn-primary" id="getLocationButton">
                            <i class="fa fa-map-marker-alt"></i> Obtener ubicación
                        </button>
                    </div>
                    <small class="text-muted">Presiona el botón para obtener tu ubicación.</small>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-3">Enviar Reporte</button>
        </form>

    </div>
    <script>
        document.querySelector('select[name="id_categoria"]').addEventListener('change', function() {
            const categoria = this.value;
            const bloqueosFields = document.getElementById('bloqueos-fields');
            const ubicacionFields = document.getElementById('ubicacion-fields');

            if (categoria == 6) { // Categoría 6: Bloqueo de vías
                bloqueosFields.style.display = 'block';
                ubicacionFields.style.display = 'block';

                // Hacer que los campos sean obligatorios
                document.getElementById('numero_personas').setAttribute('required', 'required');
                document.getElementById('presencia_autoridades').setAttribute('required', 'required');
                document.getElementById('intervencion_serenazgo').setAttribute('required', 'required');
                document.getElementById('latitud').setAttribute('required', 'required');
                document.getElementById('longitud').setAttribute('required', 'required');
            } else {
                bloqueosFields.style.display = 'none';
                ubicacionFields.style.display = 'none';

                // Quitar el atributo "required" de los campos
                document.getElementById('numero_personas').removeAttribute('required');
                document.getElementById('presencia_autoridades').removeAttribute('required');
                document.getElementById('intervencion_serenazgo').removeAttribute('required');
                document.getElementById('latitud').removeAttribute('required');
                document.getElementById('longitud').removeAttribute('required');

                // Limpiar los valores de los campos ocultos
                document.getElementById('numero_personas').value = '';
                document.getElementById('presencia_autoridades').value = '';
                document.getElementById('intervencion_serenazgo').value = '';
                document.getElementById('latitud').value = '';
                document.getElementById('longitud').value = '';
            }
        });

        // Botones para añadir imágenes
        const cameraButton = document.getElementById('cameraButton');
        const fileButton = document.getElementById('fileButton');
        const cameraInput = document.getElementById('cameraInput');
        const fileInput = document.getElementById('fileInput');
        const imagePreview = document.getElementById('image-preview');

        cameraButton.addEventListener('click', () => cameraInput.click());
        fileButton.addEventListener('click', () => fileInput.click());

        cameraInput.addEventListener('change', handleImageUpload);
        fileInput.addEventListener('change', handleImageUpload);

        function handleImageUpload(event) {
            const files = event.target.files;
            if (imagePreview.children.length + files.length > 5) {
                Swal.fire({
                    icon: 'error',
                    title: 'Límite alcanzado',
                    text: 'Solo puedes subir hasta 5 imágenes.',
                    confirmButtonColor: '#d33',
                });
                return;
            }
            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgContainer = document.createElement('div');
                    imgContainer.classList.add('position-relative', 'me-2');
                    imgContainer.innerHTML = `
                        <img src="${e.target.result}" class="rounded" style="width: 80px; height: 80px; object-fit: cover;">
                        <button type="button" class="btn-close position-absolute top-0 start-100 translate-middle" aria-label="Close"></button>
                    `;
                    imagePreview.appendChild(imgContainer);

                    // Botón para eliminar la imagen
                    imgContainer.querySelector('.btn-close').addEventListener('click', () => {
                        imgContainer.remove();
                    });
                };
                reader.readAsDataURL(file);
            });
        }

        // Botón para añadir más URLs
        const addUrlButton = document.getElementById('addUrlButton');
        const urlContainer = document.getElementById('url-container');

        addUrlButton.addEventListener('click', () => {
            if (urlContainer.children.length >= 5) {
                Swal.fire({
                    icon: 'error',
                    title: 'Límite alcanzado',
                    text: 'Solo puedes añadir hasta 5 enlaces.',
                    confirmButtonColor: '#d33',
                });
                return;
            }
            const urlGroup = document.createElement('div');
            urlGroup.classList.add('d-flex', 'align-items-center', 'mb-2');
            urlGroup.innerHTML = `
                <input type="url" name="enlace[]" class="form-control" placeholder="Añadir enlace">
                <button type="button" class="btn btn-danger ms-2 remove-url-btn">Eliminar</button>
            `;
            urlContainer.appendChild(urlGroup);

            // Añadir evento para eliminar el enlace
            urlGroup.querySelector('.remove-url-btn').addEventListener('click', () => {
                urlGroup.remove();
            });
        });

        // Añadir evento para eliminar enlaces existentes
        document.querySelectorAll('.remove-url-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                e.target.closest('.d-flex').remove();
            });
        });

        // Botón para añadir más actores
        const addActorButton = document.getElementById('addActorButton');
        const actoresContainer = document.getElementById('actores-container');

        addActorButton.addEventListener('click', () => {
            const actorGroup = document.createElement('div');
            actorGroup.classList.add('d-flex', 'align-items-center', 'mb-2');
            actorGroup.innerHTML = `
                <input type="text" name="actores_identificados[]" class="form-control me-2" placeholder="Nombre">
                <input type="text" name="actores_roles[]" class="form-control me-2" placeholder="Rol">
                <input type="text" name="actores_vinculos[]" class="form-control me-2" placeholder="Vínculo">
                <button type="button" class="btn btn-danger remove-actor-btn">Eliminar</button>
            `;
            actoresContainer.appendChild(actorGroup);

            // Botón para eliminar el actor
            actorGroup.querySelector('.remove-actor-btn').addEventListener('click', () => {
                actorGroup.remove();
            });
        });

        // Obtener ubicación del usuario
        const getLocationButton = document.getElementById('getLocationButton');
        const latitudInput = document.getElementById('latitud');
        const longitudInput = document.getElementById('longitud');

        getLocationButton.addEventListener('click', () => {
            if (navigator.geolocation) {
                Swal.fire({
                    title: 'Obteniendo ubicación...',
                    text: 'Por favor, espera mientras obtenemos tu ubicación.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        document.getElementById('latitud').value = position.coords.latitude;
                        document.getElementById('longitud').value = position.coords.longitude;

                        Swal.fire({
                            icon: 'success',
                            title: 'Ubicación obtenida',
                            text: 'Se ha obtenido tu ubicación correctamente.',
                            confirmButtonColor: '#3085d6',
                        });
                    },
                    (error) => {
                        let errorMessage = 'No se pudo obtener la ubicación.';
                        if (error.code === 1) {
                            errorMessage = 'Permiso denegado. Activa los permisos de ubicación en tu navegador.';
                        } else if (error.code === 2) {
                            errorMessage = 'Ubicación no disponible. Intenta nuevamente.';
                        } else if (error.code === 3) {
                            errorMessage = 'Tiempo de espera agotado. Intenta nuevamente.';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error al obtener ubicación',
                            text: errorMessage,
                            confirmButtonColor: '#d33',
                        });
                    }
                );
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Geolocalización no soportada',
                    text: 'Tu navegador no soporta la geolocalización.',
                    confirmButtonColor: '#d33',
                });
            }
        });

        document.getElementById('reporteForm').addEventListener('submit', function(event) {
            const categoria = document.querySelector('select[name="id_categoria"]').value;
            const latitud = document.getElementById('latitud').value;
            const longitud = document.getElementById('longitud').value;
            const submitButton = document.querySelector('button[type="submit"]');

            // Validar ubicación solo si la categoría es "6"
            if (categoria == 6) {
                if (!latitud || !longitud) {
                    event.preventDefault(); // Evita el envío del formulario
                    Swal.fire({
                        icon: 'error',
                        title: 'Ubicación requerida',
                        text: 'Debes obtener tu ubicación antes de enviar el reporte.',
                        confirmButtonColor: '#d33',
                    });
                    return;
                }
            }

            // Cambiar el estado del botón a "Cargando"
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Enviando...';
        });

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Aceptar'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            });
        @endif
    </script>
</body>
</html>
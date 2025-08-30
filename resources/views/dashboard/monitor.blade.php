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

        <form method="POST" action="{{ url('monitor/reportar') }}" enctype="multipart/form-data">
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
                <label>Actores identificados (nombres, roles, vínculo)</label>
                <textarea name="actores_identificados" class="form-control" rows="3"></textarea>
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
                <input type="file" name="imagenes[]" accept="image/*" multiple class="form-control" id="imagenes" capture="environment">
                <div class="image-preview" id="image-preview"></div>
            </div>
            <div class="form-group mb-3">
                <label>Enlace (URL):</label>
                <input type="url" name="enlace" class="form-control">
            </div>
            <div id="bloqueos-fields" style="display: none;">
                <div class="form-group mb-3">
                    <label>Número de personas estimado</label>
                    <input type="number" name="numero_personas" class="form-control">
                </div>
                <div class="form-group mb-3">
                    <label>¿Presencia de autoridades o líderes locales?</label>
                    <textarea name="presencia_autoridades" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group mb-3">
                    <label>Interviene serenazgo / PNP</label>
                    <textarea name="intervencion_serenazgo" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-3">Enviar Reporte</button>
        </form>
    </div>
    <script>
        document.querySelector('select[name="id_categoria"]').addEventListener('change', function() {
            const categoria = this.value;
            const bloqueosFields = document.getElementById('bloqueos-fields');
            if (categoria == 6) { // Categoría F
                bloqueosFields.style.display = 'block';
            } else {
                bloqueosFields.style.display = 'none';
            }
        });

        // Vista previa de imágenes
        document.getElementById('imagenes').addEventListener('change', function(event) {
            const previewContainer = document.getElementById('image-preview');
            previewContainer.innerHTML = ''; // Limpiar vista previa
            const files = event.target.files;

            if (files.length > 5) {
                alert('Solo puedes subir hasta 5 imágenes.');
                event.target.value = ''; // Limpiar selección
                return;
            }

            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    previewContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
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
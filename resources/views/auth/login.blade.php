@vite('resources/css/app.css')
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            font-family: 'Nunito', sans-serif;
            background: #fff url('{{ asset('img/bg-login-decsac.png') }}') repeat center center;
            background-size: contain;
        }
        .login-header {
            width: 100vw;
            background: #c0392b;
            color: #fff;
            padding: 12px 0;
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .login-container {
            min-height: calc(100vh - 48px);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #bdbdbd;
            border-radius: 12px;
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.2);
            padding: 40px 32px 32px 32px;
            width: 350px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .logo-circle {
            background: #fff;
            border-radius: 50%;
            width: 110px;
            height: 110px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .logo-circle img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 50%;
        }
        .input-group {
            display: flex;
            align-items: center;
            margin-bottom: 18px;
            width: 100%;
        }
        .input-group .input-icon {
            background: #b04a3a;
            color: #fff;
            padding: 0 14px;
            border-radius: 6px 0 0 6px;
            height: 44px;
            display: flex;
            align-items: center;
            font-size: 18px;
        }
        .input-group input {
            border: none;
            outline: none;
            height: 44px;
            padding: 0 12px;
            border-radius: 0 6px 6px 0;
            width: 100%;
            font-size: 16px;
            background: #e3eafc;
        }
        .input-group input:focus {
            background: #fff;
        }
        .input-group .input-eye {
            background: #b04a3a;
            color: #fff;
            padding: 0 14px;
            border-radius: 0 6px 6px 0;
            height: 44px;
            display: flex;
            align-items: center;
            font-size: 18px;
            cursor: pointer;
        }
        .login-btn {
            width: 100%;
            background: #23272b;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 10px 0;
            font-size: 17px;
            font-weight: 600;
            margin-top: 10px;
            transition: background 0.2s;
        }
        .login-btn:hover {
            background: #444;
        }
        .invalid-feedback {
            color: #c0392b;
            font-size: 13px;
            margin-top: 2px;
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="login-header">
        Grupo Desarrollo y Comunicacion S.A.C 
    </div>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-circle">
                <img src="{{ asset('img/logo.png') }}" alt="Logo">
            </div>
            <form method="POST" action="{{ route('login') }}" style="width:100%;">
                @csrf
                <div class="input-group">
                    <span class="input-icon"><i class="fa fa-envelope"></i></span>
                    <input type="email" name="email" placeholder="ejemplo@correo.com" required autofocus>
                </div>

                <div class="input-group">
                    <span class="input-icon"><i class="fa fa-key"></i></span>
                    <input id="password" type="password" name="password" placeholder="Contraseña" maxlength="16" required>
                    <span class="input-eye" onclick="togglePassword()">
                        <i id="eyeIcon" class="fa fa-eye"></i>
                    </span>
                </div>

                <button type="submit" class="login-btn">Ingresar</button>
            </form>

            @if ($errors->any())
                <div class="invalid-feedback text-center mt-2">
                    {{ $errors->first() }}
                </div>
            @endif
        </div>
    </div>
    <script>
        function togglePassword() {
            var input = document.getElementById('password');
            var icon = document.getElementById('eyeIcon');
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>

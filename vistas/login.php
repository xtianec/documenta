<?php
// login.php

session_start();

// Si el usuario ya ha iniciado sesión, redirígelo según su tipo y rol
if (isset($_SESSION['user_type'])) {
    // ... (tu código de redirección)
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Metadatos -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Responsive a ancho de pantalla -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Login de Usuarios">
    <meta name="author" content="Tu Nombre">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="/documenta/app/template/images/LOGO_FONDO_TRANSPARENTE.png">
    <title>Login Usuarios - ANDINA</title>
    <!-- Bootstrap Core CSS -->
    <link href="/documenta/app/template/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Page CSS -->
    <link href="/documenta/app/template/css/pages/login-register-lock.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/documenta/app/template/css/style.css" rel="stylesheet">
    <!-- Theme Colors -->
    <link href="/documenta/app/template/css/colors/default-dark.css" id="theme" rel="stylesheet">
    <!-- SweetAlert2 para mensajes elegantes -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- jQuery -->
    <script src="/documenta/app/template/plugins/jquery/jquery.min.js"></script>
    <!-- Popper.js y Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="/documenta/app/template/plugins/bootstrap/js/bootstrap.min.js"></script>
    <!-- Tu script personalizado -->
    <script src="/documenta/vistas/scripts/login.js"></script>
</head>

<body>
    <!-- Preloader -->
    <div class="preloader">
        <!-- ... -->
    </div>
    <!-- End Preloader -->

    <!-- Main Wrapper -->
    <section id="wrapper" class="login-register login-sidebar" style="background-image:url(/documenta/app/template/images/colaboradores.png);">
        <div class="login-box card">
            <div class="card-body">
                <!-- Formulario de Login -->
                <form class="form-horizontal form-material" id="frmAcceso" method="post">
                    <a href="javascript:void(0)" class="text-center db">
                        <img src="/documenta/app/template/images/logo.png" alt="Home" width="300" height="80" />
                    </a>
                    <br>
                    <h2 style="text-align: center; font-weight: bold;">Colaboradores</h2>
                    <div class="form-group m-t-40">
                        <div class="col-xs-12">
                            <input class="form-control" id="username" name="username" type="text" required placeholder="Usuario" autocomplete="username">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <!-- Cambiado el ID a 'password' -->
                            <input class="form-control" id="password" name="password" type="password" required placeholder="Contraseña" autocomplete="current-password">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="checkbox checkbox-primary pull-left p-t-0">
                                <input id="checkbox-signup" type="checkbox" class="filled-in chk-col-light-blue">
                                <label for="checkbox-signup"> Recordarme</label>
                            </div>
                        </div>
                    </div>
                    <div id="login-error-message" class="text-danger text-center mb-3"></div>
                    <div class="form-group text-center m-t-20">
                        <div class="col-xs-12">
                            <button class="btn btn-inverse btn-lg btn-block text-uppercase btn-rounded" type="submit">Ingresar</button>
                        </div>
                    </div>
                    <!-- Enlaces a redes sociales -->
                    <!-- ... (mantén esta sección igual) -->
                </form>
            </div>
        </div>
    </section>
    <!-- End Main Wrapper -->

    <!-- Custom JavaScript para Preloader y Toggle de Formularios -->
    <script type="text/javascript">
        $(function() {
            $(".preloader").fadeOut();
        });

        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        });
    </script>

</body>

</html>

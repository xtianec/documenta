<?php
// login_supplier.php

session_start();

// Si el usuario ya ha iniciado sesión como proveedor, redirígelo al dashboard
if (
    isset($_SESSION['user_type']) &&
    $_SESSION['user_type'] === 'supplier' &&
    $_SESSION['user_role'] === 'proveedor'
) {
    header("Location: dashboardSupplier");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

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
    <title>LOGIN PROVEEDORES - ANDINA</title>
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
    <script src="/documenta/vistas/scripts/login_supplier.js"></script>
</head>

<body>
    <!-- Preloader -->
    <div class="preloader">
        <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label">ANDINA</p>
        </div>
    </div>
    <!-- End Preloader -->

    <!-- Main Wrapper -->
    <section id="wrapper" class="login-register login-sidebar" style="background-image:url(/documenta/app/template/images/proveedores.png);">
        <div class="login-box card">
            <div class="card-body">
                <!-- Formulario de Login -->
                <form class="form-horizontal form-material" id="frmAcceso" method="post">
                    <a href="javascript:void(0)" class="text-center db">
                        <img src="/documenta/app/template/images/logo.png" alt="Home" width="300" height="80" />
                    </a>
                    <br>
                    <h2 style="text-align: center; font-weight: bold;">PROVEEDORES</h2>

                    <div class="form-group m-t-40">
                        <div class="col-xs-12">
                            <input class="form-control" id="logina" name="logina" type="text" required placeholder="Usuario" autocomplete="username">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input class="form-control" id="clavea" name="clavea" type="password" required placeholder="Contraseña" autocomplete="current-password">
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
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 m-t-10 text-center">
                            <div class="social">
                                <a href="https://www.facebook.com/profile.php?id=100083217586884" class="btn btn-facebook" data-toggle="tooltip" title="Login with Facebook">
                                    <i aria-hidden="true" class="fa fa-facebook"></i>
                                </a>
                                <a href="https://www.instagram.com/andinaenergy/" class="btn btn-instagram" data-toggle="tooltip" title="Login with Instagram">
                                    <i aria-hidden="true" class="fa fa-instagram"></i>
                                </a>

                                <a href="https://x.com/andina_energy" class="btn btn-twitter" data-toggle="tooltip" title="Login with Twitter">
                                    <i aria-hidden="true" class="fa fa-twitter"></i>
                                </a>
                                <a href="https://pe.linkedin.com/company/andina-energy" class="btn btn-linkedin" data-toggle="tooltip" title="Login with LinkedIn">
                                    <i aria-hidden="true" class="fa fa-linkedin"></i>
                                </a>

                                <a href="https://www.youtube.com/@andinaenergy8668" class="btn btn-youtube" data-toggle="tooltip" title="Login with YouTube">
                                    <i aria-hidden="true" class="fa fa-youtube"></i>
                                </a>

                            </div>
                        </div>
                    </div>
                   
                </form>
                <!-- Formulario de Recuperación de Contraseña -->
              
            </div>
        </div>
    </section>
    <!-- End Main Wrapper -->

    <!-- Custom JavaScript for Preloader and Form Toggle -->
    <script type="text/javascript">
        $(function() {
            $(".preloader").fadeOut();
        });

        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        });

        // Toggle entre el formulario de login y recuperación de contraseña
        $('#to-recover').on("click", function() {
            $("#loginform").slideUp();
            $("#recoverform").slideDown();
        });
    </script>

</body>

</html>
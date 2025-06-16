<?php
// superadmin_dashboard.php

session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'supplier' || $_SESSION['user_role'] !== 'proveedor') {
    header("Location: ../login.php");
    exit();
}

require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>

<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h3 class="text-themecolor">Subir Documentos</h3>
    </div>
    <div class="col-md-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item">Documentos</li>
            <li class="breadcrumb-item active">Subir Documentos</li>
        </ol>
    </div>
</div>

<div class="container-fluid">
    <!-- Barra de progreso general -->
    <div class="progress mb-4" style="height: 25px;">
        <div id="overallProgress" class="progress-bar bg-warning progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%; height:16px;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
            0% Completado
        </div>
    </div>

    <div class="row">
        <!-- Contenedor para los documentos -->
        <div class="col-12">
            <div id="document-container" class="row">
                <!-- Aquí se cargarán los cards de los documentos -->
                <p id="document-debug" style="display:none;"></p> <!-- Depurador adicional para mostrar posibles errores -->
            </div>
        </div>
    </div>
</div>

<?php
require 'layout/footer.php';
?>

<!-- Incluye tu archivo JavaScript -->
<script src="/documenta/vistas/scripts/documentSupplier.js"></script>
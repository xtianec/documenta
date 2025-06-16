<?php

session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit();
}

// Verificar el rol del usuario
if ($_SESSION['role'] !== 'superadmin' && $_SESSION['role'] !== 'adminrh') {
    echo "No tienes permiso para acceder a esta página.";
    exit();
}

require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h3 class="text-themecolor">Listado de Puestos con Documentos Asignados</h3>
    </div>
    <div class="col-md-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item">Configuración</li>
            <li class="breadcrumb-item active">Puestos y Documentos</li>
        </ol>
    </div>
    <div>
        <button class="right-side-toggle waves-effect waves-light btn-inverse btn btn-circle btn-sm pull-right m-l-10">
            <i class="ti-settings text-white"></i>
        </button>
    </div>
</div>

<div class="card card-body">
    <h4 class="card-title">Listado de Puestos con Documentos Asignados</h4>
    <h6 class="card-subtitle">Documentos Obligatorios y Opcionales por Puesto</h6>

    <div class="table-responsive">
        <table id="tblPuestosDocumentos" class="table color-table inverse-table" style="width:100%">
            <thead>
                <tr>
                    <th>Puesto</th>
                    <th>Documento</th>
                    <th>Tipo de Documento</th>
                    <th>Fecha de Creación</th>
                    <th>Última Actualización</th>
                </tr>
            </thead>
            <tbody>
                <!-- Datos cargados dinámicamente -->
            </tbody>
        </table>
    </div>
</div>

<script src="/documenta/vistas/scripts/listarPuestosDocumentos.js"></script>

<?php
require 'layout/footer.php';
?>

<?php

session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
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
        <h3 class="text-themecolor">Gestión de Documentos por Puesto</h3>
    </div>
    <div class="col-md-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
            <li class="breadcrumb-item">Configuración</li>
            <li class="breadcrumb-item active">Gestión de Documentos</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-body p-4">
                <div class="card shadow-sm border rounded-lg" style="border-color: #17a2b8; border-radius: 15px;">
                    <div class="card-body p-4">
                        <h2 class="text-info mb-4 font-weight-bold text-center"><i class="fa fa-file-text"></i> Puestos y Documentos</h2>

                        <!-- Componente Tabs para las dos vistas -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="asignados-tab" data-toggle="tab" href="#asignados" role="tab" aria-controls="asignados" aria-selected="true">Puestos con Documentos Asignados</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="sin-asignar-tab" data-toggle="tab" href="#sin-asignar" role="tab" aria-controls="sin-asignar" aria-selected="false">Puestos sin Documentos Asignados</a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- Tab 1: Puestos con Documentos Asignados -->
                            <div class="tab-pane fade show active" id="asignados" role="tabpanel" aria-labelledby="asignados-tab">
                                <div class="table-responsive mt-4">
                                    <table id="tblPuestosConDocumentos" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Empresa</th>
                                                <th>Puesto</th>
                                                <th>Documento</th>
                                                <th>Tipo</th>
                                                <th>Fecha Asignación</th>
                                                <th>Fecha Actualización</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab 2: Puestos sin Documentos Asignados -->
                            <div class="tab-pane fade" id="sin-asignar" role="tabpanel" aria-labelledby="sin-asignar-tab">
                                <div class="table-responsive mt-4">
                                    <table id="tblPuestosSinDocumentos" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Empresa</th>
                                                <th>ID</th>
                                                <th>Puesto</th>
                                                <th>Estado Asignación</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- Fin de las Tabs -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/documenta/vistas/scripts/puestosDocumentos.js"></script>

<?php
require 'layout/footer.php';
?>

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
        <h3 class="text-themecolor">Evaluar Documento</h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Documentos Pendientes de Evaluación</h4>
                <!-- Aquí se agrega el atributo "data-tablesaw-mode" para que Tablesaw lo reconozca -->
                <table id="documentTable" class="table color-table inverse-table" style="width:100%" data-tablesaw-mode="columntoggle">
                    <thead class="bg-inverse text-white">
                        <tr>
                            <th scope="col" data-tablesaw-priority="persist">Empresa</th>
                            <th scope="col" data-tablesaw-priority="1">Puesto</th>
                            <th scope="col" data-tablesaw-priority="2">Nombre del Postulante</th>
                            <th scope="col" data-tablesaw-priority="3">Tipo Documento</th>
                            <th scope="col" data-tablesaw-priority="4">Documento</th>
                            <th scope="col" data-tablesaw-priority="5">Fecha de Subida</th>
                            <th scope="col" data-tablesaw-priority="6">Fecha de Evaluación</th>
                            <th scope="col" data-tablesaw-priority="7">Estado</th>
                            <th scope="col" data-tablesaw-priority="8">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="documentListPendientes">
                        <!-- Documentos pendientes cargados dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script src="/documenta/vistas/scripts/evaluarDocumentoPostulante.js"></script>

<?php
require 'layout/footer.php';
?>
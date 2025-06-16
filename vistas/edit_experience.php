<?php
// superadmin_dashboard.php

session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'applicant' || $_SESSION['user_role'] !== 'postulante') {
    header("Location: ../login.php");
    exit();
}

require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>
<div class="row page-titles">
    <div class="col-md-6 align-self-center">
        <h3 class="text-themecolor font-weight-bold">Registrar/Editar Experiencia Laboral y Educacional</h3>
    </div>
    <div class="col-md-6 align-self-center">
        <ol class="breadcrumb float-right">
            <li class="breadcrumb-item"><a href="index.php"><i class="fa fa-home"></i> Inicio</a></li>
            <li class="breadcrumb-item active">Registrar/Editar Experiencia</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card card-outline-inverse">
            <div class="card-header ">
                <h4 class="m-b-0 text-white align-items-center"><i class="fa fa-book mr-2"></i> Experiencia Educativa</h4>
            </div>
            <div class="card-body">
                <form class="form-material m-t-40" id="formEducation" method="POST">
                    <div class="table-responsive">
                        <table class="table color-table inverse-table" style="width:100%" id="tablaExperienciaEducativa">
                            <thead class="thead-light">
                                <tr>
                                    <th>Institución</th>
                                    <th>Tipo de Educación</th>
                                    <th>Fecha de Inicio</th>
                                    <th>Fecha de Fin</th>
                                    <th>Duración</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Las filas se agregarán dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-primary" id="btnAddEducacion"><i class="fa fa-plus"></i> Agregar Fila</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Guardar Cambios</button>
                </form>
            </div>
        </div>

        <div class="card card-outline-inverse">
            <div class="card-header">
                <h4 class="m-b-0 text-white d-flex align-items-center"><i class="fa fa-briefcase mr-2"></i> Experiencia Laboral</h4>
            </div>
            <div class="card-body">
                <form class="form-material m-t-40" id="formWork" method="POST">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="tablaExperienciaLaboral">
                            <thead class="thead-light">
                                <tr>
                                    <th>Empresa</th>
                                    <th>Puesto</th>
                                    <th>Fecha de Inicio</th>
                                    <th>Fecha de Fin</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Las filas se agregarán dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-primary" id="btnAddTrabajo"><i class="fa fa-plus"></i> Agregar Fila</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="/documenta/vistas/scripts/edit_experience.js"></script>

<?php require 'layout/footer.php'; ?>

<!-- Estilos CSS Mejorados -->
<style>
/* Ajuste del body */
body {
    background-color: #f4f6f9;
}

/* Título de la página */
.page-titles {
    margin-bottom: 30px;
}

h3.text-themecolor {
    color: #007bff;
    font-weight: bold;
}

/* Estilos de las tarjetas */
.card {
    border-radius: 10px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
}

.card:hover {
    transform: scale(1.02);
    box-shadow: 0px 0px 25px rgba(0, 0, 0, 0.15);
}

/* Encabezado de las tarjetas */
.card-header {
    padding: 20px;
    font-weight: bold;
}

/* Botones */
.btn-primary, .btn-success {
    font-size: 1.1rem;
    padding: 10px 20px;
    margin-top: 15px;
    border-radius: 5px;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

/* Tablas */
.table-hover tbody tr:hover {
    background-color: #f5f5f5;
}

.table {
    margin-bottom: 0;
}

thead.thead-light th {
    background-color: #f8f9fa;
    color: #343a40;
    text-align: center;
}

tbody td {
    text-align: center;
    vertical-align: middle;
}

/* Breadcrumb */
.breadcrumb {
    background: none;
}

.fa-home {
    color: #007bff;
}

</style>

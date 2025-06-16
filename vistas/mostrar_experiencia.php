<?php
// superadmin_dashboard.php

session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'applicant' || $_SESSION['user_role'] !== 'postulante') {
    header("Location: login");
    exit();
}

require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>


<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h3 class="text-themecolor">Mis Experiencias Registradas</h3>

    </div>
    <div class="col-md-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item active">Mis Experiencias</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12 text-center">
        <div class="mt-4">
            <a href="edit_experience.php" class="btn btn-primary btn-lg">
                <i class="fa fa-pencil"></i> Editar Experiencias
            </a>
            <a href="experience.php" class="btn btn-success btn-lg">
                <i class="fa fa-plus"></i> Agregar Nueva Experiencia
            </a>
        </div>
    </div>
</div>
<hr>



<div class="row">
    <div class="col-12">
        <div class="card card-outline-inverse">
            <div class="card-header">
                <h4 class="m-b-0 text-white">Mis Experiencias Educativas</h4>
                
            </div>
            <div class="card-body">
                <!-- Mostrar experiencia educativa -->
                <h6 class="card-subtitle">Lista de experiencias educativas registradas</h6>
                <div class="table-responsive">
                    <table class="table color-table inverse-table" style="width:100%">
                        <thead class="thead-light">
                            <tr>
                                <th>Institución</th>
                                <th>Tipo de Educación</th>
                                <th>Fecha de Inicio</th>
                                <th>Fecha de Finalización</th>
                                <th>Duración</th>
                            </tr>
                        </thead>
                        <tbody id="tablaExperienciaEducativa">
                            <!-- Las filas se generarán dinámicamente desde JavaScript -->
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card card-outline-inverse">
            <div class="card-header">
                <h4 class="m-b-0 text-white">Mis Experiencias Laborales</h4>
            </div>
            <!-- Mostrar experiencia laboral -->
            <div class="card-body">
                <h6 class="card-subtitle">Lista de experiencias laborales registradas</h6>
                <div class="table-responsive">
                    <table class="table color-bordered-table dark-bordered-table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Empresa</th>
                                <th>Puesto</th>
                                <th>Fecha de Inicio</th>
                                <th>Fecha de Finalización</th>
                            </tr>
                        </thead>
                        <tbody id="tablaExperienciaLaboral">
                            <!-- Las filas se generarán dinámicamente desde JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Botones para editar o agregar nuevas experiencias -->

            </div>
        </div>
    </div>


    <script src="/documenta/vistas/scripts/mostrar_experiencia.js"></script>
    <?php require 'layout/footer.php'; ?>

    <style>


/* Tarjetas (cards) */
.card {
    border-radius: 10px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
}

.card:hover {
    transform: scale(1.02);
    box-shadow: 0px 0px 25px rgba(0, 0, 0, 0.15);
}

    </style>
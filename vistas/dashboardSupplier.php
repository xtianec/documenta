<?php
// supplier_dashboard.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Verificar si el proveedor ha iniciado sesión
if (
    !isset($_SESSION['user_type']) ||
    $_SESSION['user_type'] !== 'supplier' ||
    !isset($_SESSION['user_role']) ||
    $_SESSION['user_role'] !== 'proveedor'
) {
    header("Location: ../../login.php"); // Asegúrate de que esta sea la URL correcta de login
    exit();
}

require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>
<!-- Contenido del Dashboard del Proveedor -->
<div class="container-fluid">
    <!-- Título y Breadcrumb -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">
                <i class="fas fa-truck-loading"></i> Dashboard Proveedor
            </h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="escritorio.php">Inicio</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row">
        <!-- Total de Documentos Subidos -->
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Documentos Subidos</h5>
                            <h3 class="font-weight-bold" id="total-documents">0</h3>
                        </div>
                        <div>
                            <i class="fas fa-file-upload fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Documentos Aprobados -->
        <div class="col-lg-3 col-md-6">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Documentos Aprobados</h5>
                            <h3 class="font-weight-bold" id="approved-documents">0</h3>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Documentos Pendientes -->
        <div class="col-lg-3 col-md-6">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Documentos Pendientes</h5>
                            <h3 class="font-weight-bold" id="pending-documents">0</h3>
                        </div>
                        <div>
                            <i class="fas fa-hourglass-half fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Documentos Rechazados -->
        <div class="col-lg-3 col-md-6">
            <div class="card bg-danger text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Documentos Rechazados</h5>
                            <h3 class="font-weight-bold" id="rejected-documents">0</h3>
                        </div>
                        <div>
                            <i class="fas fa-times-circle fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Documentos Subidos por Tipo y Documentos por Estado -->
    <div class="row mt-4">
        <!-- Gráfico de Documentos Subidos por Tipo -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Documentos Subidos por Tipo</h5>
                </div>
                <div class="card-body">
                    <canvas id="documents-type-chart" height="150"></canvas>
                </div>
            </div>
        </div>
        <!-- Gráfico de Documentos por Estado -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Documentos por Estado</h5>
                </div>
                <div class="card-body">
                    <canvas id="documents-status-chart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Progreso de Documentos Obligatorios -->
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Progreso de Documentos Obligatorios</h5>
                </div>
                <div class="card-body">
                    <div class="progress mb-3">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="mandatory-progress">0%</div>
                    </div>
                    <p id="mandatory-status">Aún no has aprobado todos los documentos obligatorios.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Documentos -->
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Mis Documentos</h5>
                    <a href="upload_document_supplier.php" class="btn btn-sm btn-primary"><i class="fas fa-upload"></i> Subir Documento</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tabla-documentos">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Nombre del Documento</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Fecha de Subida</th>
                                    <th>Observaciones</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Contenido dinámico -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Incluir Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Incluir DataTables CSS y JS -->
<!-- DataTables CSS -->

<?php
require 'layout/footer.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Tu script personalizado -->
<script src="/documenta/vistas/scripts/dashboardSupplier.js"></script>

<!-- Opcional: Personalizar colores de las tarjetas con CSS -->
<style>
    .card-title {
        font-size: 1.2rem;
        font-weight: bold;
    }
    .card-body {
        padding: 20px;
    }
    .card {
        transition: transform 0.2s ease-in-out;
    }
    .card:hover {
        transform: scale(1.02);
    }
</style>

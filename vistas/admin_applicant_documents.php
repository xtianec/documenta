<?php 
// admin/admin_applicants_documents.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario ha iniciado sesión y es superadministrador
if (
    !isset($_SESSION['user_type']) ||
    $_SESSION['user_type'] !== 'user' ||
    !isset($_SESSION['user_role']) ||
    $_SESSION['user_role'] !== 'superadmin'
) {
    header("Location: login");
    exit();
}

require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>

<!-- Contenedor Principal -->
<div class="container-fluid mt-4">
    <h3 class="text-themecolor mb-4">Revisión de Documentos de Postulantes</h3>

    <!-- Formulario de Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="filtroForm" class="form-inline">
                <!-- Filtro por Empresa -->
                <div class="form-group mb-2">
                    <label for="companySelect" class="mr-2">Empresa:</label>
                    <select class="form-control" id="companySelect" name="companySelect">
                        <option value="">Todas las Empresas</option>
                        <!-- Opciones dinámicas -->
                    </select>
                </div>
                
                <!-- Filtro por Puesto -->
                <div class="form-group mx-sm-3 mb-2">
                    <label for="positionSelect" class="mr-2">Puesto:</label>
                    <select class="form-control" id="positionSelect" name="positionSelect" disabled>
                        <option value="">Todos los Puestos</option>
                        <!-- Opciones dinámicas -->
                    </select>
                </div>

                <!-- Filtros por Fecha -->
                <div class="form-group mb-2">
                    <label for="startDate" class="mr-2">Fecha Inicio:</label>
                    <input type="date" class="form-control" id="startDate" name="startDate">
                </div>
                <div class="form-group mx-sm-3 mb-2">
                    <label for="endDate" class="mr-2">Fecha Fin:</label>
                    <input type="date" class="form-control" id="endDate" name="endDate">
                </div>
                
                <!-- Botones de Acción -->
                <button type="submit" class="btn btn-primary mb-2">Aplicar Filtro</button>
                <button type="button" id="resetFilter" class="btn btn-secondary mb-2 ml-2">Resetear Filtro</button>
            </form>
        </div>
    </div>

    <!-- DataTable de Postulantes -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="applicantsTable" class="table color-table inverse-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>Empresa</th>
                            <th>Puesto</th>
                            <th>Foto</th>
                            <th>Postulante</th>
                            <th>Email</th>
                            <th>Subidos CV</th>
                            <th>Subidos Otros</th>
                            <th>Aprobados CV</th>
                            <th>Aprobados Otros</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Se llenará dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para visualizar la imagen maximizada -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Imagen del Postulante</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="Imagen del Postulante">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Único para Mostrar Documentos y Experiencias -->
<div class="modal fade" id="modalDetallesApplicant" tabindex="-1" role="dialog" aria-labelledby="modalDetallesApplicantLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalDetallesApplicantLabel">Detalles de <span id="applicantNombre"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Pestañas para Documentos y Experiencias -->
                <ul class="nav nav-tabs" id="detailsTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="documentos-tab" data-toggle="tab" href="#documentos" role="tab" aria-controls="documentos" aria-selected="true">Documentos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="educacion-tab" data-toggle="tab" href="#educacion" role="tab" aria-controls="educacion" aria-selected="false">Experiencia Educativa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="trabajo-tab" data-toggle="tab" href="#trabajo" role="tab" aria-controls="trabajo" aria-selected="false">Experiencia Laboral</a>
                    </li>
                </ul>
                <div class="tab-content" id="detailsTabContent">
                    <!-- Tab Documentos -->
                    <div class="tab-pane fade show active" id="documentos" role="tabpanel" aria-labelledby="documentos-tab">
                        <div class="mt-3" id="documentosApplicantContainer">
                            <!-- Contenido dinámico de Documentos -->
                        </div>
                    </div>
                    <!-- Tab Experiencia Educativa -->
                    <div class="tab-pane fade" id="educacion" role="tabpanel" aria-labelledby="educacion-tab">
                        <div class="mt-3" id="educacionApplicantContainer">
                            <!-- Contenido dinámico de Experiencia Educativa -->
                        </div>
                    </div>
                    <!-- Tab Experiencia Laboral -->
                    <div class="tab-pane fade" id="trabajo" role="tabpanel" aria-labelledby="trabajo-tab">
                        <div class="mt-3" id="trabajoApplicantContainer">
                            <!-- Contenido dinámico de Experiencia Laboral -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- Botón de cerrar -->
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Librerías Necesarias -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-/xUjone5G05Q0RuvnYjS2XkT9I9yphFXHqBOwKN/oS9eR9GBI6odVjoJ3s03APbF" crossorigin="anonymous"></script>
<!-- Bootstrap CSS y JS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-LtrjvnR4aC0+Lz6NsoMEO3ub9gIMD5c6d+ZwwGOg4Y5ICQvIYcZr/ApIkxDAk8+j" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js" integrity="sha384-LtrjvnR4aC0+Lz6NsoMEO3ub9gIMD5c6d+ZwwGOg4Y5ICQvIYcZr/ApIkxDAk8+j" crossorigin="anonymous"></script>
<!-- DataTables CSS y JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css" />
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<!-- DataTables Responsive CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.bootstrap4.min.css" />

<!-- DataTables Responsive JS -->
<script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.7/js/responsive.bootstrap4.min.js"></script>

<!-- Font Awesome para Iconos -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKVBVVx1g5G2Y2q4YF1K5jj+cWxlgDqd3fD6+ZHzsE6AZOcGhfFj4r5z4TrqT6gU8j12Aw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<!-- Toastify CSS y JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Custom CSS -->

<!-- Custom JavaScript -->
<script src="/documenta/vistas/scripts/documentEvaluationApplicant.js"></script>

<?php
require 'layout/footer.php';
?>

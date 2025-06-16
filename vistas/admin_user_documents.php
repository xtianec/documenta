<?php
// vistas/admin_user_documents.php

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
    <h3 class="text-themecolor mb-4">Revisión de Documentos de Usuarios</h3>

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

    <!-- DataTable de Usuarios -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="usuariosTable" class="table color-table inverse-table" style="width:100%">
                    <thead>
                        <tr>
                            <th width="15%">Empresa</th>
                            <th width="15%">Puesto</th>
                            <th width="15%">Nombre</th>                           
                            <th width="10%">Apellido</th>
                            <th width="15%">Foto</th>
                            <th width="5%">Subidos Obligatorios (%)</th>
                            <th width="5%">Subidos Opcionales (%)</th>
                            <th width="5%">Aprobados Obligatorios (%)</th>
                            <th width="5%">Aprobados Opcionales (%)</th>
                            <th width="10%">Acción</th>
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

<!-- Modal para mostrar documentos subidos del usuario -->
<div class="modal fade" id="modalDocumentosUsuario" tabindex="-1" role="dialog" aria-labelledby="modalDocumentosUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white font-weight-bold">
                <h5 class="modal-title" id="modalDocumentosUsuarioLabel">Documentos Subidos por <span id="usuarioNombre"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Tabs para documentos obligatorios y opcionales -->
                <ul class="nav nav-tabs mb-3" id="documentTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="obligatorios-tab" data-toggle="tab" href="#obligatorios" role="tab" aria-controls="obligatorios" aria-selected="true">Documentos Obligatorios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="opcionales-tab" data-toggle="tab" href="#opcionales" role="tab" aria-controls="opcionales" aria-selected="false">Documentos Opcionales</a>
                    </li>
                </ul>

                <div class="tab-content" id="documentTabsContent">
                    <div class="tab-pane fade show active" id="obligatorios" role="tabpanel" aria-labelledby="obligatorios-tab">
                        <div id="documentosObligatoriosContainer">
                            <!-- Se llenará dinámicamente con JavaScript -->
                        </div>
                    </div>
                    <div class="tab-pane fade" id="opcionales" role="tabpanel" aria-labelledby="opcionales-tab">
                        <div id="documentosOpcionalesContainer">
                            <!-- Se llenará dinámicamente con JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- No es necesario el botón de guardar observación aquí -->
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mostrar imagen ampliada -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body p-0">
                <img src="" id="modalImage" class="img-fluid" alt="Imagen">
            </div>
        </div>
    </div>
</div>

<!-- Librerías Necesarias -->
<!-- CSS -->
<!-- Bootstrap CSS -->


<style>
    #usuariosTable thead th {
        background-color: #343a40;
        color: white;
        text-align: center;
        padding: 20px;
    }
</style>

<!-- Custom JavaScript -->
<script src="/documenta/vistas/scripts/user_documents.js"></script>

<?php
require 'layout/footer.php';
?>

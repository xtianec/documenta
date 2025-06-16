<?php
// superadmin_dashboard.php

session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'user' || $_SESSION['user_role'] !== 'superadmin') {
    header("Location: ../login.php");
    exit();
}

require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>


<!-- Lista de proveedores -->
<div class="container-fluid mt-4">
    <h3 class="text-themecolor mb-4">Revisión de Documentos de Proveedores</h3>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="proveedoresTable" class="table color-table inverse-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>RUC</th>
                            <th>Nombre del Proveedor</th>
                            <th>Email</th>
                            <th>Documentos Subidos</th>
                            <th>Documentos Aprobados</th>
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

<!-- Modal para mostrar documentos subidos del proveedor -->
<div class="modal fade" id="modalDocumentosProveedor" tabindex="-1" role="dialog" aria-labelledby="modalDocumentosProveedorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white font-weight-bold">
                <h5 class="modal-title" id="modalDocumentosProveedorLabel">Documentos Subidos por <span id="proveedorNombre"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="documentosProveedorContainer">
                    <!-- Se llenará dinámicamente con JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Librerías Necesarias -->

<style>
    #proveedoresTable thead th {
        background-color: #343a40;
        /* Dark color for the background */
        color: white;
        /* White text color */
        text-align: center;
        /* Center align text */
        padding: 10px;
        /* Adjust padding for better spacing */
    }
</style>

<script src="/documenta/vistas/scripts/proveedores.js"></script>

<?php
require 'layout/footer.php';
?>
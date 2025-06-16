<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Verificar si el usuario ha iniciado sesión y es un adminpr (Administrador de Proveedores) o superadmin
if (
    !isset($_SESSION['user_type']) ||
    $_SESSION['user_type'] !== 'user' ||
    !isset($_SESSION['user_role']) ||
    !in_array($_SESSION['user_role'], ['superadmin', 'adminpr']) // Permitir 'superadmin' o 'adminpr'
) {
    header("Location: login"); // Asegúrate de que esta sea la URL correcta de login
    exit();
}
// No se requiere session_start() ya que no hay gestión de usuarios
require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>
<!-- Contenido Principal -->
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Gestión de Empresas, Áreas y Puestos</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                <li class="breadcrumb-item">Configuración</li>
                <li class="breadcrumb-item active">Empresas, Áreas y Puestos</li>
            </ol>
        </div>
    </div>

    <!-- Tabulador para Empresas, Áreas y Puestos -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="companies-tab" data-toggle="tab" href="#companies" role="tab" aria-controls="companies" aria-selected="true">Empresas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="areas-tab" data-toggle="tab" href="#areas" role="tab" aria-controls="areas" aria-selected="false">Áreas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="jobs-tab" data-toggle="tab" href="#jobs" role="tab" aria-controls="jobs" aria-selected="false">Puestos</a>
                        </li>
                    </ul>

                    <!-- Contenido de los Tabs -->
                    <div class="tab-content" id="myTabContent">
                        <!-- Tab de Empresas -->
                        <div class="tab-pane fade show active" id="companies" role="tabpanel" aria-labelledby="companies-tab">
                            <div class="mt-4">
                                <button class="btn btn-success mb-3" data-toggle="modal" data-target="#formulario">
                                    <i class="fa fa-plus"></i> Agregar Empresa
                                </button>

                                <!-- Tabla para listar empresas -->
                                <div class="table-responsive">
                                    <table id="tbllistadoCompanies" class="table color-table inverse-table" style="width:100%">
                                        <thead style="background-color: #2A3E52; color: white;">
                                            <tr>
                                                <th width="5%">ID</th>
                                                <th width="20%">Empresa</th>
                                                <th width="10%">RUC</th>
                                                <th width="25%">Descripción</th>
                                                <th width="10%">F. Creación</th>
                                                <th width="10%">F. Actualización</th>
                                                <th width="10%">Estado</th>
                                                <th width="10%">Opciones</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Tab de Áreas -->
                        <div class="tab-pane fade" id="areas" role="tabpanel" aria-labelledby="areas-tab">
                            <div class="mt-4">
                                <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#formularioArea">
                                    <i class="fa fa-plus"></i> Agregar Área
                                </button>

                                <!-- Tabla para listar áreas -->
                                <div class="table-responsive">
                                    <table id="tbllistadoAreas" class="table color-table inverse-table" style="width:100%">
                                        <thead style="background-color: #2A3E52; color: white;">
                                            <tr>
                                                <th width="5%">ID</th>
                                                <th width="25%">Área</th>
                                                <th width="25%">Empresa</th>
                                                <th width="10%">Estado</th>
                                                <th width="35%">Opciones</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Tab de Puestos -->
                        <div class="tab-pane fade" id="jobs" role="tabpanel" aria-labelledby="jobs-tab">
                            <div class="mt-4">
                                <!-- Filtros y Botón de Agregar -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Filtros de Búsqueda</h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="filtersForm">
                                            <div class="form-row">
                                                <!-- Selector de Empresa -->
                                                <div class="form-group col-md-4">
                                                    <label for="filter_company">Empresa</label>
                                                    <select id="filter_company" class="form-control">
                                                        <option value="">Todas las Empresas</option>
                                                        <?php
                                                        // Listar empresas activas
                                                        require_once "../modelos/Companies.php";
                                                        $companies = new Companies();
                                                        $rspta = $companies->select();
                                                        while ($reg = $rspta->fetch_object()) {
                                                            echo '<option value="' . $reg->id . '">' . htmlspecialchars($reg->company_name) . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <!-- Selector de Área -->
                                                <div class="form-group col-md-4">
                                                    <label for="filter_area">Área</label>
                                                    <select id="filter_area" class="form-control">
                                                        <option value="">Todas las Áreas</option>
                                                        <!-- Las áreas se cargarán dinámicamente según la empresa seleccionada -->
                                                    </select>
                                                </div>

                                                <!-- Selector de Puesto de Trabajo -->
                                                <div class="form-group col-md-4">
                                                    <label for="filter_job">Puesto de Trabajo</label>
                                                    <select id="filter_job" class="form-control">
                                                        <option value="">Todos los Puestos</option>
                                                        <!-- Los puestos se cargarán dinámicamente según el área seleccionada -->
                                                    </select>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-footer text-right">
                                        <button class="btn btn-success" data-toggle="modal" data-target="#formularioJob">
                                            <i class="fa fa-plus"></i> Agregar Puesto de Trabajo
                                        </button>
                                    </div>
                                </div>

                                <!-- Tabla para listar puestos -->
                                <div class="table-responsive">
                                    <table id="tbllistadoJobs" class="table color-table inverse-table" style="width:100%">
                                        <thead style="background-color: #2A3E52; color: white;">
                                            <tr>
                                                <th width="5%">ID</th>
                                                <th width="25%">Puesto</th>
                                                <th width="20%">Área</th>
                                                <th width="20%">Empresa</th>
                                                <th width="10%">Estado</th>
                                                <th width="20%">Opciones</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modales -->
                    <!-- Modal para agregar empresa -->
                    <div class="modal fade" id="formulario" tabindex="-1" role="dialog" aria-labelledby="tituloModalGuardar" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title" id="tituloModalGuardar">Agregar Empresa</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <form id="formularioEmpresa" method="POST" class="form-material">
                                        <div class="form-group">
                                            <label for="company_name">Empresa <span class="text-danger">*</span></label>
                                            <input type="text" id="company_name" name="company_name" class="form-control" maxlength="255" placeholder="Nombre de la empresa" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="ruc">RUC <span class="text-danger">*</span></label>
                                            <input type="text" id="ruc" name="ruc" class="form-control" maxlength="20" placeholder="Número de RUC" required pattern="\d{11}" title="Ingrese un RUC válido de 11 dígitos">
                                            <small id="rucHelp" class="form-text text-muted">Ingrese un RUC de 11 dígitos.</small>
                                            <div id="rucFeedback" class="invalid-feedback">
                                                RUC ya existe. Por favor, ingrese un RUC diferente.
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Descripción <span class="text-danger">*</span></label>
                                            <textarea id="description" name="description" class="form-control" rows="3" placeholder="Descripción de la empresa" required></textarea>
                                        </div>
                                        <!-- Botones -->
                                        <button type="button" class="btn btn-success" onclick="guardarEmpresa();">
                                            <i class="fa fa-check"></i> Guardar
                                        </button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            <i class="fa fa-times"></i> Cancelar
                                        </button>
                                    </form>
                                </div>
                                <div class="modal-footer"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal para agregar área -->
                    <div class="modal fade" id="formularioArea" tabindex="-1" role="dialog" aria-labelledby="tituloModalArea" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="tituloModalArea">Agregar Área</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <form id="formularioAreaForm" method="POST" class="form-material">
                                        <div class="form-group">
                                            <label for="company_id">Empresa <span class="text-danger">*</span></label>
                                            <select id="company_id" name="company_id" class="form-control" required>
                                                <option value="">Seleccione una empresa</option>
                                                <?php
                                                // Listar empresas activas
                                                require_once "../modelos/Companies.php";
                                                $companies = new Companies();
                                                $rspta = $companies->select();
                                                while ($reg = $rspta->fetch_object()) {
                                                    echo '<option value="' . $reg->id . '">' . htmlspecialchars($reg->company_name) . '</option>';
                                                }
                                                ?>
                                            </select>

                                        </div>
                                        <div class="form-group">
                                            <label for="area_name">Área <span class="text-danger">*</span></label>
                                            <input type="text" id="area_name" name="area_name" class="form-control" maxlength="255" placeholder="Nombre del área" required>
                                        </div>



                                        <!-- Botones -->
                                        <button type="button" class="btn btn-primary" onclick="guardarArea();">
                                            <i class="fa fa-check"></i> Guardar
                                        </button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">
                                            <i class="fa fa-times"></i> Cancelar
                                        </button>
                                    </form>
                                </div>
                                <div class="modal-footer"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal para agregar puesto de trabajo -->
                    <div class="modal fade" id="formularioJob" tabindex="-1" role="dialog" aria-labelledby="tituloModalJob" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-secondary text-white">
                                    <h5 class="modal-title" id="tituloModalJob">Agregar Puesto de Trabajo</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <form id="formularioJobForm" method="POST" class="form-material">
                                        <div class="form-group">
                                            <label for="company_select">Empresa <span class="text-danger">*</span></label>
                                            <select id="company_select" name="company_select" class="form-control" required>
                                                <option value="">Seleccione una empresa</option>
                                                <?php
                                                // Listar empresas activas
                                                require_once "../modelos/Companies.php";
                                                $companies = new Companies();
                                                $rspta_companies = $companies->select();
                                                while ($reg_comp = $rspta_companies->fetch_object()) {
                                                    echo '<option value="' . $reg_comp->id . '">' . htmlspecialchars($reg_comp->company_name) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="area_select">Área <span class="text-danger">*</span></label>
                                            <select id="area_select" name="area_select" class="form-control" required>
                                                <option value="">Seleccione un área</option>
                                                <!-- Las áreas se cargarán dinámicamente según la empresa seleccionada -->
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="position_name">Puesto de Trabajo <span class="text-danger">*</span></label>
                                            <input type="text" id="position_name" name="position_name" class="form-control" maxlength="255" placeholder="Nombre del puesto" required>
                                            <div id="positionFeedback" class="invalid-feedback">
                                                Puesto ya existe en esta área. Por favor, ingrese un nombre diferente.
                                            </div>
                                        </div>

                                        <!-- Botones -->
                                        <button type="button" class="btn btn-secondary" onclick="guardarJob();">
                                            <i class="fa fa-check"></i> Guardar
                                        </button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">
                                            <i class="fa fa-times"></i> Cancelar
                                        </button>
                                    </form>
                                </div>
                                <div class="modal-footer"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal para actualizar empresa -->
                    <div class="modal fade" id="formularioActualizar" tabindex="-1" role="dialog" aria-labelledby="tituloModalActualizar" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title" id="tituloModalActualizar">Actualizar Empresa</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <form id="formularioActualizarEmpresa" method="POST" class="form-material">
                                        <div class="form-group">
                                            <label for="company_nameUpdate">Empresa <span class="text-danger">*</span></label>
                                            <input type="text" id="company_nameUpdate" name="company_nameUpdate" class="form-control" maxlength="255" placeholder="Nombre de la empresa" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="rucUpdate">RUC <span class="text-danger">*</span></label>
                                            <input type="text" id="rucUpdate" name="rucUpdate" class="form-control" maxlength="20" placeholder="Número de RUC" required pattern="\d{11}" title="Ingrese un RUC válido de 11 dígitos">
                                            <small id="rucUpdateHelp" class="form-text text-muted">Ingrese un RUC de 11 dígitos.</small>
                                            <div id="rucUpdateFeedback" class="invalid-feedback">
                                                RUC ya existe. Por favor, ingrese un RUC diferente.
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="descriptionUpdate">Descripción <span class="text-danger">*</span></label>
                                            <textarea id="descriptionUpdate" name="descriptionUpdate" class="form-control" rows="3" placeholder="Descripción de la empresa" required></textarea>
                                        </div>
                                        <input type="hidden" id="idUpdate" name="idUpdate">
                                        <!-- Botones -->
                                        <button type="button" class="btn btn-success" onclick="actualizarEmpresa();">
                                            <i class="fa fa-check"></i> Guardar Cambios
                                        </button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            <i class="fa fa-times"></i> Cancelar
                                        </button>
                                    </form>
                                </div>
                                <div class="modal-footer"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal para actualizar área -->
                    <div class="modal fade" id="formularioActualizarArea" tabindex="-1" role="dialog" aria-labelledby="tituloModalActualizarArea" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="tituloModalActualizarArea">Actualizar Área</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <form id="formularioActualizarAreaForm" method="POST" class="form-material">
                                        <div class="form-group">
                                            <label for="company_idUpdate">Empresa <span class="text-danger">*</span></label>
                                            <select id="company_idUpdate" name="company_idUpdate" class="form-control" required>
                                                <option value="">Seleccione una empresa</option>
                                                <?php
                                                // Listar empresas activas
                                                require_once "../modelos/Companies.php";
                                                $companies = new Companies();
                                                $rspta = $companies->select();
                                                while ($reg = $rspta->fetch_object()) {
                                                    echo '<option value="' . $reg->id . '">' . htmlspecialchars($reg->company_name) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="area_nameUpdate">Área <span class="text-danger">*</span></label>
                                            <input type="text" id="area_nameUpdate" name="area_nameUpdate" class="form-control" maxlength="255" placeholder="Nombre del área" required>
                                        </div>

                                        <input type="hidden" id="area_idUpdate" name="area_idUpdate">
                                        <!-- Botones -->
                                        <button type="button" class="btn btn-primary" onclick="actualizarArea();">
                                            <i class="fa fa-check"></i> Guardar Cambios
                                        </button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">
                                            <i class="fa fa-times"></i> Cancelar
                                        </button>
                                    </form>
                                </div>
                                <div class="modal-footer"></div>
                            </div>
                        </div>
                    </div>


                    <!-- Modal para actualizar puesto de trabajo -->
                    <div class="modal fade" id="formularioActualizarJob" tabindex="-1" role="dialog" aria-labelledby="tituloModalActualizarJob" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-secondary text-white">
                                    <h5 class="modal-title" id="tituloModalActualizarJob">Actualizar Puesto de Trabajo</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <form id="formularioActualizarJobForm" method="POST" class="form-material">
                                        <div class="form-group">
                                            <label for="company_selectUpdate">Empresa <span class="text-danger">*</span></label>
                                            <select id="company_selectUpdate" name="company_selectUpdate" class="form-control" required>
                                                <option value="">Seleccione una empresa</option>
                                                <?php
                                                // Listar empresas activas
                                                require_once "../modelos/Companies.php";
                                                $companies = new Companies();
                                                $rspta_companies = $companies->select();
                                                while ($reg_comp = $rspta_companies->fetch_object()) {
                                                    echo '<option value="' . $reg_comp->id . '">' . htmlspecialchars($reg_comp->company_name) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="area_selectUpdate">Área <span class="text-danger">*</span></label>
                                            <select id="area_selectUpdate" name="area_selectUpdate" class="form-control" required>
                                                <option value="">Seleccione un área</option>
                                                <!-- Las áreas se cargarán dinámicamente según la empresa seleccionada -->
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="position_nameUpdate">Puesto de Trabajo <span class="text-danger">*</span></label>
                                            <input type="text" id="position_nameUpdate" name="position_nameUpdate" class="form-control" maxlength="255" placeholder="Nombre del puesto" required>
                                            <div id="positionUpdateFeedback" class="invalid-feedback">
                                                Puesto ya existe en esta área. Por favor, ingrese un nombre diferente.
                                            </div>
                                        </div>

                                        <input type="hidden" id="job_idUpdate" name="job_idUpdate">
                                        <!-- Botones -->
                                        <button type="button" class="btn btn-secondary" onclick="actualizarJob();">
                                            <i class="fa fa-check"></i> Guardar Cambios
                                        </button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">
                                            <i class="fa fa-times"></i> Cancelar
                                        </button>
                                    </form>
                                </div>
                                <div class="modal-footer"></div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <!-- Carga de scripts -->
    <?php
    require 'layout/footer.php';
    ?>
    <script src="/documenta/vistas/scripts/companies.js"></script>
    <script src="/documenta/vistas/scripts/areas.js"></script>
    <script src="/documenta/vistas/scripts/jobs.js"></script>
</div>
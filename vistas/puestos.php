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

<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h3 class="text-themecolor">Gestionar Puestos</h3>
    </div>
    <div class="col-md-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item">Configuración</li>
            <li class="breadcrumb-item active">Puestos</li>
        </ol>
    </div>
    <div>
        <button class="right-side-toggle waves-effect waves-light btn-inverse btn btn-circle btn-sm pull-right m-l-10">
            <i class="ti-settings text-white"></i>
        </button>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div id="accordionBasic" class="widget-header">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                            <h4>Administrar Puestos</h4>
                        </div>
                    </div>
                </div>
                <!-- Botón para agregar puestos -->
                <div class="text-left">
                    <button type="button" class="btn btn-primary mb-2 mr-2" data-toggle="modal" data-target="#formularioregistros">
                        Agregar Puesto
                    </button>
                </div>
                <div class="widget-content widget-content-area">
                    <div class="table-responsive mb-4 mt-4" id="listadoregistros">
                        <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
                            <thead style="background-color: #2A3E52; color: white;">
                                <tr>
                                    <th>ID</th>
                                    <th>Empresa</th>
                                    <th>Puesto</th>
                                    <th>F. Creación</th>
                                    <th>F. Actualización</th>
                                    <th>Estado</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal para guardar puesto -->
                <div class="modal fade" id="formularioregistros" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Agregar Puesto</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>
                            </div>

                            <div class="modal-body">
                                <form id="formulario" method="POST">
                                    <div class="row">
                                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                            <label for="company_id"> Empresa</label>
                                            <select id="company_id" class="select2 form-control custom-select" style="width:100%; height:100%;"></select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                            <label for="position_name"> Puesto</label>
                                            <input class="form-control" type="text" id="position_name" name="position_name" maxlength="50" placeholder="Nombre del puesto" required>
                                        </div>
                                    </div>
                                    <!-- Botón convertido a type="button" -->
                                    <button type="button" class="btn btn-primary" onclick="guardar();"><i class="fa fa-check"></i> Guardar</button>
                                    <button class="btn btn-danger" data-dismiss="modal"><i class="flaticon-cancel-12"></i>Cancelar</button>
                                </form>
                            </div>
                            <div class="modal-footer"></div>
                        </div>
                    </div>
                </div>

                <!-- Modal para actualizar puesto -->
                <div class="modal fade" id="formularioActualizar" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Actualizar Puesto</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>
                            </div>

                            <div class="modal-body">
                                <form id="formActualizar" method="POST">
                                    <div class="row">
                                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                            <label for="company_idUpdate"> Empresa</label>
                                            <select id="company_idUpdate" class="select2 form-control custom-select" style="width:100%; height:100%;"></select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                            <label for="position_nameUpdate"> Puesto</label>
                                            <input class="form-control" type="text" id="position_nameUpdate" name="position_nameUpdate" maxlength="50" placeholder="Nombre del puesto" required>
                                        </div>
                                    </div>
                                    <!-- Botón convertido a type="button" -->
                                    <button type="button" class="btn btn-primary" onclick="Actualizar();"><i class="fa fa-check"></i> Guardar Cambios</button>
                                    <button class="btn btn-danger" data-dismiss="modal"><i class="flaticon-cancel-12"></i>Cancelar</button>
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
<script src="/documenta/vistas/scripts/job_positions.js"></script>

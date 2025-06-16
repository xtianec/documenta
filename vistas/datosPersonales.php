<?php
require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>

<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h3 class="text-themecolor">Mis Datos Personales</h3>
    </div>
    <div class="col-md-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item">Configuración</li>
            <li class="breadcrumb-item active">Mis Datos Personales</li>
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

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header bg-info">
                                <h4 class="m-b-0 text-white">DATOS PERSONALES</h4>
                            </div>
                            <div class="card-body">
                                <form class="form-material m-t-40" action="#">
                                    <div class="form-body">
                                        <h3 class="card-title">MI INFORMACIÓN PERSONAL</h3>
                                        <hr>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">DNI</label>
                                                    <input type="text" id="firstName" class="form-control" placeholder="000000000">
                                                    <small class="form-control-feedback"> Escribir su DNI </small>
                                                </div>
                                            </div>
                                            <!--/span-->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">APELLIDOS Y NOMBRES</label>
                                                    <input type="text" id="lastName" class="form-control" placeholder="Apellidos y Nombres">

                                                </div>
                                                <!--/span-->
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Correo</label>
                                                        <input type="text" id="firstName" class="form-control" placeholder="Escriba su correo">
                                                        <small class="form-control-feedback"> Escribir su Correo </small>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Número Personal</label>
                                                        <input type="text" id="lastName" class="form-control" placeholder="Escribir su Numero Personal">
                                                    </div>
                                                </div>
                                                <!--/span-->
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Número de Emergencia</label>
                                                        <input type="text" id="lastName" class="form-control" placeholder="Escribir Numero de un familiar">

                                                    </div>
                                                    <!--/span-->
                                                </div>
                                                <!--/row-->
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label">Puesto</label>
                                                            <select class="form-control custom-select">
                                                                <option value="">Analista</option>
                                                                <option value="">Ejecutivo Comercial</option>
                                                                <option value="">Operario</option>
                                                            </select>
                                                            <small class="form-control-feedback"> Escriba o Seleccione el Puesto </small>
                                                        </div>
                                                    </div>
                                                    <!--/span-->
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label">Fecha de Ingreso</label>
                                                            <input type="date" class="form-control" placeholder="dd/mm/yyyy">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label">Fecha de Nacimiento</label>
                                                            <input type="date" class="form-control" placeholder="dd/mm/yyyy">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!--/row-->
                                                </d iv>
                                                <!--/row-->
                                                
                                            </div>
                                            <div class="form-actions">
                                                <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Save</button>
                                                <button type="button" class="btn btn-inverse">Cancel</button>
                                            </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <script src="/documenta/vistas/scripts/companies.js"></script>
            </div>
        </div>
    </div>
</div>

<?php
require 'layout/footer.php';
?>
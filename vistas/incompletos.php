<?php
require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>

<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h3 class="text-themecolor">Documentos Faltantes Obligatorios por Trabajador</h3>
    </div>
    <div class="col-md-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item">Configuración</li>
            <li class="breadcrumb-item active">Documentos Faltantes Obligatorios</li>
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


                <div class="table-responsive mb-4 mt-4">
                    <table id="tbllistado" class="table table-hover non-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>DNI</th>
                                <th>Apellidos Y Nombres</th>
                                <th>Puesto</th>
                                <th>Tipo Documento</th>
                                <th>Nombre Documento</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>44444444</th>
                                <th>Juan Perez</th>
                                <th>Maquinista</th>
                                <th>Obligatorio</th>
                                <th>DNI</th>
                                <th>Documento Faltante</th>
                                <th><span class="label label-info">Enviar Correo</span>
                            </tr>
                            <tr>
                                <th>44444444</th>
                                <th>Juan Perez</th>
                                <th>Maquinista</th>
                                <th>Obligatorio</th>
                                <th>Certificado Unico Laboral</th>
                                <th>Documento Faltante</th>
                                <th><span class="label label-info">Enviar Correo</span>
                            </tr>
                            <tr>
                                <th>44444444</th>
                                <th>Juan Perez</th>
                                <th>Maquinista</th>
                                <th>Obligatorio</th>
                                <th>Declaración Jurada de Domicilio</th>
                                <th>Documento Faltante</th>
                                <th><span class="label label-info">Enviar Correo</span>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Modal para Agregar/Editar Celular -->
                <div class="modal fade" id="formularioregistros" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Agregar Usuario</h5>
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
                                            <label for="">User</label>
                                            <input class="form-control" type="hidden" id="id" name="id">
                                            <input class="form-control" type="text" id="company_name" name="company_name" placeholder="User" required>
                                        </div>
                                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                            <label for="">Password</label>
                                            <input class="form-control" type="text" id="is_active" name="is_active" placeholder="Password" required>
                                        </div>
                                        <div class="form-group col-lg-12 col-md-12 col-xs-12">

                                            <label for="is_active">Perfil</label>
                                            <select class="form-control" id="is_active" name="is_active" required>
                                                <option value="">Seleccione su Perfil</option> <!-- Opción predeterminada vacía -->
                                                <option value="activo">Administrador</option>
                                                <option value="de_baja">Usuario</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                            <label for="">email</label>
                                            <input class="form-control" type="text" id="is_active" name="is_active" placeholder="email" required>
                                        </div>
                                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                            <label for="">DNI</label>
                                            <input class="form-control" type="text" id="is_active" name="is_active" placeholder="DNI" required>
                                        </div>
                                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                            <label for="">Nombres</label>
                                            <input class="form-control" type="text" id="is_active" name="is_active" placeholder="Nombres" required>
                                        </div>
                                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                            <label for="">Apellidos</label>
                                            <input class="form-control" type="text" id="is_active" name="is_active" placeholder="Apellidos" required>
                                        </div>
                                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                            <label for="is_active">Estado</label>
                                            <select class="form-control" id="is_active" name="is_active" required>
                                                <option value="">Seleccione un estado</option> <!-- Opción predeterminada vacía -->
                                                <option value="activo">Activo</option>
                                                <option value="de_baja">De Baja</option>
                                            </select>
                                        </div>


                                    </div>
                                    <button type="submit" class="btn btn-primary" onclick="guardar();"><i class="fa fa-check"></i>Guardar</button>
                                    <button class="btn btn-danger" data-dismiss="modal"><i class="flaticon-cancel-12"></i>Cancelar</button>

                                    </button>
                                </form>
                            </div>
                            <div class="modal-footer"></div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="formularioActualizar" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Actualizar Empresa</h5>
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
                                            <label for="">Nombre Empresa</label>
                                            <input class="form-control" type="hidden" id="idUpdate" name="idUpdate">
                                            <input class="form-control" type="text" id="company_nameUpdate" name="company_nameUpdate" placeholder="Nombre" required>
                                        </div>
                                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                            <label for="">Estado Empresa</label>
                                            <input class="form-control" type="text" id="is_activeUpdate" name="is_activeUpdate" placeholder="Estado" required>
                                        </div>


                                    </div>
                                    <button type="submit" class="btn btn-primary" onclick="actualizar();"><i class="fa fa-check"></i>Actualizar</button>
                                    <button class="btn btn-danger" data-dismiss="modal"><i class="flaticon-cancel-12"></i>Cancelar</button>
                                </form>
                            </div>
                            <div class="modal-footer"></div>
                        </div>
                    </div>
                </div>

                <script src="scripts/companies.js"></script>
            </div>
        </div>
    </div>
</div>

<?php
require 'layout/footer.php';
?>
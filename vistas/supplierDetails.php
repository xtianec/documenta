<?php
// supplier_dashboard.php

session_start();

// Verificar si el usuario ha iniciado sesión y es un proveedor
if (
    !isset($_SESSION['user_type']) ||
    $_SESSION['user_type'] !== 'supplier' ||
    $_SESSION['user_role'] !== 'proveedor'
) {
    header("Location: login_suplier"); // Asegúrate de que esta sea la URL correcta de login
    exit();
}

require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>


<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h3 class="text-themecolor">Registrar/Actualizar Detalles del Proveedor</h3>
    </div>
    <div class="col-md-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item">Proveedores</li>
            <li class="breadcrumb-item active">Registrar/Actualizar Detalles</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <!-- Formulario para registrar detalles del proveedor -->
                <form class="form-material m-t-40" id="formSupplierDetailsRegister" method="POST" style="display:none;">
                    <div class="card shadow-sm border rounded-lg" style="border-color: #17a2b8; border-radius: 15px;">
                        <div class="card-body p-4">
                            <h4 class="text-info mb-4 font-weight-bold text-center"><i class="fa fa-user-plus"></i> Registrar Detalles del Proveedor</h4>

                            <!-- Fila 1: Nombre del Contacto y Email del Contacto -->
                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6 col-xs-6">
                                    <label for="contactNameAccouting" class="font-weight-bold"><i class="fa fa-user"></i> Nombre del Contacto de Contabilidad y Finanzas</label>
                                    <input type="text" class="form-control rounded-lg" id="contactNameAccouting" name="contactNameAccouting" maxlength="255" placeholder="Nombre del contacto de Contabilidad y Finanzas" required>
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-xs-6">
                                    <label for="contactEmailAccouting" class="font-weight-bold"><i class="fa fa-envelope"></i> Email del Contacto de Contabilidad y Finanzas</label>
                                    <input type="email" class="form-control rounded-lg" id="contactEmailAccouting" name="contactEmailAccouting" maxlength="255" placeholder="Email del contacto de Contabilidad y Finanzas" required>
                                </div>
                            </div>

                            <!-- Fila 2: Teléfono del Contacto y Proveedor -->
                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6 col-xs-6">
                                    <label for="contactPhoneAccouting" class="font-weight-bold"><i class="fa fa-phone"></i> Teléfono del Contacto de Contabilidad y Finanzas</label>
                                    <input type="text" class="form-control rounded-lg" id="contactPhoneAccouting" name="contactPhoneAccouting" maxlength="15" placeholder="Teléfono del contacto de Contabilidad y Finanzas" required>
                                </div>

                                <div class="form-group col-lg-6 col-md-6 col-xs-6">
                                    <label for="Provide" class="font-weight-bold"><i class="fa fa-building"></i>Que es lo que proveo</label>
                                    <input type="text" class="form-control rounded-lg" id="Provide" name="Provide" maxlength="255" placeholder="Producto/Servicio" required>
                                </div>
                            </div>

                            <!-- Botón de Envío -->
                            <button type="submit" class="btn btn-primary btn-block rounded-lg font-weight-bold">
                                <i class="fa fa-save"></i> Guardar Detalles
                            </button>

                        </div>
                    </div>
                </form>

                <!-- Mostrar los detalles registrados del proveedor -->
                <div class="row justify-content-center">
                    <div class="col-8">
                        <div id="datosRegistrados" style="display:none;">
                            <div class="card shadow-sm border rounded-lg" style="border-color: #17a2b8; border-radius: 15px;">
                                <div class="card-body p-4">
                                    <h2 class="text-info mb-4 font-weight-bold text-center">
                                        <i class="fa fa-user-check"></i> Detalles Registrados del Proveedor
                                    </h2>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-bottom-0">
                                            <div class="d-flex align-items-center">
                                                <i class="fa fa-user fa-lg text-info mr-3"></i>
                                                <span><strong>Nombre del Contacto de Contabilidad y Finanzas:</strong></span>
                                            </div>
                                            <span id="verContactName" class="text-muted"></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-bottom-0">
                                            <div class="d-flex align-items-center">
                                                <i class="fa fa-envelope fa-lg text-info mr-3"></i>
                                                <span><strong>Email del Contacto de Contabilidad y Finanzas:</strong></span>
                                            </div>
                                            <span id="verContactEmail" class="text-muted"></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-bottom-0">
                                            <div class="d-flex align-items-center">
                                                <i class="fa fa-phone fa-lg text-info mr-3"></i>
                                                <span><strong>Teléfono del Contacto de Contabilidad y Finanzas:</strong></span>
                                            </div>
                                            <span id="verContactPhone" class="text-muted"></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-bottom-0">
                                            <div class="d-flex align-items-center">
                                                <i class="fa fa-building fa-lg text-info mr-3"></i>
                                                <span><strong>Que es lo que proveo:</strong></span>
                                            </div>
                                            <span id="verProvide" class="text-muted"></span>
                                        </li>
                                    </ul>
                                    <button id="btnEditarDetalles" class="btn btn-info btn-lg btn-block rounded-lg font-weight-bold" style="border: none; padding: 12px 0;">
                                        <i class="fa fa-edit"></i> Editar Detalles
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario para actualizar los detalles del proveedor -->
                <form class="form-material m-t-40" id="formSupplierDetailsUpdate" method="POST" style="display:none;">
                    <div class="card shadow-sm border rounded-lg" style="border-color: #17a2b8; border-radius: 15px;">
                        <div class="card-body p-4">
                            <h2 class="text-info mb-4 font-weight-bold text-center"><i class="fa fa-user-edit"></i> Actualizar Detalles del Proveedor</h2>

                            <!-- Fila 1: Nombre del Contacto y Email del Contacto -->
                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6 col-xs-6">
                                    <label for="contactNameAccoutingUpdate" class="font-weight-bold"><i class="fa fa-user"></i> Nombre del Contacto de Contabilidad y Finanzas</label>
                                    <input type="text" class="form-control rounded-lg" id="contactNameAccoutingUpdate" name="contactNameAccoutingUpdate" maxlength="255">
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-xs-6">
                                    <label for="contactEmailAccoutingUpdate" class="font-weight-bold"><i class="fa fa-envelope"></i> Email del Contacto de Contabilidad y Finanzas</label>
                                    <input type="email" class="form-control rounded-lg" id="contactEmailAccoutingUpdate" name="contactEmailAccoutingUpdate" maxlength="255">
                                </div>
                            </div>

                            <!-- Fila 2: Teléfono del Contacto y Proveedor -->
                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6 col-xs-6">
                                    <label for="contactPhoneAccoutingUpdate" class="font-weight-bold"><i class="fa fa-phone"></i> Teléfono del Contacto de Contabilidad y Finanzas</label>
                                    <input type="text" class="form-control rounded-lg" id="contactPhoneAccoutingUpdate" name="contactPhoneAccoutingUpdate" maxlength="15">
                                </div>

                                <div class="form-group col-lg-6 col-md-6 col-xs-6">
                                    <label for="ProvideUpdate" class="font-weight-bold"><i class="fa fa-building"></i>Que es lo que proveo</label>
                                    <input type="text" class="form-control rounded-lg" id="ProvideUpdate" name="ProvideUpdate" maxlength="255">
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="row justify-content-center">
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block rounded-lg font-weight-bold">
                                        <i class="fas fa-sync-alt"></i> Actualizar Detalles
                                    </button>
                                </div>

                                <div class="col-md-2">
                                    <button type="button" id="btnBackToDetails" class="btn btn-secondary btn-lg btn-block rounded-lg font-weight-bold">
                                        <i class="fa fa-arrow-left"></i> Regresar a Detalles
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require 'layout/footer.php';
?>

<script src="/documenta/vistas/scripts/supplierDetails.js"></script>

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

            <div class="row">
                <!-- Card 1 -->
                <div class="col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Documentos Obligatorios</h4>
                            <label for="input-file-now">DNI</label>
                            <div class="dropify-wrapper">
                                <div class="dropify-message"><span class="file-icon"></span>
                                    <p>DNI (Solo PDF o Imágenes)</p>
                                    <p class="dropify-error">Ooops, something wrong appended.</p>
                                </div>
                                <div class="dropify-loader"></div>
                                <div class="dropify-errors-container"><ul></ul></div>
                                <input type="file" id="input-file-now" class="dropify">
                                <button type="button" class="dropify-clear">Remove</button>
                                <div class="dropify-preview"><span class="dropify-render"></span>
                                    <div class="dropify-infos">
                                        <div class="dropify-infos-inner">
                                            <p class="dropify-filename"><span class="file-icon"></span> <span class="dropify-filename-inner"></span></p>
                                            <p class="dropify-infos-message">Máximo 2mb</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Textarea para observaciones -->
                            <div class="form-group mt-3">
                                <label for="dni-observaciones">Observaciones</label>
                                <textarea class="form-control" id="dni-observaciones" name="dni-observaciones" rows="3" placeholder="Escriba sus observaciones aquí..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <label for="input-file-now">Ficha de Datos</label>
                            <div class="dropify-wrapper">
                                <div class="dropify-message"><span class="file-icon"></span>
                                    <p>Ficha de Datos (Solo PDF o Imágenes)</p>
                                    <p class="dropify-error">Ooops, something wrong appended.</p>
                                </div>
                                <div class="dropify-loader"></div>
                                <div class="dropify-errors-container"><ul></ul></div>
                                <input type="file" id="input-file-now" class="dropify">
                                <button type="button" class="dropify-clear">Remove</button>
                                <div class="dropify-preview"><span class="dropify-render"></span>
                                    <div class="dropify-infos">
                                        <div class="dropify-infos-inner">
                                            <p class="dropify-filename"><span class="file-icon"></span> <span class="dropify-filename-inner"></span></p>
                                            <p class="dropify-infos-message">Máximo 2mb</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Textarea para observaciones -->
                            <div class="form-group mt-3">
                                <label for="ficha-observaciones">Observaciones</label>
                                <textarea class="form-control" id="ficha-observaciones" name="ficha-observaciones" rows="3" placeholder="Escriba sus observaciones aquí..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

<?php
require 'layout/footer.php';
?>

<?php
require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>

<div class="container">
    <h2>Revisión de Documentos</h2>
    <p>Revisa y aprueba o rechaza los documentos subidos por los usuarios.</p>

    <!-- Nav Tabs -->
    <ul class="nav nav-tabs customtab2" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#obligatorios" role="tab" aria-expanded="true">
                <span class="hidden-sm-up"><i class="ti-file"></i></span>
                <span class="hidden-xs-down">Obligatorios</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#opcionales" role="tab" aria-expanded="false">
                <span class="hidden-sm-up"><i class="ti-check-box"></i></span>
                <span class="hidden-xs-down">Opcionales</span>
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <div class="tab-pane active" id="obligatorios" role="tabpanel">
            <form class="form-material m-t-40" id="reviewFormObligatorios">
                <div id="document-review-list-obligatorios">
                    <!-- Aquí se cargarán los documentos obligatorios dinámicamente -->
                </div>
                <!-- Comentario del administrador -->
                <div class="form-group mt-4">
                    <label for="admin_observation_obligatorios">Comentario del Administrador:</label>
                    <textarea id="admin_observation_obligatorios" name="admin_observation_obligatorios" class="form-control" placeholder="Agregar comentario sobre la revisión..."></textarea>
                </div>
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-success">Aprobar Documentos</button>
                    <button type="button" class="btn btn-danger" onclick="rechazarDocumentos()">Rechazar Documentos</button>
                </div>
            </form>
        </div>
        <div class="tab-pane" id="opcionales" role="tabpanel">
            <form class="form-material m-t-40" id="reviewFormOpcionales">
                <div id="document-review-list-opcionales">
                    <!-- Aquí se cargarán los documentos opcionales dinámicamente -->
                </div>
                <!-- Comentario del administrador -->
                <div class="form-group mt-4">
                    <label for="admin_observation_opcionales">Comentario del Administrador:</label>
                    <textarea id="admin_observation_opcionales" name="admin_observation_opcionales" class="form-control" placeholder="Agregar comentario sobre la revisión..."></textarea>
                </div>
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-success">Aprobar Documentos</button>
                    <button type="button" class="btn btn-danger" onclick="rechazarDocumentos()">Rechazar Documentos</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/documenta/vistas/scripts/adminDocumentReview.js"></script>

<?php
require 'layout/footer.php';
?>

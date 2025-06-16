<?php
session_start();

// Verificar si el usuario ha iniciado sesión y tiene los permisos adecuados
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>

<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h3 class="text-themecolor">Registrar/Actualizar Datos Personales</h3>
    </div>
    <div class="col-md-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item">Usuarios</li>
            <li class="breadcrumb-item active">Registrar/Actualizar Datos</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <!-- Formulario para registrar o actualizar datos -->
                <form class="needs-validation" id="formUserDetails" method="POST" enctype="multipart/form-data" novalidate>
                    <div class="card shadow-sm border rounded-lg">
                        <div class="card-body p-4">
                            <h4 class="text-info mb-4 font-weight-bold text-center"><i class="fa fa-user-plus"></i> Datos Personales</h4>

                            <!-- Persona de contacto -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="person_contact_name" class="font-weight-bold">Persona de Contacto</label>
                                    <input type="text" class="form-control" id="person_contact_name" name="person_contact_name" maxlength="100" placeholder="Ingrese nombre de persona de contacto">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="person_contact_phone" class="font-weight-bold">Teléfono de Persona de Contacto</label>
                                    <input type="text" class="form-control" id="person_contact_phone" name="person_contact_phone" maxlength="15" placeholder="Ingrese teléfono de persona de contacto">
                                </div>
                            </div>

                            <!-- Teléfono personal y email -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="phone" class="font-weight-bold">Teléfono Personal</label>
                                    <input type="text" class="form-control" id="phone" name="phone" maxlength="15" placeholder="Ingrese su teléfono" required pattern="^\d{7,15}$">
                                    <div class="invalid-feedback">
                                        Por favor, ingrese un teléfono válido (7-15 dígitos).
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="email" class="font-weight-bold">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" maxlength="100" placeholder="Ingrese su email" required>
                                    <div class="invalid-feedback">
                                        Por favor, ingrese un email válido.
                                    </div>
                                </div>
                            </div>

                            <!-- Nacionalidad y fecha de ingreso -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="country" class="font-weight-bold">Nacionalidad</label>
                                    <select class="form-control" id="country" name="country" required>
                                        <option value="">Seleccione su país</option>
                                        <?php
                                        $paisesAmerica = [
                                            "Argentina",
                                            "Bolivia",
                                            "Brasil",
                                            "Canadá",
                                            "Chile",
                                            "Colombia",
                                            "Costa Rica",
                                            "Cuba",
                                            "Ecuador",
                                            "El Salvador",
                                            "Estados Unidos",
                                            "Guatemala",
                                            "Honduras",
                                            "México",
                                            "Nicaragua",
                                            "Panamá",
                                            "Paraguay",
                                            "Perú",
                                            "República Dominicana",
                                            "Uruguay",
                                            "Venezuela"
                                        ];
                                        foreach ($paisesAmerica as $pais) {
                                            echo "<option value=\"$pais\">$pais</option>";
                                        }
                                        ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        Por favor, seleccione su nacionalidad.
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="entry_date" class="font-weight-bold">Fecha de Ingreso</label>
                                    <input type="date" class="form-control" id="entry_date" name="entry_date">
                                </div>
                            </div>

                            <!-- Fecha de nacimiento y tipo de sangre -->
                            <div class="form-row">
                                <!-- Fecha de nacimiento y edad -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="birth_date" class="font-weight-bold">Fecha de Nacimiento</label>
                                        <input type="date" class="form-control" id="birth_date" name="birth_date" required>
                                        <div class="invalid-feedback">
                                            Por favor, ingrese su fecha de nacimiento.
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="age" class="font-weight-bold">Edad</label>
                                        <input type="number" class="form-control" id="age" name="age" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="blood_type" class="font-weight-bold">Tipo de Sangre</label>
                                    <select class="form-control" id="blood_type" name="blood_type">
                                        <option value="">Seleccione su tipo de sangre</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Alergias -->
                            <div class="form-group">
                                <label for="allergies" class="font-weight-bold">Alergias</label>
                                <textarea class="form-control" id="allergies" name="allergies" rows="3" maxlength="255" placeholder="Ingrese sus alergias"></textarea>
                            </div>

                            <!-- Foto de perfil -->
                            <div class="form-group">
                                <label class="font-weight-bold">Foto de Perfil</label>
                                <div class="custom-file-upload d-flex align-items-center">
                                    <input type="file" id="photo" name="photo" accept="image/*" style="display: none;">
                                    <label for="photo" class="btn btn-primary rounded-lg mr-3">
                                        <i class="fa fa-upload"></i> Seleccionar Foto
                                    </label>
                                    <span id="file-name">No se ha seleccionado ninguna foto</span>
                                </div>
                                <div class="invalid-feedback d-block">
                                    Por favor, sube una foto de perfil válida (JPG, PNG, GIF).
                                </div>
                                <div class="mt-2">
                                    <img src="" alt="Vista Previa" id="previewPhoto" class="img-thumbnail rounded-circle" style="width: 100px; height: 100px; display: none;">
                                </div>
                            </div>

                            <!-- Botón de Envío -->
                            <button type="submit" class="btn btn-primary btn-block rounded-lg font-weight-bold">
                                <i class="fa fa-save"></i> Guardar Datos
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Vista de Datos Registrados -->
                <div id="datosRegistrados" style="display:none;" class="mt-4">
                    <div class="card shadow-sm border rounded-lg">
                        <div class="card-body p-4">
                            <div class="little-profile text-center mb-4">
                                <div class="pro-img m-t-20">
                                    <img src="" alt="Foto del Usuario" id="verPhoto" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px;">
                                </div>
                                <h3 class="m-b-0" id="verNombre">Nombre Completo</h3>
                            </div>

                            <!-- Información Personal -->
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <strong>Persona de Contacto:</strong> <span id="verPersonContactName"></span>
                                </li>
                                <li class="list-group-item">
                                    <strong>Teléfono de Persona de Contacto:</strong> <span id="verPersonContactPhone"></span>
                                </li>
                                <li class="list-group-item">
                                    <strong>Teléfono Personal:</strong> <span id="verPhone"></span>
                                </li>
                                <li class="list-group-item">
                                    <strong>Email:</strong> <span id="verEmail"></span>
                                </li>
                                <li class="list-group-item">
                                    <strong>Nacionalidad:</strong> <span id="verCountry"></span>
                                </li>
                                <li class="list-group-item">
                                    <strong>Fecha de Ingreso:</strong> <span id="verEntryDate"></span>
                                </li>
                                <li class="list-group-item">
                                    <strong>Fecha de Nacimiento:</strong> <span id="verBirthDate"></span>
                                </li>
                                <li class="list-group-item">
                                    <strong>Edad:</strong> <span id="verAge"></span>
                                </li>

                                <li class="list-group-item">
                                    <strong>Tipo de Sangre:</strong> <span id="verBloodType"></span>
                                </li>
                                <li class="list-group-item">
                                    <strong>Alergias:</strong> <span id="verAllergies"></span>
                                </li>
                            </ul>

                            <!-- Botón para editar -->
                            <button id="btnEditarPerfil" class="btn btn-info btn-lg btn-block rounded-lg font-weight-bold mt-3">
                                <i class="fa fa-edit"></i> Editar Perfil
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Puedes añadir más contenido aquí si es necesario -->
            </div>
        </div>
    </div>
</div>

<?php
require 'layout/footer.php';
?>

<!-- Incluir el archivo JavaScript externo -->
<script src="/documenta/vistas/scripts/user_details.js"></script>
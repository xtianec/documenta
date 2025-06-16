<?php
session_start();
require_once "../modelos/User.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = new User();
    $userId = $_SESSION['id'];
    $newPassword = $_POST["new_password"];

    if ($user->cambiarPassword($userId, $newPassword)) {
        header("Location: http://localhost/reclutamiento/vistas/login.php");
        exit();
    } else {
        echo "No se pudo restablecer la contraseña";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <link href="../app/template/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../app/template/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center">Restablecer Contraseña</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="new_password">Nueva Contraseña:</label>
                        <input type="password" id="new_password" name="new_password" class="form-control" required>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary">Restablecer Contraseña</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

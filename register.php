<?php

session_start();

if (isset($_SESSION["idUsuario"])) {
    header("Location: panel.php");
    exit();
}

if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirmar_password"])) {
        $con = mysqli_connect("localhost", "adm_webgenerator", "webgenerator2020", "webgenerator");

        $email = $_POST["email"];
        $password = $_POST["password"];
        $confirmarPassword = $_POST["confirmar_password"];

        if ($password !== $confirmarPassword) {
            echo "Las contraseñas no coinciden.";
        } else {
            $sql = "SELECT * FROM `usuarios` WHERE email = '$email'";
            $res = mysqli_query($con, $sql);

            if (mysqli_num_rows($res) > 0) {
            	echo "El email ya está registrado.";
            } else {
                $insertarSql = "INSERT INTO `usuarios` (email, password, fechaRegistro) VALUES ('$email', '$password', CURRENT_TIMESTAMP)";
                if (mysqli_query($con, $insertarSql)) {
                    $_SESSION["registro_exitoso"] = true;
                    header("Location: login.php");
                    exit();
                } else {
                    echo "Error en el registro. Por favor, inténtalo de nuevo.";
                }
            }
        }

    mysqli_close($con);
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Registro</title>
</head>
<body>
	<h1>Registrarte es simple</h1>
	<form action="" method="POST">
        <div>
            Email: <input type="email" name="email" required>
        </div>
        <div>
            Contraseña: <input type="password" name="password" required>
        </div>
        <div>
            Repetir Contraseña: <input type="password" name="confirmar_password" required>
        </div>
        <input type="submit" value="Registrar">
    </form>
</body>
</html>
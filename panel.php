<?php
session_start();

if (isset($_SESSION["idUsuario"])) {
    $idUsuario = $_SESSION["idUsuario"];
} else{
    header("Location: login.php");
    exit();
}

if (isset($_POST["nombre_web"])) {
    $nombre = $_POST["nombre_web"];
    $dominio = $idUsuario . $nombre;

    $con = mysqli_connect("localhost", "adm_webgenerator", "webgenerator2020", "webgenerator");

    $sql = "SELECT * FROM `webs` WHERE dominio = '$dominio'";
    $res = mysqli_query($con, $sql);

    if (!$res) {
	    echo "Error: " . mysqli_error($con);
	}

    if (mysqli_num_rows($res) == 0) {

        $insertarQuery = "INSERT INTO `webs` (dominio, idUsuario, fechaCreacion) VALUES ('$dominio', '$idUsuario', CURRENT_TIMESTAMP)";
        mysqli_query($con, $insertarQuery);

		shell_exec("chmod 757 wix.sh");
        shell_exec("./wix.sh $dominio");

    } else {
        echo "El dominio ya existe.";
    }
}

if (isset($_GET["eliminar_web"])){

	$idWeb = $_GET["eliminar_web"];

    $con = mysqli_connect("localhost", "7239", "buey.avellano.pesa", "7239");

    $selectQuery = "SELECT dominio FROM `webs` WHERE idWeb = '$idWeb'";
    $result = mysqli_query($con, $selectQuery);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $dominio = $row['dominio'];

        $eliminarQuery = "DELETE FROM `webs` WHERE idWeb = '$idWeb'";
        mysqli_query($con, $eliminarQuery);

        $eliminar = "rm -rf $dominio";
        shell_exec($eliminar);
    }
}

if (isset($_GET['download'])) {
    $nombreCarpeta = $_GET['download'];
    $zip = $nombreCarpeta . '.zip';

    $comandoZip = 'zip -r "' . $zip . '" "' . $nombreCarpeta . '"';
    shell_exec($comandoZip);

    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $zip . '"');
    header('Content-Length: ' . filesize($zip));

    readfile($zip);
    unlink($zip);

    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Bienvenido a tu panel</h1>

    <form action="" method="POST">
    	<?php
    		echo '<a href="logout.php">Cerrar sesión de ' . $idUsuario . '</a>';
    	?>
        <div>
            <label for="nombre_web">Generar Web de:</label>
            <input type="text" name="nombre_web" required>
        </div>
        <input type="submit" value="Crear web">
    </form>
    <h2>Tus sitios web:</h2>
    <ul>
        <?php
            $con = mysqli_connect("localhost", "7239", "buey.avellano.pesa", "7239");

            if (isset($_SESSION["emailUsuario"]) && isset($_SESSION["pwdUsuario"])) {
            	
    			$email = $_SESSION["emailUsuario"];
    			$password = $_SESSION["pwdUsuario"];

    			if (($email == "admin@server.com") && ($password == "serveradmin")) {
    				$sql = "SELECT * FROM `webs`";
		            $res = mysqli_query($con, $sql);

		            while ($row = mysqli_fetch_assoc($res)) {
		            	$idWeb = $row['idWeb'];
		                $dominio = $row['dominio'];
		                echo '<li><a href="' . $dominio . '/index.php">' . $dominio . '</a> - <a href="?download=' . $dominio . '">Descargar web</a> - <a href="?eliminar_web=' . $idWeb . '">Eliminar</a></li>';

		            }

		            mysqli_close($con);

    			} else{

    				$sql = "SELECT * FROM `webs` WHERE idUsuario = '$idUsuario'";
		            $res = mysqli_query($con, $sql);

		            while ($row = mysqli_fetch_assoc($res)) {
		            	$idWeb = $row['idWeb'];
		                $dominio = $row['dominio'];
		                echo '<li><a href="' . $dominio . '/index.php">' . $dominio . '</a> - <a href="?download=' . $dominio . '">Descargar web</a> - <a href="?eliminar_web=' . $idWeb . '">Eliminar</a></li>';

		            }

		            mysqli_close($con);
    			}
			}
		?>
            
    </ul>
</body>
</html>
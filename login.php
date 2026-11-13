
<?php
session_start();
require_once "conexion.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = trim($_POST["correo"] ?? "");
    $password = $_POST["password"] ?? "";

    $sql = "SELECT id_cliente, nombre, correo, password, rol
            FROM clientes
            WHERE correo = ?";

    $consulta = $conexion->prepare($sql);
    $consulta->bind_param("s", $correo);
    $consulta->execute();

    $resultado = $consulta->get_result();

    if ($cliente = $resultado->fetch_assoc()) {
        if (password_verify($password, $cliente["password"])) {

            session_regenerate_id(true);

            $_SESSION["id_cliente"] = $cliente["id_cliente"];
            $_SESSION["nombre_cliente"] = $cliente["nombre"];
            $_SESSION["correo_cliente"] = $cliente["correo"];
            $_SESSION["rol"] = $cliente["rol"];

            $consulta->close();

            if ($cliente["rol"] === "admin") {
                header("Location: admin.php");
            } else {
                header("Location: mi_cuenta.php");
            }

            exit;
        }
    }

    $mensaje = "Correo o contraseña incorrectos.";
    $consulta->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Aluna Bya</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eaf4ff;
            margin: 0;
            padding: 40px 15px;
        }

        .login {
            max-width: 400px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 18px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        h1 {
            color: #527da8;
            text-align: center;
        }

        p {
            text-align: center;
            color: #555;
        }

        label {
            display: block;
            margin-top: 18px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-top: 7px;
            border: 1px solid #cbd9e8;
            border-radius: 8px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 13px;
            margin-top: 25px;
            background: #83b6e6;
            color: white;
            border: none;
            border-radius: 9px;
            font-size: 16px;
            cursor: pointer;
        }

        .mensaje {
            background: #edf6ff;
            color: #315d83;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
        }
    </style>
</head>

<body>

<div class="login">

    <h1>Aluna Bya</h1>
    <p>¡Qué alegría tenerte de nuevo!</p>

    <?php if ($mensaje !== ""): ?>
        <div class="mensaje">
            <?= htmlspecialchars($mensaje, ENT_QUOTES, "UTF-8") ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php">

        <label>Correo electrónico</label>
        <input type="email" name="correo" required>

        <label>Contraseña</label>
        <input type="password" name="password" required>

        <button type="submit">Iniciar sesión</button>

    </form>

    <p>¿Todavía no tienes cuenta?
        <a href="registro.php">Regístrate aquí</a>
    </p>

</div>

</body>
</html>

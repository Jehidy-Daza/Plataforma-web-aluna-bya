
<?php
require_once "conexion.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $password = $_POST["password"] ?? "";

    $acepta_correo = isset($_POST["acepta_correo"]) ? 1 : 0;
    $acepta_whatsapp = isset($_POST["acepta_whatsapp"]) ? 1 : 0;

    if (
        $nombre === "" ||
        !filter_var($correo, FILTER_VALIDATE_EMAIL) ||
        $telefono === "" ||
        strlen($password) < 8
    ) {
        $mensaje = "Revisa tus datos. La contraseña debe tener al menos 8 caracteres.";
    } else {
        $password_segura = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO clientes
                (nombre, correo, telefono, password,
                 acepta_correo, acepta_whatsapp)
                VALUES (?, ?, ?, ?, ?, ?)";

        $consulta = $conexion->prepare($sql);
        $consulta->bind_param(
            "ssssii",
            $nombre,
            $correo,
            $telefono,
            $password_segura,
            $acepta_correo,
            $acepta_whatsapp
        );

        if ($consulta->execute()) {
            $mensaje = "¡Tu cuenta fue creada correctamente! Ya puedes iniciar sesión.";
        } else {
            if ($consulta->errno === 1062) {
                $mensaje = "Este correo ya está registrado. Intenta iniciar sesión.";
            } else {
                $mensaje = "No fue posible crear la cuenta. Inténtalo nuevamente.";
            }
        }

        $consulta->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta - Aluna Bya</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eaf4ff;
            margin: 0;
            padding: 30px 15px;
        }

        .registro {
            max-width: 430px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 18px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        h1 {
            text-align: center;
            color: #527da8;
        }

        p {
            color: #555;
            text-align: center;
        }

        label {
            display: block;
            margin-top: 15px;
            color: #444;
        }

        input:not([type="checkbox"]) {
            width: 100%;
            padding: 11px;
            margin-top: 6px;
            border: 1px solid #cbd9e8;
            border-radius: 8px;
            box-sizing: border-box;
        }

        .promocion {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        button {
            width: 100%;
            margin-top: 22px;
            padding: 13px;
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

<div class="registro">

    <h1>Aluna Bya</h1>
    <p>¡Crea tu cuenta y disfruta nuestros dulces momentos!</p>

    <?php if ($mensaje !== ""): ?>
        <div class="mensaje">
            <?= htmlspecialchars($mensaje, ENT_QUOTES, "UTF-8") ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="registro.php">

        <label>Nombre completo</label>
        <input type="text" name="nombre" required maxlength="100">

        <label>Correo electrónico</label>
        <input type="email" name="correo" required maxlength="150">

        <label>Número de teléfono</label>
        <input type="tel" name="telefono" required maxlength="20">

        <label>Contraseña</label>
        <input type="password" name="password" required minlength="8">

        <p>¿Deseas recibir nuestras promociones?</p>

        <label class="promocion">
            <input type="checkbox" name="acepta_correo" value="1">
            Acepto recibir promociones por correo electrónico.
        </label>

        <label class="promocion">
            <input type="checkbox" name="acepta_whatsapp" value="1">
            Acepto recibir promociones por WhatsApp.
        </label>

        <button type="submit">Crear mi cuenta</button>

    </form>

</div>

</body>
</html>

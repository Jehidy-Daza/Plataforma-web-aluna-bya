
<?php
session_start();

if (!isset($_SESSION["id_cliente"])) {
    header("Location: login.php");
    exit;
}

$nombre = htmlspecialchars(
    $_SESSION["nombre_cliente"],
    ENT_QUOTES,
    "UTF-8"
);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi cuenta - Aluna Bya</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eaf4ff;
            margin: 0;
            padding: 40px 15px;
        }

        .cuenta {
            max-width: 600px;
            margin: auto;
            padding: 35px;
            background: white;
            border-radius: 18px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        h1 {
            color: #527da8;
        }

        p {
            color: #555;
            font-size: 17px;
        }

        a {
            display: inline-block;
            margin: 12px;
            padding: 12px 22px;
            background: #83b6e6;
            color: white;
            text-decoration: none;
            border-radius: 9px;
        }
    </style>
</head>

<body>

<div class="cuenta">

    <h1>¡Bienvenido a Aluna Bya! 💙</h1>

    <p>Hola, <?= $nombre ?>.</p>

    <p>Tu cuenta está activa. Aquí podrás consultar tus compras y disfrutar nuestros dulces momentos.</p>

    <a href="index.php">Ir al catálogo</a>

    <a href="logout.php">Cerrar sesión</a>

</div>

</body>
</html>

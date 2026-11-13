
<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["id_cliente"])) {
    header("Location: login.php");
    exit;
}

$id_cliente = $_SESSION["id_cliente"];

$sql = "SELECT id_pedido, fecha_pedido, total, estado,
               metodo_pago, estado_pago
        FROM pedidos
        WHERE id_cliente = ?
        ORDER BY fecha_pedido DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$pedidos = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mis pedidos | Aluna Bya</title>

<style>
body {
    margin: 0;
    padding: 25px;
    background: #f7fbff;
    color: #35465c;
    font-family: "Times New Roman", serif;
    font-size: 12pt;
}

.contenedor {
    max-width: 900px;
    margin: 35px auto;
}

h1 {
    text-align: center;
    color: #477da9;
}

.pedido {
    background: white;
    border-radius: 15px;
    padding: 22px;
    margin: 20px 0;
    box-shadow: 0 3px 12px #00000012;
}

.pedido h2 {
    color: #477da9;
}

.total {
    color: #a45c8b;
    font-weight: bold;
    font-size: 18px;
}

.estado {
    display: inline-block;
    background: #eaf5ff;
    color: #477da9;
    padding: 7px 12px;
    border-radius: 15px;
    margin: 5px 5px 5px 0;
}

.boton {
    display: inline-block;
    background: #7caed6;
    color: white;
    padding: 10px 16px;
    border-radius: 20px;
    text-decoration: none;
    margin-top: 10px;
}

.boton:hover {
    background: #548dbd;
}

.volver {
    text-align: center;
    margin: 25px;
}

</style>
</head>

<body>

<div class="contenedor">

    <h1>Mis pedidos 💙</h1>

    <?php if (isset($_GET["mensaje"]) && $_GET["mensaje"] === "pago_reportado"): ?>
        <p class="estado">
            Tu pago fue reportado correctamente. Está pendiente de verificación.
        </p>
    <?php endif; ?>

    <?php if ($pedidos->num_rows > 0): ?>

        <?php while ($pedido = $pedidos->fetch_assoc()): ?>

            <div class="pedido">

                <h2>Pedido #<?php echo $pedido["id_pedido"]; ?></h2>

                <p>
                    <strong>Fecha:</strong>
                    <?php echo htmlspecialchars($pedido["fecha_pedido"]); ?>
                </p>

                <p class="total">
                    Total: $<?php echo number_format($pedido["total"], 0, ",", "."); ?> COP
                </p>

                <p>
                    <strong>Estado del pedido:</strong>
                    <span class="estado">
                        <?php echo htmlspecialchars($pedido["estado"]); ?>
                    </span>
                </p>

                <p>
                    <strong>Estado del pago:</strong>
                    <span class="estado">
                        <?php echo htmlspecialchars($pedido["estado_pago"]); ?>
                    </span>
                </p>

                <?php if ($pedido["estado_pago"] === "pendiente"): ?>
                    <a class="boton"
                       href="pago.php?id=<?php echo $pedido["id_pedido"]; ?>">
                        Realizar pago
                    </a>
                <?php elseif ($pedido["estado_pago"] === "reportado"): ?>
                    <p>Estamos revisando tu reporte de pago.</p>
                <?php else: ?>
                    <p>Tu pago ha sido verificado. ¡Gracias por tu compra!</p>
                <?php endif; ?>

            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <p style="text-align:center;">
            Todavía no tienes pedidos registrados.
        </p>

    <?php endif; ?>

    <div class="volver">
        <a class="boton" href="index.php">Volver a la tienda</a>
        <a class="boton" href="mi_cuenta.php">Mi cuenta</a>
    </div>

</div>

</body>
</html>

<?php
$stmt->close();
?>

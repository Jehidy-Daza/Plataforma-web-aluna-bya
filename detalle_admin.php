
<?php
session_start();
require_once "conexion.php";

// Verificar que solamente la administradora pueda entrar
if (
    !isset($_SESSION["id_cliente"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] !== "admin"
) {
    header("Location: login.php");
    exit;
}

// Obtener el número del pedido
$id_pedido = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id_pedido) {
    die("Número de pedido no válido.");
}

// Consultar información general del pedido y del cliente
$sql = "SELECT p.id_pedido, p.fecha_pedido, p.total,
               p.estado, p.estado_pago, p.referencia_pago,
               p.metodo_pago,
               c.nombre, c.correo, c.telefono
        FROM pedidos p
        INNER JOIN clientes c ON p.id_cliente = c.id_cliente
        WHERE p.id_pedido = ?";

$consulta = $conexion->prepare($sql);
$consulta->bind_param("i", $id_pedido);
$consulta->execute();
$resultado = $consulta->get_result();
$pedido = $resultado->fetch_assoc();
$consulta->close();

if (!$pedido) {
    die("No se encontró el pedido.");
}

// Consultar los productos del pedido
$sql_detalle = "SELECT nombre_producto, cantidad,
                       precio_unitario, subtotal
                FROM detalle_pedido
                WHERE id_pedido = ?";

$consulta_detalle = $conexion->prepare($sql_detalle);
$consulta_detalle->bind_param("i", $id_pedido);
$consulta_detalle->execute();
$productos = $consulta_detalle->get_result();

function e($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Detalle del pedido | Aluna Bya</title>

<style>
body {
    margin: 0;
    padding: 25px 15px;
    background: #f2f8ff;
    color: #35465c;
    font-family: "Times New Roman", serif;
    font-size: 12pt;
}

.contenedor {
    max-width: 900px;
    margin: auto;
}

.tarjeta {
    background: white;
    padding: 25px;
    margin-bottom: 20px;
    border-radius: 16px;
    box-shadow: 0 3px 12px #00000012;
}

h1, h2, h3 {
    color: #477da9;
}

h1 {
    text-align: center;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #e4edf5;
}

th {
    color: #477da9;
    background: #eaf5ff;
}

.tabla {
    overflow-x: auto;
}

.boton {
    display: inline-block;
    padding: 10px 18px;
    background: #7caed6;
    color: white;
    text-decoration: none;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-family: inherit;
    font-size: 12pt;
}

.boton:hover {
    background: #6198c5;
}

select {
    display: block;
    width: 100%;
    max-width: 400px;
    padding: 11px;
    margin: 8px 0 18px;
    border: 1px solid #cbd9e8;
    border-radius: 8px;
    background: white;
    font-family: inherit;
    font-size: 12pt;
}

.total {
    color: #a65d88;
    font-size: 20px;
    text-align: right;
    font-weight: bold;
}

.mensaje {
    padding: 12px;
    background: #e6f6ed;
    color: #247548;
    border-radius: 8px;
    margin-bottom: 15px;
}

.nota {
    color: #65788c;
    font-size: 11pt;
}

@media (max-width: 600px) {
    .tarjeta {
        padding: 16px;
    }
}
</style>
</head>

<body>

<div class="contenedor">

    <div class="tarjeta">
        <h1>Aluna Bya 💙</h1>
        <h2>Detalle del pedido #<?= (int)$pedido["id_pedido"] ?></h2>

        <p><strong>Cliente:</strong> <?= e($pedido["nombre"]) ?></p>
        <p><strong>Correo:</strong> <?= e($pedido["correo"]) ?></p>
        <p><strong>Teléfono:</strong> <?= e($pedido["telefono"]) ?></p>
        <p><strong>Fecha:</strong> <?= e($pedido["fecha_pedido"]) ?></p>
        <p><strong>Método de pago:</strong> <?= e($pedido["metodo_pago"]) ?></p>

        <p><strong>Estado del pedido:</strong>
            <?= e($pedido["estado"]) ?>
        </p>

        <p><strong>Estado del pago:</strong>
            <?= e($pedido["estado_pago"]) ?>
        </p>

        <p><strong>Referencia:</strong>
            <?= e($pedido["referencia_pago"] ?: "Sin reportar") ?>
        </p>
    </div>

    <div class="tarjeta">
        <h2>Productos comprados</h2>

        <div class="tabla">
            <table>
                <thead>
                    <tr>
                        <th>Producto y personalización</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                <?php if ($productos->num_rows > 0): ?>
                    <?php while ($producto = $productos->fetch_assoc()): ?>
                    <tr>
                        <td><?= e($producto["nombre_producto"]) ?></td>
                        <td><?= (int)$producto["cantidad"] ?></td>
                        <td>
                            $<?= number_format((float)$producto["precio_unitario"], 0, ",", ".") ?>
                        </td>
                        <td>
                            $<?= number_format((float)$producto["subtotal"], 0, ",", ".") ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Este pedido no tiene productos registrados.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <p class="total">
            Total: $<?= number_format((float)$pedido["total"], 0, ",", ".") ?> COP
        </p>
    </div>

    <div class="tarjeta">
        <h2>Administrar pedido</h2>

        <?php if (isset($_GET["actualizado"])): ?>
            <div class="mensaje">
                ¡Los cambios del pedido se guardaron correctamente!
            </div>
        <?php endif; ?>

        <form action="actualizar_pedido.php" method="POST">

            <input
                type="hidden"
                name="id_pedido"
                value="<?= (int)$pedido["id_pedido"] ?>"
            >

            <h3>Estado del pedido</h3>

            <select name="estado" required>
                <option value="pendiente" <?= $pedido["estado"] === "pendiente" ? "selected" : "" ?>>Pendiente</option>
                <option value="confirmado" <?= $pedido["estado"] === "confirmado" ? "selected" : "" ?>>Confirmado</option>
                <option value="en_preparacion" <?= $pedido["estado"] === "en_preparacion" ? "selected" : "" ?>>En preparación</option>
                <option value="listo" <?= $pedido["estado"] === "listo" ? "selected" : "" ?>>Listo</option>
                <option value="entregado" <?= $pedido["estado"] === "entregado" ? "selected" : "" ?>>Entregado</option>
                <option value="cancelado" <?= $pedido["estado"] === "cancelado" ? "selected" : "" ?>>Cancelado</option>
            </select>

            <h3>Estado del pago</h3>

            <select name="estado_pago" required>
                <option value="pendiente" <?= $pedido["estado_pago"] === "pendiente" ? "selected" : "" ?>>Pendiente</option>
                <option value="reportado" <?= $pedido["estado_pago"] === "reportado" ? "selected" : "" ?>>Reportado</option>
                <option value="verificado" <?= $pedido["estado_pago"] === "verificado" ? "selected" : "" ?>>Verificado</option>
            </select>

            <p class="nota">
                Recuerda marcar el pago como verificado únicamente después
                de comprobar que recibiste el dinero en Nequi.
            </p>

            <button type="submit" class="boton">
                Guardar cambios
            </button>

        </form>
    </div>

    <a href="admin.php" class="boton">Volver al panel</a>

</div>

</body>
</html>


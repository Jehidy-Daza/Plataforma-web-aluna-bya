
<?php
session_start();
require_once "conexion.php";

// Solo la administradora puede actualizar pedidos
if (
    !isset($_SESSION["id_cliente"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] !== "admin"
) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin.php");
    exit;
}

$id_pedido = filter_input(INPUT_POST, "id_pedido", FILTER_VALIDATE_INT);
$estado = $_POST["estado"] ?? "";
$estado_pago = $_POST["estado_pago"] ?? "";

$estados_validos = [
    "pendiente",
    "confirmado",
    "en_preparacion",
    "listo",
    "entregado",
    "cancelado"
];

$pagos_validos = [
    "pendiente",
    "reportado",
    "verificado"
];

if (
    !$id_pedido ||
    !in_array($estado, $estados_validos, true) ||
    !in_array($estado_pago, $pagos_validos, true)
) {
    die("Los datos enviados no son válidos.");
}

// Comprobar que el pedido existe
$consulta = $conexion->prepare(
    "SELECT id_pedido FROM pedidos WHERE id_pedido = ?"
);
$consulta->bind_param("i", $id_pedido);
$consulta->execute();
$existe = $consulta->get_result()->num_rows > 0;
$consulta->close();

if (!$existe) {
    die("El pedido no existe.");
}

// Actualizar los estados
$sql = "UPDATE pedidos
        SET estado = ?, estado_pago = ?
        WHERE id_pedido = ?";

$consulta = $conexion->prepare($sql);
$consulta->bind_param("ssi", $estado, $estado_pago, $id_pedido);

if ($consulta->execute()) {
    $consulta->close();
    header("Location: detalle_admin.php?id=" . $id_pedido . "&actualizado=1");
    exit;
}

$consulta->close();
die("No fue posible actualizar el pedido.");
?>


<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["id_cliente"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$id_pedido = filter_input(INPUT_POST, "id_pedido", FILTER_VALIDATE_INT);
$referencia = trim($_POST["referencia"] ?? "");

if (!$id_pedido || $referencia === "") {
    die("Debes ingresar una referencia válida.");
}

$sql = "UPDATE pedidos
        SET referencia_pago = ?, estado_pago = 'reportado'
        WHERE id_pedido = ?
        AND id_cliente = ?
        AND estado_pago = 'pendiente'";

$stmt = $conexion->prepare($sql);
$stmt->bind_param(
    "sii",
    $referencia,
    $id_pedido,
    $_SESSION["id_cliente"]
);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $stmt->close();
    header("Location: mis_pedidos.php?mensaje=pago_reportado");
    exit;
}

$stmt->close();
die("No se pudo reportar el pago. Verifica el estado de tu pedido.");
?>

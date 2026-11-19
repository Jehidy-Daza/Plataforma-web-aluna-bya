
<?php
session_start();
require_once "conexion.php";

// Verificar que solamente la administradora pueda actualizar pedidos
if (
    !isset($_SESSION["id_cliente"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] !== "admin"
) {
    header("Location: login.php");
    exit;
}

// Aceptar únicamente solicitudes POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin.php");
    exit;
}

// Verificar el token CSRF
$token_recibido = $_POST["csrf_token"] ?? "";

if (
    !isset($_SESSION["csrf_token"]) ||
    !is_string($token_recibido) ||
    !hash_equals($_SESSION["csrf_token"], $token_recibido)
) {
    http_response_code(403);
    die("Solicitud no válida. No fue posible verificar el token de seguridad.");
}

// Validar el identificador del pedido
$id_pedido = filter_input(INPUT_POST, "id_pedido", FILTER_VALIDATE_INT);

if (
    $id_pedido === false ||
    $id_pedido === null ||
    $id_pedido <= 0
) {
    die("El número del pedido no es válido.");
}

// Recibir los estados enviados
$estado = $_POST["estado"] ?? "";
$estado_pago = $_POST["estado_pago"] ?? "";

// Estados permitidos para los pedidos
$estados_validos = [
    "pendiente",
    "confirmado",
    "en_preparacion",
    "listo",
    "entregado",
    "cancelado"
];

// Estados permitidos para los pagos
$pagos_validos = [
    "pendiente",
    "reportado",
    "verificado"
];

// Comprobar que los estados recibidos sean válidos
if (
    !is_string($estado) ||
    !in_array($estado, $estados_validos, true) ||
    !is_string($estado_pago) ||
    !in_array($estado_pago, $pagos_validos, true)
) {
    die("Los estados enviados no son válidos.");
}

// Consultar el estado actual del pedido
$consulta = $conexion->prepare(
    "SELECT estado FROM pedidos WHERE id_pedido = ?"
);

$consulta->bind_param("i", $id_pedido);
$consulta->execute();

$resultado = $consulta->get_result();
$pedido_actual = $resultado->fetch_assoc();

$consulta->close();

// Comprobar que el pedido exista
if (!$pedido_actual) {
    die("El pedido no existe.");
}

$estado_actual = $pedido_actual["estado"];

// Definir las transiciones permitidas
$transiciones = [
    "pendiente" => ["confirmado", "cancelado"],
    "confirmado" => ["en_preparacion", "cancelado"],
    "en_preparacion" => ["listo", "cancelado"],
    "listo" => ["entregado"],
    "entregado" => [],
    "cancelado" => []
];

// Permitir conservar el mismo estado o avanzar a uno permitido
if (
    $estado !== $estado_actual &&
    !in_array($estado, $transiciones[$estado_actual] ?? [], true)
) {
    die(
        "No se permite cambiar el pedido de '" .
        htmlspecialchars($estado_actual, ENT_QUOTES, "UTF-8") .
        "' a '" .
        htmlspecialchars($estado, ENT_QUOTES, "UTF-8") .
        "'. Verifica la transición del estado."
    );
}

// Actualizar los estados del pedido y del pago
$sql = "UPDATE pedidos
        SET estado = ?, estado_pago = ?
        WHERE id_pedido = ?";

$consulta = $conexion->prepare($sql);
$consulta->bind_param("ssi", $estado, $estado_pago, $id_pedido);

if ($consulta->execute()) {

    $consulta->close();

    header(
        "Location: detalle_admin.php?id=" .
        $id_pedido .
        "&actualizado=1"
    );

    exit;
}

$consulta->close();

die("No fue posible actualizar el pedido.");
?>


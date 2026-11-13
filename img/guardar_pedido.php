
<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

require_once "conexion.php";

// Verificar que el cliente haya iniciado sesión
if (!isset($_SESSION["id_cliente"])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "mensaje" => "Debes iniciar sesión para realizar tu pedido."
    ]);
    exit;
}

// Recibir los datos enviados desde el carrito
$datos = json_decode(file_get_contents("php://input"), true);

if (!$datos || empty($datos["productos"]) || !is_array($datos["productos"])) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "mensaje" => "El carrito está vacío o los datos no son válidos."
    ]);
    exit;
}

// Precios oficiales de los productos
$precios = [
    "Caja sencilla" => 12000,
    "Caja especial" => 15000,
    "Vaso pequeño" => 14000,
    "Vaso grande" => 18000,
    "Cuchareable mini" => 8000,
    "Cuchareable mediano" => 12000,
    "Cuchareable grande" => 16000,
    "Pavé 10 onzas" => 13000,
    "Oblea mini" => 10000,
    "Oblea mediana" => 15000,
    "Oblea grande" => 25000,
    "Frutas con crema mini" => 9000,
    "Frutas con crema mediana" => 14000,
    "Frutas con crema grande" => 17000,
    "Choco bowl mini" => 11000,
    "Choco bowl mediano" => 15000,
    "Choco bowl grande" => 25000,
    "Waffle individual" => 7000,
    "Waffle amor" => 26000,
    "Waffle saludable" => 20000
];

$id_cliente = $_SESSION["id_cliente"];
$total = 0;
$productos_validos = [];

// Comprobar los productos y calcular el total en el servidor
foreach ($datos["productos"] as $producto) {
    $nombre = $producto["nombre"] ?? "";

    if (!isset($precios[$nombre])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "mensaje" => "Uno de los productos no es válido."
        ]);
        exit;
    }

    $precio = $precios[$nombre];
    $personalizaciones = $producto["personalizaciones"] ?? [];

    if (!is_array($personalizaciones)) {
        $personalizaciones = [];
    }

    $detalle = implode(" | ", $personalizaciones);

    $total += $precio;

    $productos_validos[] = [
        "nombre" => $nombre,
        "precio" => $precio,
        "detalle" => $detalle
    ];
}

try {
    $conexion->begin_transaction();

    // Registrar el pedido
    $sqlPedido = "INSERT INTO pedidos
        (id_cliente, total, estado, metodo_pago, estado_pago)
        VALUES (?, ?, 'pendiente', 'Nequi', 'pendiente')";

    $stmtPedido = $conexion->prepare($sqlPedido);
    $stmtPedido->bind_param("id", $id_cliente, $total);
    $stmtPedido->execute();

    $id_pedido = $conexion->insert_id;
    $stmtPedido->close();

    // Registrar cada producto del pedido
    $sqlDetalle = "INSERT INTO detalle_pedido
        (id_pedido, nombre_producto, cantidad, precio_unitario, subtotal)
        VALUES (?, ?, 1, ?, ?)";

    $stmtDetalle = $conexion->prepare($sqlDetalle);

    foreach ($productos_validos as $producto) {
        $nombreCompleto = $producto["nombre"];

        if ($producto["detalle"] !== "") {
            $nombreCompleto .= " (" . $producto["detalle"] . ")";
        }

        $precio = $producto["precio"];
        $subtotal = $precio;

        $stmtDetalle->bind_param(
            "isii",
            $id_pedido,
            $nombreCompleto,
            $precio,
            $subtotal
        );

        $stmtDetalle->execute();
    }

    $stmtDetalle->close();

    $conexion->commit();

    echo json_encode([
        "success" => true,
        "mensaje" => "Tu pedido fue registrado correctamente.",
        "id_pedido" => $id_pedido,
        "total" => $total
    ]);

} catch (Exception $e) {
    $conexion->rollback();

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "mensaje" => "No fue posible registrar el pedido. Inténtalo nuevamente."
    ]);
}
?>

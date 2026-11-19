
<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

require_once "conexion.php";

/*
|--------------------------------------------------------------------------
| Función para responder al cliente
|--------------------------------------------------------------------------
*/
function responder($codigo, $mensaje, $datos = []) {
    http_response_code($codigo);

    echo json_encode(
        array_merge([
            "success" => false,
            "mensaje" => $mensaje
        ], $datos),
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| Opciones permitidas para cada producto
|--------------------------------------------------------------------------
*/
function obtenerOpciones($nombre) {

    $saboresCuchareables = [
        "Fresa", "Mango", "Chocorramo", "Gansito",
        "Leche Klim", "Oreo"
    ];

    $saboresPaves = [
        "Milo", "Frutos rojos", "Maracuyá", "Oreo",
        "Leche Klim", "Chocorramo", "Gansito"
    ];

    $frutas = [
        "Fresa", "Mango", "Banano", "Durazno", "Kiwi"
    ];

    $salsas = [
        "Chocolate", "Uva", "Fresa", "Lecherita"
    ];

    $toppings = [
        "Oreo triturada", "Fresa", "Mango", "Banano",
        "Chocorramo", "Gansito", "Maní",
        "Chispitas de chocolate"
    ];

    if (str_contains($nombre, "Cuchareable")) {
        return ["Sabor" => $saboresCuchareables];
    }

    if (str_contains($nombre, "Pavé")) {
        return ["Sabor" => $saboresPaves];
    }

    if (str_contains($nombre, "Caja sencilla")) {
        return [
            "Salsa 1" => $salsas,
            "Salsa 2" => $salsas,
            "Topping 1" => $toppings,
            "Topping 2" => $toppings,
            "Topping 3" => $toppings
        ];
    }

    if (str_contains($nombre, "Caja especial")) {
        return [
            "Salsa" => $salsas,
            "Topping 1" => $toppings,
            "Topping 2" => $toppings
        ];
    }

    if (str_contains($nombre, "Vaso")) {
        return [
            "Chocolate" => ["Negro", "Blanco", "Ambos"],
            "Topping 1" => $toppings,
            "Topping 2" => $toppings,
            "Topping 3" => $toppings
        ];
    }

    if (str_contains($nombre, "Frutas con crema mini")) {
        return [
            "Fruta" => $frutas,
            "Salsa" => $salsas,
            "Topping" => $toppings
        ];
    }

    if (str_contains($nombre, "Frutas con crema mediana")) {
        return [
            "Fruta 1" => $frutas,
            "Fruta 2" => $frutas,
            "Salsa" => $salsas,
            "Topping 1" => $toppings,
            "Topping 2" => $toppings
        ];
    }

    if (str_contains($nombre, "Frutas con crema grande")) {
        return [
            "Fruta 1" => $frutas,
            "Fruta 2" => $frutas,
            "Salsa 1" => $salsas,
            "Salsa 2" => $salsas,
            "Topping 1" => $toppings,
            "Topping 2" => $toppings
        ];
    }

    if (str_contains($nombre, "Oblea mini")) {
        return [
            "Fruta" => $frutas,
            "Topping" => $toppings
        ];
    }

    if (str_contains($nombre, "Oblea mediana")) {
        return [
            "Fruta 1" => $frutas,
            "Fruta 2" => $frutas,
            "Topping 1" => $toppings,
            "Topping 2" => $toppings
        ];
    }

    if (str_contains($nombre, "Oblea grande")) {
        return [
            "Fruta 1" => $frutas,
            "Fruta 2" => $frutas,
            "Fruta 3" => $frutas,
            "Topping 1" => $toppings,
            "Topping 2" => $toppings
        ];
    }

    if (str_contains($nombre, "Choco bowl mini")) {
        return ["Fruta" => $frutas];
    }

    if (str_contains($nombre, "Choco bowl mediano")) {
        return [
            "Fruta 1" => $frutas,
            "Fruta 2" => $frutas
        ];
    }

    if (str_contains($nombre, "Choco bowl grande")) {
        return [
            "Fruta 1" => $frutas,
            "Fruta 2" => $frutas,
            "Fruta 3" => $frutas
        ];
    }

    if (str_contains($nombre, "Waffle individual")) {
        return [
            "Salsa" => $salsas,
            "Topping" => $toppings
        ];
    }

    if (str_contains($nombre, "Waffle amor")) {
        return [
            "Topping 1" => $toppings,
            "Topping 2" => $toppings,
            "Topping 3" => $toppings,
            "Topping 4" => $toppings,
            "Salsa" => $salsas
        ];
    }

    return [];
}

/*
|--------------------------------------------------------------------------
| Verificar sesión del cliente
|--------------------------------------------------------------------------
*/
if (
    !isset($_SESSION["id_cliente"]) ||
    filter_var($_SESSION["id_cliente"], FILTER_VALIDATE_INT) === false ||
    (int) $_SESSION["id_cliente"] <= 0
) {
    responder(401, "Debes iniciar sesión para realizar tu pedido.");
}

/*
|--------------------------------------------------------------------------
| Recibir y validar los datos del carrito
|--------------------------------------------------------------------------
*/
$contenido = file_get_contents("php://input");
$datos = json_decode($contenido, true);

if (
    json_last_error() !== JSON_ERROR_NONE ||
    !is_array($datos) ||
    !isset($datos["productos"]) ||
    !is_array($datos["productos"]) ||
    count($datos["productos"]) === 0 ||
    count($datos["productos"]) > 100
) {
    responder(400, "El carrito está vacío o los datos no son válidos.");
}

$id_cliente = (int) $_SESSION["id_cliente"];
$total = 0;
$productos_validos = [];
$transaccion_iniciada = false;

try {

    /*
    |--------------------------------------------------------------------------
    | Consultar productos activos en la base de datos
    |--------------------------------------------------------------------------
    */
    $sqlProducto = "SELECT nombre, precio
                    FROM productos
                    WHERE nombre = ? AND activo = 1
                    LIMIT 1";

    $stmtProducto = $conexion->prepare($sqlProducto);

    if (!$stmtProducto) {
        throw new RuntimeException("Error al preparar consulta de productos.");
    }

    foreach ($datos["productos"] as $producto) {

        if (!is_array($producto)) {
            throw new InvalidArgumentException("Un producto enviado no es válido.");
        }

        $nombre = trim($producto["nombre"] ?? "");

        if ($nombre === "" || strlen($nombre) > 150) {
            throw new InvalidArgumentException("El nombre de un producto no es válido.");
        }

        $stmtProducto->bind_param("s", $nombre);
        $stmtProducto->execute();

        $resultadoProducto = $stmtProducto->get_result();
        $productoBD = $resultadoProducto->fetch_assoc();

        if (!$productoBD) {
            throw new InvalidArgumentException(
                "El producto " . $nombre . " no existe o está inactivo."
            );
        }

        $precio = (float) $productoBD["precio"];

        if ($precio <= 0) {
            throw new InvalidArgumentException("El precio del producto no es válido.");
        }

        /*
        |--------------------------------------------------------------------------
        | Validar personalizaciones
        |--------------------------------------------------------------------------
        */
        $opcionesPermitidas = obtenerOpciones($nombre);
        $personalizaciones = $producto["personalizaciones"] ?? [];

        if (!is_array($personalizaciones)) {
            throw new InvalidArgumentException(
                "Las personalizaciones del producto no son válidas."
            );
        }

        $detalleValidado = [];

        foreach ($personalizaciones as $etiqueta => $valor) {

            // También permite recibir elementos como "Sabor: Fresa".
            if (is_int($etiqueta) && is_string($valor)) {
                $partes = explode(":", $valor, 2);

                if (count($partes) !== 2) {
                    throw new InvalidArgumentException(
                        "Una personalización no tiene un formato válido."
                    );
                }

                $etiqueta = trim($partes[0]);
                $valor = trim($partes[1]);
            }

            if (!is_string($etiqueta) || !is_string($valor)) {
                throw new InvalidArgumentException(
                    "Una personalización contiene datos no válidos."
                );
            }

            $etiqueta = trim($etiqueta);
            $valor = trim($valor);

            if (
                !isset($opcionesPermitidas[$etiqueta]) ||
                !in_array($valor, $opcionesPermitidas[$etiqueta], true)
            ) {
                throw new InvalidArgumentException(
                    "La personalización seleccionada para " . $nombre . " no está permitida."
                );
            }

            $detalleValidado[] = $etiqueta . ": " . $valor;
        }

        $detalle = implode(" | ", $detalleValidado);

        $total += $precio;

        $productos_validos[] = [
            "nombre" => $nombre,
            "precio" => $precio,
            "detalle" => $detalle
        ];
    }

    $stmtProducto->close();

    /*
    |--------------------------------------------------------------------------
    | Registrar pedido y detalles
    |--------------------------------------------------------------------------
    */
    $conexion->begin_transaction();
    $transaccion_iniciada = true;

    $sqlPedido = "INSERT INTO pedidos
        (id_cliente, total, estado, metodo_pago, estado_pago)
        VALUES (?, ?, 'pendiente', 'Nequi', 'pendiente')";

    $stmtPedido = $conexion->prepare($sqlPedido);

    if (!$stmtPedido) {
        throw new RuntimeException("No fue posible preparar el pedido.");
    }

    $stmtPedido->bind_param("id", $id_cliente, $total);
    $stmtPedido->execute();

    $id_pedido = $conexion->insert_id;
    $stmtPedido->close();

    $sqlDetalle = "INSERT INTO detalle_pedido
        (id_pedido, nombre_producto, cantidad, precio_unitario, subtotal)
        VALUES (?, ?, 1, ?, ?)";

    $stmtDetalle = $conexion->prepare($sqlDetalle);

    if (!$stmtDetalle) {
        throw new RuntimeException("No fue posible preparar el detalle del pedido.");
    }

    foreach ($productos_validos as $producto) {

        $nombreCompleto = $producto["nombre"];

        if ($producto["detalle"] !== "") {
            $nombreCompleto .= " (" . $producto["detalle"] . ")";
        }

        $precio = $producto["precio"];
        $subtotal = $precio;

        $stmtDetalle->bind_param(
            "isdd",
            $id_pedido,
            $nombreCompleto,
            $precio,
            $subtotal
        );

        $stmtDetalle->execute();
    }

    $stmtDetalle->close();

    $conexion->commit();
    $transaccion_iniciada = false;

    echo json_encode([
        "success" => true,
        "mensaje" => "Tu pedido fue registrado correctamente.",
        "id_pedido" => $id_pedido,
        "total" => $total
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {

    if ($transaccion_iniciada) {
        $conexion->rollback();
    }

    error_log("Error al guardar pedido: " . $e->getMessage());

    if ($e instanceof InvalidArgumentException) {
        responder(400, $e->getMessage());
    }

    responder(500, "No fue posible registrar el pedido. Inténtalo nuevamente.");
}
?>

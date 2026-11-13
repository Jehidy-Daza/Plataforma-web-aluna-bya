
<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["id_cliente"])) {
    header("Location: login.php");
    exit;
}

$id_cliente = $_SESSION["id_cliente"];
$id_pedido = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id_pedido) {
    die("Número de pedido no válido.");
}

$sql = "SELECT id_pedido, total, estado, estado_pago
        FROM pedidos
        WHERE id_pedido = ? AND id_cliente = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id_pedido, $id_cliente);
$stmt->execute();
$resultado = $stmt->get_result();
$pedido = $resultado->fetch_assoc();
$stmt->close();

if (!$pedido) {
    die("No encontramos este pedido en tu cuenta.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pago | Aluna Bya</title>

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
    max-width: 600px;
    margin: 40px auto;
    background: white;
    padding: 30px;
    border-radius: 18px;
    box-shadow: 0 4px 15px #00000012;
}

h1, h2 {
    color: #477da9;
    text-align: center;
}

.datos {
    background: #eaf5ff;
    padding: 20px;
    border-radius: 12px;
    margin: 20px 0;
}

.numero {
    font-size: 26px;
    font-weight: bold;
    text-align: center;
    color: #477da9;
}

.total {
    font-size: 24px;
    text-align: center;
    color: #a45c8b;
    font-weight: bold;
}

input {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border: 1px solid #c5d9e9;
    border-radius: 8px;
    font-family: inherit;
    font-size: 12pt;
    box-sizing: border-box;
}

button, .boton {
    display: block;
    width: 100%;
    padding: 12px;
    background: #7caed6;
    color: white;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    font-family: inherit;
    font-size: 12pt;
    box-sizing: border-box;
}

button:hover, .boton:hover {
    background: #548dbd;
}

.estado {
    text-align: center;
    color: #a45c8b;
    font-weight: bold;
}

</style>
</head>

<body>

<div class="contenedor">

    <h1>Aluna Bya 💙</h1>
    <h2>Pago de tu pedido</h2>

    <p>Pedido número:
        <strong>#<?php echo $pedido["id_pedido"]; ?></strong>
    </p>

    <p class="total">
        $<?php echo number_format($pedido["total"], 0, ",", "."); ?> COP
    </p>

    <?php if ($pedido["estado_pago"] === "pendiente"): ?>

        <div class="datos">
            <h2>Transferencia por Nequi</h2>

            <p>Realiza tu transferencia a estos datos:</p>

            <p><strong>Titular:</strong> Jehidy Daza</p>

            <p class="numero">312 306 3188</p>

            <p>Después de realizar el pago, registra la referencia de tu transferencia.</p>
        </div>

        <form action="reportar_pago.php" method="POST">

            <input type="hidden" name="id_pedido"
                   value="<?php echo $pedido["id_pedido"]; ?>">

            <label for="referencia">
                Referencia de la transferencia:
            </label>

            <input
                type="text"
                id="referencia"
                name="referencia"
                maxlength="100"
                required
                placeholder="Escribe la referencia de Nequi">

            <button type="submit">
                Reportar mi pago
            </button>

        </form>

    <?php else: ?>

        <p class="estado">
            Tu pago fue reportado y está pendiente de verificación.
        </p>

    <?php endif; ?>

    <br>

    <a class="boton" href="mis_pedidos.php">
        Ver mis pedidos
    </a>

</div>

</body>
</html>

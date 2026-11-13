
<?php
session_start();
require_once "conexion.php";

// Proteger el panel: solo puede entrar una administradora
if (
    !isset($_SESSION["id_cliente"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] !== "admin"
) {
    header("Location: login.php");
    exit;
}

// Consultar los pedidos
$sql = "SELECT p.id_pedido, p.fecha_pedido, p.total,
               p.estado, p.estado_pago, p.referencia_pago,
               c.nombre, c.telefono, c.correo
        FROM pedidos p
        INNER JOIN clientes c ON p.id_cliente = c.id_cliente
        ORDER BY p.fecha_pedido DESC";

$resultado = $conexion->query($sql);

function e($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, "UTF-8");
}

// Preparar número para WhatsApp
function numeroWhatsApp($telefono) {
    $numero = preg_replace('/\D/', '', (string)$telefono);

    if (strlen($numero) === 10) {
        $numero = "57" . $numero;
    }

    return $numero;
}

// Traducir estado del pedido y asignar color
function claseEstadoPedido($estado) {
    $estados = [
        "pendiente" => "pendiente",
        "confirmado" => "confirmado",
        "en_preparacion" => "preparacion",
        "listo" => "listo",
        "entregado" => "entregado",
        "cancelado" => "cancelado"
    ];

    return $estados[$estado] ?? "desconocido";
}

function nombreEstadoPedido($estado) {
    $nombres = [
        "pendiente" => "Pendiente",
        "confirmado" => "Confirmado",
        "en_preparacion" => "En preparación",
        "listo" => "Listo",
        "entregado" => "Entregado",
        "cancelado" => "Cancelado"
    ];

    return $nombres[$estado] ?? ucfirst(str_replace("_", " ", $estado));
}

// Traducir estado del pago y asignar color
function claseEstadoPago($estado) {
    $estados = [
        "pendiente" => "pago-pendiente",
        "reportado" => "pago-reportado",
        "verificado" => "pago-verificado"
    ];

    return $estados[$estado] ?? "desconocido";
}

function nombreEstadoPago($estado) {
    $nombres = [
        "pendiente" => "Pendiente",
        "reportado" => "Reportado",
        "verificado" => "Verificado"
    ];

    return $nombres[$estado] ?? ucfirst($estado);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Administración | Aluna Bya</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 25px;
    background: #f2f8ff;
    color: #35465c;
    font-family: "Times New Roman", serif;
    font-size: 12pt;
}

.contenedor {
    max-width: 1200px;
    margin: auto;
}

h1 {
    color: #477da9;
    text-align: center;
}

h2 {
    color: #477da9;
}

.encabezado, .tabla-contenedor {
    background: white;
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 25px;
    box-shadow: 0 3px 12px #00000012;
}

.tabla-contenedor {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1100px;
}

th, td {
    padding: 12px;
    border-bottom: 1px solid #e4edf5;
    text-align: left;
}

th {
    color: #477da9;
    background: #eaf5ff;
}

tr:hover td {
    background: #f8fbff;
}

/* Etiquetas de estados */
.estado {
    display: inline-block;
    padding: 6px 11px;
    border-radius: 20px;
    font-size: 11pt;
    font-weight: bold;
    white-space: nowrap;
}

/* Estados del pedido */
.pendiente {
    background: #fff2cc;
    color: #946200;
}

.confirmado {
    background: #dceeff;
    color: #28649b;
}

.preparacion {
    background: #ffe5cc;
    color: #a65316;
}

.listo {
    background: #d9f5df;
    color: #26743b;
}

.entregado {
    background: #ccefe9;
    color: #17685b;
}

.cancelado {
    background: #ffe0e0;
    color: #a52d35;
}

/* Estados del pago */
.pago-pendiente {
    background: #fff2cc;
    color: #946200;
}

.pago-reportado {
    background: #e9e0ff;
    color: #6843a0;
}

.pago-verificado {
    background: #d9f5df;
    color: #26743b;
}

.desconocido {
    background: #eeeeee;
    color: #555555;
}

/* Botones */
.boton {
    display: inline-block;
    padding: 9px 15px;
    border-radius: 20px;
    background: #7caed6;
    color: white;
    text-decoration: none;
    border: none;
    cursor: pointer;
    font-family: inherit;
    font-size: 12pt;
    margin: 3px;
}

.boton:hover {
    opacity: 0.85;
}

.salir {
    background: #b46d8e;
}

.whatsapp {
    background: #25a85a;
}

@media (max-width: 600px) {
    body {
        padding: 12px;
    }

    .encabezado, .tabla-contenedor {
        padding: 15px;
    }
}
</style>
</head>

<body>

<div class="contenedor">

    <div class="encabezado">
        <h1>Aluna Bya 💙</h1>
        <h2>Panel de administración</h2>

        <p>
            Bienvenida,
            <strong><?= e($_SESSION["nombre_cliente"]) ?></strong>
        </p>

        <p>Desde aquí puedes consultar y administrar los pedidos de tu emprendimiento.</p>

        <a href="index.php" class="boton">Ver tienda</a>
        <a href="logout.php" class="boton salir">Cerrar sesión</a>
    </div>

    <div class="tabla-contenedor">

        <h2>Pedidos recibidos</h2>

        <?php if ($resultado && $resultado->num_rows > 0): ?>

        <table>
            <thead>
                <tr>
                    <th>Pedido</th>
                    <th>Cliente</th>
                    <th>Teléfono</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado del pedido</th>
                    <th>Estado del pago</th>
                    <th>Referencia</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>

                <?php while ($pedido = $resultado->fetch_assoc()): ?>

                <?php
                $numero = numeroWhatsApp($pedido["telefono"]);

                $mensaje = "Hola " . $pedido["nombre"] .
                    ", te contactamos de Aluna Bya 💙. " .
                    "Queremos comunicarnos contigo sobre tu pedido #" .
                    $pedido["id_pedido"] . ". ¡Gracias por tu compra!";

                $enlaceWhatsApp = "https://wa.me/" . $numero .
                    "?text=" . urlencode($mensaje);
                ?>

                <tr>
                    <td>#<?= (int)$pedido["id_pedido"] ?></td>

                    <td><?= e($pedido["nombre"]) ?></td>

                    <td><?= e($pedido["telefono"]) ?></td>

                    <td><?= e($pedido["fecha_pedido"]) ?></td>

                    <td>
                        $<?= number_format((float)$pedido["total"], 0, ",", ".") ?> COP
                    </td>

                    <td>
                        <span class="estado <?= e(claseEstadoPedido($pedido["estado"])) ?>">
                            <?= e(nombreEstadoPedido($pedido["estado"])) ?>
                        </span>
                    </td>

                    <td>
                        <span class="estado <?= e(claseEstadoPago($pedido["estado_pago"])) ?>">
                            <?= e(nombreEstadoPago($pedido["estado_pago"])) ?>
                        </span>
                    </td>

                    <td>
                        <?= e($pedido["referencia_pago"] ?: "Sin reportar") ?>
                    </td>

                    <td>
                        <a
                            href="detalle_admin.php?id=<?= (int)$pedido["id_pedido"] ?>"
                            class="boton"
                        >
                            Ver detalle
                        </a>

                        <a
                            href="<?= e($enlaceWhatsApp) ?>"
                            class="boton whatsapp"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            WhatsApp
                        </a>
                    </td>
                </tr>

                <?php endwhile; ?>

            </tbody>
        </table>

        <?php else: ?>

            <p>Todavía no hay pedidos registrados.</p>

        <?php endif; ?>

    </div>

</div>

</body>
</html>




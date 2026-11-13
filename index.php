
<?php
session_start();

$productos = [
    [
        "categoria" => " Donitas",
        "items" => [
            ["nombre" => "Caja sencilla", "precio" => 12000, "descripcion" => "6 mini donitas, 2 salsas y 3 toppings.", "imagen" => "Caja_Sencilla.jpeg"],
            ["nombre" => "Caja especial", "precio" => 15000, "descripcion" => "6 mini donitas, 1 salsa y 2 toppings.", "imagen" => "Caja_Especial.jpeg"],
            ["nombre" => "Vaso pequeño", "precio" => 14000, "descripcion" => "5 mini donitas con chocolate y toppings.", "imagen" => "Vaso_Mini.jpeg"],
            ["nombre" => "Vaso grande", "precio" => 18000, "descripcion" => "8 mini donitas con chocolate y toppings.", "imagen" => "Vaso_Grande.jpeg"]
        ]
    ],
    [
        "categoria" => "Cuchareables",
        "items" => [
            ["nombre" => "Cuchareable mini", "precio" => 8000, "descripcion" => "Elige tu sabor favorito.", "imagen" => ""],
            ["nombre" => "Cuchareable mediano", "precio" => 12000, "descripcion" => "Elige tu sabor favorito.", "imagen" => ""],
            ["nombre" => "Cuchareable grande", "precio" => 16000, "descripcion" => "Elige tu sabor favorito.", "imagen" => ""]
        ]
    ],
    [
        "categoria" => "Pavés",
        "items" => [
            ["nombre" => "Pavé 10 onzas", "precio" => 13000, "descripcion" => "Presentación de 10 onzas. Elige tu sabor favorito.", "imagen" => "Pavespej.jpeg"]
        ]
    ],
    [
        "categoria" => "Obleas cuchareables",
        "items" => [
            ["nombre" => "Oblea mini", "precio" => 10000, "descripcion" => "Oblea triturada, crema de la casa, 1 fruta y 1 topping.", "imagen" => ""],
            ["nombre" => "Oblea mediana", "precio" => 15000, "descripcion" => "Oblea triturada, crema de la casa, 2 frutas y 2 toppings.", "imagen" => ""],
            ["nombre" => "Oblea grande", "precio" => 25000, "descripcion" => "Oblea triturada, crema de la casa, 3 frutas y toppings.", "imagen" => ""]
        ]
    ],
    [
        "categoria" => "Frutas con crema",
        "items" => [
            ["nombre" => "Frutas con crema mini", "precio" => 9000, "descripcion" => "Galleta triturada, crema de la casa, 1 fruta, 1 salsa y 1 topping.", "imagen" => ""],
            ["nombre" => "Frutas con crema mediana", "precio" => 14000, "descripcion" => "Galleta triturada, Ducales, crema de la casa, 2 frutas, 1 salsa y 2 toppings.", "imagen" => "Frutas_Mediano.jpeg"],
            ["nombre" => "Frutas con crema grande", "precio" => 17000, "descripcion" => "Galleta triturada, crema de la casa, 2 frutas, 2 salsas, queso y 2 toppings.", "imagen" => "Frutas_Grandes.jpeg"]
        ]
    ],
    [
        "categoria" => "Choco bowls",
        "items" => [
            ["nombre" => "Choco bowl mini", "precio" => 11000, "descripcion" => "Crema de la casa, 1 fruta y chocolate.", "imagen" => ""],
            ["nombre" => "Choco bowl mediano", "precio" => 15000, "descripcion" => "Crema de la casa, 2 frutas y chocolate.", "imagen" => ""],
            ["nombre" => "Choco bowl grande", "precio" => 25000, "descripcion" => "Crema de la casa, 3 frutas y chocolate.", "imagen" => ""]
        ]
    ],
    [
        "categoria" => "Waffles",
        "items" => [
            ["nombre" => "Waffle individual", "precio" => 7000, "descripcion" => "Incluye 1 salsa y 1 topping.", "imagen" => ""],
            ["nombre" => "Waffle amor", "precio" => 26000, "descripcion" => "Incluye 4 toppings, helado y salsa.", "imagen" => ""],
            ["nombre" => "Waffle saludable", "precio" => 20000, "descripcion" => "Preparado con pollo desmechado y lechuga.", "imagen" => ""]
        ]
    ]
];

function obtenerOpciones($nombre) {
    $saboresCuchareables = ["Fresa", "Mango", "Chocorramo", "Gansito", "Leche Klim", "Oreo"];
    $saboresPaves = ["Milo", "Frutos rojos", "Maracuyá", "Oreo", "Leche Klim", "Chocorramo", "Gansito"];
    $frutas = ["Fresa", "Mango", "Banano", "Durazno", "Kiwi"];
    $salsas = ["Chocolate", "Uva", "Fresa", "Lecherita"];
    $toppings = ["Oreo triturada", "Fresa", "Mango", "Banano", "Chocorramo", "Gansito", "Maní", "Chispitas de chocolate"];

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
        return ["Fruta" => $frutas, "Topping" => $toppings];
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
        return ["Fruta 1" => $frutas, "Fruta 2" => $frutas];
    }

    if (str_contains($nombre, "Choco bowl grande")) {
        return [
            "Fruta 1" => $frutas,
            "Fruta 2" => $frutas,
            "Fruta 3" => $frutas
        ];
    }

    if (str_contains($nombre, "Waffle individual")) {
        return ["Salsa" => $salsas, "Topping" => $toppings];
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Aluna Bya | Postres hechos con amor</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: "Times New Roman", serif;
    font-size: 12pt;
    background: #f7fbff;
    color: #35465c;
}

header {
    background: #dceeff;
    padding: 15px 5%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}

.logo h1 {
    color: #477da9;
    margin: 0;
}

nav {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: center;
}

nav a {
    text-decoration: none;
    color: #315d83;
}

nav a:hover {
    color: #a45c8b;
}

.portada {
    text-align: center;
    padding: 55px 15px;
    background: linear-gradient(135deg, #eaf5ff, #fff0f7);
}

.portada h2 {
    font-size: 30px;
    color: #477da9;
}

.portada p {
    max-width: 650px;
    margin: auto;
}

.contenedor {
    width: 92%;
    max-width: 1200px;
    margin: 35px auto;
}

.titulo-categoria {
    color: #477da9;
    border-bottom: 2px solid #dceeff;
    padding-bottom: 8px;
    margin-top: 35px;
}

.catalogo {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    gap: 20px;
}

.tarjeta {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 3px 12px #00000012;
    padding-bottom: 18px;
}

.tarjeta img {
    width: 100%;
    height: 320px;
    object-fit: contain;
    display: block;
    background: #ffffff;
    padding: 8px;
}

.sin-imagen {
    height: 320px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #eaf5ff;
    color: #477da9;
    text-align: center;
    padding: 15px;
}

.tarjeta h3,
.tarjeta p {
    margin: 12px 15px;
}

.precio {
    color: #a45c8b;
    font-weight: bold;
}

.tarjeta button,
.boton {
    background: #7caed6;
    color: white;
    border: none;
    padding: 10px 16px;
    border-radius: 20px;
    cursor: pointer;
    font-family: inherit;
    font-size: 12pt;
}

.tarjeta button {
    margin: 5px 15px;
}

button:hover,
.boton:hover {
    background: #548dbd;
}

.opciones-producto {
    padding: 0 15px;
}

select {
    width: 100%;
    padding: 8px;
    margin: 5px 0 12px;
    border: 1px solid #c5d9e9;
    border-radius: 7px;
    font-family: inherit;
    font-size: 12pt;
    background: white;
}

#carrito {
    position: fixed;
    top: 0;
    right: -450px;
    width: 100%;
    max-width: 420px;
    height: 100vh;
    background: white;
    box-shadow: -4px 0 15px #0002;
    z-index: 10;
    padding: 22px;
    overflow-y: auto;
    transition: right .3s;
}

#carrito.abierto {
    right: 0;
}

#carrito h2 {
    color: #477da9;
}

.cerrar {
    float: right;
    border: none;
    background: none;
    font-size: 25px;
    cursor: pointer;
}

.item-carrito {
    border-bottom: 1px solid #ddd;
    padding: 12px 0;
}

.item-carrito button {
    margin: 5px;
}

.total {
    font-weight: bold;
    font-size: 18px;
    color: #a45c8b;
}

.contacto-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-top: 25px;
}

.contacto-card {
    background: #ffffff;
    padding: 25px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 3px 12px #00000012;
}

.contacto-card h3 {
    color: #477da9;
}

.contacto-card p {
    color: #35465c;
}

.contacto-card a {
    display: inline-block;
    margin-top: 10px;
    color: #477da9;
    text-decoration: none;
    font-weight: bold;
}

.contacto-card a:hover {
    color: #a45c8b;
    text-decoration: underline;
}

footer {
    background: #dceeff;
    text-align: center;
    padding: 25px;
    margin-top: 50px;
}

@media(max-width: 600px) {
    header {
        justify-content: center;
    }

    nav {
        justify-content: center;
    }

    .tarjeta img,
    .sin-imagen {
        height: 320px;
    }
}
</style>
</head>

<body>

<header>
    <div class="logo">
        <h1>Aluna Bya</h1>
    </div>

    <nav>
        <a href="#inicio">Inicio</a>
        <a href="#catalogo">Catálogo</a>
        <a href="#contacto">Contacto</a>

        <?php if (isset($_SESSION["id_cliente"])): ?>
            <a href="mi_cuenta.php">Mi cuenta</a>
            <a href="mis_pedidos.php">Mis pedidos</a>
            <a href="logout.php">Cerrar sesión</a>
        <?php else: ?>
            <a href="login.php">Iniciar sesión</a>
            <a href="registro.php">Registrarme</a>
        <?php endif; ?>

        <button class="boton" onclick="abrirCarrito()">
            Carrito (<span id="contador">0</span>)
        </button>
    </nav>
</header>

<section class="portada" id="inicio">
    <h2>Un antojo hecho con amor 💙</h2>
    <p>Descubre nuestros deliciosos postres y personaliza tus favoritos en Aluna Bya.</p>
</section>

<main class="contenedor" id="catalogo">

    <h2 style="text-align:center;color:#477da9;">
        Nuestro catálogo
    </h2>

    <?php foreach ($productos as $grupo): ?>

        <h2 class="titulo-categoria">
            <?php echo htmlspecialchars($grupo["categoria"]); ?>
        </h2>

        <div class="catalogo">

            <?php foreach ($grupo["items"] as $producto): ?>

                <article class="tarjeta">

                    <?php if (!empty($producto["imagen"])): ?>

                        <img
                            src="img/<?php echo htmlspecialchars($producto["imagen"]); ?>"
                            alt="<?php echo htmlspecialchars($producto["nombre"]); ?>"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

                        <div class="sin-imagen" style="display:none;">
                            Fotografía próximamente 💙
                        </div>

                    <?php else: ?>

                        <div class="sin-imagen">
                            Fotografía próximamente 💙
                        </div>

                    <?php endif; ?>

                    <h3><?php echo htmlspecialchars($producto["nombre"]); ?></h3>

                    <p><?php echo htmlspecialchars($producto["descripcion"]); ?></p>

                    <p class="precio">
                        $<?php echo number_format($producto["precio"], 0, ",", "."); ?> COP
                    </p>

                    <div class="opciones-producto">

                        <?php foreach (obtenerOpciones($producto["nombre"]) as $grupoOpciones => $lista): ?>

                            <label><?php echo htmlspecialchars($grupoOpciones); ?></label>

                            <select
                                class="personalizacion"
                                data-grupo="<?php echo htmlspecialchars($grupoOpciones); ?>">

                                <option value="">Seleccionar</option>

                                <?php foreach ($lista as $opcion): ?>
                                    <option value="<?php echo htmlspecialchars($opcion); ?>">
                                        <?php echo htmlspecialchars($opcion); ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>

                        <?php endforeach; ?>

                    </div>

                    <button
                        onclick="agregarProducto(this)"
                        data-nombre="<?php echo htmlspecialchars($producto["nombre"]); ?>"
                        data-precio="<?php echo $producto["precio"]; ?>">
                        Agregar al carrito
                    </button>

                </article>

            <?php endforeach; ?>

        </div>

    <?php endforeach; ?>

</main>

<section class="contenedor" id="contacto">

    <h2 class="titulo-categoria">Contáctanos 💙</h2>

    <p>¡Estamos felices de atenderte! Puedes comunicarte con nosotros a través de nuestros canales oficiales.</p>

    <div class="contacto-info">

        <div class="contacto-card">
            <h3>📍 Nuestra ubicación</h3>
            <p>Tauramena, Casanare, Colombia.</p>
        </div>

        <div class="contacto-card">
            <h3>💬 WhatsApp</h3>
            <p>320 365 2416</p>
            <a href="https://wa.me/573203652416" target="_blank" rel="noopener noreferrer">
                Escríbenos por WhatsApp
            </a>
        </div>

        <div class="contacto-card">
            <h3>📸 Instagram</h3>
            <p>@Aluna.bya</p>
            <a href="https://www.instagram.com/aluna.bya/" target="_blank" rel="noopener noreferrer">
                Visita nuestro Instagram
            </a>
        </div>

        <div class="contacto-card">
            <h3>🎵 TikTok</h3>
            <p>@Aluna.bya</p>
            <a href="https://www.tiktok.com/@aluna.bya" target="_blank" rel="noopener noreferrer">
                Visita nuestro TikTok
            </a>
        </div>

    </div>

</section>

<aside id="carrito">

    <button class="cerrar" onclick="cerrarCarrito()">×</button>

    <h2>Mi carrito 🛒</h2>

    <div id="lista-carrito"></div>

    <p class="total">
        Total: $<span id="total">0</span> COP
    </p>

    <button class="boton" onclick="vaciarCarrito()">
        Vaciar carrito
    </button>

    <button class="boton" onclick="continuarCompra()">
        Continuar compra
    </button>

</aside>

<footer>
    <p>© <?php echo date("Y"); ?> Aluna Bya. Todos los derechos reservados.</p>
    <p>Hecho con amor 💙</p>
    <p>Tauramena, Casanare, Colombia.</p>
</footer>

<script>
let carrito = [];

function abrirCarrito() {
    document.getElementById("carrito").classList.add("abierto");
}

function cerrarCarrito() {
    document.getElementById("carrito").classList.remove("abierto");
}

function agregarProducto(boton) {
    const tarjeta = boton.closest(".tarjeta");
    const nombre = boton.dataset.nombre;
    const precio = Number(boton.dataset.precio);
    const personalizaciones = [];

    const selects = tarjeta.querySelectorAll(".personalizacion");

    for (const select of selects) {
        if (!select.value) {
            alert("Por favor selecciona: " + select.dataset.grupo);
            select.focus();
            return;
        }

        personalizaciones.push(
            select.dataset.grupo + ": " + select.value
        );
    }

    carrito.push({
        nombre: nombre,
        precio: precio,
        personalizaciones: personalizaciones
    });

    actualizarCarrito();
    abrirCarrito();
}

function actualizarCarrito() {
    const lista = document.getElementById("lista-carrito");
    lista.innerHTML = "";

    let total = 0;

    carrito.forEach((producto, indice) => {
        total += producto.precio;

        const div = document.createElement("div");
        div.className = "item-carrito";

        const titulo = document.createElement("strong");
        titulo.textContent = producto.nombre;
        div.appendChild(titulo);

        producto.personalizaciones.forEach(detalle => {
            const p = document.createElement("p");
            p.textContent = detalle;
            div.appendChild(p);
        });

        const precio = document.createElement("p");
        precio.textContent =
            "$" + producto.precio.toLocaleString("es-CO") + " COP";
        div.appendChild(precio);

        const eliminar = document.createElement("button");
        eliminar.textContent = "Eliminar";
        eliminar.onclick = () => eliminarProducto(indice);
        div.appendChild(eliminar);

        lista.appendChild(div);
    });

    document.getElementById("total").textContent =
        total.toLocaleString("es-CO");

    document.getElementById("contador").textContent = carrito.length;
}

function eliminarProducto(indice) {
    carrito.splice(indice, 1);
    actualizarCarrito();
}

function vaciarCarrito() {
    carrito = [];
    actualizarCarrito();
}

async function continuarCompra() {
    if (carrito.length === 0) {
        alert("Tu carrito está vacío.");
        return;
    }

    <?php if (!isset($_SESSION["id_cliente"])): ?>

        alert("Para continuar con tu compra, primero debes iniciar sesión o registrarte.");
        window.location.href = "login.php";
        return;

    <?php endif; ?>

    const confirmar = confirm(
        "¿Deseas registrar tu pedido en Aluna Bya?"
    );

    if (!confirmar) {
        return;
    }

    try {
        const respuesta = await fetch("guardar_pedido.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                productos: carrito
            })
        });

        const resultado = await respuesta.json();

        if (!respuesta.ok || !resultado.success) {
            alert(resultado.mensaje || "No se pudo registrar el pedido.");
            return;
        }

        carrito = [];
        actualizarCarrito();

        window.location.href = "pago.php?id=" + resultado.id_pedido;

    } catch (error) {
        alert("Ocurrió un error al comunicarse con el servidor. Inténtalo nuevamente.");
        console.error(error);
    }
}
</script>

</body>
</html>




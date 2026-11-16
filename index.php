<?php
require "conexion.php";

$marcas = ["Zanella", "Gilera", "Honda", "Yamaha", "Rouser", "Guerrero", "Cerro", "Otra"];
$servicios = ["Mantenimiento básico", "Frenos y chasis", "Motor y personalización", "Carburación", "Regulación de válvulas", "Cambio de aceite", "Revisión de batería", "Otro"];

$nombre = "";
$telefono = "";
$marca = "";
$servicio = "";
$fecha = "";
$mensaje = "";
$errores = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $telefono = trim($_POST["telefono"]);
    $marca = $_POST["marca"];
    $servicio = $_POST["servicio"];
    $fecha = $_POST["fecha"];
    $mensaje = trim($_POST["mensaje"]);

    // validar
    if (strlen($nombre) < 3 || strlen($nombre) > 60) {
        $errores[] = "El nombre debe tener entre 3 y 60 letras.";
    }

    if (!preg_match("/^[0-9+ -]{8,20}$/", $telefono)) {
        $errores[] = "El teléfono no es válido.";
    }

    if (!in_array($marca, $marcas)) {
        $errores[] = "Elegí una marca.";
    }

    if (!in_array($servicio, $servicios)) {
        $errores[] = "Elegí un servicio.";
    }

    if ($fecha == "" || $fecha < date("Y-m-d")) {
        $errores[] = "La fecha no puede ser anterior a hoy.";
    }

    if (strlen($mensaje) > 300) {
        $errores[] = "El mensaje es muy largo.";
    }

    // guardar turno
    if (count($errores) == 0) {
        $stmt = $conexion->prepare("INSERT INTO turnos (nombre, telefono, marca, servicio, fecha, mensaje) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nombre, $telefono, $marca, $servicio, $fecha, $mensaje);
        $stmt->execute();

        header("Location: index.php?turno=ok#turnos");
        exit;
    }
}

$limite = 8;
if (isset($_GET["todos"])) {
    $limite = 100;
}
$productos = $conexion->query("SELECT nombre, precio, imagen FROM productos ORDER BY id LIMIT $limite");
$total = $conexion->query("SELECT COUNT(*) AS total FROM productos")->fetch_assoc();

$fotos = glob("assets/galeria/*.{jpg,jpeg,png,webp,avif}", GLOB_BRACE);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zampedri</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Days+One&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img class="logo" src="assets/logo.jpeg" alt="logo">
                ZAMPEDRI
            </a>
            <div class="menu">
                <a href="#servicios">SERVICIOS</a>
                <a href="#productos">PRODUCTOS</a>
                <a href="#contacto">CONTACTO</a>
            </div>
        </div>
    </nav>

    <section class="principal">
        <div class="contenido">
            <h2 class="titulo">MOTOMECÁNICA</h2>
            <p>En Zampedri nos dedicamos a la mecánica de motos de distintas marcas. Realizamos mantenimiento, reparaciones y puesta a punto, además de trabajos en motores y modificaciones según las necesidades de cada moto.</p>
        </div>
        <div class="imagen-principal">
            <img src="assets/portada.avif" alt="moto en reparación en el taller">
        </div>
        <div class="boton-centro">
            <a class="boton" href="#turnos">AGENDAR TURNO</a>
        </div>
    </section>

    <section class="servicios" id="servicios">
        <h2 class="titulo">SERVICIOS</h2>
        <div class="servicios-prin">
            <div class="servicio">
                <h3>Mantenimiento básico</h3>
                <p>Revisión general y ajustes, cambio de aceite y filtro, limpieza del filtro de aire y revisión de batería.</p>
                <div class="servicio-fotos">
                    <img src="assets/servicios/mantenimiento1.avif" alt="mecánico revisando un motor">
                    <img src="assets/servicios/mantenimiento2.avif" alt="motor de moto">
                </div>
            </div>
            <div class="servicio">
                <h3>Frenos y chasis</h3>
                <p>Revisión de frenos, cambio de pastillas, horquilla, dirección y transmisión.</p>
                <div class="servicio-fotos">
                    <img src="assets/servicios/frenos1.avif" alt="manos de mecánico en una moto">
                    <img src="assets/servicios/frenos2.avif" alt="motor en reparación">
                </div>
            </div>
            <div class="servicio servicio-ancho">
                <h3>Motor y personalización</h3>
                <div class="servicio-motor">
                    <p>Carburación, regulación de válvulas, rectificación y optimización de motores, con instalación de accesorios y modificaciones personalizadas.</p>
                    <img src="assets/servicios/motor.avif" alt="mecánico armando un carburador">
                </div>
            </div>
        </div>
    </section>

    <section class="productos" id="productos">
        <h2 class="titulo">PRODUCTOS</h2>
        <div class="productos-prin">
            <?php while ($p = $productos->fetch_assoc()) { ?>
                <div class="producto">
                    <?php if ($p["imagen"]) { ?>
                        <img src="assets/productos/<?php echo limpiar($p["imagen"]); ?>" alt="<?php echo limpiar($p["nombre"]); ?>">
                    <?php } else { ?>
                        <img src="assets/sin-foto.svg" alt="sin foto disponible">
                    <?php } ?>
                    <span class="producto-nombre"><?php echo limpiar($p["nombre"]); ?></span>
                    <span class="producto-precio">$<?php echo number_format($p["precio"], 0, ",", "."); ?></span>
                </div>
            <?php } ?>
        </div>

        <?php if ($total["total"] > 8) { ?>
            <div class="boton-centro">
                <?php if (isset($_GET["todos"])) { ?>
                    <a class="boton" href="index.php#productos">VER MENOS</a>
                <?php } else { ?>
                    <a class="boton" href="index.php?todos=1#productos">VER MÁS PRODUCTOS</a>
                <?php } ?>
            </div>
        <?php } ?>
    </section>

    <section class="carrusel">
        <h2 class="titulo">GALERÍA DE FOTOS</h2>
        <?php if (count($fotos) == 0) { ?>
            <p class="aviso">Todavía no hay fotos.</p>
        <?php } else { ?>
            <div id="carouselFotos" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <?php foreach ($fotos as $i => $foto) { ?>
                        <button type="button" data-bs-target="#carouselFotos" data-bs-slide-to="<?php echo $i; ?>" <?php if ($i == 0) { echo 'class="active"'; } ?> aria-label="Foto <?php echo $i + 1; ?>"></button>
                    <?php } ?>
                </div>
                <div class="carousel-inner">
                    <?php foreach ($fotos as $i => $foto) { ?>
                        <div class="carousel-item <?php if ($i == 0) { echo "active"; } ?>">
                            <img src="<?php echo $foto; ?>" class="d-block w-100" alt="foto del taller">
                        </div>
                    <?php } ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselFotos" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselFotos" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
        <?php } ?>
    </section>

    <section class="turnos" id="turnos">
        <h2 class="titulo">SOLICITAR TURNO</h2>

        <?php if (isset($_GET["turno"])) { ?>
            <div class="alert alert-success">¡Turno solicitado! Te vamos a contactar.</div>
        <?php } ?>

        <?php if (count($errores) > 0) { ?>
            <div class="alert alert-danger">
                <?php foreach ($errores as $error) { ?>
                    <p><?php echo $error; ?></p>
                <?php } ?>
            </div>
        <?php } ?>

        <form method="post" action="index.php#turnos" class="formulario">
            <label for="nombre">Nombre y apellido</label>
            <input type="text" id="nombre" name="nombre" class="form-control" minlength="3" maxlength="60" required value="<?php echo limpiar($nombre); ?>">

            <label for="telefono">Teléfono</label>
            <input type="tel" id="telefono" name="telefono" class="form-control" maxlength="20" required value="<?php echo limpiar($telefono); ?>">

            <label for="marca">Marca de la moto</label>
            <select id="marca" name="marca" class="form-select" required>
                <option value="">Elegí una marca</option>
                <?php foreach ($marcas as $m) { ?>
                    <option value="<?php echo $m; ?>" <?php if ($marca == $m) { echo "selected"; } ?>><?php echo $m; ?></option>
                <?php } ?>
            </select>

            <label for="servicio">Servicio</label>
            <select id="servicio" name="servicio" class="form-select" required>
                <option value="">Elegí un servicio</option>
                <?php foreach ($servicios as $s) { ?>
                    <option value="<?php echo $s; ?>" <?php if ($servicio == $s) { echo "selected"; } ?>><?php echo $s; ?></option>
                <?php } ?>
            </select>

            <label for="fecha">Fecha preferida</label>
            <input type="date" id="fecha" name="fecha" class="form-control" min="<?php echo date("Y-m-d"); ?>" required value="<?php echo limpiar($fecha); ?>">

            <label for="mensaje">Mensaje (opcional)</label>
            <textarea id="mensaje" name="mensaje" class="form-control" rows="3" maxlength="300"><?php echo limpiar($mensaje); ?></textarea>

            <div class="boton-centro">
                <button type="submit" class="boton">ENVIAR</button>
            </div>
        </form>
    </section>

    <section class="contacto" id="contacto">
        <h2 class="titulo">CONTACTO</h2>
        <p>Av. Francisco Ramírez 2158, Los Charrúas</p>
        <iframe src="https://www.google.com/maps?q=Av.+Francisco+Ramírez+2158,+Los+Charrúas&output=embed" title="mapa"></iframe>
    </section>

    <footer>
        <p>Carlos Zampedri – Taller de Motos</p>
        <div class="redes">
            <a href="https://www.facebook.com/carlos.zampedri" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="https://www.tiktok.com/@carloszampedri" target="_blank" rel="noopener noreferrer" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
            <a href="https://www.instagram.com/zampedri" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

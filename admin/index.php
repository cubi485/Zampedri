<?php
session_start();
require "../conexion.php";

// cambiar clave
$clave_admin = "zampedri2026";
$aviso = "";

if (isset($_GET["salir"])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

if (isset($_POST["entrar"])) {
    if ($_POST["clave"] == $clave_admin) {
        $_SESSION["admin"] = true;
    } else {
        $aviso = "Clave incorrecta.";
    }
}

if (!isset($_SESSION["admin"])) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-dark text-white">
    <div class="container login">
        <h1 class="h3">Administración</h1>
        <?php if ($aviso != "") { ?>
            <div class="alert alert-danger"><?php echo $aviso; ?></div>
        <?php } ?>
        <form method="post">
            <label for="clave">Clave</label>
            <input type="password" id="clave" name="clave" class="form-control mb-3" required>
            <button type="submit" name="entrar" class="btn btn-danger">Entrar</button>
        </form>
    </div>
</body>
</html>
<?php
    exit;
}

if (isset($_POST["agregar"])) {
    $nombre = trim($_POST["nombre"]);
    $precio = (int)$_POST["precio"];
    $imagen = null;

    if ($_FILES["imagen"]["error"] == 0) {
        $extension = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));
        if (in_array($extension, ["jpg", "jpeg", "png", "webp"])) {
            $imagen = uniqid() . "." . $extension;
            move_uploaded_file($_FILES["imagen"]["tmp_name"], "../assets/productos/" . $imagen);
        }
    }

    if ($nombre == "" || $precio <= 0) {
        $aviso = "Completá nombre y precio.";
    } else {
        $stmt = $conexion->prepare("INSERT INTO productos (nombre, precio, imagen) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $nombre, $precio, $imagen);
        $stmt->execute();
        $aviso = "Producto agregado.";
    }
}

if (isset($_POST["guardar"])) {
    $id = (int)$_POST["id"];
    $nombre = trim($_POST["nombre"]);
    $precio = (int)$_POST["precio"];
    $imagen = null;

    if ($_FILES["imagen"]["error"] == 0) {
        $extension = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));
        if (in_array($extension, ["jpg", "jpeg", "png", "webp"])) {
            $imagen = uniqid() . "." . $extension;
            move_uploaded_file($_FILES["imagen"]["tmp_name"], "../assets/productos/" . $imagen);
        }
    }

    if ($nombre == "" || $precio <= 0) {
        $aviso = "Completá nombre y precio.";
    } else {
        if ($imagen != null) {
            $stmt = $conexion->prepare("UPDATE productos SET nombre = ?, precio = ?, imagen = ? WHERE id = ?");
            $stmt->bind_param("sisi", $nombre, $precio, $imagen, $id);
        } else {
            $stmt = $conexion->prepare("UPDATE productos SET nombre = ?, precio = ? WHERE id = ?");
            $stmt->bind_param("sii", $nombre, $precio, $id);
        }
        $stmt->execute();
        $aviso = "Producto actualizado.";
    }
}

if (isset($_POST["eliminar"])) {
    $id = (int)$_POST["id"];
    $stmt = $conexion->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $aviso = "Producto eliminado.";
}

if (isset($_POST["atendido"])) {
    $id = (int)$_POST["id"];
    $stmt = $conexion->prepare("UPDATE turnos SET atendido = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

if (isset($_POST["eliminar_turno"])) {
    $id = (int)$_POST["id"];
    $stmt = $conexion->prepare("DELETE FROM turnos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

$editando = null;
if (isset($_GET["editar"])) {
    $id = (int)$_GET["editar"];
    $stmt = $conexion->prepare("SELECT id, nombre, precio FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $editando = $stmt->get_result()->fetch_assoc();
}

$productos = $conexion->query("SELECT id, nombre, precio, imagen FROM productos ORDER BY id");
$turnos = $conexion->query("SELECT * FROM turnos ORDER BY atendido, fecha");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Administración Zampedri</span>
            <a href="index.php?salir=1" class="btn btn-outline-light btn-sm">Salir</a>
        </div>
    </nav>

    <div class="container my-4">
        <?php if ($aviso != "") { ?>
            <div class="alert alert-info"><?php echo $aviso; ?></div>
        <?php } ?>

        <h2 class="h4">Productos</h2>

        <?php if ($editando) { ?>
            <form method="post" enctype="multipart/form-data" class="card card-body mb-4">
                <h3 class="h6">Editar producto</h3>
                <input type="hidden" name="id" value="<?php echo $editando["id"]; ?>">
                <div class="campos campos-editar">
                    <input type="text" name="nombre" class="form-control" maxlength="100" required value="<?php echo limpiar($editando["nombre"]); ?>">
                    <input type="number" name="precio" class="form-control" min="1" required value="<?php echo $editando["precio"]; ?>">
                    <input type="file" name="imagen" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                    <div class="acciones">
                        <button type="submit" name="guardar" class="btn btn-primary">Guardar</button>
                        <a href="index.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
            </form>
        <?php } else { ?>
            <form method="post" enctype="multipart/form-data" class="card card-body mb-4">
                <h3 class="h6">Agregar producto</h3>
                <div class="campos campos-agregar">
                    <input type="text" name="nombre" class="form-control" placeholder="Nombre" maxlength="100" required>
                    <input type="number" name="precio" class="form-control" placeholder="Precio" min="1" required>
                    <input type="file" name="imagen" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                    <button type="submit" name="agregar" class="btn btn-success">Agregar</button>
                </div>
            </form>
        <?php } ?>

        <div class="tabla">
            <table class="table table-striped bg-white">
                <tr>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th></th>
                </tr>
                <?php while ($p = $productos->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo limpiar($p["nombre"]); ?></td>
                        <td>$<?php echo number_format($p["precio"], 0, ",", "."); ?></td>
                        <td class="text-end">
                            <a href="index.php?editar=<?php echo $p["id"]; ?>" class="btn btn-sm btn-primary">Editar</a>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="id" value="<?php echo $p["id"]; ?>">
                                <button type="submit" name="eliminar" class="btn btn-sm btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>

        <h2 class="h4 mt-5">Turnos</h2>
        <div class="tabla">
            <table class="table table-striped bg-white">
                <tr>
                    <th>Fecha</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Moto</th>
                    <th>Servicio</th>
                    <th>Mensaje</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
                <?php while ($t = $turnos->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo date("d/m/Y", strtotime($t["fecha"])); ?></td>
                        <td><?php echo limpiar($t["nombre"]); ?></td>
                        <td><?php echo limpiar($t["telefono"]); ?></td>
                        <td><?php echo limpiar($t["marca"]); ?></td>
                        <td><?php echo limpiar($t["servicio"]); ?></td>
                        <td><?php echo limpiar($t["mensaje"]); ?></td>
                        <td><?php echo $t["atendido"] ? "Atendido" : "Pendiente"; ?></td>
                        <td class="text-end">
                            <form method="post" class="d-inline">
                                <input type="hidden" name="id" value="<?php echo $t["id"]; ?>">
                                <?php if ($t["atendido"] == 0) { ?>
                                    <button type="submit" name="atendido" class="btn btn-sm btn-success">Atendido</button>
                                <?php } ?>
                                <button type="submit" name="eliminar_turno" class="btn btn-sm btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</body>
</html>

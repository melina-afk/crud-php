<?php
include 'conexion.php';

try {
    // Usuarios
    $stmt = $pdo->query("SELECT * FROM usuario");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $usuarios = [];
    echo "Error usuarios: " . $e->getMessage();
}

try {
    // Productos
    $stmt = $pdo->query("SELECT p.*, c.nombre_categoria FROM producto p JOIN categoria c ON p.categoria_id = c.id_categoria");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $productos = [];
    echo "Error productos: " . $e->getMessage();
}

try {
    // P
    $stmt = $pdo->query("
        SELECT pe.*, u.nombre_usuario AS usuario, pr.nombre_producto 
        FROM pedido pe 
        JOIN usuario u ON pe.fk_id_usuario = u.id_usuario 
        JOIN producto pr ON pe.fk_id_producto = pr.id_producto
    ");
    //convierte en array asociativo
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $pedidos = [];
    echo "Error pedidos: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel CRUD</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        h2 { margin-top: 40px; }
        form, table { margin-bottom: 30px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 8px; border: 1px solid #ccc; text-align: left; }
        input[type="text"], input[type="number"], input[type="email"] { width: 200px; }
        button { padding: 6px 12px; }
        .seccion { border: 1px solid #ccc; padding: 20px; border-radius: 10px; margin-bottom: 40px; background: #f9f9f9; }
    </style>
</head>
<body>

    <h1>Panel de acciones</h1>

    <!--usuario-->
    <div class="seccion">
        <h2>Usuarios</h2>

        <!--insertar-->
        <form action="crud_usuario.php" method="POST" enctype="multipart/form-data">
            <label>Importar CSV de usuarios:</label><br><br>
            <input type="file" name="archivo_csv" required>
            <button type="submit">Importar</button>
        </form>

        <!--editar-->
        <form method="POST" action="crud_usuario.php">
            <label>Editar datos de usuario:</label><br><br>
            <input type="hidden" name="accion" value="editar">
            Id de usuario: <input type="number" name="id_usuario" required>
            Nombre de usuario: <input type="text" name="nombre_usuario">
            Email: <input type="email" name="email">
            Contraseña: <input type="text" name="clave">
            <button type="submit">Actualizar Usuario</button>
        </form>

        <!--eliminar -->
        <label>Eliminar usuario:</label><br><br>
        <form method="post" action="crud_usuario.php">
            <input type="hidden" name="accion" value="eliminar">
            Id de usuario: <input type="number" name="id" required>
            <button type="submit">Eliminar Usuario</button>
        </form>

        <!-- Tabla -->
        <table>
            <tr><th>ID</th><th>Nombre</th><th>Email</th><th>Contraseña</th></tr>
            <?php

        // Obtener usuarios desde la bd
        try {
            $stmt = $pdo->query("SELECT * FROM usuario");
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo "Error al obtener usuarios: " . $e->getMessage();
            $usuarios = [];
        }
?>
            <!--muestra los datos de la tabla usuario -->
            <?php foreach ($usuarios as $u): ?>

                <tr>
                    <td><?= $u['id_usuario'] ?></td>
                    <td><?= htmlspecialchars($u['nombre_usuario']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['clave']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!--tabla producto -->
    <div class="seccion">
        <h2>Productos</h2>

        <!--insertar-->
        <form action="crud_producto.php" method="POST" enctype="multipart/form-data">
            <label>Importar CSV de productos:</label><br><br>
            <input type="file" name="archivo_csv" required>
            <button type="submit">Importar</button>
        </form>
        <!-- para editar -->
        <form method="post" action="crud_producto.php">
            <label>Editar datos de producto: </label><br><br>
            <input type="hidden" name="accion_producto" value="editar">
            ID: <input type="number" name="id_producto">
            Nombre: <input type="text" name="nombre_producto">
            Descripción: <input type="text" name="descripcion">
            Precio: <input type="text" name="precio">
            Stock: <input type="number" name="stock">
            Categoría ID: <input type="number" name="categoria">
            <button type="submit">Actualizar Producto</button>
        </form>

        <!-- para eliminar -->
        <form method="post" action="crud_producto.php">
            <label>Eliminar producto:</label><br><br>
            <input type="hidden" name="accion_producto" value="eliminar">
            Id de producto: <input type="number" name="id_producto">
            <button type="submit">Eliminar Producto</button>
        </form>

        <!-- tabla que muestra los datos de los product-->
        <table>
            <tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Precio</th><th>Stock</th><th>Categoría</th></tr>
            <?php foreach ($productos as $p): ?>
                <tr>
                    <td><?= $p['id_producto'] ?></td>
                    <td><?= htmlspecialchars($p['nombre_producto']) ?></td>
                    <td><?= htmlspecialchars($p['descripcion']) ?></td>
                    <td><?= $p['precio'] ?></td>
                    <td><?= $p['stock'] ?></td>
                    <td><?= htmlspecialchars($p['nombre_categoria']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!--tabla pedido -->
    <div class="seccion">
        <h2>Pedidos</h2>
        <!--insertar-->
        <form action="crud_pedido.php" method="POST" enctype="multipart/form-data">
            <label>Importar CSV de pedidos:</label><br><br>
            <input type="file" name="archivo_csv" required>
            <button type="submit">Importar</button>
        </form>
<table>
    <tr>
        <th>Usuario</th>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>Fecha</th>
        <th>Estado</th>
        <th>Acción</th>
    </tr>
    <?php foreach ($pedidos as $ped): ?>
        <tr>
            <!-- Formulario para editar -->
            <form method="post" action="crud_pedido.php">
                <td>
                    <?= htmlspecialchars($ped['usuario']) ?> (ID: <?= $ped['fk_id_usuario'] ?>)
                    <input type="hidden" name="id_usuario" value="<?= $ped['fk_id_usuario'] ?>">
                </td>
                <td>
                    <?= htmlspecialchars($ped['nombre_producto']) ?> (ID: <?= $ped['fk_id_producto'] ?>)
                    <input type="hidden" name="id_producto" value="<?= $ped['fk_id_producto'] ?>">
                </td>
                <td>
                    <input type="number" name="cantidad" value="<?= $ped['cantidad'] ?>" required>
                </td>
                <td>
                    <input type="date" name="fecha_pedido" value="<?= $ped['fecha_pedido'] ?>" required>
                </td>
                <td>
                    <input type="text" name="estado" value="<?= $ped['estado'] ?>" required>
                </td>
                <td>
                    <input type="hidden" name="accion_pedido" value="editar">
                    <button type="submit">Actualizar</button>
            </form>

            <!-- Formulario para eliminar -->
            <form method="post" action="crud_pedido.php" style="display:inline;">
                <input type="hidden" name="accion_pedido" value="eliminar">
                <input type="hidden" name="id_usuario" value="<?= $ped['fk_id_usuario'] ?>">
                <input type="hidden" name="id_producto" value="<?= $ped['fk_id_producto'] ?>">
                <button type="submit" onclick="return confirm('¿Estás seguro de eliminar este pedido?');">Eliminar</button>
            </form>
                </td>
        </tr>
    <?php endforeach; ?>
</table>


</body>
</html>

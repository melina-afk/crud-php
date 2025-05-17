<?php
include 'conexion.php';

try {
    // usuarios
    $stmt = $pdo->query("SELECT * FROM usuario");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $usuarios = [];
    echo "Error usuarios: " . $e->getMessage();
}

try {
    // productos con nombre de categoría
    $stmt = $pdo->query("SELECT p.*, c.nombre_categoria FROM producto p JOIN categoria c ON p.categoria_id = c.id_categoria");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $productos = [];
    echo "Error productos: " . $e->getMessage();
}

try {
    // pedidos con nombre de usuario y producto
    $stmt = $pdo->query("SELECT pe.*, u.nombre_usuario AS usuario, pr.nombre_producto FROM pedido pe JOIN usuario u ON pe.fk_id_usuario = u.id_usuario JOIN producto pr ON pe.fk_id_producto = pr.id_producto");
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

    <div class="seccion">
    <!--Insertar-->
        <h2>Usuarios</h2>
        <form action="crud_usuario.php" method="POST" enctype="multipart/form-data">
            <label>Importar CSV de usuarios:</label><br><br>
            <input type="file" name="archivo_csv" required>
            <button type="submit">Importar</button>
        </form>
     <!--Editar-->
        <form method="POST" action="crud_usuario.php">
            <label>Editar datos de usuario:</label><br><br>
            <input type="hidden" name="accion" value="editar">
            Id de usuario: <input type="number" name="id_usuario" required>
            Nombre de usuario: <input type="text" name="nombre_usuario">
            Email: <input type="email" name="email">
            Contraseña: <input type="text" name="clave">
            <button type="submit">Actualizar Usuario</button>
        </form>
     <!--Eliminar-->
        <form method="post" action="crud_usuario.php">
            <label>Eliminar usuario:</label><br><br>
            <input type="hidden" name="accion" value="eliminar">
            Id de usuario: <input type="number" name="id" required>
            <button type="submit">Eliminar Usuario</button>
        </form>
         <!--muestra los usuarios existentes en la bd-->
        <table>
            <tr><th>ID</th><th>Nombre</th><th>Email</th><th>Contraseña</th></tr>
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
        <!--Tabla productos-->
        <!--Insertar-->
    <div class="seccion">
        <h2>Productos</h2>
        <form action="crud_producto.php" method="POST" enctype="multipart/form-data">
            <label>Importar CSV de productos:</label><br><br>
            <input type="file" name="archivo_csv" required>
            <button type="submit">Importar</button>
        </form>
        <!--editar-->
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
        <!--eliminar-->
        <form method="post" action="crud_producto.php">
            <label>Eliminar producto:</label><br><br>
            <input type="hidden" name="accion_producto" value="eliminar">
            Id de producto: <input type="number" name="id_producto">
            <button type="submit">Eliminar Producto</button>
        </form>
        <!--muestra los productos-->
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

        <h3>Productos Electrónicos con precio mayor a $500</h3>
        <?php
        try {
            //muestra los productos que sean de electronicos y tengan precio >500
            $stmt = $pdo->prepare("SELECT p.* FROM producto p JOIN categoria c ON p.categoria_id = c.id_categoria WHERE c.nombre_categoria = 'Electrónicos' AND p.precio > 500");
            $stmt->execute();
            $electronicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($electronicos) {
                foreach ($electronicos as $fila) {
                    echo "<div class='producto'>";
                    echo "<strong>{$fila['nombre_producto']}</strong><br>";
                    echo "Precio: \${$fila['precio']}<br>";
                    echo "</div>";
                }
            } else {
                echo "<p>No hay productos electrónicos con precio mayor a \$500.</p>";
            }
        } catch (Exception $e) {
            echo "Error al buscar productos electrónicos: " . $e->getMessage();
        }
        ?>

        <h3>Producto más vendido</h3>
        <?php
        try {
            //agrupa por fk de id_producto, suma la cantidad pedida y lo ordena de
            //+ vendido a - vendido
            //toma el más vendido como limit 1 (el primero)
            $stmt = $pdo->query("SELECT pr.nombre_producto, SUM(pe.cantidad) AS total FROM producto pr JOIN pedido pe ON pr.id_producto = pe.fk_id_producto GROUP BY pe.fk_id_producto ORDER BY total DESC LIMIT 1");
            $producto_mas_vendido = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($producto_mas_vendido) {
                echo "<div class='destacado'>";
                echo "<p><strong>{$producto_mas_vendido['nombre_producto']}</strong> - Vendido: {$producto_mas_vendido['total']} unidades</p>";
                echo "</div>";
            } else {
                echo "<p>No hay ventas registradas.</p>";
            }
        } catch (Exception $e) {
            echo "Error al obtener el producto más vendido: " . $e->getMessage();
        }
        ?>
    </div>
    <!--Tabla pedido-->
    <div class="seccion">
        <h2>Pedidos</h2>
        <!--Insertar-->
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
                    <!--Editar-->
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
                    <!--Eliminar-->
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
    </div>
</body>
</html>
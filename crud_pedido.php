<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_csv'])) {
    if ($_FILES['archivo_csv']['error'] === 0) {
        try {
            $pdo->beginTransaction();

            $file = fopen($_FILES['archivo_csv']['tmp_name'], 'r');
            fgetcsv($file); // Saltar encabezado 

            $insertado = 0;
            $errores = [];

            $stmtInsert = $pdo->prepare("INSERT INTO pedido (fk_id_usuario, fk_id_producto, cantidad, fecha_pedido, estado) VALUES (?, ?, ?, ?, ?)");

            $stmtCheckUsuario = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE id_usuario = ?");
            $stmtCheckProducto = $pdo->prepare("SELECT COUNT(*) FROM producto WHERE id_producto = ?");
            $stmtCheckPedido = $pdo->prepare("SELECT COUNT(*) FROM pedido WHERE fk_id_usuario = ? AND fk_id_producto = ?");

            while (($fila = fgetcsv($file)) !== false) {
                if (count($fila) < 5) continue;

                list($usuario, $producto, $cantidad, $fecha, $estado) = $fila;

                // Verificar existencia de usuario y producto
                $stmtCheckUsuario->execute([$usuario]);
                $stmtCheckProducto->execute([$producto]);

                if ($stmtCheckUsuario->fetchColumn() == 0 || $stmtCheckProducto->fetchColumn() == 0) {
                    $errores[] = "Usuario o producto no existen (Usuario $usuario, Producto $producto)";
                    continue;
                }

                // Verificar si ya existe el pedido
                $stmtCheckPedido->execute([$usuario, $producto]);
                if ($stmtCheckPedido->fetchColumn() > 0) {
                    $errores[] = "Pedido ya existe (Usuario $usuario, Producto $producto)";
                    continue;
                }

                // Insertar pedido
                $stmtInsert->execute([$usuario, $producto, $cantidad, $fecha, $estado]);
                $insertado++;
            }

            fclose($file);
            $pdo->commit();

            if ($insertado > 0) {
                echo "<p style='color:green;'>$insertado pedidos importados correctamente.</p>";
            }

            // Mostrar errores
            foreach ($errores as $error) {
                echo "<p style='color:red;'>$error</p>";
            }

            echo '<meta http-equiv="refresh" content="3;url=index.php">';
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color:red;'>Error al subir archivo CSV.</p>";
        echo '<meta http-equiv="refresh" content="3;url=index.php">';
    }
}


// editar
if (isset($_POST['accion_pedido']) && $_POST['accion_pedido'] === 'editar') {
    $usuario = $_POST['id_usuario'] ?? null;
    $producto = $_POST['id_producto'] ?? null;
    $cantidad = $_POST['cantidad'] ?? null;
    $fecha = $_POST['fecha_pedido'] ?? null;
    $estado = $_POST['estado'] ?? null;

    if ($usuario && $producto && $cantidad && $fecha && $estado) {
        $stmt = $pdo->prepare("UPDATE pedido SET cantidad = ?, fecha_pedido = ?, estado = ? WHERE fk_id_usuario = ? AND fk_id_producto = ?");
        $stmt->execute([$cantidad, $fecha, $estado, $usuario, $producto]);
        echo "<p style='color:blue;'>Pedido actualizado.</p>";
    } else {
        echo "Faltan datos para actualizar.";
    }
    echo '<meta http-equiv="refresh" content="3;url=index.php">';
}

// eliminar
if (isset($_POST['accion_pedido']) && $_POST['accion_pedido'] === 'eliminar') {
    $usuario_id = $_POST['id_usuario'] ?? null;
    $producto_id = $_POST['id_producto'] ?? null;


    if ($usuario_id && $producto_id) {
        $stmt = $pdo->prepare("DELETE FROM pedido WHERE fk_id_usuario = ? AND fk_id_producto = ?");
        $stmt->execute([$usuario_id, $producto_id]);
        echo "Pedido eliminado.";
    } else {
        echo "Faltan datos para poder eliminar el pedido.";
    }
    echo '<meta http-equiv="refresh" content="3;url=index.php">';
}

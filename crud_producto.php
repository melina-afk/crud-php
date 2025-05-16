<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // insertar
        if (isset($_FILES['archivo_csv']) && $_FILES['archivo_csv']['error'] === 0) {
            $pdo->beginTransaction();

            $file = fopen($_FILES['archivo_csv']['tmp_name'], 'r');
            if (!$file) throw new Exception("No se pudo abrir el archivo CSV");

            fgetcsv($file); // Saltar encabezados

            //prepara consulta: busca la categoria y sino existe crea una nueva
            $stmtBuscarCategoria = $pdo->prepare("SELECT id_categoria FROM categoria WHERE nombre_categoria = ?");
            $stmtInsertarCategoria = $pdo->prepare("INSERT INTO categoria (nombre_categoria) VALUES (?)");

            $stmtBuscarProducto = $pdo->prepare("SELECT COUNT(*) FROM producto WHERE nombre_producto = ?");
            $stmtInsertarProducto = $pdo->prepare("INSERT INTO producto (nombre_producto, descripcion, precio, stock, categoria_id) VALUES (?, ?, ?, ?, ?)");

            while (($fila = fgetcsv($file)) !== false) {
                if (count($fila) < 5) continue;
                list($nombre, $descripcion, $precio, $stock, $nombre_categoria) = $fila;

                // Verificar si el producto ya existe
                $stmtBuscarProducto->execute([$nombre]);
                if ($stmtBuscarProducto->fetchColumn() > 0) {
                    echo "<p style='color:orange;'>Producto '$nombre' ya existe. Se omitió.</p>";
                    continue;
                }

                // Obtiene la categoria
                $stmtBuscarCategoria->execute([$nombre_categoria]);
                $cat = $stmtBuscarCategoria->fetch(PDO::FETCH_ASSOC);

                if ($cat) {
                    $categoria_id = $cat['id_categoria'];
                } else {
                    $stmtInsertarCategoria->execute([$nombre_categoria]);
                    $categoria_id = $pdo->lastInsertId();
                }

                // inserta el nuevo producto
                $stmtInsertarProducto->execute([$nombre, $descripcion, $precio, $stock, $categoria_id]);
            }

            $pdo->commit();
            fclose($file);
            echo "<p style='color:green;'>Productos importados correctamente.</p>";
            echo '<meta http-equiv="refresh" content="3;url=index.php">';
        }

        // editar
        elseif ($_POST['accion_producto'] === 'editar') {
            $id = $_POST['id_producto'] ?? null;
            $nombre = $_POST['nombre_producto'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $precio = $_POST['precio'] ?? 0;
            $stock = $_POST['stock'] ?? 0;
            $categoria_id = $_POST['categoria'] ?? null;

            if ($id && $categoria_id) {
                $sql = "UPDATE producto SET nombre_producto=?, descripcion=?, precio=?, stock=?, categoria_id=? WHERE id_producto=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nombre, $descripcion, $precio, $stock, $categoria_id, $id]);
                echo "<p style='color:blue;'>Producto actualizado.</p>";
                echo '<meta http-equiv="refresh" content="3;url=index.php">';
            } else {
                echo "<p style='color:red;'>Faltan datos para actualizar.</p>";
            }
        }

        // eliminar
        elseif ($_POST['accion_producto'] === 'eliminar') {
            $id = $_POST['id_producto'] ?? null;
            if ($id) {
                $stmt = $pdo->prepare("DELETE FROM producto WHERE id_producto = ?");
                $stmt->execute([$id]);
                echo "<p style='color:red;'>Producto eliminado.</p>";
                echo '<meta http-equiv="refresh" content="3;url=index.php">';
            } else {
                echo "<p style='color:red;'>Ingrese el ID del producto.</p>";
                echo '<meta http-equiv="refresh" content="3;url=index.php">';
            }
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
    }
}

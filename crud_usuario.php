<?php
include 'conexion.php';

// Insertar datos desde CSV
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_csv'])) {
    if ($_FILES['archivo_csv']['error'] === 0) {
        try {
            $pdo->beginTransaction();

            $file = fopen($_FILES['archivo_csv']['tmp_name'], 'r');
            fgetcsv($file); // Saltar encabezado

            $stmtInsert = $pdo->prepare("INSERT INTO usuario (nombre_usuario, email, clave, genero_id) VALUES (?, ?, ?, ?)");
            $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE email = ?");

            while (($fila = fgetcsv($file)) !== false) {
                if (count($fila) < 4) continue;
                list($nombre, $email, $contraseña, $genero_id) = $fila;

                // Verificar si el mail ya existe y si existe lo omite
                $stmtCheck->execute([$email]);
                if ($stmtCheck->fetchColumn() > 0) {
                    echo "<p style='color:orange;'>Usuario con email $email ya existe. Sera omitido para evitar duplicados</p>";
                    continue;
                }

                // inserta nuevo usuario luego de verificar que no existe
                $stmtInsert->execute([$nombre, $email, $contraseña, $genero_id]);
            }

            $pdo->commit();
            fclose($file);
            echo "<p style='color:green;'>Usuarios importados correctamente. Serás redirigido automáticamente.</p>";
            echo '<meta http-equiv="refresh" content="3;url=index.php">';

        } catch (Exception $e) {
            $pdo->rollBack();
            echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color:red;'>Error al subir archivo CSV.</p>";
    }
}

// editar usuario
if (isset($_POST['accion']) && $_POST['accion'] === 'editar') {
    $id = $_POST['id_usuario'];
    $nombre = $_POST['nombre_usuario'];
    $email = $_POST['email'];
    $contraseña = $_POST['clave'];

    $stmt = $pdo->prepare("UPDATE usuario SET nombre_usuario = ?, email = ?, clave = ? WHERE id_usuario = ?");
    $stmt->execute([$nombre, $email, $contraseña, $id]);

    echo "<p style='color:blue;'>Usuario actualizado. Serás redirigido automáticamente.</p>";
    echo '<meta http-equiv="refresh" content="3;url=index.php">';
}

// eliminar usuario
if (isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $id = $_POST['id'];

    $stmt = $pdo->prepare("DELETE FROM usuario WHERE id_usuario = ?");
    $stmt->execute([$id]);

    echo "<p style='color:red;'>Usuario eliminado. Redirigiendo...</p>";
    echo '<meta http-equiv="refresh" content="5;url=index.php">';
}

// Obtener usuarios y mostrar
$usuarios = $pdo->query("SELECT * FROM usuario")->fetchAll(PDO::FETCH_ASSOC);
?>

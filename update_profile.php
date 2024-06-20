<?php
session_start();
include 'database.php';
header('Content-Type: application/json');

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $nombre = $_POST['nombre'];
    $Ap = $_POST['Ap'];
    $Am = $_POST['Am'];
    $telefono = $_POST['telefono'];
    $Con = !empty($_POST['Con']) ? password_hash($_POST['Con'], PASSWORD_DEFAULT) : null;
    $profilePic = $_FILES['profilePic'];

    // Verificar si el correo existe en la base de datos
    $sql = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Actualizar información del usuario
        $sqlUpdate = "UPDATE usuarios SET nombre=?, Ap=?, Am=?, telefono=?";
        $params = array($nombre, $Ap, $Am, $telefono);

        // Incluir la contraseña si se proporcionó
        if ($Con) {
            $sqlUpdate .= ", Con=?";
            $params[] = $Con;
        }

        // Incluir la foto de perfil si se subió
        if ($profilePic['error'] == UPLOAD_ERR_OK) {
            $sqlUpdate .= ", profile_pic=?";
            $params[] = $correo . "." . pathinfo($profilePic['name'], PATHINFO_EXTENSION);
        }

        $sqlUpdate .= " WHERE correo=?";
        $params[] = $correo;

        $stmtUpdate = $conn->prepare($sqlUpdate);

        // Bind parameters
        $types = str_repeat('s', count($params)); // assuming all params are strings
        $stmtUpdate->bind_param($types, ...$params);

        if ($stmtUpdate->execute()) {
            $response['success'] = true;
            $response['message'] = "Perfil actualizado exitosamente!";

            // Manejo de la foto de perfil si se subió
            if ($profilePic['error'] == UPLOAD_ERR_OK) {
                $targetDir = "uploads/";
                $targetFile = $targetDir . $correo . "." . pathinfo($profilePic['name'], PATHINFO_EXTENSION);

                // Crear el directorio si no existe
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                // Mover el archivo subido a su destino
                if (move_uploaded_file($profilePic['tmp_name'], $targetFile)) {
                    $response['message'] .= " Foto de perfil actualizada.";
                } else {
                    $response['message'] .= " No se pudo actualizar la foto de perfil.";
                }
            }
        } else {
            $response['success'] = false;
            $response['message'] = "Error al actualizar el perfil: " . $stmtUpdate->error;
        }

        $stmtUpdate->close();
    } else {
        $response['success'] = false;
        $response['message'] = "No se encontró el usuario con el correo proporcionado.";
    }

    $stmt->close();
    $conn->close();
} else {
    $response['success'] = false;
    $response['message'] = "Método de solicitud no válido.";
}

echo json_encode($response);
?>

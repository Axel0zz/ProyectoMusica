<?php
include 'database.php';
header('Content-Type: application/json');

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['producto'];
    $categoria = $_POST['categoria'];
    $precio = $_POST['precio'];
    $image_path = null;

    // Manejo de la subida de imágenes
    if (isset($_FILES['productoPic']) && $_FILES['productoPic']['error'] == UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['productoPic']['name'], PATHINFO_EXTENSION);
        $targetDir = "uploads/";
        $image_path = $targetDir . uniqid() . "." . $ext;

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        if (!move_uploaded_file($_FILES['productoPic']['tmp_name'], $image_path)) {
            $response['success'] = false;
            $response['message'] = "Error al subir la imagen.";
            echo json_encode($response);
            exit;
        }
    }

    $sql = "INSERT INTO productos (nombre, categoria, precio, imagen) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $categoria, $precio, $image_path);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = "Registro exitoso!";
    } else {
        $response['success'] = false;
        $response['message'] = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    $response['success'] = false;
    $response['message'] = "Método de solicitud no válido.";
}

echo json_encode($response);
?>

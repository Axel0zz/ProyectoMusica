<?php
include 'database.php';
header('Content-Type: application/json');

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $Ap = $_POST['Ap'];
    $Am = $_POST['Am'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $Con = password_hash($_POST['Con'], PASSWORD_DEFAULT);  // Encriptamos la contraseña

    $sql = "INSERT INTO usuarios (nombre, Ap, Am, telefono, correo, Con) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $nombre, $Ap, $Am, $telefono, $correo, $Con);

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

<?php
session_start();
include 'database.php';
header('Content-Type: application/json');

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $Con = $_POST['Con'];

    // Verificar credenciales de inicio de sesión
    $sql = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($Con, $row['Con'])) {
            // Iniciar sesión y guardar el correo en $_SESSION
            $_SESSION['correo'] = $correo;

            // Preparar la respuesta JSON
            $response['success'] = true;
            $response['message'] = "Inicio de sesión exitoso.";

        } else {
            $response['success'] = false;
            $response['message'] = "Contraseña incorrecta.";
        }
    } else {
        $response['success'] = false;
        $response['message'] = "Correo electrónico no encontrado.";
    }

    $stmt->close();
    $conn->close();
} else {
    $response['success'] = false;
    $response['message'] = "Método de solicitud no válido.";
}

echo json_encode($response);
?>

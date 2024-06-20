<?php
include 'database.php';
header('Content-Type: application/json');

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $producto_id = $_POST['producto_id'];
    $cantidad = $_POST['cantidad'];
    $correo = $_POST['correo'];

    // Validar datos recibidos
    if (empty($producto_id) || empty($cantidad) || empty($correo)) {
        $response['success'] = false;
        $response['message'] = "Datos incompletos recibidos";
        error_log("Datos recibidos: " . print_r($_POST, true));

        echo json_encode($response);
        exit;
    }

    // Insertar producto en el carrito
    $sql = "INSERT INTO carrito (producto_id, cantidad, correo) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $producto_id, $cantidad, $correo);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = "Producto agregado al carrito exitosamente!";
    } else {
        $response['success'] = false;
        $response['message'] = "Error al agregar el producto al carrito: " . $stmt->error;
    }
    

    $stmt->close();
    $conn->close();
} else {
    $response['success'] = false;
    $response['message'] = "Método de solicitud no válido.";
    error_log("Datos recibidos: " . print_r($_POST, true));

}

echo json_encode($response);
?>

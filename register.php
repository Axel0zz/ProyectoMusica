<?php
include 'database.php';

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
        echo "Registro exitoso!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>

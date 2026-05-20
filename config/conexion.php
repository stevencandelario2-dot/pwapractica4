<?php
$host = "localhost";
$db   = "01_calif";
$user = "root";
$pass = ""; // En XAMPP la contraseña por defecto va vacía

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
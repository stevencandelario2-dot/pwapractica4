<?php
session_start();
require_once 'config/conexion.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['contrasena']);

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND contrasena = ?");
        $stmt->execute([$email, $password]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];

            // Redirección según el Rol de la Base de Datos
            if ($usuario['rol'] == 1) {
                header("Location: docente/dashboard.php");
            } else {
                header("Location: estudiante/notas.php");
            }
            exit;
        } else {
            $error = "El correo o la contraseña son incorrectos.";
        }
    } else {
        $error = "Por favor, llene todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Calificaciones - Login</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0;">
    <div style="background: #ffffff; padding: 35px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 360px; text-align: center;">
        <h2 style="color: #1e3a8a; margin-bottom: 5px;">Control de Calificaciones</h2>
        <p style="color: #6b7280; font-size: 14px; margin-bottom: 25px;">Inicie sesión con su rol asignado</p>
        
        <?php if (!empty($error)): ?>
            <div style="background-color: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-weight: bold;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div style="text-align: left; margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151; font-size: 14px;">Correo Electrónico:</label>
                <input type="email" name="email" placeholder="ejemplo@ube.edu.ec" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box;">
            </div>
            <div style="text-align: left; margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151; font-size: 14px;">Contraseña:</label>
                <input type="password" name="contrasena" placeholder="******" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box;">
            </div>
            <button type="submit" style="background-color: #1e3a8a; color: white; border: none; padding: 12px; width: 100%; border-radius: 6px; font-weight: bold; cursor: pointer; margin-top: 10px;">Ingresar al Sistema</button>
        </form>
    </div>
</body>
</html>
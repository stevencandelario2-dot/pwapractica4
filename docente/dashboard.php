<?php
session_start();

// SEGURIDAD: Si no ha iniciado sesión o no es Docente (Rol 1), lo bota al Login
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 1) {
    header("Location: ../index.php");
    exit;
}

require_once '../config/conexion.php';

$mensaje = "";

// LÓGICA PARA GUARDAR O ACTUALIZAR CALIFICACIONES (Corregida sin columnas inexistentes)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_nota'])) {
    $estudiante_id = $_POST['estudiante_id'];
    $teoria = $_POST['teoria'];
    $practica = $_POST['practica'];
    $asignatura_id = 1; 
    $docente_id = $_SESSION['usuario_id'];

    // Comprobar si ya existe un registro de nota para este alumno
    $stmt_check = $pdo->prepare("SELECT id FROM notas WHERE usuario_id = ? AND asignatura_id = ?");
    $stmt_check->execute([$estudiante_id, $asignatura_id]);
    $nota_id = $stmt_check->fetchColumn();

    if ($nota_id) {
        // ACTUALIZAR NOTA (Usando solo las columnas reales de la tabla 'notas')
        $sql = "UPDATE notas SET teoria = ?, practica = ?, obs = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$teoria, $practica, "Actualizado por Docente ID: " . $docente_id, $nota_id]);
        $mensaje = "Nota actualizada correctamente.";
    } else {
        // INSERTAR NOTA NUEVA (Usa los campos nativos de la tabla del documento)
        $sql = "INSERT INTO notas (asignatura_id, usuario_id, parcial, teoria, practica, usuario_id_creacion, fecha_creacion, hora_creacion) VALUES (?, ?, 1, ?, ?, ?, NOW(), CURTIME())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$asignatura_id, $estudiante_id, $teoria, $practica, $docente_id]);
        $mensaje = "Nota registrada con éxito.";
    }
}

// CONSULTA: Traer los estudiantes (Rol 2) y sus notas si ya existen
$sql_estudiantes = "SELECT u.id, u.nombre, u.email, n.teoria, n.practica 
                    FROM usuarios u 
                    LEFT JOIN notas n ON u.id = n.usuario_id AND n.asignatura_id = 1
                    WHERE u.rol = 2";
$stmt_estudiantes = $pdo->query($sql_estudiantes);
$estudiantes = $stmt_estudiantes->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Docente - Gestión de Notas</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 20px;">
    <div style="max-width: 900px; margin: 30px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
        
        <a href="../logout.php" style="float: right; background-color: #dc3545; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 14px;">Cerrar Sesión</a>
        
        <h2 style="color: #1e3a8a; margin-top: 0;">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h2>
        <p style="color: #4b5563; font-size: 15px;">Módulo de Calificaciones: <strong>Programación Web Avanzada</strong></p>
        
        <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 20px 0;">

        <?php if (!empty($mensaje)): ?>
            <div style="background-color: #d1fae5; color: #065f46; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; text-align: center;">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr style="background-color: #1e3a8a; color: white;">
                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">Estudiante</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">Correo</th>
                    <th style="padding: 12px; text-align: center; border-bottom: 2px solid #e5e7eb; width: 120px;">Nota Teoría</th>
                    <th style="padding: 12px; text-align: center; border-bottom: 2px solid #e5e7eb; width: 120px;">Nota Práctica</th>
                    <th style="padding: 12px; text-align: center; border-bottom: 2px solid #e5e7eb; width: 100px;">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($estudiantes as $est): ?>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <form method="POST" action="">
                        <td style="padding: 12px; color: #374151;"><?php echo htmlspecialchars($est['nombre']); ?></td>
                        <td style="padding: 12px; color: #6b7280; font-size: 14px;"><?php echo htmlspecialchars($est['email']); ?></td>
                        <td style="padding: 12px; text-align: center;">
                            <input type="number" step="0.1" min="0" max="10" name="teoria" value="<?php echo $est['teoria'] !== null ? $est['teoria'] : ''; ?>" required style="width: 80px; padding: 6px; text-align: center; border: 1px solid #d1d5db; border-radius: 4px;">
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <input type="number" step="0.1" min="0" max="10" name="practica" value="<?php echo $est['practica'] !== null ? $est['practica'] : ''; ?>" required style="width: 80px; padding: 6px; text-align: center; border: 1px solid #d1d5db; border-radius: 4px;">
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <input type="hidden" name="estudiante_id" value="<?php echo $est['id']; ?>">
                            <button type="submit" name="guardar_nota" style="background-color: #10b981; color: white; border: none; padding: 6px 12px; border-radius: 4px; font-weight: bold; cursor: pointer;">Guardar</button>
                        </td>
                    </form>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
session_start();

// SEGURIDAD: Si no ha iniciado sesión o no es Estudiante (Rol 2), lo bota al Login
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 2) {
    header("Location: ../index.php");
    exit;
}

require_once '../config/conexion.php';

$estudiante_id = $_SESSION['usuario_id'];

// CONSULTA: Buscar las notas del estudiante logueado para la asignatura asignada
$sql = "SELECT a.nombre AS asignatura, n.teoria, n.practica, n.fecha_creacion 
        FROM usuarios u
        LEFT JOIN notas n ON u.id = n.usuario_id
        LEFT JOIN asignaturas a ON a.id = n.asignatura_id OR (a.id = 1)
        WHERE u.id = ? 
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([$estudiante_id]);
$datos = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Estudiante - Mis Notas</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 20px;">
    <div style="max-width: 700px; margin: 40px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
        
        <a href="../logout.php" style="float: right; background-color: #dc3545; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 14px;">Cerrar Sesión</a>
        
        <h2 style="color: #1e3a8a; margin-top: 0;">Portal del Estudiante</h2>
        <p style="color: #4b5563; font-size: 15px;">Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></p>
        
        <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 20px 0;">

        <h3 style="color: #374151; font-size: 16px; margin-bottom: 15px;">Mis Calificaciones Actuales</h3>

        <table style="width: 100%; border-collapse: collapse; background-color: #ffffff;">
            <thead>
                <tr style="background-color: #1e3a8a; color: white;">
                    <th style="padding: 12px; text-align: left; border-radius: 4px 0 0 4px;">Asignatura</th>
                    <th style="padding: 12px; text-align: center; width: 110px;">Nota Teoría</th>
                    <th style="padding: 12px; text-align: center; width: 110px;">Nota Práctica</th>
                    <th style="padding: 12px; text-align: center; width: 110px; border-radius: 0 4px 4px 0;">Promedio</th>
                </tr>
            </thead>
            <tbody>
                <tr style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 15px; font-weight: bold; color: #1f2937;">
                        Programación Web Avanzada
                    </td>
                    <td style="padding: 15px; text-align: center; color: #374151; font-size: 16px; font-weight: bold;">
                        <?php echo ($datos['teoria'] !== null) ? number_format($datos['teoria'], 2) : '-.--'; ?>
                    </td>
                    <td style="padding: 15px; text-align: center; color: #374151; font-size: 16px; font-weight: bold;">
                        <?php echo ($datos['practica'] !== null) ? number_format($datos['practica'], 2) : '-.--'; ?>
                    </td>
                    <td style="padding: 15px; text-align: center; color: #1e3a8a; font-size: 16px; font-weight: bold; background-color: #eff6ff;">
                        <?php 
                        if ($datos['teoria'] !== null && $datos['practica'] !== null) {
                            $promedio = ($datos['teoria'] + $datos['practica']) / 2;
                            echo number_format($promedio, 2);
                        } else {
                            echo '-.--';
                        }
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 25px; background-color: #f0fdf4; border: 1px solid #bbf7d0; padding: 12px; border-radius: 6px; text-align: center;">
            <p style="margin: 0; color: #166534; font-size: 13px; font-weight: bold;">
                * Sistema de consulta en tiempo real conectado de forma segura con la Base de Datos.
            </p>
        </div>

    </div>
</body>
</html>
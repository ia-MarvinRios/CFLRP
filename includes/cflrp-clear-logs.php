<?php
    // Incluir wp-load.php para acceder al entorno de WordPress
    require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );

    // Verifica que la solicitud sea válida (puedes agregar más validaciones de seguridad)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['action']) && $data['action'] === 'clear_logs') {
            // Ruta del archivo de logs
            $logFilePath = ABSPATH . 'wp-content/debug.log';

            // Intenta limpiar el archivo de logs
            if (file_put_contents($logFilePath, '') !== false) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'No se pudo borrar el archivo de logs.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Acción no válida.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Método de solicitud no permitido.']);
    }
?>

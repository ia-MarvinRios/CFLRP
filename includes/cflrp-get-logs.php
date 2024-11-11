<?php
// Incluir wp-load.php para acceder al entorno de WordPress
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );

header("Content-Type: application/json");

// Ruta del archivo de logs
$log_file_path = ABSPATH . 'wp-content/debug.log';

if (file_exists($log_file_path)) {
    // Intenta leer el archivo de log y manejar errores posibles
    try {
        $log_content = file_get_contents($log_file_path);
        
        // Verifica si el contenido se obtuvo correctamente
        if ($log_content === false) {
            throw new Exception("Error al leer el archivo de logs.");
        }
        
        echo json_encode(['logs' => $log_content]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'No se encontró el archivo de logs en ' . $log_file_path]);
}
exit;
?>

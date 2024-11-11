<div class="wrap">
    <h1 class="wp-heading-inline" style="
    color: #141E39; 
    font-weight: bold; 
    font-size: 3rem;
    ">
        <?php echo get_admin_page_title(); ?>
    </h1>
    <hr><br>

    <h1>Archivo de logs del servidor:</h1>
    <br>

    <div id="log-container" style="background-color: black; color: gray; padding: 20px; height: 400px; overflow-y: scroll; font-family: monospace;">
        <pre id="log-content">Cargando logs...</pre>
    </div>

    <button onclick="clearLogs()" style="margin-top: 20px; padding: 10px 20px; font-size: 1.2rem; background-color: #FF5C5C; color: white; border: none; cursor: pointer;">
        Limpiar Logs
    </button>
</div>

<script type="text/javascript">
    // Función para actualizar el contenido de los logs
    function fetchLogs() {
        console.log("Actualizando Log...");
        fetch('https://cflsantatecla.info/wp-content/plugins/CFLRP/includes/cflrp-get-logs.php')
            .then(response => response.json())
            .then(data => {
                // Verifica si se obtuvo un error o logs
                if (data.error) {
                    document.getElementById('log-content').innerText = 'Error: ' + data.error;
                } else {
                    // Llama a una función para procesar los logs y formatearlos
                    const formattedLogs = formatLogs(data.logs);
                    document.getElementById('log-content').innerHTML = formattedLogs;
                }
            })
            .catch(error => console.error('Error al obtener los logs:', error));
    }

    // Función para limpiar la consola (vaciar el contenido de los logs)
    function clearLogs() {
        // Eliminar el contenido del contenedor de logs en la página
        document.getElementById('log-content').innerHTML = 'Cargando logs...';  // O puedes dejarlo vacío ''

        // Opcional: Enviar una solicitud al servidor para borrar los logs del archivo
        fetch('https://cflsantatecla.info/wp-content/plugins/CFLRP/includes/cflrp-clear-logs.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ action: 'clear_logs' })  // Aquí puedes enviar una acción para indicar que debe limpiar los logs
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Los logs han sido borrados exitosamente.');
            } else {
                console.error('Error al borrar los logs:', data.error);
            }
        })
        .catch(error => console.error('Error al enviar solicitud para limpiar los logs:', error));
    }


    // Función para formatear los logs
    function formatLogs(logs) {
        // Aquí puedes agregar más patrones para errores, advertencias, etc.
        logs = logs.replace(/(error)/gi, '<span style="color: red; font-weight: bold;">$1</span>');
        logs = logs.replace(/(warning)/gi, '<span style="color: orange; font-weight: bold;">$1</span>');
        logs = logs.replace(/(PHP Deprecated:)/gi, '<span style="color: orange; font-weight: bold;">$1</span>');
        logs = logs.replace(/(success)/gi, '<span style="color: lime; font-weight: bold;">$1</span>');
        logs = logs.replace(/(output)/gi, '<span style="color: skyblue; font-weight: bold;">$1</span>');
        logs = logs.replace(/(\[.*\])/g, '<span style="color: lightgray;">$1</span>'); // Colorea las fechas
        return logs;
    }

    // Intervalo para actualizar los logs cada 1.5 segundos
    setInterval(fetchLogs, 1500);

    // Llama a la función una vez para cargar los logs al principio
    fetchLogs();
</script>
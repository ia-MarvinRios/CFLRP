<?php

//----------------------------------------------------------------------------------------


//----------------------------------------------------------------------------------------
// MANEJO DE SUBIDA DE ARCHIVOS POR FORMULARIO
function check_upload_pages($fileType) {
    global $wpdb;

    $pageName = get_the_title();
    error_log('LA PÁGINA ACTUAL ES: '.$pageName);

    // Nombre de la tabla y de la columna a consultar
    $table_name = 'wpUploadsManager';
    $column_name = 'pagename';
    $file_type = sanitize_text_field($fileType); // Sanitiza la entrada

    // Consulta preparada para obtener todos los valores de la columna especificada
    $query = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT $column_name FROM $table_name WHERE filetype = %s",
            $file_type
        )
    );

    // Verificar si el valor dado existe en el array resultante
    if (in_array($pageName, $query)) {
        return 1;
    } else {
        return 0;
    }
}


add_filter( 'elementor_pro/forms/upload_path', function( $path ){

    if(check_upload_pages('cv') === 1){
        //The folder name
        $folder = 'CVs';

        // Get the WordPress uploads folder
        $wp_upload_dir = wp_upload_dir();

        // This is the new path where we want to store the Form file uploads
        $path = $wp_upload_dir['basedir'] . '/' . $folder;

        /**
         * Check to see if the folder already exists, create if not.
         */
        if ( !file_exists( $path ) ) {
            mkdir( $path, 0755, true);
        }

        return $path;
    }

    return $path;
    
}, 10, 1 );

add_filter( 'elementor_pro/forms/upload_url', function( $file_name ){

    if(check_upload_pages('cv') === 1){
        //The folder name
        $folder = 'CVs';

        // Get the WordPress uploads folder
        $wp_upload_dir = wp_upload_dir();

        // This is the new file URL path
        $filename_arr = explode( '/', $file_name );
        $url = $wp_upload_dir['baseurl'] . '/' . $folder . '/' . end( $filename_arr );

        return $url;
    }

    return $file_name;

}, 10, 1 );

//----------------------------------------------------------------------------------------
// MANEJO DE LAS INSERCIONES EN LA BASE DE DATOS

function checkDUI($input) {
    // Elimina todos los caracteres que no sean números
    $sanitized = preg_replace('/\D/', '', $input);

    // Verifica si la longitud de la cadena es exactamente 9 caracteres
    if (strlen($sanitized) === 9) {
        return $sanitized; // Devuelve la cadena sanada
    }

    // Si no cumple con la longitud de 9 caracteres, devuelve false o un mensaje de error
    return false;
}
function checkAlphabetic($string) {
    // Expresión regular para validar solo letras y letras con tilde
    $pattern = '/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/';

    // Comprobar si el string coincide con la expresión regular
    if (preg_match($pattern, $string)) {
        return $string;
    } else {
        return false; // El string contiene caracteres no permitidos
    }
}


add_action('elementor_pro/forms/new_record', function( $record, $ajax_handler ) {

    // Obtener los campos del formulario enviado
    $raw_fields = $record->get('fields');
    $fields = [];
    foreach ( $raw_fields as $id => $field ) {
        $fields[ $id ] = $field['value'];
    }

    // Obtener el nombre del formulario
    $form_name = $record->get_form_settings('form_name');

    global $wpdb;
    $output = [];

    // Realizar diferentes inserciones según el nombre del formulario
    if ($form_name === 'aplicaciones') {

        // Comprobar si los valores son válidos
        $dui = checkDUI($fields['dui']);
        $nombre = checkAlphabetic($fields['nombre']);

        if (!$dui || !$nombre) {
            // Si el DUI o algún otro valor es inválido, cancelar la inserción y devolver error
            // Si alguno de los valores es inválido, devolver un error a Elementor
            $ajax_handler->add_error('general', 'Datos inválidos');
            error_log('Datos inválidos: Asegúrese de que el DUI y otros campos son correctos.');
            return;
        } else {
            // Si todo es válido, proceder con la inserción
            $insert_result = $wpdb->insert('cfl-aplicaciones', array(
                'nombre' => $fields['nombre'],
                'dui' => $dui,
                'plaza' => $fields['plazas'],
                'plaza-no-listada' => $fields['plaza_no_listada'],
                'cv_url' => $fields['cv_file']
            ));

            if ($insert_result === false) {
                // Si la inserción falla, devolver un error
                $output = $insert_result;
                error_log('Hubo un error al guardar registros de sus datos.');
            } else {
                // Si la inserción es exitosa, devolver éxito
                $output = $insert_result;
            }
        }

    } elseif ($form_name === 'registro-candidatos') {

        // Comprobar si los valores son válidos
        $dui = checkDUI($fields['dui']);
        $nombre = checkAlphabetic($fields['nombre']);
        
        if (!$dui || !$nombre) {
            // Si el DUI o algún otro valor es inválido, cancelar la inserción y devolver error
            // Si alguno de los valores es inválido, devolver un error a Elementor
            $ajax_handler->add_error('general', 'Datos inválidos');
            error_log('Datos inválidos: Asegúrese de que el DUI y otros campos son correctos.');
            return;
        }else {
            // Si todo es válido, proceder con la inserción
            $insert_result = $wpdb->insert('cfl-candidatos', array(
                'nombre' => $fields['nombre'],
                'dui' => $dui,
                'fecha-nacimiento' => $fields['fecha_nacimiento'],
                'sexo' => $fields['sexo'],
                'correo' => $fields['correo'],
                'telefono' => $fields['telefono'],
                'departamento' => $fields['departamento'],
                'municipio' => $fields['municipio'],
                'zona-residencia' => $fields['zona_residencia'],
                'nivel-academico' => $fields['nivel_academico'],
                'posee-competencias' => $fields['posee_competencias'],
                'dominio-idioma' => $fields['dominio_idioma'],
                'nivel-idioma' => $fields['nivel_idioma'],
                'herramientas-tecnologicas' => $fields['herramientas_tecnologicas'],
                'maquinaria' => $fields['maquinaria'],
                'situacion-actual' => $fields['situacion_actual'],
                'discapacidad' => $fields['discapacidad'],
                'discapacidad-nombre' => $fields['discapacidad_nombre'],
                'vehiculo' => $fields['vehiculo'],
                'licencia' => $fields['licencia'],
                'experiencia-laboral' => $fields['experiencia_laboral'],
                'cv-file' => $fields['cv_file']
            ));

            if ($insert_result === false) {
                // Si la inserción falla, devolver un error
                $output = $insert_result;
                error_log('Hubo un error al guardar registros de sus datos.');
            } else {
                // Si la inserción es exitosa, devolver éxito
                $output = $insert_result;
            }
        }

    }
	
	//Devolver una respuesta exitosa TRUE con un mensaje output
    error_log('OUTPUT: ' . json_encode($output));
    $ajax_handler->add_response_data(false, $output);

}, 10, 2);

//----------------------------------------------------------------------------------------
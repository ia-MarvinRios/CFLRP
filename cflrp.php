<?php
/*
Plugin Name: CFLRP
Description: IMPORTANTE: Este plugin modifica el comportamiento de otros plugins como Elementor, para controlar el envío de formularios y también agrega funcionalidad al sitio web del CFL. NO DESINSTALAR.
Author: Raydell Rios
Version: 1.0
*/

// Requiere el archivo cflrp-functions.php para funcionar.
require_once plugin_dir_path(__FILE__) . 'includes/cflrp-functions.php';


function activate(){
    global $wpdb;
}

add_action('admin_menu', 'createMenu');
register_activation_hook(__FILE__, 'activate');

function createMenu(){
    add_menu_page(
        'CFLRP', // Título de la página
        'CFLRP', // Título del menú
        'manage_options', // Capability
        plugin_dir_path(__FILE__).'admin/cflrp-management.php', // Slug
        null, // Función para mostrar el contenido
        plugin_dir_url(__FILE__).'admin/img/icon.png', // Ícono
        '1' // Posición en la lista
    );

    add_submenu_page(
        plugin_dir_path(__FILE__).'admin/cflrp-management.php', // Slug del padre del menú
        'Ofertas Laborales Panel', // Título de la página
        'Ofertas Laborales Panel', // Título del menú
        'manage_options', // Capability
        plugin_dir_path(__FILE__).'admin/cflrp-joboffers.php', // Slug propio
        null // Función para mostrar el contenido
    );
}
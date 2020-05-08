<?php

/**
 * Plugin Name: Contact Form 7 to SFMC
 * Plugin URI: http://www.example.com
 * Description: Propaga información de formulario a SFMC
 * Version: 1.0
 * Author: Everis
 * Author URI: http://www.everis.com
 */

// REGISTRO DE CONFIGURACIONES - TODO!

add_action('admin_init', 'cf7_to_sfmc_config_settings');

function cf7_to_sfmc_config_settings()
{
    //
    $args = array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => NULL,
    );
    register_setting('cf7_to_sfmc_group', 'client_key', $args);

    $args = array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => NULL,
    );
    register_setting('cf7_to_sfmc_group', 'client_secret', $args);

    $args = array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => NULL,
    );
    register_setting('cf7_to_sfmc_group', 'endpoint', $args);
}

// MENU CONFIG

add_action('admin_menu', 'cf7_to_sfmc_config_menu');

function cf7_to_sfmc_config_menu()
{
    add_options_page('CF7 to SFMC Config', 'CF7 to SFMC', 'manage_options', 'cf7-to-sfmc-options', 'cf7_to_sfmc_config_options');
}

function cf7_to_sfmc_config_options()
{
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    echo '<div class="wrap">';
    echo '<h1>Configuración CF7 to SFMC</h1>';
    echo '<form method="post" action="options.php">';
    settings_fields( 'cf7_to_sfmc_group' );
    do_settings_sections( 'cf7_to_sfmc_group' );
    echo submit_button();
    echo '</form>';
    echo '</div>';
}


// LOGICA SFMC

add_action('wpcf7_before_send_mail', 'sfmc_call_after_form_submit');

function sfmc_call_after_form_submit($contact_data)
{

    wp_die(__('ENTRA TRAS SUBMIT'));

    // Obtiene configuracion del plugin y comprueba que sea correcta

    // Obtiene array con datos del formulario y comprueba que sean correctos

    // Si todo es OK, hace llamada a SFMC

}

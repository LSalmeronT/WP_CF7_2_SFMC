<?php

// LOGICA SFMC

add_action('wpcf7_before_send_mail', 'sfmc_call_after_form_submit');

function sfmc_call_after_form_submit($contact_data)
{

    // Obtiene configuracion del plugin y comprueba que sea correcta
    $client_key = get_option('cf7tosfmc_client_key');
    $client_secret = get_option('cf7tosfmc_client_secret');
    $client_endpoint = get_option('cf7tosfmc_client_endpoint');

    // Obtiene array con datos del formulario y comprueba que sean correctos
    $data = [
        'firstName' => $_POST['your-name'], // DIVIDIR NOMBRE COMPLETO PARA ENVIAR APELLIDOS POR SEPARADO ?
        'LastName' => null, // APELLIDOS ES REQUERIDO !
        'Company' => $_POST['your-deal'],
        'Subtipo__c' => 'Pymes',
        'Canal__c' => 'INSIDESALES',
        'Tipo_de_documento__c' => null,
        'Numero_de_documento__c' => null,
        'Phone' => null,
        'MobilePhone' => null,
        'Email' => $_POST['your-email'],
        'Correo_electronico__c' => $_POST['your-email'],
        'Tipo_via__c' => null,
        'Nombre_via__c' => null,
        'Numero__c' => null,
        'Poblacion__c' => null,
        'Provincia__c' => null,
        'Comunidad_Autonoma__c' => null,
        'Codigo_Postal__c' => null,
        'NumberOfEmployees' => $_POST['menu-364'], // TODO Hay que convertir lo que devuelve el select en un valor numérico entero.
        'Producto_Interesado__c' => null,
    ];

    // Llamada a SFMC

    // wp_die($data);
}

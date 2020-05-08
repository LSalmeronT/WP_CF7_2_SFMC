<?php

// LOGICA SFMC

add_action('wpcf7_before_send_mail', 'sfmc_call_after_form_submit');

function sfmc_call_after_form_submit($contact_data)
{

    // Obtiene configuracion del plugin
    $client_key = get_option('cf7tosfmc_client_key');
    $client_secret = get_option('cf7tosfmc_client_secret');
    $endpoint = get_option('cf7tosfmc_endpoint');
    $actualToken = get_option('cf7tosfmc_actual_token');

    // Obtiene array con datos del formulario
    $data = [
        'FirstName' => separateNames($_POST['your-name'])['firstName'],
        'LastName' => separateNames($_POST['your-name'])['lastName'],
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
        'NumberOfEmployees' => employeesInteger($_POST['menu-364']),
        'Producto_Interesado__c' => null,
    ];

    // Llamada a SFMC
    // Si no hay token almacenado o ya no es valido, solicita uno nuevo
    if(!$actualToken || false) { //Falta comprobar si es valido

        // Conecta para obtener token
        $actualToken = '';

        // Actualiza option con nuevo token recibido
        update_option( 'cf7tosfmc_actual_token', $actualToken );

    } 

    if($actualToken){
        // Envia data
    }

    //wp_die(json_encode($contact_data));
}

/*
 * Devuelve un valor entero en función aun String recibido de valores prestabecidos en el formulario
 */

function employeesInteger($employeesString)
{
    switch ($employeesString) {
        case "1 a 10":
            $employees = 10;
            break;
        case "10 a 100":
            $employees = 100;
            break;
        case "100 a 200":
            $employees = 200;
            break;
        case "> 200":
            $employees = 201;
            break;
        default:
            $employees = 0;
            break;
    }
    return $employees;
}

/*
 * Separa nombre y apellidos
 */

function separateNames($full_name)
{
    $tokens = explode(' ', trim($full_name));
    $names = array();
    $special_tokens = array('da', 'de', 'del', 'la', 'las', 'los', 'y', 'i', 'san', 'santa');
    $prev = "";
    foreach ($tokens as $token) {
        $_token = strtolower($token);
        if (in_array($_token, $special_tokens)) {
            $prev .= "$token ";
        } else {
            $names[] = $prev . $token;
            $prev = "";
        }
    }

    $num_names = count($names);
    $firstName = $lastName = "";
    switch ($num_names) {
        case 0:
            $firstName = '';
            break;
        case 1:
            $firstName = $names[0];
            break;
        case 2:
            $firstName    = $names[0];
            $lastName  = $names[1];
            break;
        case 3:
            $firstName = $names[0];
            $lastName = $names[1] . ' ' . $names[2];
            break;
        case 4:
            $firstName = $names[0] . ' ' . $names[1];
            $lastName = $names[2] . ' ' . $names[3];
            break;
        default:
            $firstName = $names[0] . ' ' . $names[1];
            unset($names[0]);
            unset($names[1]);
            $lastName = implode(' ', $names);
            break;
    }

    $firstName    = mb_convert_case($firstName, MB_CASE_TITLE, 'UTF-8');
    $lastName  = mb_convert_case($lastName, MB_CASE_TITLE, 'UTF-8');
    return ['firstName' => $firstName, 'lastName' => $lastName];
}

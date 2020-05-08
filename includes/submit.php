<?php

// LOGICA SFMC

add_action('wpcf7_before_send_mail', 'sfmc_call_after_form_submit');

function sfmc_call_after_form_submit($contact_data)
{

    // Obtiene configuracion del plugin
    $client_key = get_option('cf7tosfmc_client_key');
    $client_secret = get_option('cf7tosfmc_client_secret');
    $client_endpoint = get_option('cf7tosfmc_client_endpoint');

    // Obtiene array con datos del formulario

    // Procesa lo que devuelve el select de empleados para enviar un valor numerico entero
    switch ($_POST['menu-364']) {
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

    // Separa nombres y apellidos
    $separateNames = separateNames($_POST['your-name']);

    $data = [
        'FirstName' => $separateNames['firstName'],
        'LastName' => $separateNames['lastName'],
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
        'NumberOfEmployees' => $employees,
        'Producto_Interesado__c' => null,
    ];

    // Llamada a SFMC

    wp_die($data);
}

function separateNames($full_name)
{
    /* separar el nombre completo en espacios */
    $tokens = explode(' ', trim($full_name));
    /* arreglo donde se guardan las "palabras" del nombre */
    $names = array();
    /* palabras de apellidos (y nombres) compuetos */
    $special_tokens = array('da', 'de', 'del', 'la', 'las', 'los', 'mac', 'mc', 'van', 'von', 'y', 'i', 'san', 'santa');

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

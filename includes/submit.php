<?php

// LOGICA SFMC

add_action('wpcf7_before_send_mail', 'sfmc_call_after_form_submit');


function sfmc_call_after_form_submit()
{
    // Comprueba si se ha configurado el plugin
    if (checkOptions()) {

        // Genera array con datos del formulario
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

        // Comprueba validez de token almacenado
        $currenToken = get_option('cf7tosfmc_token');
        $currentTokenExpireTime = get_option('cf7tosfmc_token_expire_time');
        $currentTokenIssuedDate = get_option('cf7tosfmc_token_issued_date');

        // Si no es valido, lo obtiene
        if (!$currenToken || (time() >= $currentTokenIssuedDate + $currentTokenExpireTime)) {
            // Conecta para obtener token nuevo
            $currentToken = sfAuthenticate();
        }

        // Envia información del formulario
        if ($currentToken) {
            sfSendData($data);
        }
    }
}

/*
 * CFG - Comprueba si se ha configurado el plugin
 * 
 * @return Bool
 */

function checkOptions()
{
    $client_key = get_option('cf7tosfmc_client_key');
    $client_secret = get_option('cf7tosfmc_client_secret');
    $endpoint = get_option('cf7tosfmc_endpoint');
    $auth_endpoint = get_option('cf7tosfmc_auth_endpoint');
    $user = get_option('cf7tosfmc_user');
    $pass = get_option('cf7tosfmc_pass');
    $userSecurity = get_option('cf7tosfmc_user_security');
    $tokenExpireTime = get_option('cf7tosfmc_token_expire_time');

    // Por el momento solo compruebo que ninguno esté vacio. 
    if (!$client_key || !$client_secret || !$endpoint || !$auth_endpoint || !$user || !$pass || !$userSecurity || !$tokenExpireTime) {
        return false;
    }
    return true;
}


/*
 * SF - Autenticación
 * 
 * @return String / null
 */

function sfAuthenticate()
{
    sfAddLog('INFO', 'Autentication started');
    $client_key = get_option('cf7tosfmc_client_key');
    $client_secret = get_option('cf7tosfmc_client_secret');
    $auth_endpoint = get_option('cf7tosfmc_auth_endpoint');
    $username = get_option('cf7tosfmc_user');
    $pass = get_option('cf7tosfmc_pass');
    $userSecurity = get_option('cf7tosfmc_user_security');

    try {
        $args = array(
            'body'        => array(
                'grant_type'    => 'password',
                'client_id'   => $client_key,
                'client_secret' => $client_secret,
                'username' => $username,
                'password' => $pass . $userSecurity
            )
        );

        $response = wp_remote_post($auth_endpoint, $args);
        $http_code = wp_remote_retrieve_response_code($response);

        if ($http_code == 200) {
            update_option('cf7tosfmc_token', 'TODO'); // TODO => Obtener 'access_token' de $response
            update_option('cf7tosfmc_token_issued_date', time());
            sfAddLog('SUCCESS', 'Autentication done!');
            return 'TODO'; // TODO => Obtener 'access_token' de $response
        } else {
            sfAddLog('ERROR', 'Autentication fail! Error code: ' . $http_code);
            return null;
        }
    } catch (\Exception $e) {
        sfAddLog('ERROR', 'Autentication fail!');
        return null;
    }
}

/*
 * SF - Envia datos de formulario
 * 
 * @param Array $data Información recibida en el formulario
 * @return bool
 */

function sfSendData($data)
{
    sfAddLog('INFO', 'Sending data');
    $currentToken = get_option('cf7tosfmc_token');
    $endpoint = get_option('cf7tosfmc_endpoint');

    try {
        // Proceso de envio de información a SF
        $args = array(
            'body'    => $data,
            'headers'     => array(
                'Authorization' => 'Bearer ' . $currentToken,
            ),
        );

        sfAddLog('INFO', 'Sending data body : ' . json_encode($args));

        $response = wp_remote_post($endpoint, $args);
        $http_code = wp_remote_retrieve_response_code($response);
        if ($http_code == 200) {
            sfAddLog('SUCCESS', 'Sending data done!');
            return true;
        } else {
            sfAddLog('ERROR', 'Error sending data! Error code: ' . $http_code);
            return false;
        }
    } catch (\Exception $e) {
        sfAddLog('ERROR', 'Error sending data!');
        return false;
    }
}

/*
 * AUX - Devuelve un valor entero en función aun String recibido de valores prestabecidos en el formulario
 * 
 * @param String $employeesString 
 * @return Integer
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
 * AUX - Separa nombre y apellidos
 * 
 * @param String $full_name 
 * @return Array
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

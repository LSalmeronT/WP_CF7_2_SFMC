<?php

// LOGICA SFMC

add_action('wpcf7_before_send_mail', 'sfmc_call_after_form_submit');


function sfmc_call_after_form_submit()
{
    // Comprueba si se ha configurado el plugin
    if (checkOptions()) {

        // Comprueba si el ID de formulario recibido concuerda con alguno de los configurados
        $currentFormId = $_POST['_wpcf7'];
        $allowedFormIds = explode(",", get_option('cf7tosfmc_form_ids'));

        if (in_array($currentFormId, $allowedFormIds)) {
            $data = sfGenerateData();

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
    $dataMapping = get_option('cf7tosfmc_data_mapping');
    $tokenExpireTime = get_option('cf7tosfmc_token_expire_time');

    // Por el momento solo compruebo que ninguno esté vacio. 
    if (!$client_key || !$client_secret || !$endpoint || !$auth_endpoint || !$user || !$pass || !$tokenExpireTime || !$dataMapping) {
        return false;
    }
    return true;
}

/*
 * SF - Genera array con información del formulario basandose en el mapeado configurado
 * 
 * @return String / null
 */

function sfGenerateData()
{
    $dataMapping = get_option('cf7tosfmc_data_mapping');
    $lines = explode(PHP_EOL, $dataMapping);
    $data = array();
    foreach ($lines as $line) {
        $lineArray = str_getcsv($line, ":");
        if (trim($lineArray[0]) != '') {
            switch (count($lineArray)) {
                case 1:
                    $data[$lineArray[0]] = null;
                    break;
                case 2:
                    if (isset($_POST[$lineArray[1]])) {
                        $data[$lineArray[0]] = $_POST[$lineArray[1]];
                    } else {
                        $data[$lineArray[0]] = null;
                    }
                    break;
                case 3:
                    if ($lineArray[1] == 'string') {
                        $data[$lineArray[0]] = $lineArray[2];
                    } else if ($lineArray[1] == 'int') {
                        $data[$lineArray[0]] = intval($lineArray[2]);
                    } else if ($lineArray[1] == 'float') {
                        $data[$lineArray[0]] = floatval($lineArray[2]);
                    } else if ($lineArray[1] == 'bool') {
                        $data[$lineArray[0]] = boolval($lineArray[2]);
                    } else {
                        if (isset($_POST[$lineArray[1]])) {
                            $data[$lineArray[0]] = $lineArray[2]($_POST[$lineArray[1]]);
                        } else {
                            $data[$lineArray[0]] = null;
                        }
                    }
                    break;
                case 4:
                    if (isset($_POST[$lineArray[1]])) {
                        $data[$lineArray[0]] = $lineArray[2]($_POST[$lineArray[1]], $lineArray[3]);
                    } else {
                        $data[$lineArray[0]] = null;
                    }
                    break;
                default:
                    break;
            }
        }
    }
    return $data;
}


/*
 * SF - Autenticación
 * 
 * @return String / null
 */

function sfAuthenticate()
{
    sfAddLog('INFO', 'Authentication started');
    $client_key = get_option('cf7tosfmc_client_key');
    $client_secret = get_option('cf7tosfmc_client_secret');
    $auth_endpoint = get_option('cf7tosfmc_auth_endpoint');
    $username = get_option('cf7tosfmc_user');
    $pass = get_option('cf7tosfmc_pass');

    try {
        $args = array(
            'body'        => array(
                'grant_type'    => 'password',
                'client_id'   => $client_key,
                'client_secret' => $client_secret,
                'username' => $username,
                'password' => $pass
            )
        );

        $response = wp_remote_post($auth_endpoint, $args);
        $http_code = wp_remote_retrieve_response_code($response);
        if ($http_code == 200) {
            $responseBody = json_decode(wp_remote_retrieve_body($response), TRUE);
            update_option('cf7tosfmc_token', $responseBody['access_token']);
            update_option('cf7tosfmc_token_issued_date', time());
            sfAddLog('SUCCESS', 'Authentication done! - ' . $response['body']);
            return $responseBody['access_token'];
        } else {
            if (is_wp_error($response)) {
                sfAddLog('ERROR', 'Authentication fail! Error: ' . $response->get_error_code() . ' - ' . $response->get_error_message());
            } else {
                sfAddLog('ERROR', 'Authentication fail! Error: ' . $http_code . ' - ' . $response['body']);
            }
            return null;
        }
    } catch (\Exception $e) {
        sfAddLog('ERROR', 'Authentication fail!');
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
            'body'    => json_encode($data),
            'headers'     => array(
                'Authorization' => 'Bearer ' . $currentToken,
                'Content-Type' => 'application/json; charset=utf-8',
            ),
        );

        sfAddLog('INFO', 'Sending data body : ' . json_encode($args));

        $response = wp_remote_post($endpoint, $args);
        $http_code = wp_remote_retrieve_response_code($response);

        if ($http_code == 201) {
            sfAddLog('SUCCESS', 'Sending data done!');
            return true;
        } else {
            if (is_wp_error($response)) {
                sfAddLog('ERROR', 'Sending data fail! Error: ' . $response->get_error_code() . ' - ' . $response->get_error_message());
            } else {
                sfAddLog('ERROR', 'Error sending data! Error code: ' . $http_code . ' - Error message: ' . $response['body']);
            }
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
 * @param String Index  0-1
 * @return Array
 */

function separateNames($full_name, $index)
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
            $firstName = '-';
            $lastName  = '-';
            break;
        case 1:
            $firstName = $names[0];
            $lastName  = '-';
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
    if ($index == "0") {
        return $firstName;
    } else {
        return $lastName;
    }
}

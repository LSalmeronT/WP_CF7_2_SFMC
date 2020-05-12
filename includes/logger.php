<?php

function sfAddLog($type, $message)
{
    if (!is_array(get_option('cf7tosfmc_last_logs'))) {
        update_option('cf7tosfmc_last_logs', []);
    }
    $actualLog = get_option('cf7tosfmc_last_logs');
    $actualLog[]=[date("d-m-Y H:i:s"),$type,$message,time()];
    $actualLog=sfReorderLog($actualLog);
    update_option('cf7tosfmc_last_logs', $actualLog);

}

function sfReorderLog($actualLog){

    // Ordena por el campo 'timestamp' decreciente
    usort($actualLog, "sfLogCmp");
    // Solo mantiene los 100 ultimos (primeros tras la reordenacion) logs
    return array_slice($actualLog, 0, 100);

}

function sfLogCmp($a, $b)
{
    return strcmp($b[3], $a[3]);
}



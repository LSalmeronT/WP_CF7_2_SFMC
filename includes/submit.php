<?php

// LOGICA SFMC

add_action('wpcf7_before_send_mail', 'sfmc_call_after_form_submit');

function sfmc_call_after_form_submit($contact_data)
{

    wp_die(__('ENTRA TRAS SUBMIT'));

    // Obtiene configuracion del plugin y comprueba que sea correcta

    // Obtiene array con datos del formulario y comprueba que sean correctos

    // Si todo es OK, hace llamada a SFMC

}
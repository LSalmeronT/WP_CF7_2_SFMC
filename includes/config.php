<?php

// create custom plugin settings menu
add_action('admin_menu', 'cf7_to_sfmc_config_menu');

function cf7_to_sfmc_config_menu() {

    add_options_page('CF7 to SFMC Config', 'CF7 to SFMC', 'manage_options', 'cf7-to-sfmc-options', 'cf7_to_sfmc_config_options');

	//call register settings function
	add_action( 'admin_init', 'register_cf7_to_sfmc_settings' );
}


function register_cf7_to_sfmc_settings() {
	//register our settings
	register_setting( 'cf7-to-sfmc', 'client_key' );
	register_setting( 'cf7-to-sfmc', 'client_secret' );
	register_setting( 'cf7-to-sfmc', 'endpoint' );
}

function cf7_to_sfmc_config_options() {
?>
<div class="wrap">
<h1>Configuración CF7 to SFMC</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'cf7-to-sfmc' ); ?>
    <?php do_settings_sections( 'cf7-to-sfmc' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Client Key</th>
        <td><input type="text" name="new_option_name" value="<?php echo esc_attr( get_option('client_key') ); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Client secret</th>
        <td><input type="text" name="some_other_option" value="<?php echo esc_attr( get_option('client_secret') ); ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Endpoint</th>
        <td><input type="text" name="option_etc" value="<?php echo esc_attr( get_option('endpoint') ); ?>" /></td>
        </tr>
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php } ?>
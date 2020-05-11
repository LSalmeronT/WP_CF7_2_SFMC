<?php

// create custom plugin settings menu
add_action('admin_menu', 'cf7_to_sfmc_config_menu');

function cf7_to_sfmc_config_menu()
{

    add_options_page('CF7 to SF Config', 'CF7 to SF', 'manage_options', 'cf7-to-sfmc-options', 'cf7_to_sfmc_config_options');

    //call register settings function
    add_action('admin_init', 'register_cf7_to_sfmc_settings');
}

function register_cf7_to_sfmc_settings()
{
    //register our settings
    $group = 'cf7-to-sfmc';
    register_setting($group, 'cf7tosfmc_client_key');
    register_setting($group, 'cf7tosfmc_client_secret');
    register_setting($group, 'cf7tosfmc_auth_endpoint');
    register_setting($group, 'cf7tosfmc_endpoint');
    register_setting($group, 'cf7tosfmc_token');
    register_setting($group, 'cf7tosfmc_token_expiration');
    register_setting($group, 'cf7tosfmc_user');
    register_setting($group, 'cf7tosfmc_pass');
}

function cf7_to_sfmc_config_options()
{
?>
    <div class="wrap">
        <h1>Configuración CF7 to SF</h1>

        <form method="post" action="options.php">

            <?php settings_fields('cf7-to-sfmc'); ?>
            <?php do_settings_sections('cf7-to-sfmc'); ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Client Key</th>
                    <td><input type="text" name="cf7tosfmc_client_key" value="<?php echo esc_attr(get_option('cf7tosfmc_client_key')); ?>" style="width:100%;" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Client secret</th>
                    <td><input type="text" name="cf7tosfmc_client_secret" value="<?php echo esc_attr(get_option('cf7tosfmc_client_secret')); ?>" style="width:100%;" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Auth endpoint</th>
                    <td><input type="text" name="cf7tosfmc_auth_endpoint" value="<?php echo esc_attr(get_option('cf7tosfmc_auth_endpoint')); ?>" style="width:100%;" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Endpoint</th>
                    <td><input type="text" name="cf7tosfmc_endpoint" value="<?php echo esc_attr(get_option('cf7tosfmc_endpoint')); ?>" style="width:100%;" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">User</th>
                    <td><input type="text" name="cf7tosfmc_user" value="<?php echo esc_attr(get_option('cf7tosfmc_user')); ?>" style="width:100%;" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Pass</th>
                    <td><input type="text" name="cf7tosfmc_pass" value="<?php echo esc_attr(get_option('cf7tosfmc_pass')); ?>" style="width:100%;" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Current token</th>
                    <td><input type="text" name="" value="<?php echo esc_attr(get_option('cf7tosfmc_token')); ?>" style="width:100%;" disabled /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Current token expiration</th>
                    <td><input type="text" name="" value="<?php echo esc_attr(get_option('cf7tosfmc_token_expiration')); ?>" style="width:100%;" disabled /></td>
                </tr>
            </table>

            <?php submit_button(); ?>

        </form>
    </div>
<?php } ?>
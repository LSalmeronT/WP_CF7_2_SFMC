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
    register_setting($group, 'cf7tosfmc_token_issued_date');
    register_setting($group, 'cf7tosfmc_token_expire_time');
    register_setting($group, 'cf7tosfmc_user');
    register_setting($group, 'cf7tosfmc_pass');
    register_setting($group, 'cf7tosfmc_last_logs');
    register_setting($group, 'cf7tosfmc_data_mapping');
    register_setting($group, 'cf7tosfmc_form_ids');
}

function cf7_to_sfmc_config_options()
{
    $preTest = sfAuthenticate();
?>
    <div class="wrap">
        <h1>CF7 to SF Settings</h1>

        <?php
        if ($preTest) {
        ?>
            <div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible">
                <p><strong>Autenticación realizada con exito usando la configuración actual</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Descartar este aviso.</span></button>
            </div>
        <?php
        } else {
        ?>
            <div id="setting-error-settings_updated" class="notice notice-error settings-error is-dismissible">
                <p><strong>Autenticación fallida usando la configuración actual</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Descartar este aviso.</span></button>
            </div>
        <?php
        }

        ?>
        <form method="post" action="options.php">

            <?php settings_fields('cf7-to-sfmc'); ?>
            <?php do_settings_sections('cf7-to-sfmc'); ?>

            <h3>Endpoints</h3><hr/>
            <table class="form-table">
               <tr valign="top">
                    <th scope="row">Auth endpoint</th>
                    <td><input type="url" name="cf7tosfmc_auth_endpoint" value="<?php echo esc_attr(get_option('cf7tosfmc_auth_endpoint')); ?>" style="width:100%;" required /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Data endpoint</th>
                    <td><input type="url" name="cf7tosfmc_endpoint" value="<?php echo esc_attr(get_option('cf7tosfmc_endpoint')); ?>" style="width:100%;" required /></td>
                </tr>
            </table>

            <h3>Authentication</h3><hr/>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Client Key</th>
                    <td><input type="text" name="cf7tosfmc_client_key" value="<?php echo esc_attr(get_option('cf7tosfmc_client_key')); ?>" style="width:100%;" required /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Client secret</th>
                    <td><input type="text" name="cf7tosfmc_client_secret" value="<?php echo esc_attr(get_option('cf7tosfmc_client_secret')); ?>" style="width:100%;" required /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">User</th>
                    <td><input type="text" name="cf7tosfmc_user" value="<?php echo esc_attr(get_option('cf7tosfmc_user')); ?>" style="width:100%;" required /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Pass</th>
                    <td><input type="password" name="cf7tosfmc_pass" value="<?php echo esc_attr(get_option('cf7tosfmc_pass')); ?>" style="width:100%;" required /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Token expiration (seconds)</th>
                    <td><input type="number" name="cf7tosfmc_token_expire_time" value="<?php echo esc_attr(get_option('cf7tosfmc_token_expire_time')); ?>" style="width:100%;" required /></td>
                </tr>

            </table>

            <h3>Form filter and data mapping</h3><hr/>
            <table class="form-table">

                <tr valign="top">
                    <th scope="row">Form IDs (Comma separated)</th>
                    <td><input type="text" name="cf7tosfmc_form_ids" value="<?php echo esc_attr(get_option('cf7tosfmc_form_ids')); ?>" style="width:100%;" required />
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Data mapping</th>
                    <td><textarea name="cf7tosfmc_data_mapping" style="width:100%;height:150px;"><?php echo esc_attr(get_option('cf7tosfmc_data_mapping')); ?></textarea>
                    </td>
                </tr>

            </table>
            
            <?php submit_button(); ?>

            <h3>Debugging</h3><hr/>
            <table class="form-table">

                <tr valign="top">
                    <th scope="row">Current token</th>
                    <td><input type="text" name="" value="<?php echo esc_attr(get_option('cf7tosfmc_token')); ?>" style="width:100%;" disabled /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Current token issued date</th>
                    <td><input type="text" name="" value="<?php if (get_option('cf7tosfmc_token_issued_date')) {
                                                                echo date("d-m-Y H:i:s", esc_attr(get_option('cf7tosfmc_token_issued_date')));
                                                            } ?>" style="width:100%;" disabled /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Latest 100 logs</th>
                    <td><textarea style="width:100%;height:150px;" disabled><?php
                                                                            if (is_array(get_option('cf7tosfmc_last_logs'))) {
                                                                                foreach (get_option('cf7tosfmc_last_logs') as $row) {
                                                                                    echo $row[0] . " - " . $row[1] . " - " . $row[2] . "\r\n";
                                                                                }
                                                                            }
                                                                            ?></textarea></td>
                </tr>

            </table>

        </form>
    </div>
<?php } ?>
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
                <p><strong>SUCCESS! Authentication done successfully using the current configuration</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Descartar este aviso.</span></button>
            </div>
        <?php
        } else {
        ?>
            <div id="setting-error-settings_updated" class="notice notice-error settings-error is-dismissible">
                <p><strong>FAIL! Authentication failed using current configuration</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Descartar este aviso.</span></button>
            </div>
        <?php
        }

        ?>
        <form method="post" action="options.php">

            <?php settings_fields('cf7-to-sfmc'); ?>
            <?php do_settings_sections('cf7-to-sfmc'); ?>

            <h2>Endpoints</h2>
            <p>Data provided by SalesForce.</p>
            <hr />
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

            <h2>Authentication</h2>
            <p>Data provided by SalesForce.</p>
            <hr />
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
                    <th scope="row">Token expiration</th>
                    <td><input type="number" name="cf7tosfmc_token_expire_time" value="<?php echo esc_attr(get_option('cf7tosfmc_token_expire_time')); ?>" style="width:100%;" required />
                        <p>Value in seconds ( Example: 10 )</p>
                    </td>
                </tr>

            </table>

            <h2>Form filter and data mapping</h2>
            <p>Configure which forms will be affected and which fields will be sent to SalesForce.</p>
            <hr />
            <table class="form-table">

                <tr valign="top">
                    <th scope="row">Form IDs</th>
                    <td><input type="text" name="cf7tosfmc_form_ids" value="<?php echo esc_attr(get_option('cf7tosfmc_form_ids')); ?>" style="width:100%;" required />
                        <p>CF7 form IDs where apply, comma separated ( Example: 5,7 )</p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Data mapping</th>
                    <td><textarea name="cf7tosfmc_data_mapping" style="width:100%;height:150px;"><?php echo esc_attr(get_option('cf7tosfmc_data_mapping')); ?></textarea>
                        <p>Each line represents an index that will be added to the data array that will be sent to SalesForce. Each line is made up of a series of values ​​separated by ":" symbol.Blank lines and lines starting with ":" symbol will be ignored. Depending on the number of existing values ​​in each line, it will be processed to generate the index and the value.</p>
                        <ul>
                            <li><h3>Null value</h3>
                                <p>To add an index matched to null, simply add a line with the string to be used for the index name.</p>
                                <p><strong>Example:</strong><br/>'indexName' will add {'indexName':null} to data array.</p>
                            </li>
                            <li><h2>Literal value</h2>
                            <p>To add a matched index to a literal value, which will be the same on all posts to SalesForce, you need to add the variable type and the desired value.</p>
                                <p><strong>Examples:</strong>
                                <br/>'indexName:string:John' will add {'indexName': 'John'} to data array.
                                <br/>'indexName:bool:true' will add {'indexName': true} to data array (PHP boolval("true") result).
                                <br/>'indexName:int:500' will add {'indexName': 500} to data array (PHP intval("500") result).
                                <br/>'indexName:float:5.5' will add {'indexName': 5.5} to data array (PHP floatval("5.5") result).</p>
                            </li>
                            <li><h2>Form field</h2>
                                <p>To add a matched index to the value of a form field, you need to enter the field ID in CF7. If it does not exist, that index is returned equal to null.</p>
                                <p><strong>Example:</strong><br/>'indexName:cf7fieldId' will add {'indexName':cf7fieldIdValue} to data array (Where cf7fieldIdValue is the value returned in form or null).</p>
                            </li>
                            <li><h2>Form field processed</h2>
                                <p>If the value of a field must be processed first, it is necessary to include the field ID in CF7, the name of the function that will process it as the first parameter, and a second parameter if necessary.</p>
                                <p><strong>Examples:</strong>
                                <br/>'indexName:cf7fieldId:functionName' will add {'indexName': returnValue} to the data array, where returnValue is the return of the PHP function functionName(cf7fieldIdValue).
                                <br/>'indexName:cf7fieldId:functionName:secondParam' will add {'indexName': returnValue} to the data array, where returnValue is the return of the PHP function functionName(cf7fieldIdValue, secondParam).
                            </li>
                        </ul>
                    </td>
                </tr>

            </table>

            <?php submit_button(); ?>

            <h2>Debugging</h2>
            <p>Debugging info and event log.</p>
            <hr />
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
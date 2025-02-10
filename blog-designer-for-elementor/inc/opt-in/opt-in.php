<?php
$emailAlreadySent = get_option('rswpthemes_optin_email_sent');
$apiKeyRegistered = get_option('rswpthemes_api_key_registered');
if ( $apiKeyRegistered && $emailAlreadySent ) {
    return;
}

add_action('admin_init', 'bdfe_control_optin_notice');
function bdfe_control_optin_notice() {
    $optin_success = get_option('bdfe_optin_success');
    $hide_notice_transient = get_transient('hide_notice_for_3_days');

    $notice_show = true;

    if ('1' == $optin_success) {
        $notice_show = false;
    } elseif ('1' == $hide_notice_transient) {
        // Check if more than 3 days have passed since setting the transient
        $time_since_transient = current_time('timestamp') - get_option('rswpbs_optin_unsuccess_time');

        if ($time_since_transient >= 3 * 24 * 60 * 60) {
            $notice_show = true;
            // Update the option to record the time of the last "No Thanks" button click
            update_option('rswpbs_optin_unsuccess_time', current_time('timestamp'));
        } else {
            $notice_show = false;
        }
    }
    if (isset($_GET['opt_in_unsuccess'])) {
        $notice_show = false;
    }
    if ( true == $notice_show) {
        add_action('admin_notices', 'bdfe_optin_notice');
    }
}

/**
 * Handle Button Clicks
 */
add_action('admin_init', 'bdfe_handle_opt_notice_button_clicks');

function bdfe_handle_opt_notice_button_clicks() {
    if (isset($_GET['opt_in_success'])) {
        update_option('bdfe_optin_success', '1');
    } elseif (isset($_GET['opt_in_unsuccess'])) {
        set_transient('hide_notice_for_3_days', true, 3 * 24 * 60 * 60);
        update_option('rswpbs_optin_unsuccess_time', current_time('timestamp'));
    }
}

add_action('wp_ajax_bdfe_update_activation_time', 'bdfe_update_activation_time');

function bdfe_update_activation_time() {
    if (current_user_can('manage_options')) {
        update_option('bdfe_optin_success', '1');
        wp_send_json_success();
    } else {
        wp_send_json_error(array('error' => 'Permission denied'));
    }
}


/**
 * Admin Notice HTML Markup
 */
function bdfe_optin_notice() {
    ?>
    <div class="notice notice-info blog-designer-for-elementor-notice-container is-dismissible">
        <div class="blog-designer-for-elementor-opt-in-wrapper">
            <div class="blog-designer-for-elementor-optin-inner">
                <div class="blog-designer-for-elementor-opt-in-content-col">
                    <h4><?php esc_html_e( 'Love using Blog Designer For Elementor?', 'rswpbs' );?></h4>
                    <p><?php esc_html_e('Become a super contributor by opting in to share non-sensitive plugin data and to receive periodic email updates from us.', 'rswpbs'); ?></p>
                </div>
                <div class="blog-designer-for-elementor-button-col">
                    <a href="?opt_in_success" id="yes-i-would-love-to" class="button button-primary"><?php esc_html_e('Sure! I\'d love to help', 'rswpbs'); ?></a>
                    <a href="?opt_in_unsuccess" id="no-thank-you" class="button"><?php esc_html_e( 'No Thanks', 'rswpbs' );?></a>
                </div>
            </div>
        </div>
    </div>
    <style type="text/css">
        .blog-designer-for-elementor-optin-inner {
            display: flex;
        }

        .blog-designer-for-elementor-opt-in-content-col {
            align-self: center;
        }

        .blog-designer-for-elementor-button-col {
            align-self: center;
            padding-left: 40px;
            border-left: 2px solid #ddd;
            margin-left: 40px;
        }

        .blog-designer-for-elementor-opt-in-content-col h4 {
            font-size: 18px;
            margin-bottom: 7px;
            margin-top: 0;
        }

        .blog-designer-for-elementor-opt-in-content-col p {
            font-size: 15px;
            margin: 0;
        }

        .blog-designer-for-elementor-opt-in-wrapper {
            padding-top: 10px;
            padding-bottom: 10px;
        }
    </style>
    <?php
}

function bdfe_send_email() {
    $admin_email = get_option('admin_email');
    if ( empty($admin_email) ) {
        return new WP_Error( 'no_admin_email', 'Admin email not found.' );
    }

    $user_id    = get_current_user_id();
    $first_name = get_user_meta($user_id, 'first_name', true);
    $last_name  = get_user_meta($user_id, 'last_name', true);
    $website_url = untrailingslashit( home_url() );
    $api_url     = 'https://rswpthemes.com/wp-json/rswpthemes/v1/collect_email/';
    $api_key     = get_option('rswpthemes_api_key');

    $response = wp_remote_post($api_url, array(
        'method'    => 'POST',
        'timeout'   => 10,
        'blocking'  => true,
        'headers'   => array(
            'Content-Type'         => 'application/json',
            'X-RSWPTHEMES-API-Key'   => $api_key,
        ),
        'body'      => json_encode(array(
            'email'        => $admin_email,
            'website_name' => get_bloginfo('name'),
            'website_url'  => $website_url,
            'first_name'   => $first_name,
            'last_name'    => $last_name
        )),
        // Uncomment the following line if testing on a local dev server with SSL issues:
        // 'sslverify' => false,
    ));

    return $response;
}



add_action('wp_ajax_befe_collect_email', 'befe_collect_email');
add_action('wp_ajax_nopriv_befe_collect_email', 'befe_collect_email');
function befe_collect_email() {
    $response = bdfe_send_email();

    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        error_log('WP Remote Post Error: ' . $error_message);
        wp_send_json_error(array('error' => 'Failed to send request: ' . $error_message));
    } else {
        // Optionally, you can check the HTTP status code and response body here.
        wp_send_json_success(array('message' => 'Email stored successfully.'));
    }

    wp_die();
}

function bdfe_auto_send_email_if_opted_in() {
    // Check if the site has already opted in...
    if ( get_option('bdfe_optin_success') === '1' && ! get_option('rswpthemes_optin_email_sent') ) {
        $response = bdfe_send_email();
        if ( is_wp_error( $response ) ) {
            error_log('Auto email send failed: ' . $response->get_error_message());
        } else {
            $response_code = wp_remote_retrieve_response_code($response);
            if ( $response_code === 200 ) {
                // Mark that the email has been sent so we don't send it again
                update_option('rswpthemes_optin_email_sent', '1');
                error_log('Auto email sent successfully.');
            } else {
                error_log('Auto email send failed with response code: ' . $response_code);
            }
        }
    }
}
add_action('admin_init', 'bdfe_auto_send_email_if_opted_in');


function bdfe_opt_in_script() {
    wp_enqueue_script('bdfe-opt-ins', BDFE_PLUGIN_URL . '/includes/opt-in/opt-in.js', array('jquery'), '1.0', true);
    wp_localize_script( 'bdfe-opt-ins', 'bdfe_opt_ins',
        array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
        )
    );
}
add_action('admin_enqueue_scripts', 'bdfe_opt_in_script', 99);


function bdfe_ensure_api_key_exists() {
    // Check if an API key already exists
    $existing_key = get_option('rswpthemes_api_key');
    // Check if the key has been registered already
    $registered = get_option('rswpthemes_api_key_registered');

    if (!$existing_key) {
        // No key exists: generate one and store it.
        $new_api_key = wp_generate_password(32, false, false);
        update_option('rswpthemes_api_key', $new_api_key);
        // Register the generated API key on the central server.
        bdfe_register_api_key_on_server($new_api_key);
        // Set the flag indicating that registration is done.
        update_option('rswpthemes_api_key_registered', '1');
    } else {
        // If an API key exists but it hasn't been registered, register it.
        if (!$registered) {
            bdfe_register_api_key_on_server($existing_key);
            update_option('rswpthemes_api_key_registered', '1');
        }
        // If the key is already registered, do nothing.
    }
}
add_action('admin_init', 'bdfe_ensure_api_key_exists');


function bdfe_register_api_key_on_server($api_key) {
    $server_url = 'https://rswpthemes.com/wp-json/rswpthemes/v1/register_api_key/';
    $website_url = untrailingslashit( home_url() );

    error_log("Registering API key for website: $website_url");

    $response = wp_remote_post($server_url, array(
        'method'    => 'POST',
        'timeout'   => 10,
        'blocking'  => true,
        'headers'   => array(
            'Content-Type' => 'application/json'
        ),
        'body'      => json_encode(array(
            'api_key'      => $api_key,
            'website_name' => get_bloginfo('name'),
            'website_url'  => $website_url
        )),
    ));
   if (is_wp_error($response)) {
        error_log('Failed to register API key on the server: ' . $response->get_error_message());
    }
}


<?php
/**
 * RS WP Book Showcase Promotion Content
 */
function bdfe_rswpbs_promoting_notice() {
    if (class_exists('Rswpbs')) {
        return;
    }
    // Check if the transient exists, and only show the notice if it doesn't.
    if (!get_transient('bdfe_rswpbs_promoting_notice_dismissed')) {
        ?>
        <div class="notice notice-info is-dismissible rswpbs-promoting-notice">
            <div class="rswpbs-notice-wrapper">
                <div class="rswpbs-image-wrapper">
                    <img src="<?php echo esc_url( BDFE_PLUGIN_URL . '/assets/admin/img/rswpbs-logo.png' );?>" alt="<?php esc_attr_e( 'rswpbs', 'bdfe' );?>">
                </div>
                <div class="rswpbs-notice-content-wrapper">
                    <p><?php esc_html_e( 'RS WP Book Showcase is a WordPress plugin designed for authors, publishers, book bloggers, and bookstores to showcase books effectively. Key features include customizable layouts, WooCommerce integration for direct sales, Ajax-powered advanced search, CSV import for bulk uploads, and support for multiple book formats. It also offers options for detailed book information, multiple purchase links, and reader reviews. Ideal for anyone looking to create a professional book gallery or enhance their website\'s functionality, this plugin simplifies book presentation while offering seamless user experiences and advanced customization.', 'bdfe' ); ?></p>
                    <div class="rswpbs-buttons-wrapper">
                        <a href="#" class="install-rswpbs button-primary button" id="install-rswpbs"><?php esc_html_e('Activate RS WP Book Showcase', 'bdfe'); ?></a>
                        <a target="_blank" href="<?php echo esc_url('https://rswpthemes.com/rs-wp-book-showcase-wordpress-plugin/');?>" class="rswpbs-demo align-self-center button button-info" id="rswpbs-demo"><?php esc_html_e('View Details & Demo', 'bdfe'); ?></a>
                    </div>
                </div>
            </div>
        </div>
        <style type="text/css">
            .notice.notice-info.is-dismissible.rswpbs-promoting-notice {
                padding: 30px;
                border: 3px solid #03A9F4;
                background: #fff;
            }
            .rswpbs-buttons-wrapper {
                margin-top: 20px;
                display: flex;
            }
            .rswpbs-notice-content-wrapper{
                width: calc(100% - 200px);
            }
            .rswpbs-notice-content-wrapper p {
                font-size: 18px;
                color: #000;
                margin: 0;
            }
            .rswpbs-notice-wrapper {
                display: flex;
                align-items: center;
            }
            a#rswpbs-demo {
                padding: 12px 20px;
                display: inline-block;
                line-height: normal;
                border-radius: 0;
                margin-left: 12px;
            }
            .rswpbs-buttons-wrapper a {
                align-self: center;
            }
            .rswpbs-image-wrapper {
                margin-right: 20px;
                width: 180px;
            }
            .rswpbs-image-wrapper img{
                display: block;
                max-width: 100%;
            }
            .rswpbs-notice-content-wrapper .install-rswpbs {
                padding: 12px 20px;
                display: inline-block;
                font-size: 16px;
                text-decoration: none;
                line-height: normal;
            }
        </style>
        <script>
            (function($) {
                $(document).on('click', '.rswpbs-promoting-notice .notice-dismiss', function() {
                    $.post(ajaxurl, {
                        action: 'bdfe_rswpbs_promote_transient'
                    });
                });
            }(jQuery))
        </script>
        <?php
    }
}

add_action('admin_notices', 'bdfe_rswpbs_promoting_notice');


/**
 * RS WP Book Showcase Notice Transiant
 */
add_action('wp_ajax_bdfe_rswpbs_promote_transient', 'bdfe_rswpbs_promote_transient');
function bdfe_rswpbs_promote_transient() {
    // Set a transient to hide the notice for 1 minute (60 seconds).
    set_transient('bdfe_rswpbs_promoting_notice_dismissed', true, 24 * HOUR_IN_SECONDS);
    wp_send_json_success();
}

/**
 * RS WP Book Showcase Installer Scripts
 */
add_action('admin_enqueue_scripts', 'bdfe_rswpbs_installer_scripts');
function bdfe_rswpbs_installer_scripts() {
    wp_enqueue_script( 'bdfe-rswpbs-installer-script', BDFE_PLUGIN_URL . 'assets/js/rswpbs-install.js', array('jquery'),  '1.0', true);
    wp_localize_script('bdfe-rswpbs-installer-script', 'bdfe_rswpbs_ajax_object', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('rswpbs_install_nonce')
    ]);
}

/**
 * RS WP Book Showcase Ajax Controller
 */
add_action('wp_ajax_install_rswpbs_plugin', 'install_rswpbs_plugin');
function install_rswpbs_plugin() {
    include_once ABSPATH . '/wp-admin/includes/file.php';
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

    if (!file_exists(WP_PLUGIN_DIR . '/rs-wp-books-showcase')) {
        $api = plugins_api('plugin_information', [
            'slug'   => sanitize_key(wp_unslash('rs-wp-books-showcase')),
            'fields' => ['sections' => false],
        ]);

        $skin = new WP_Ajax_Upgrader_Skin();
        $upgrader = new Plugin_Upgrader($skin);
        $upgrader->install($api->download_link);
    }

    if (current_user_can('activate_plugin')) {
        activate_plugin('rs-wp-books-showcase/rs-wp-books-showcase.php');
    }
}
<?php
function bdfe_amz_import_admin_notice() {
    if (!class_exists('Rswpbs')):
        $plugin_slug = 'rs-wp-books-showcase';
        $plugin_file = 'rs-wp-books-showcase/rs-wp-books-showcase.php';
        $plugin_installed = file_exists(WP_PLUGIN_DIR . '/' . $plugin_file);
        $plugin_active = is_plugin_active($plugin_file);

        $user_id = get_current_user_id();
        $dismissed_time = get_user_meta($user_id, 'bdfe_amz_notice_dismissed_time', true);
        if (!$dismissed_time || (time() - $dismissed_time) > 86400) {
            ?>
            <div class="notice notice-info is-dismissible bdfe-amz-import-admin-notice">
                <h3 class="amz-notice-heading"><?php echo esc_html__('🚀 Effortless Book Catalog + Affiliate Earnings! 📚💰', 'bdfe'); ?></h3>
                <p class="amz-notice-sub-heading"><strong>
                    <?php echo esc_html__('Want to monetize your website effortlessly? Now, you can import 1,000+ books from Amazon to your website site in just 10 minutes – no manual work needed!', 'bdfe'); ?></strong>
                </p>
                <ul>
                    <li><?php echo esc_html__('✅ ', 'bdfe'); ?><strong><?php echo esc_html__('Instant Book Catalog –', 'bdfe'); ?></strong> <?php echo esc_html__('Add hundreds (or thousands) of books with just a few clicks. No need to manually enter titles, descriptions, or images!', 'bdfe'); ?></li>

                    <li><?php echo esc_html__('✅ ', 'bdfe'); ?><strong><?php echo esc_html__('Earn Commissions Automatically –', 'bdfe'); ?></strong> <?php echo esc_html__('Insert your Amazon Tracking ID and earn every time someone buys a book through your website.', 'bdfe'); ?></li>

                    <li><?php echo esc_html__('✅ ', 'bdfe'); ?><strong><?php echo esc_html__('Works for Any Niche –', 'bdfe'); ?></strong> <?php echo esc_html__('Whether your site is about business, fitness, self-improvement, cooking, tech, or anything else, you can recommend relevant books to your audience.', 'bdfe'); ?></li>

                    <li><?php echo esc_html__('✅ ', 'bdfe'); ?><strong><?php echo esc_html__('The Bigger Your Catalog, The More You Earn –', 'bdfe'); ?></strong> <?php echo esc_html__('A large book collection = higher chances of sales & commissions!', 'bdfe'); ?></li>

                    <li><?php echo esc_html__('✅ ', 'bdfe'); ?><strong><?php echo esc_html__('No Tech Skills Needed –', 'bdfe'); ?></strong> <?php echo esc_html__('Set up everything easily with our step-by-step video guide included in the Import Books from Amazon page.', 'bdfe'); ?></li>
                </ul>
                <p><strong><?php echo esc_html__('Get Started in Just a Few Clicks!', 'bdfe'); ?></strong></p>
                <div class="rswpbs-amz-admin-notice-btn-wrapper">
                    <?php if ($plugin_active) : ?>
                        <a href="<?php echo esc_url(admin_url('edit.php?post_type=book&page=import-books-from-json')); ?>" class="import-books-from-amazon-btn button button-secondary"><span class="dashicons dashicons-amazon"></span>
                            <?php esc_html_e('Import Books from Amazon', 'bdfe'); ?>
                        </a>
                    <?php else : ?>
                        <a href="#" class="import-books-from-amazon-btn button button-secondary bdfe-rswpbs-install" data-plugin="<?php echo esc_attr($plugin_slug); ?>">
                            <span class="dashicons dashicons-amazon"></span><?php echo esc_html__('Import Books from Amazon', 'bdfe'); ?>
                        </a>
                    <?php endif; ?>
                </div>
                <p><strong><?php echo esc_html__('💰 Start building your book catalog today and turn your website into a passive income machine!', 'bdfe'); ?></strong></p>
            </div>
             <script type="text/javascript">
                jQuery(document).ready(function($) {
                    $('.bdfe-amz-import-admin-notice').on('click', '.notice-dismiss', function() {
                        $.post(ajaxurl, {
                            action: 'bdfe_amz_dismiss_notice',
                            security: '<?php echo wp_create_nonce("bdfe_amz_dismiss_notice"); ?>'
                        });
                    });
                });
            </script>
            <style>
                .rswpbs-amz-admin-notice-btn-wrapper{
                    display: flex;
                }
                .rswpbs-amz-admin-notice-btn-wrapper {
                    margin-top: 15px;
                }
                p.amz-notice-sub-heading {
                    font-size: 14px;
                }
                .notice.notice-info.is-dismissible.rswpbs-amz-admin-notice p {
                    font-size: 14px;
                }
                .notice.notice-info.is-dismissible.bdfe-amz-import-admin-notice ul li {
                    font-size: 14px;
                    margin-bottom: 10px;
                }
                a.import-books-from-amazon-btn.button.button-secondary span {
                    margin-right: 7px;
                }
                a.import-books-from-amazon-btn.button.button-secondary {
                    background: #ffd814 !important;
                    font-weight: 700;
                    border-color: #ffd814 !important;
                    color: #000 !important;
                    display: flex;
                    align-items: center;
                }
            </style>
            <?php
        }
    endif;
}
add_action('admin_notices', 'bdfe_amz_import_admin_notice');

/**
 * Install RSWPBS
 */
add_action( 'wp_ajax_install_rswpbs_only', 'bdfe_install_activate_rswpbs_only' );
function bdfe_install_activate_rswpbs_only() {
    /**
     * Install Plugin.
     */
    include_once ABSPATH . '/wp-admin/includes/file.php';
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

    if ( ! file_exists( WP_PLUGIN_DIR . '/rs-wp-books-showcase' ) ) {
        $api = plugins_api( 'plugin_information', array(
            'slug'   => sanitize_key( wp_unslash( 'rs-wp-books-showcase' ) ),
            'fields' => array(
                'sections' => false,
            ),
        ) );
        $skin     = new WP_Ajax_Upgrader_Skin();
        $upgrader = new Plugin_Upgrader( $skin );
        $result   = $upgrader->install( $api->download_link );
    }
    // Activate plugin.
    if ( current_user_can( 'activate_plugin' ) ) {
        $result = activate_plugin( 'rs-wp-books-showcase/rs-wp-books-showcase.php' );
    }
}

// Handle notice dismissal and store time
function bdfe_amz_dismiss_notice() {
    check_ajax_referer('bdfe_amz_dismiss_notice', 'security');
    update_user_meta(get_current_user_id(), 'bdfe_amz_notice_dismissed_time', time());
    wp_die();
}
add_action('wp_ajax_bdfe_amz_dismiss_notice', 'bdfe_amz_dismiss_notice');

function bdfe_plugins_installer_scripts(){
    if (!class_exists('Rswpbs')) :
        wp_enqueue_script( 'bdfe-plugin-installer', BDFE_PLUGIN_URL . '/inc/import-books-from-amazon-notice/import-books-from-amazon-notice.js', array( 'jquery' ), '', true );
        wp_localize_script( 'bdfe-plugin-installer', 'bdfe_ajax_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ) )
        );
    endif;
}
add_action( 'admin_enqueue_scripts', 'bdfe_plugins_installer_scripts' );
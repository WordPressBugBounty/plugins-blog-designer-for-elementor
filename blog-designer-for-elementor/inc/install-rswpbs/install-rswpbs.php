<?php
class RswpbsInstaller {

    public function __construct() {
        add_action('admin_footer', [$this, 'admin_footer_ads']);
        add_action('admin_enqueue_scripts', [$this, 'install_rswpbs']);
        add_action('wp_ajax_install_rswpbs_plugin', [$this, 'install_rswpbs_plugin']);
    }

    public function admin_footer_ads() {
        ?>
        <div class="blog-designer-for-elementor-admin-footer-ad-wrapper" id="rswpbs-banner-ad">
            <div class="blog-designer-for-elementor-admin-footer-ad-inner">
                <div class="close-popup">
                    <div class="rswpbs-banner-close"><span class="dashicons dashicons-no-alt"></span></div>
                </div>
                <div class="blog-designer-for-elementor-ad-content">
                    <div class="blog-designer-for-elementor-ad-image image-left">
                         <img src="<?php echo esc_url(BDFE_PLUGIN_URL . 'inc/install-rswpbs/author-portfolio-pro-thumb.png');?>" alt="<?php esc_attr_e('RS WP BOOK SHOWCASE', 'blog-designer-for-elementor');?>">
                    </div>
                    <h2><?php esc_html_e('Enhance Your Website with RS WP Book Showcase Plugin!', 'blog-designer-for-elementor'); ?></h2>
                    <p><?php echo esc_html__('Do you have a collection of books or literary works to showcase on your website? With the ', 'blog-designer-for-elementor') . '<strong>' . esc_html__('RS WP Book Showcase Plugin', 'blog-designer-for-elementor') . '</strong>' . esc_html__(', you can easily display your books in stunning layouts, create beautiful book galleries, and even provide detailed descriptions with purchase links. It\'s the perfect tool for authors, publishers, and bloggers who want to present their books in an elegant and professional way. Start attracting more readers and book lovers today!', 'blog-designer-for-elementor'); ?></p>
                </div>
                <div class="blog-designer-for-elementor-ad-button-wrapper">
                    <a class="rswpbs-install" href="#"><?php esc_html_e('Install Now', 'blog-designer-for-elementor'); ?></a>
                    <a class="rswpbs-learn-more" target="_blank" href="<?php echo esc_url('https://rswpthemes.com/rs-wp-book-showcase-wordpress-plugin/');?>"><?php esc_html_e('View Details', 'blog-designer-for-elementor'); ?></a>
                </div>
            </div>
        </div>
        <?php
    }

    public function install_rswpbs() {
        wp_enqueue_style('blog-designer-for-elementor-rswpbs-install', BDFE_PLUGIN_URL . '/inc/install-rswpbs/install-rswpbs.css');
        wp_enqueue_script('blog-designer-for-elementor-rswpbs-installer', BDFE_PLUGIN_URL . '/inc/install-rswpbs/install-rswpbs.js', array('jquery'), '', true);

        wp_localize_script('blog-designer-for-elementor-rswpbs-installer', 'blog_designer_for_elementor_rswpbs_ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('rswpbs_install_nonce')
        ]);
    }

    public function install_rswpbs_plugin() {
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
}

// Initialize the class
new RswpbsInstaller();

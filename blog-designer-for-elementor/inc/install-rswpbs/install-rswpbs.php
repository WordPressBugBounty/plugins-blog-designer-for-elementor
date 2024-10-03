<?php
add_action( 'admin_footer', 'blog_designer_for_elementor_admin_footer_ads' );
function blog_designer_for_elementor_admin_footer_ads(){
	?>
	<div class="blog-designer-for-elementor-admin-footer-ad-wrapper" id="rswpbs-banner-ad">
		<div class="blog-designer-for-elementor-admin-footer-ad-inner">
			<div class="close-popup">
				<div class="rswpbs-banner-close"><span class="dashicons dashicons-no-alt"></span></div>
			</div>
			<div class="blog-designer-for-elementor-ad-image image-left">
				 <img src="<?php echo esc_url( BDFE_PLUGIN_URL . 'inc/install-rswpbs/author-portfolio-pro-thumb.png' );?>" alt="<?php esc_attr_e('RS WP BOOK SHOWCASE', 'blog-designer-for-elementor');?>">
			</div>
			<div class="blog-designer-for-elementor-ad-content">
			    <h2><?php esc_html_e('Take Your Book Display to the Next Level!', 'blog-designer-for-elementor'); ?></h2>
			    <p><?php esc_html_e('Do you feature books on your website? Make them stand out with the RS WP Book Showcase Plugin – a must-have plugin for authors, bloggers, and publishers.', 'blog-designer-for-elementor'); ?></p>
			    <ul>
			        <li>✅ <?php esc_html_e('Showcase your books in a stunning, organized layout.', 'blog-designer-for-elementor'); ?></li>
			        <li>✅ <?php esc_html_e('Customize it to fit your website\'s unique style.', 'blog-designer-for-elementor'); ?></li>
			        <li>✅ <?php esc_html_e('Boost engagement with a user-friendly, mobile-responsive design.', 'blog-designer-for-elementor'); ?></li>
			    </ul>
			    <p><?php esc_html_e('And the best part? It\'s FREE to install!', 'blog-designer-for-elementor'); ?></p>
			    <p><strong><?php esc_html_e('Unlock a beautiful book showcase with just one click.', 'blog-designer-for-elementor'); ?></strong><br>
			    <?php esc_html_e('Click', 'blog-designer-for-elementor'); ?> <strong><?php esc_html_e('Install Now', 'blog-designer-for-elementor'); ?></strong> <?php esc_html_e('and start transforming your book display today!', 'blog-designer-for-elementor'); ?></p>
			    <a class="rswpbs-install" href="#"><?php esc_html_e('Install Now', 'blog-designer-for-elementor'); ?></a>
			    <a class="rswpbs-learn-more" target="_blank" href="<?php echo esc_url('https://rswpthemes.com/rs-wp-book-showcase-wordpress-plugin/');?>"><?php esc_html_e('View Details', 'blog-designer-for-elementor'); ?></a>
			</div>
		</div>
	</div>
	<?php
}

/**************************
 *   RSWPBS Installer
 **************************/

 //Admin Enqueue for Admin
function blog_designer_for_elementor_install_rswpbs(){
	wp_enqueue_style( 'blog-designer-for-elementor-rswpbs-install', BDFE_PLUGIN_URL . '/inc/install-rswpbs/install-rswpbs.css' );
	wp_enqueue_script( 'blog-designer-for-elementor-rswpbs-installer', BDFE_PLUGIN_URL . '/inc/install-rswpbs/install-rswpbs.js', array( 'jquery' ), '', true );
    wp_localize_script( 'blog-designer-for-elementor-rswpbs-installer', 'blog_designer_for_elementor_rswpbs_ajax_object',
        array(
        	'ajax_url' => admin_url( 'admin-ajax.php' ),
        	'nonce'    => wp_create_nonce('rswpbs_install_nonce')
        ),
    );
}
add_action( 'admin_enqueue_scripts', 'blog_designer_for_elementor_install_rswpbs' );

add_action( 'wp_ajax_install_rswpbs_plugin', 'blog_designer_for_elementor_rswpbs_install_plugin' );

function blog_designer_for_elementor_rswpbs_install_plugin() {
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
    if ( current_user_can( 'activate_plugin' ) ) {
        $result = activate_plugin( 'rs-wp-books-showcase/rs-wp-books-showcase.php' );
    }
}

<?php
add_action( 'admin_menu', 'bdfe_welcome_page_menu' );
function bdfe_welcome_page_menu(){
	add_menu_page(
		__('Blog Design Tutorial', 'bdfe')
		, __('Blog Design Tutorial'),
		'manage_options',
		'rswpthemes-blog-designer',
		'bdfe_welcome_page',
		'dashicons-layout',
		60
	);

}
function bdfe_welcome_page(){
	?>
	<div class="section-blog-designer">
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<div class="blog-desinger-single-item">
						<h2><?php esc_html_e( 'This video shows you how you can create a blog page using Blog Designer and Elmentor Page builder plugin.', 'bdfe' );?></h2>
						<iframe width="600" height="320" src="https://www.youtube.com/embed/gAZFDGNJTeQ" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}

add_action( 'admin_enqueue_scripts', 'bdfe_admin_welcome_page_scripts', 10, 1 );

function bdfe_admin_welcome_page_scripts(){
	if (admin_url('theimran-blog-designer')) {
		wp_enqueue_style( 'bdfe_welcome_page', BDFE_PLUGIN_URL . 'assets/admin/admin.css' );
	}
}


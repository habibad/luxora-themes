<?php
/**
 * Widget areas (Widget API).
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register sidebars / widget areas.
 */
function luxora_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Blog Sidebar', 'luxora' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Widgets for the blog & single posts.', 'luxora' ),
			'before_widget' => '<section id="%1$s" class="luxora-widget mb-12 %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="eyebrow mb-5">',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Shop Sidebar (Filters)', 'luxora' ),
			'id'            => 'shop-filters',
			'description'   => __( 'WooCommerce filter widgets shown on the shop archive.', 'luxora' ),
			'before_widget' => '<div id="%1$s" class="luxora-filter-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="eyebrow mb-5">',
			'after_title'   => '</h3>',
		)
	);

	for ( $i = 1; $i <= 4; $i++ ) {
		register_sidebar(
			array(
				/* translators: %d: footer column number */
				'name'          => sprintf( __( 'Footer Column %d', 'luxora' ), $i ),
				'id'            => 'footer-' . $i,
				'description'   => __( 'Optional footer widget column.', 'luxora' ),
				'before_widget' => '<div id="%1$s" class="luxora-footer-widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h4 class="eyebrow text-cream/60 mb-5">',
				'after_title'   => '</h4>',
			)
		);
	}
}
add_action( 'widgets_init', 'luxora_widgets_init' );

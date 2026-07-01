<?php
/**
 * One-time content provisioning: create the pages the theme expects, assign
 * their page templates, and build starter navigation menus. Runs on activation.
 *
 * Everything here is idempotent — nothing is duplicated or overwritten if the
 * site already has it.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Find a page by title, or create it (optionally with a page template).
 *
 * @param string $title    Page title.
 * @param string $template Template filename (e.g. 'template-about.php'), or ''.
 * @param string $content  Optional initial content.
 * @return int Page ID (0 on failure).
 */
function luxora_get_or_create_page( $title, $template = '', $content = '' ) {
	$existing = get_posts(
		array(
			'post_type'              => 'page',
			'title'                  => $title,
			'post_status'            => array( 'publish', 'draft', 'pending', 'private' ),
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	if ( ! empty( $existing ) ) {
		$page_id = (int) $existing[0];
		if ( $template && ! get_post_meta( $page_id, '_wp_page_template', true ) ) {
			update_post_meta( $page_id, '_wp_page_template', $template );
		}
		return $page_id;
	}

	$page_id = wp_insert_post(
		array(
			'post_title'   => $title,
			'post_content' => $content,
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_author'  => get_current_user_id() ? get_current_user_id() : 1,
		)
	);

	if ( $page_id && ! is_wp_error( $page_id ) ) {
		if ( $template ) {
			update_post_meta( $page_id, '_wp_page_template', $template );
		}
		return (int) $page_id;
	}

	return 0;
}

/**
 * Provision all expected pages + starter menus on theme activation.
 */
function luxora_provision_content() {
	// ---- Pages (title => [template, content]) ------------------------------
	$pages = array(
		'About'             => array( 'template-about.php', '' ),
		'Contact'           => array( 'template-contact.php', '' ),
		'FAQ'               => array( 'template-faq.php', '' ),
		'Collections'       => array( 'template-collections.php', '' ),
		'Best Sellers'      => array( 'template-best-sellers.php', '' ),
		'New Arrivals'      => array( 'template-new-arrivals.php', '' ),
		'Wishlist'          => array( 'template-wishlist.php', '' ),
		'Track Order'       => array( 'template-track.php', '' ),
		'Privacy Policy'    => array( '', __( 'Add your privacy policy here.', 'luxora' ) ),
		'Terms & Conditions' => array( '', __( 'Add your terms and conditions here.', 'luxora' ) ),
	);

	$ids = array();
	foreach ( $pages as $title => $meta ) {
		$ids[ $title ] = luxora_get_or_create_page( $title, $meta[0], $meta[1] );
	}

	// Shop URL (WooCommerce) for menu links.
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop' );

	// ---- Starter menus -----------------------------------------------------
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( ! is_array( $locations ) ) {
		$locations = array();
	}

	/**
	 * Build a menu (if missing) with the supplied items and assign it to a location.
	 *
	 * @param string $menu_name Menu name.
	 * @param string $location  Theme location slug.
	 * @param array  $items     List of [label, url].
	 */
	$build_menu = function ( $menu_name, $location, $items ) use ( &$locations ) {
		if ( ! empty( $locations[ $location ] ) && is_nav_menu( $locations[ $location ] ) ) {
			return; // Already assigned.
		}

		$menu = wp_get_nav_menu_object( $menu_name );
		$menu_id = $menu ? (int) $menu->term_id : (int) wp_create_nav_menu( $menu_name );

		if ( is_wp_error( $menu_id ) || ! $menu_id ) {
			return;
		}

		// Only populate a freshly created (empty) menu.
		$current_items = wp_get_nav_menu_items( $menu_id );
		if ( empty( $current_items ) ) {
			foreach ( $items as $item ) {
				if ( empty( $item[1] ) ) {
					continue;
				}
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'  => $item[0],
						'menu-item-url'    => $item[1],
						'menu-item-status' => 'publish',
					)
				);
			}
		}

		$locations[ $location ] = $menu_id;
	};

	$permalink = function ( $title ) use ( $ids ) {
		return ! empty( $ids[ $title ] ) ? get_permalink( $ids[ $title ] ) : '';
	};

	$build_menu(
		'Luxora Primary',
		'primary',
		array(
			array( __( 'Shop', 'luxora' ), $shop_url ),
			array( __( 'New Arrivals', 'luxora' ), $permalink( 'New Arrivals' ) ),
			array( __( 'Best Sellers', 'luxora' ), $permalink( 'Best Sellers' ) ),
			array( __( 'Collections', 'luxora' ), $permalink( 'Collections' ) ),
			array( __( 'About', 'luxora' ), $permalink( 'About' ) ),
		)
	);

	$build_menu(
		'Luxora Mobile',
		'mobile',
		array(
			array( __( 'Shop', 'luxora' ), $shop_url ),
			array( __( 'New Arrivals', 'luxora' ), $permalink( 'New Arrivals' ) ),
			array( __( 'Best Sellers', 'luxora' ), $permalink( 'Best Sellers' ) ),
			array( __( 'Collections', 'luxora' ), $permalink( 'Collections' ) ),
			array( __( 'About', 'luxora' ), $permalink( 'About' ) ),
			array( __( 'Contact', 'luxora' ), $permalink( 'Contact' ) ),
		)
	);

	$build_menu(
		'Luxora Footer — Maison',
		'footer-maison',
		array(
			array( __( 'About', 'luxora' ), $permalink( 'About' ) ),
			array( __( 'Contact', 'luxora' ), $permalink( 'Contact' ) ),
			array( __( 'FAQ', 'luxora' ), $permalink( 'FAQ' ) ),
		)
	);

	$build_menu(
		'Luxora Footer — Shop',
		'footer-shop',
		array(
			array( __( 'Shop', 'luxora' ), $shop_url ),
			array( __( 'New Arrivals', 'luxora' ), $permalink( 'New Arrivals' ) ),
			array( __( 'Best Sellers', 'luxora' ), $permalink( 'Best Sellers' ) ),
			array( __( 'Collections', 'luxora' ), $permalink( 'Collections' ) ),
		)
	);

	$build_menu(
		'Luxora Footer — Care',
		'footer-care',
		array(
			array( __( 'Track Order', 'luxora' ), $permalink( 'Track Order' ) ),
			array( __( 'Wishlist', 'luxora' ), $permalink( 'Wishlist' ) ),
			array( __( 'FAQ', 'luxora' ), $permalink( 'FAQ' ) ),
		)
	);

	$build_menu(
		'Luxora Footer — Legal',
		'footer-legal',
		array(
			array( __( 'Privacy Policy', 'luxora' ), $permalink( 'Privacy Policy' ) ),
			array( __( 'Terms & Conditions', 'luxora' ), $permalink( 'Terms & Conditions' ) ),
		)
	);

	set_theme_mod( 'nav_menu_locations', $locations );

	update_option( 'luxora_content_provisioned', LUXORA_VERSION );
}
add_action( 'after_switch_theme', 'luxora_provision_content' );

/**
 * Force the WooCommerce Cart & Checkout pages to use the CLASSIC shortcodes
 * instead of the block-based Cart/Checkout, so the Luxora custom templates
 * (woocommerce/cart/*, woocommerce/checkout/*) actually render.
 *
 * WooCommerce 8.3+ ships block cart/checkout by default; those ignore PHP
 * template overrides entirely, which is why an un-migrated site shows the
 * generic block UI.
 */
function luxora_force_classic_woo_pages() {
	if ( ! function_exists( 'wc_get_page_id' ) ) {
		return;
	}

	$map = array(
		wc_get_page_id( 'cart' )     => '[woocommerce_cart]',
		wc_get_page_id( 'checkout' ) => '[woocommerce_checkout]',
	);

	foreach ( $map as $page_id => $shortcode ) {
		$page_id = (int) $page_id;
		if ( $page_id <= 0 ) {
			continue;
		}
		$content = (string) get_post_field( 'post_content', $page_id );

		// Already classic? Leave it alone.
		if ( false !== strpos( $content, $shortcode ) && false === strpos( $content, 'wp:woocommerce/' ) ) {
			continue;
		}

		wp_update_post(
			array(
				'ID'           => $page_id,
				'post_content' => $shortcode,
			)
		);
	}
}

/**
 * Ensure at least one payment method is available so checkout is never dead.
 * Enables Cash on Delivery only if the store currently has no enabled gateway.
 */
function luxora_maybe_enable_cod() {
	if ( ! function_exists( 'WC' ) ) {
		return;
	}

	// Is any gateway already switched on?
	$has_enabled = false;
	foreach ( array( 'cod', 'bacs', 'cheque', 'stripe', 'ppcp-gateway', 'paypal' ) as $gw ) {
		$settings = get_option( 'woocommerce_' . $gw . '_settings', array() );
		if ( is_array( $settings ) && isset( $settings['enabled'] ) && 'yes' === $settings['enabled'] ) {
			$has_enabled = true;
			break;
		}
	}

	if ( $has_enabled ) {
		return;
	}

	$cod = get_option( 'woocommerce_cod_settings', array() );
	if ( ! is_array( $cod ) ) {
		$cod = array();
	}
	$cod['enabled']     = 'yes';
	$cod['title']       = isset( $cod['title'] ) && $cod['title'] ? $cod['title'] : __( 'Cash on delivery', 'luxora' );
	$cod['description'] = isset( $cod['description'] ) && $cod['description'] ? $cod['description'] : __( 'Pay with cash upon delivery.', 'luxora' );
	update_option( 'woocommerce_cod_settings', $cod );

	// Make sure COD is in the ordered gateway list.
	$order = get_option( 'woocommerce_gateway_order', array() );
	if ( is_array( $order ) && ! isset( $order['cod'] ) ) {
		$order['cod'] = count( $order );
		update_option( 'woocommerce_gateway_order', $order );
	}
}

/**
 * Run one-time migrations whenever the theme version changes.
 * Hooked late on admin so WooCommerce is fully loaded.
 */
function luxora_run_migrations() {
	if ( get_option( 'luxora_migrated_version' ) === LUXORA_VERSION ) {
		return;
	}
	luxora_force_classic_woo_pages();
	luxora_maybe_enable_cod();
	update_option( 'luxora_migrated_version', LUXORA_VERSION );
}
add_action( 'admin_init', 'luxora_run_migrations' );

// Also run immediately on (re)activation.
add_action( 'after_switch_theme', 'luxora_force_classic_woo_pages', 20 );
add_action( 'after_switch_theme', 'luxora_maybe_enable_cod', 21 );

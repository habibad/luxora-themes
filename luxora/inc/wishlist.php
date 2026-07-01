<?php
/**
 * Lightweight wishlist — works for guests (cookie) and logged-in users (meta).
 * "Wishlist ready" with no plugin dependency. Integrates with YITH if present.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cookie name for guest wishlists.
 */
function luxora_wishlist_cookie() {
	return 'luxora_wishlist';
}

/**
 * Get current wishlist product IDs.
 *
 * @return int[]
 */
function luxora_get_wishlist() {
	$ids = array();

	if ( is_user_logged_in() ) {
		$stored = get_user_meta( get_current_user_id(), '_luxora_wishlist', true );
		$ids    = is_array( $stored ) ? $stored : array();
	} else {
		$cookie = isset( $_COOKIE[ luxora_wishlist_cookie() ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ luxora_wishlist_cookie() ] ) ) : '';
		if ( $cookie ) {
			$ids = array_filter( array_map( 'absint', explode( ',', $cookie ) ) );
		}
	}

	return array_values( array_unique( array_map( 'absint', $ids ) ) );
}

/**
 * Persist the wishlist.
 *
 * @param int[] $ids Product IDs.
 */
function luxora_save_wishlist( $ids ) {
	$ids = array_values( array_unique( array_map( 'absint', (array) $ids ) ) );

	if ( is_user_logged_in() ) {
		update_user_meta( get_current_user_id(), '_luxora_wishlist', $ids );
	} else {
		$value = implode( ',', $ids );
		// 30-day cookie.
		setcookie( luxora_wishlist_cookie(), $value, time() + ( 30 * DAY_IN_SECONDS ), COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), false );
		$_COOKIE[ luxora_wishlist_cookie() ] = $value;
	}
}

/**
 * Is a product in the wishlist?
 *
 * @param int $product_id Product ID.
 * @return bool
 */
function luxora_in_wishlist( $product_id ) {
	return in_array( absint( $product_id ), luxora_get_wishlist(), true );
}

/**
 * AJAX: toggle wishlist.
 */
function luxora_ajax_toggle_wishlist() {
	check_ajax_referer( 'luxora_nonce', 'nonce' );

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	if ( ! $product_id || ! wc_get_product( $product_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid product.', 'luxora' ) ) );
	}

	$ids   = luxora_get_wishlist();
	$index = array_search( $product_id, $ids, true );

	if ( false !== $index ) {
		unset( $ids[ $index ] );
		$active = false;
	} else {
		$ids[]  = $product_id;
		$active = true;
	}

	luxora_save_wishlist( $ids );

	wp_send_json_success(
		array(
			'active'  => $active,
			'count'   => count( $ids ),
			'message' => $active ? __( 'Saved to wishlist', 'luxora' ) : __( 'Removed from wishlist', 'luxora' ),
		)
	);
}
add_action( 'wp_ajax_luxora_toggle_wishlist', 'luxora_ajax_toggle_wishlist' );
add_action( 'wp_ajax_nopriv_luxora_toggle_wishlist', 'luxora_ajax_toggle_wishlist' );

/**
 * Merge guest cookie wishlist into the user account on login.
 *
 * @param string  $user_login Username.
 * @param WP_User $user       User.
 */
function luxora_merge_wishlist_on_login( $user_login, $user ) {
	$cookie = isset( $_COOKIE[ luxora_wishlist_cookie() ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ luxora_wishlist_cookie() ] ) ) : '';
	if ( ! $cookie ) {
		return;
	}
	$guest  = array_filter( array_map( 'absint', explode( ',', $cookie ) ) );
	$stored = get_user_meta( $user->ID, '_luxora_wishlist', true );
	$stored = is_array( $stored ) ? $stored : array();
	$merged = array_values( array_unique( array_merge( $stored, $guest ) ) );
	update_user_meta( $user->ID, '_luxora_wishlist', $merged );
	setcookie( luxora_wishlist_cookie(), '', time() - 3600, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN );
}
add_action( 'wp_login', 'luxora_merge_wishlist_on_login', 10, 2 );

<?php
/**
 * AJAX handlers: add-to-cart, mini-cart, quantity, newsletter.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AJAX add to cart (simple products + sensible variation handling).
 */
function luxora_ajax_add_to_cart() {
	check_ajax_referer( 'luxora_nonce', 'nonce' );

	if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart ) {
		wp_send_json_error( array( 'message' => __( 'Cart unavailable.', 'luxora' ) ) );
	}

	$product_id   = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$quantity     = isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : 1;
	$variation_id = isset( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;
	$variation    = array();

	if ( isset( $_POST['variation'] ) && is_array( $_POST['variation'] ) ) {
		foreach ( wp_unslash( $_POST['variation'] ) as $key => $value ) {
			$variation[ sanitize_text_field( $key ) ] = sanitize_text_field( $value );
		}
	}

	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		wp_send_json_error( array( 'message' => __( 'Invalid product.', 'luxora' ) ) );
	}

	$quantity = $quantity > 0 ? $quantity : 1;

	$passed = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
	if ( ! $passed ) {
		wp_send_json_error(
			array(
				'message'   => __( 'This piece could not be added.', 'luxora' ),
				'fragments' => luxora_get_cart_fragments(),
			)
		);
	}

	$added = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation );

	if ( $added ) {
		do_action( 'woocommerce_ajax_added_to_cart', $product_id );
		wp_send_json_success(
			array(
				'message'   => __( 'Added to bag', 'luxora' ),
				'count'     => WC()->cart->get_cart_contents_count(),
				'fragments' => luxora_get_cart_fragments(),
			)
		);
	}

	$notices = function_exists( 'wc_get_notices' ) ? wc_get_notices( 'error' ) : array();
	$message = ! empty( $notices ) ? wp_strip_all_tags( $notices[0]['notice'] ) : __( 'Could not add to bag.', 'luxora' );
	if ( function_exists( 'wc_clear_notices' ) ) {
		wc_clear_notices();
	}
	wp_send_json_error( array( 'message' => $message ) );
}
add_action( 'wp_ajax_luxora_add_to_cart', 'luxora_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_luxora_add_to_cart', 'luxora_ajax_add_to_cart' );

/**
 * AJAX update cart line quantity (used on the cart page).
 */
function luxora_ajax_update_qty() {
	check_ajax_referer( 'luxora_nonce', 'nonce' );

	if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart ) {
		wp_send_json_error( array( 'message' => __( 'Cart unavailable.', 'luxora' ) ) );
	}

	$key = isset( $_POST['cart_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_key'] ) ) : '';
	$qty = isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : 1;

	if ( ! $key || ! WC()->cart->get_cart_item( $key ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid cart item.', 'luxora' ) ) );
	}

	if ( $qty <= 0 ) {
		WC()->cart->remove_cart_item( $key );
	} else {
		WC()->cart->set_quantity( $key, $qty, true );
	}
	WC()->cart->calculate_totals();

	$item = WC()->cart->get_cart_item( $key );

	wp_send_json_success(
		array(
			'count'      => WC()->cart->get_cart_contents_count(),
			'line_total' => $item ? wp_strip_all_tags( wc_price( $item['line_total'] ) ) : '',
			'subtotal'   => wp_strip_all_tags( WC()->cart->get_cart_subtotal() ),
			'total'      => wp_strip_all_tags( WC()->cart->get_total() ),
			'fragments'  => luxora_get_cart_fragments(),
			'removed'    => ( $qty <= 0 ),
		)
	);
}
add_action( 'wp_ajax_luxora_update_qty', 'luxora_ajax_update_qty' );
add_action( 'wp_ajax_nopriv_luxora_update_qty', 'luxora_ajax_update_qty' );

/**
 * AJAX remove cart item.
 */
function luxora_ajax_remove_item() {
	check_ajax_referer( 'luxora_nonce', 'nonce' );
	if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart ) {
		wp_send_json_error();
	}
	$key = isset( $_POST['cart_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_key'] ) ) : '';
	if ( $key ) {
		WC()->cart->remove_cart_item( $key );
		WC()->cart->calculate_totals();
	}
	wp_send_json_success(
		array(
			'count'     => WC()->cart->get_cart_contents_count(),
			'subtotal'  => wp_strip_all_tags( WC()->cart->get_cart_subtotal() ),
			'total'     => wp_strip_all_tags( WC()->cart->get_total() ),
			'fragments' => luxora_get_cart_fragments(),
		)
	);
}
add_action( 'wp_ajax_luxora_remove_item', 'luxora_ajax_remove_item' );
add_action( 'wp_ajax_nopriv_luxora_remove_item', 'luxora_ajax_remove_item' );

/**
 * Build the cart fragments used to live-update the header bubble & mini-cart.
 *
 * @return array
 */
function luxora_get_cart_fragments() {
	$count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;

	ob_start();
	luxora_cart_count_bubble();
	$bubble = ob_get_clean();

	ob_start();
	if ( function_exists( 'woocommerce_mini_cart' ) ) {
		echo '<div class="luxora-mini-cart-inner flex-1 overflow-y-auto">';
		woocommerce_mini_cart();
		echo '</div>';
	}
	$mini = ob_get_clean();

	return array(
		'span.luxora-cart-count' => $bubble,
		'.luxora-mini-cart-inner' => $mini,
	);
}

/**
 * The header cart count bubble fragment.
 */
function luxora_cart_count_bubble() {
	$count = luxora_cart_count();
	printf(
		'<span class="luxora-cart-count absolute -top-0 -right-0 bg-gold text-ink text-[10px] rounded-full h-4 w-4 grid place-items-center font-medium%s">%s</span>',
		$count > 0 ? '' : ' hidden',
		esc_html( $count )
	);
}

/**
 * Ensure our bubble is part of the default WooCommerce fragment refresh too.
 *
 * @param array $fragments Fragments.
 * @return array
 */
function luxora_woocommerce_cart_fragments( $fragments ) {
	ob_start();
	luxora_cart_count_bubble();
	$fragments['span.luxora-cart-count'] = ob_get_clean();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'luxora_woocommerce_cart_fragments' );

/**
 * AJAX newsletter capture (stored as a CPT-free option list; pluggable).
 */
function luxora_ajax_newsletter() {
	check_ajax_referer( 'luxora_nonce', 'nonce' );

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Please enter a valid email.', 'luxora' ) ) );
	}

	$list = get_option( 'luxora_newsletter_emails', array() );
	if ( ! is_array( $list ) ) {
		$list = array();
	}
	if ( ! in_array( $email, $list, true ) ) {
		$list[] = $email;
		update_option( 'luxora_newsletter_emails', $list, false );
	}

	do_action( 'luxora_newsletter_subscribed', $email );

	wp_send_json_success( array( 'message' => __( 'Welcome to The List.', 'luxora' ) ) );
}
add_action( 'wp_ajax_luxora_newsletter', 'luxora_ajax_newsletter' );
add_action( 'wp_ajax_nopriv_luxora_newsletter', 'luxora_ajax_newsletter' );

/**
 * AJAX: contact form → email the site admin.
 */
function luxora_ajax_contact() {
	check_ajax_referer( 'luxora_nonce', 'nonce' );

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( ! $name || ! is_email( $email ) || ! $message ) {
		wp_send_json_error( array( 'message' => __( 'Please complete your name, a valid email, and a message.', 'luxora' ) ) );
	}

	$to      = luxora_opt( 'luxora_contact_email' );
	$to      = is_email( $to ) ? $to : get_option( 'admin_email' );
	$subject = $subject ? $subject : __( 'New enquiry via the website', 'luxora' );

	$body  = sprintf( "Name: %s\n", $name );
	$body .= sprintf( "Email: %s\n\n", $email );
	$body .= $message . "\n";

	$headers = array(
		'Reply-To: ' . $name . ' <' . $email . '>',
	);

	$sent = wp_mail( $to, '[' . get_bloginfo( 'name' ) . '] ' . $subject, $body, $headers );

	do_action( 'luxora_contact_submitted', compact( 'name', 'email', 'subject', 'message', 'sent' ) );

	if ( $sent ) {
		wp_send_json_success( array( 'message' => __( 'Thank you — your message is on its way. We will be in touch shortly.', 'luxora' ) ) );
	}
	wp_send_json_error( array( 'message' => __( 'Your message could not be sent right now. Please email us directly.', 'luxora' ) ) );
}
add_action( 'wp_ajax_luxora_contact', 'luxora_ajax_contact' );
add_action( 'wp_ajax_nopriv_luxora_contact', 'luxora_ajax_contact' );

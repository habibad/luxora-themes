<?php
/**
 * My Account navigation — styled sidebar matching the reference design.
 * Override of woocommerce/myaccount/navigation.php
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );

/**
 * Icon + label map for each endpoint.
 * Matches the reference: Profile, Orders, Wishlist, Addresses, Payment, Sign out.
 */
$luxora_nav_map = array(
	'dashboard'       => array( 'icon' => 'user',      'label' => __( 'Profile', 'luxora' ) ),
	'orders'          => array( 'icon' => 'package',   'label' => __( 'Orders', 'luxora' ) ),
	'edit-address'    => array( 'icon' => 'map-pin',   'label' => __( 'Addresses', 'luxora' ) ),
	'edit-account'    => array( 'icon' => 'user',      'label' => __( 'Account details', 'luxora' ) ),
	'payment-methods' => array( 'icon' => 'credit-card', 'label' => __( 'Payment', 'luxora' ) ),
	'customer-logout' => array( 'icon' => 'log-out',   'label' => __( 'Sign out', 'luxora' ) ),
);

// Wishlist is a custom page, add it manually after orders.
$all_items = wc_get_account_menu_items();

?>
<nav class="woocommerce-MyAccount-navigation luxora-account-nav" aria-label="<?php esc_attr_e( 'Account navigation', 'luxora' ); ?>">
	<?php foreach ( $all_items as $endpoint => $default_label ) :
		$is_active = wc_is_current_account_menu_item( $endpoint );
		$is_logout = ( 'customer-logout' === $endpoint );

		// Get our custom label + icon, falling back to WooCommerce default.
		$map   = isset( $luxora_nav_map[ $endpoint ] ) ? $luxora_nav_map[ $endpoint ] : null;
		$icon  = $map ? $map['icon'] : 'user';
		$label = $map ? $map['label'] : $default_label;
		?>
		<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"
		   class="luxora-account-nav-item woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--<?php echo esc_attr( $endpoint ); ?><?php echo $is_active ? ' is-active' : ''; ?><?php echo $is_logout ? ' is-logout' : ''; ?>"
		   <?php echo $is_active ? 'aria-current="page"' : ''; ?>>
			<?php echo luxora_icon( $icon, 'luxora-nav-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<span class="luxora-nav-label"><?php echo esc_html( $label ); ?></span>
		</a>
	<?php endforeach; ?>

	<?php
	// Add Wishlist link if there's a wishlist page.
	$wishlist_url = luxora_page_url_by_title( 'Wishlist', '' );
	if ( $wishlist_url ) :
		$is_wishlist_active = ( trailingslashit( home_url( $_SERVER['REQUEST_URI'] ) ) === trailingslashit( $wishlist_url ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		?>
		<a href="<?php echo esc_url( $wishlist_url ); ?>"
		   class="luxora-account-nav-item<?php echo $is_wishlist_active ? ' is-active' : ''; ?>"
		   <?php echo $is_wishlist_active ? 'aria-current="page"' : ''; ?>>
			<?php echo luxora_icon( 'heart', 'luxora-nav-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<span class="luxora-nav-label"><?php esc_html_e( 'Wishlist', 'luxora' ); ?></span>
		</a>
	<?php endif; ?>
</nav>
<?php
do_action( 'woocommerce_after_account_navigation' );

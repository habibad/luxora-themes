<?php
/**
 * My Account navigation — styled tab rail.
 * Override of woocommerce/myaccount/navigation.php
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );

$luxora_ep_icons = array(
	'dashboard'       => 'user',
	'orders'          => 'package',
	'downloads'       => 'package',
	'edit-address'    => 'map-pin',
	'edit-account'    => 'user',
	'payment-methods' => 'user',
	'customer-logout' => 'arrow-right',
	'wishlist'        => 'heart',
);
?>
<nav class="woocommerce-MyAccount-navigation luxora-account-nav flex lg:flex-col gap-2 overflow-x-auto" aria-label="<?php esc_attr_e( 'Account', 'luxora' ); ?>">
	<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
		<?php
		$is_active = wc_is_current_account_menu_item( $endpoint );
		$icon      = isset( $luxora_ep_icons[ $endpoint ] ) ? $luxora_ep_icons[ $endpoint ] : 'user';
		$classes   = 'flex items-center gap-3 px-4 py-3 text-sm tracking-wide whitespace-nowrap transition ';
		$classes  .= $is_active ? 'bg-cream text-ink' : 'text-muted-foreground hover:text-ink';
		?>
		<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" class="woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--<?php echo esc_attr( $endpoint ); ?> <?php echo esc_attr( $classes ); ?>">
			<?php echo luxora_icon( $icon, 'h-4 w-4' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php echo esc_html( $label ); ?>
		</a>
	<?php endforeach; ?>
</nav>
<?php
do_action( 'woocommerce_after_account_navigation' );

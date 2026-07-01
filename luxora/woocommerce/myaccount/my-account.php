<?php
/**
 * My Account wrapper — mirrors account.tsx.
 * Override of woocommerce/myaccount/my-account.php
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user = wp_get_current_user();
$first        = $current_user->first_name ? $current_user->first_name : $current_user->display_name;
?>
<section class="container-luxe py-20 luxora-account">
	<p class="eyebrow mb-4"><?php esc_html_e( 'Welcome back', 'luxora' ); ?></p>
	<h1 class="font-display text-5xl"><?php echo esc_html( $first ); ?></h1>

	<div class="mt-12 grid lg:grid-cols-[240px_1fr] gap-12">
		<?php do_action( 'woocommerce_account_navigation' ); ?>

		<div class="woocommerce-MyAccount-content luxora-account-content">
			<?php do_action( 'woocommerce_account_content' ); ?>
		</div>
	</div>
</section>

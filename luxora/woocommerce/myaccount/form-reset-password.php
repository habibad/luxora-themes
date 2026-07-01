<?php
/**
 * Custom "set a new password" form.
 * Override of woocommerce/myaccount/form-reset-password.php
 *
 * @package Luxora
 *
 * @var string $args['key']   Reset key.
 * @var string $args['login'] User login.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_reset_password_form' );

$field_input = 'bg-transparent border-b border-ink/30 focus:border-ink outline-none py-3 w-full text-base transition-colors';
?>
<section class="container-luxe py-20 md:py-28 luxora-auth">
	<div class="max-w-md mx-auto" data-reveal>
		<div class="text-center mb-12">
			<p class="eyebrow mb-4"><?php esc_html_e( 'Almost there', 'luxora' ); ?></p>
			<h1 class="font-display text-4xl md:text-5xl"><?php esc_html_e( 'Set a new password', 'luxora' ); ?></h1>
			<p class="mt-4 font-serif text-lg text-muted-foreground"><?php esc_html_e( 'Choose a new password for your account below.', 'luxora' ); ?></p>
		</div>

		<form method="post" class="woocommerce-ResetPassword lost_reset_password flex flex-col gap-8">

			<label class="block">
				<span class="eyebrow block mb-2"><?php esc_html_e( 'New password', 'luxora' ); ?></span>
				<input class="woocommerce-Input woocommerce-Input--text input-text <?php echo esc_attr( $field_input ); ?>" type="password" name="password_1" id="password_1" autocomplete="new-password" required />
			</label>

			<label class="block">
				<span class="eyebrow block mb-2"><?php esc_html_e( 'Confirm new password', 'luxora' ); ?></span>
				<input class="woocommerce-Input woocommerce-Input--text input-text <?php echo esc_attr( $field_input ); ?>" type="password" name="password_2" id="password_2" autocomplete="new-password" required />
			</label>

			<input type="hidden" name="reset_key" value="<?php echo esc_attr( $args['key'] ); ?>" />
			<input type="hidden" name="reset_login" value="<?php echo esc_attr( $args['login'] ); ?>" />

			<?php do_action( 'woocommerce_resetpassword_form' ); ?>

			<div>
				<input type="hidden" name="wc_reset_password" value="true" />
				<button type="submit" class="btn-luxe w-full justify-center" value="<?php esc_attr_e( 'Save password', 'luxora' ); ?>">
					<?php esc_html_e( 'Save password', 'luxora' ); ?> <?php echo luxora_icon( 'check', 'h-4 w-4' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</button>
			</div>

			<?php wp_nonce_field( 'reset_password', 'woocommerce-reset-password-nonce' ); ?>
		</form>
	</div>
</section>
<?php
do_action( 'woocommerce_after_reset_password_form' );

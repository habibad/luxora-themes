<?php
/**
 * Custom "forgot password" form.
 * Override of woocommerce/myaccount/form-lost-password.php
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_lost_password_form' );

$field_input = 'bg-transparent border-b border-ink/30 focus:border-ink outline-none py-3 w-full text-base transition-colors';
?>
<section class="container-luxe py-20 md:py-28 luxora-auth">
	<div class="max-w-md mx-auto" data-reveal>
		<div class="text-center mb-12">
			<p class="eyebrow mb-4"><?php esc_html_e( 'Account recovery', 'luxora' ); ?></p>
			<h1 class="font-display text-4xl md:text-5xl"><?php esc_html_e( 'Reset your password', 'luxora' ); ?></h1>
			<p class="mt-4 font-serif text-lg text-muted-foreground"><?php esc_html_e( 'Enter your email or username and we will send a secure link to set a new password.', 'luxora' ); ?></p>
		</div>

		<form method="post" class="woocommerce-ResetPassword lost_reset_password flex flex-col gap-8">

			<label class="block">
				<span class="eyebrow block mb-2"><?php esc_html_e( 'Email or username', 'luxora' ); ?></span>
				<input class="woocommerce-Input woocommerce-Input--text input-text <?php echo esc_attr( $field_input ); ?>" type="text" name="user_login" id="user_login" autocomplete="username" required />
			</label>

			<?php do_action( 'woocommerce_lostpassword_form' ); ?>

			<div>
				<input type="hidden" name="wc_reset_password" value="true" />
				<button type="submit" class="btn-luxe w-full justify-center" value="<?php esc_attr_e( 'Send reset link', 'luxora' ); ?>">
					<?php esc_html_e( 'Send reset link', 'luxora' ); ?> <?php echo luxora_icon( 'mail', 'h-4 w-4' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</button>
			</div>

			<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="block text-center text-xs uppercase tracking-[0.18em] text-muted-foreground hover:text-ink"><?php esc_html_e( 'Back to sign in', 'luxora' ); ?></a>

			<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>
		</form>
	</div>
</section>
<?php
do_action( 'woocommerce_after_lost_password_form' );

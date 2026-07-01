<?php
/**
 * Custom login + registration — mirrors the Luxora aesthetic.
 * Override of woocommerce/myaccount/form-login.php
 *
 * Shown to logged-out visitors on the My Account page. Preserves all
 * WooCommerce field names, hooks, and nonces so authentication keeps working.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_customer_login_form' );

$registration_enabled = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
$field_input          = 'bg-transparent border-b border-ink/30 focus:border-ink outline-none py-3 w-full text-base transition-colors';
?>
<section class="container-luxe py-20 md:py-28 luxora-auth">
	<div class="max-w-xl <?php echo $registration_enabled ? 'lg:max-w-6xl' : ''; ?> mx-auto">
		<div class="text-center mb-14" data-reveal>
			<p class="eyebrow mb-4"><?php esc_html_e( 'The Luxora account', 'luxora' ); ?></p>
			<h1 class="font-display text-5xl md:text-6xl"><?php esc_html_e( 'Account', 'luxora' ); ?></h1>
			<p class="mt-4 font-serif text-lg text-muted-foreground"><?php esc_html_e( 'Sign in to view orders, saved pieces, and your atelier preferences.', 'luxora' ); ?></p>
		</div>

		<div class="grid <?php echo $registration_enabled ? 'lg:grid-cols-2' : ''; ?> gap-x-20 gap-y-16" id="customer_login">

			<!-- ============================ Sign in ============================ -->
			<div class="luxora-auth-col">
				<h2 class="font-display text-2xl mb-2"><?php esc_html_e( 'Sign in', 'luxora' ); ?></h2>
				<p class="text-sm text-muted-foreground mb-8"><?php esc_html_e( 'Welcome back to the maison.', 'luxora' ); ?></p>

				<form class="woocommerce-form woocommerce-form-login login flex flex-col gap-7" method="post">

					<?php do_action( 'woocommerce_login_form_start' ); ?>

					<label class="block">
						<span class="eyebrow block mb-2"><?php esc_html_e( 'Email or username', 'luxora' ); ?></span>
						<input type="text" class="woocommerce-Input woocommerce-Input--text input-text <?php echo esc_attr( $field_input ); ?>" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; // phpcs:ignore ?>" required />
					</label>

					<label class="block">
						<span class="eyebrow block mb-2"><?php esc_html_e( 'Password', 'luxora' ); ?></span>
						<input class="woocommerce-Input woocommerce-Input--text input-text <?php echo esc_attr( $field_input ); ?>" type="password" name="password" id="password" autocomplete="current-password" required />
					</label>

					<?php do_action( 'woocommerce_login_form' ); ?>

					<div class="flex items-center justify-between text-sm">
						<label class="flex items-center gap-2 text-muted-foreground cursor-pointer select-none">
							<input class="accent-ink" name="rememberme" type="checkbox" id="rememberme" value="forever" />
							<span><?php esc_html_e( 'Remember me', 'luxora' ); ?></span>
						</label>
						<a href="<?php echo esc_url( wc_lostpassword_url() ); ?>" class="link-underline text-xs uppercase tracking-[0.18em]"><?php esc_html_e( 'Forgot password?', 'luxora' ); ?></a>
					</div>

					<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>

					<div>
						<button type="submit" class="btn-luxe w-full justify-center" name="login" value="<?php esc_attr_e( 'Sign in', 'luxora' ); ?>">
							<?php esc_html_e( 'Sign in', 'luxora' ); ?> <?php echo luxora_icon( 'arrow-right', 'h-4 w-4' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</button>
					</div>

					<?php do_action( 'woocommerce_login_form_end' ); ?>
				</form>
			</div>

			<?php if ( $registration_enabled ) : ?>
				<!-- ============================ Register ============================ -->
				<div class="luxora-auth-col lg:border-l lg:border-border lg:pl-20">
					<h2 class="font-display text-2xl mb-2"><?php esc_html_e( 'Create an account', 'luxora' ); ?></h2>
					<p class="text-sm text-muted-foreground mb-8"><?php esc_html_e( 'Join The List for early access and private previews.', 'luxora' ); ?></p>

					<form method="post" class="woocommerce-form woocommerce-form-register register flex flex-col gap-7" <?php do_action( 'woocommerce_register_form_tag' ); ?>>

						<?php do_action( 'woocommerce_register_form_start' ); ?>

						<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
							<label class="block">
								<span class="eyebrow block mb-2"><?php esc_html_e( 'Username', 'luxora' ); ?></span>
								<input type="text" class="woocommerce-Input woocommerce-Input--text input-text <?php echo esc_attr( $field_input ); ?>" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; // phpcs:ignore ?>" />
							</label>
						<?php endif; ?>

						<label class="block">
							<span class="eyebrow block mb-2"><?php esc_html_e( 'Email address', 'luxora' ); ?></span>
							<input type="email" class="woocommerce-Input woocommerce-Input--text input-text <?php echo esc_attr( $field_input ); ?>" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; // phpcs:ignore ?>" required />
						</label>

						<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
							<label class="block">
								<span class="eyebrow block mb-2"><?php esc_html_e( 'Password', 'luxora' ); ?></span>
								<input type="password" class="woocommerce-Input woocommerce-Input--text input-text <?php echo esc_attr( $field_input ); ?>" name="password" id="reg_password" autocomplete="new-password" required />
							</label>
						<?php else : ?>
							<p class="text-sm text-muted-foreground"><?php esc_html_e( 'A secure password will be emailed to you.', 'luxora' ); ?></p>
						<?php endif; ?>

						<?php do_action( 'woocommerce_register_form' ); ?>

						<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>

						<div>
							<button type="submit" class="btn-luxe w-full justify-center" name="register" value="<?php esc_attr_e( 'Create account', 'luxora' ); ?>">
								<?php esc_html_e( 'Create account', 'luxora' ); ?> <?php echo luxora_icon( 'arrow-right', 'h-4 w-4' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</button>
						</div>

						<?php do_action( 'woocommerce_register_form_end' ); ?>
					</form>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php
do_action( 'woocommerce_after_customer_login_form' );

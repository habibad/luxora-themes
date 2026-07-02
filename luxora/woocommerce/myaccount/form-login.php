<?php
/**
 * Custom login + registration — fully custom tab UI.
 * Override of woocommerce/myaccount/form-login.php
 *
 * Shown to logged-out visitors on the My Account page.
 * Preserves all WooCommerce field names, nonces, and hooks so
 * WooCommerce authentication keeps working natively.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_customer_login_form' );

$registration_enabled = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
?>
<section class="luxora-auth-section">
	<div class="luxora-auth-hero">
		<p class="eyebrow"><?php esc_html_e( 'The Luxora Atelier', 'luxora' ); ?></p>
		<h1 class="luxora-auth-title"><?php esc_html_e( 'My Account', 'luxora' ); ?></h1>
		<p class="luxora-auth-subtitle"><?php esc_html_e( 'Sign in to view orders, saved pieces, and your atelier preferences.', 'luxora' ); ?></p>
	</div>

	<div class="luxora-auth-card" id="customer_login">

		<?php if ( $registration_enabled ) : ?>
		<!-- Tab switcher -->
		<div class="luxora-auth-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Account options', 'luxora' ); ?>">
			<button type="button"
				class="luxora-auth-tab is-active"
				id="tab-login"
				role="tab"
				aria-selected="true"
				aria-controls="panel-login">
				<?php esc_html_e( 'Sign in', 'luxora' ); ?>
			</button>
			<button type="button"
				class="luxora-auth-tab"
				id="tab-register"
				role="tab"
				aria-selected="false"
				aria-controls="panel-register">
				<?php esc_html_e( 'Create account', 'luxora' ); ?>
			</button>
			<span class="luxora-auth-tab-indicator" aria-hidden="true"></span>
		</div>
		<?php else : ?>
		<h2 class="luxora-auth-heading"><?php esc_html_e( 'Sign in', 'luxora' ); ?></h2>
		<?php endif; ?>

		<!-- ============================================================
		     LOGIN PANEL
		     ============================================================ -->
		<div class="luxora-auth-panel is-active"
			id="panel-login"
			role="tabpanel"
			aria-labelledby="tab-login">

			<?php if ( ! $registration_enabled ) : ?>
			<p class="luxora-auth-panel-desc"><?php esc_html_e( 'Welcome back to the maison.', 'luxora' ); ?></p>
			<?php endif; ?>

			<form class="woocommerce-form woocommerce-form-login login luxora-auth-form" method="post">
				<?php do_action( 'woocommerce_login_form_start' ); ?>

				<div class="luxora-field">
					<label class="luxora-field-label" for="username">
						<?php esc_html_e( 'Email or username', 'luxora' ); ?>
					</label>
					<input
						type="text"
						class="luxora-field-input woocommerce-Input woocommerce-Input--text input-text"
						name="username"
						id="username"
						autocomplete="username"
						value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; // phpcs:ignore ?>"
						required
					/>
				</div>

				<div class="luxora-field">
					<label class="luxora-field-label" for="password">
						<?php esc_html_e( 'Password', 'luxora' ); ?>
					</label>
					<input
						class="luxora-field-input woocommerce-Input woocommerce-Input--text input-text"
						type="password"
						name="password"
						id="password"
						autocomplete="current-password"
						required
					/>
				</div>

				<?php do_action( 'woocommerce_login_form' ); ?>

				<div class="luxora-field-row">
					<label class="luxora-remember">
						<input class="accent-ink" name="rememberme" type="checkbox" id="rememberme" value="forever" />
						<span><?php esc_html_e( 'Remember me', 'luxora' ); ?></span>
					</label>
					<a href="<?php echo esc_url( wc_lostpassword_url() ); ?>" class="luxora-forgot">
						<?php esc_html_e( 'Forgot password?', 'luxora' ); ?>
					</a>
				</div>

				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>

				<button type="submit" class="luxora-auth-submit" name="login" value="<?php esc_attr_e( 'Sign in', 'luxora' ); ?>">
					<?php esc_html_e( 'Sign in', 'luxora' ); ?>
					<?php echo luxora_icon( 'arrow-right', 'h-4 w-4' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</button>

				<?php do_action( 'woocommerce_login_form_end' ); ?>
			</form>
		</div><!-- /panel-login -->

		<?php if ( $registration_enabled ) : ?>
		<!-- ============================================================
		     REGISTER PANEL
		     ============================================================ -->
		<div class="luxora-auth-panel"
			id="panel-register"
			role="tabpanel"
			aria-labelledby="tab-register"
			hidden>

			<form method="post" class="woocommerce-form woocommerce-form-register register luxora-auth-form" <?php do_action( 'woocommerce_register_form_tag' ); ?>>
				<?php do_action( 'woocommerce_register_form_start' ); ?>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
				<div class="luxora-field">
					<label class="luxora-field-label" for="reg_username">
						<?php esc_html_e( 'Username', 'luxora' ); ?>
					</label>
					<input
						type="text"
						class="luxora-field-input woocommerce-Input woocommerce-Input--text input-text"
						name="username"
						id="reg_username"
						autocomplete="username"
						value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; // phpcs:ignore ?>"
					/>
				</div>
				<?php endif; ?>

				<div class="luxora-field">
					<label class="luxora-field-label" for="reg_email">
						<?php esc_html_e( 'Email address', 'luxora' ); ?>
					</label>
					<input
						type="email"
						class="luxora-field-input woocommerce-Input woocommerce-Input--text input-text"
						name="email"
						id="reg_email"
						autocomplete="email"
						value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; // phpcs:ignore ?>"
						required
					/>
				</div>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
				<div class="luxora-field">
					<label class="luxora-field-label" for="reg_password">
						<?php esc_html_e( 'Password', 'luxora' ); ?>
					</label>
					<input
						type="password"
						class="luxora-field-input woocommerce-Input woocommerce-Input--text input-text"
						name="password"
						id="reg_password"
						autocomplete="new-password"
						required
					/>
				</div>
				<?php else : ?>
				<p class="luxora-auth-note"><?php esc_html_e( 'A secure password will be emailed to you.', 'luxora' ); ?></p>
				<?php endif; ?>

				<?php do_action( 'woocommerce_register_form' ); ?>

				<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>

				<button type="submit" class="luxora-auth-submit" name="register" value="<?php esc_attr_e( 'Create account', 'luxora' ); ?>">
					<?php esc_html_e( 'Create account', 'luxora' ); ?>
					<?php echo luxora_icon( 'arrow-right', 'h-4 w-4' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</button>

				<?php do_action( 'woocommerce_register_form_end' ); ?>
			</form>
		</div><!-- /panel-register -->
		<?php endif; ?>

	</div><!-- /.luxora-auth-card -->

	<!-- Decorative divider -->
	<div class="luxora-auth-divider" aria-hidden="true"></div>
</section>
<?php
do_action( 'woocommerce_after_customer_login_form' );

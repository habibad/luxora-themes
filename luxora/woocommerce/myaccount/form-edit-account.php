<?php
/**
 * Custom profile editor — mirrors the account.tsx ProfilePanel.
 * Override of woocommerce/myaccount/form-edit-account.php
 *
 * Rendered inside my-account.php (logged-in content column).
 *
 * @package Luxora
 *
 * @var WP_User $user
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user        = wp_get_current_user();
$field_input = 'bg-transparent border-b border-ink/30 focus:border-ink outline-none py-3 w-full text-base transition-colors';
?>
<div class="luxora-edit-account">
	<h2 class="font-display text-3xl mb-8"><?php esc_html_e( 'Profile', 'luxora' ); ?></h2>

	<form class="woocommerce-EditAccountForm edit-account max-w-2xl" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?>>

		<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

		<div class="grid md:grid-cols-2 gap-6">
			<label class="block">
				<span class="eyebrow block mb-2"><?php esc_html_e( 'First name', 'luxora' ); ?></span>
				<input type="text" class="woocommerce-Input input-text <?php echo esc_attr( $field_input ); ?>" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" />
			</label>

			<label class="block">
				<span class="eyebrow block mb-2"><?php esc_html_e( 'Last name', 'luxora' ); ?></span>
				<input type="text" class="woocommerce-Input input-text <?php echo esc_attr( $field_input ); ?>" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" />
			</label>

			<label class="block md:col-span-2">
				<span class="eyebrow block mb-2"><?php esc_html_e( 'Display name', 'luxora' ); ?></span>
				<input type="text" class="woocommerce-Input input-text <?php echo esc_attr( $field_input ); ?>" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" />
				<span class="block text-xs text-muted-foreground mt-2"><?php esc_html_e( 'This is how your name appears in the account area.', 'luxora' ); ?></span>
			</label>

			<label class="block md:col-span-2">
				<span class="eyebrow block mb-2"><?php esc_html_e( 'Email address', 'luxora' ); ?></span>
				<input type="email" class="woocommerce-Input input-text <?php echo esc_attr( $field_input ); ?>" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" />
			</label>
		</div>

		<fieldset class="mt-12 border-t border-border pt-10">
			<legend class="font-display text-2xl mb-6 px-0"><?php esc_html_e( 'Password change', 'luxora' ); ?></legend>

			<div class="grid md:grid-cols-2 gap-6">
				<label class="block md:col-span-2">
					<span class="eyebrow block mb-2"><?php esc_html_e( 'Current password (leave blank to keep)', 'luxora' ); ?></span>
					<input type="password" class="woocommerce-Input input-text <?php echo esc_attr( $field_input ); ?>" name="password_current" id="password_current" autocomplete="off" />
				</label>

				<label class="block">
					<span class="eyebrow block mb-2"><?php esc_html_e( 'New password', 'luxora' ); ?></span>
					<input type="password" class="woocommerce-Input input-text <?php echo esc_attr( $field_input ); ?>" name="password_1" id="password_1" autocomplete="off" />
				</label>

				<label class="block">
					<span class="eyebrow block mb-2"><?php esc_html_e( 'Confirm new password', 'luxora' ); ?></span>
					<input type="password" class="woocommerce-Input input-text <?php echo esc_attr( $field_input ); ?>" name="password_2" id="password_2" autocomplete="off" />
				</label>
			</div>
		</fieldset>

		<?php do_action( 'woocommerce_edit_account_form' ); ?>

		<div class="mt-10">
			<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
			<button type="submit" class="btn-luxe" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'luxora' ); ?>"><?php esc_html_e( 'Save changes', 'luxora' ); ?></button>
			<input type="hidden" name="action" value="save_account_details" />
		</div>

		<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
	</form>
</div>

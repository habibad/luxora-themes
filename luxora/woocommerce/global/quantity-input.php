<?php
/**
 * Quantity input — Luxora stepper styling for WooCommerce contexts.
 * Override of woocommerce/global/quantity-input.php
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* phpcs:disable WordPress.WhiteSpace.PrecisionAlignment.Found */
?>
<div class="quantity luxora-qty inline-flex items-center border border-ink" data-qty>
	<?php if ( $max_value && $min_value === $max_value ) : ?>
		<input
			type="hidden"
			id="<?php echo esc_attr( $input_id ); ?>"
			class="qty luxora-qty-input"
			name="<?php echo esc_attr( $input_name ); ?>"
			value="<?php echo esc_attr( $min_value ); ?>"
		/>
		<span class="w-10 text-center text-sm font-medium py-3"><?php echo esc_html( $min_value ); ?></span>
	<?php else : ?>
		<button type="button" class="h-11 w-11 grid place-items-center hover:bg-ink hover:text-cream transition" data-qty-minus tabindex="-1" aria-label="<?php esc_attr_e( 'Decrease quantity', 'luxora' ); ?>">
			<?php echo luxora_icon( 'minus', 'h-3.5 w-3.5' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</button>
		<label class="sr-only" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $label ); ?></label>
		<input
			type="number"
			id="<?php echo esc_attr( $input_id ); ?>"
			class="input-text qty text luxora-qty-input w-12 text-center text-sm font-medium bg-transparent outline-none"
			step="<?php echo esc_attr( $step ); ?>"
			min="<?php echo esc_attr( $min_value ); ?>"
			max="<?php echo esc_attr( 0 < $max_value ? $max_value : '' ); ?>"
			name="<?php echo esc_attr( $input_name ); ?>"
			value="<?php echo esc_attr( $input_value ); ?>"
			title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'luxora' ); ?>"
			inputmode="<?php echo esc_attr( $inputmode ); ?>"
			autocomplete="off"
			data-qty-input
		/>
		<button type="button" class="h-11 w-11 grid place-items-center hover:bg-ink hover:text-cream transition" data-qty-plus tabindex="-1" aria-label="<?php esc_attr_e( 'Increase quantity', 'luxora' ); ?>">
			<?php echo luxora_icon( 'plus', 'h-3.5 w-3.5' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</button>
	<?php endif; ?>
</div>

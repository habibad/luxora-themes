<?php
/**
 * Shop filter sidebar — mirrors shop.tsx FilterGroups, wired to WooCommerce query args.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shop_url = luxora_shop_url();

// Current selections.
$active_cats  = isset( $_GET['product_cat'] ) ? array_map( 'sanitize_title', (array) wp_unslash( $_GET['product_cat'] ) ) : array(); // phpcs:ignore WordPress.Security.NonceVerification
$active_min   = isset( $_GET['min_price'] ) ? absint( wp_unslash( $_GET['min_price'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$active_max   = isset( $_GET['max_price'] ) ? absint( wp_unslash( $_GET['max_price'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$active_color = isset( $_GET['filter_color'] ) ? sanitize_title( wp_unslash( $_GET['filter_color'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

$cats = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'exclude'    => array( get_option( 'default_product_cat' ) ),
	)
);

$price_bands = array(
	array(
		'label' => __( 'Under ৳5,000', 'luxora' ),
		'min'   => 0,
		'max'   => 5000,
	),
	array(
		'label' => __( '৳5,000 – ৳10,000', 'luxora' ),
		'min'   => 5000,
		'max'   => 10000,
	),
	array(
		'label' => __( '৳10,000 – ৳20,000', 'luxora' ),
		'min'   => 10000,
		'max'   => 20000,
	),
	array(
		'label' => __( '৳20,000+', 'luxora' ),
		'min'   => 20000,
		'max'   => '',
	),
);

// Colors: prefer pa_color attribute terms, else the source default swatches.
$color_terms = array();
if ( taxonomy_exists( 'pa_color' ) ) {
	$ct = get_terms( array( 'taxonomy' => 'pa_color', 'hide_empty' => true ) );
	if ( ! is_wp_error( $ct ) ) {
		foreach ( $ct as $t ) {
			$color_terms[ $t->slug ] = array(
				'name' => $t->name,
				'hex'  => luxora_color_hex( $t->name ),
			);
		}
	}
}
if ( empty( $color_terms ) ) {
	foreach ( array( '#111111', '#C8A96A', '#FAF8F5', '#5B1A20', '#9A5B2A', '#E8C7C0' ) as $hex ) {
		$color_terms[ sanitize_title( $hex ) ] = array(
			'name' => $hex,
			'hex'  => $hex,
		);
	}
}
?>
<aside class="luxora-filters hidden lg:block" data-filter-panel>
	<form method="get" action="<?php echo esc_url( $shop_url ); ?>" class="sticky top-32 space-y-10">

		<div class="lg:hidden flex items-center justify-between">
			<h2 class="font-display text-2xl"><?php esc_html_e( 'Filter', 'luxora' ); ?></h2>
			<button type="button" class="p-2" data-filter-close aria-label="<?php esc_attr_e( 'Close filters', 'luxora' ); ?>">
				<?php echo luxora_icon( 'x', 'h-5 w-5' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</button>
		</div>

		<?php if ( ! is_wp_error( $cats ) && $cats ) : ?>
			<div>
				<h3 class="eyebrow mb-5"><?php esc_html_e( 'Category', 'luxora' ); ?></h3>
				<div class="flex flex-col gap-3">
					<?php foreach ( $cats as $cat ) : ?>
						<label class="flex items-center gap-3 text-sm cursor-pointer hover:text-ink">
							<input type="checkbox" name="product_cat[]" value="<?php echo esc_attr( $cat->slug ); ?>" class="accent-ink" <?php checked( in_array( $cat->slug, $active_cats, true ) ); ?> />
							<span><?php echo esc_html( $cat->name ); ?></span>
							<span class="ml-auto text-xs text-muted-foreground"><?php echo absint( $cat->count ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<div>
			<h3 class="eyebrow mb-5"><?php esc_html_e( 'Price', 'luxora' ); ?></h3>
			<div class="flex flex-col gap-3">
				<?php foreach ( $price_bands as $band ) : ?>
					<?php $checked = ( (string) $active_min === (string) $band['min'] && (string) $active_max === (string) $band['max'] ); ?>
					<label class="flex items-center gap-3 text-sm cursor-pointer">
						<input type="radio" name="price_band" value="<?php echo esc_attr( $band['min'] . '-' . $band['max'] ); ?>" class="accent-ink luxora-price-band" data-min="<?php echo esc_attr( $band['min'] ); ?>" data-max="<?php echo esc_attr( $band['max'] ); ?>" <?php checked( $checked ); ?> />
						<?php echo esc_html( $band['label'] ); ?>
					</label>
				<?php endforeach; ?>
			</div>
			<input type="hidden" name="min_price" value="<?php echo esc_attr( $active_min ); ?>" data-price-min />
			<input type="hidden" name="max_price" value="<?php echo esc_attr( $active_max ); ?>" data-price-max />
		</div>

		<div>
			<h3 class="eyebrow mb-5"><?php esc_html_e( 'Color', 'luxora' ); ?></h3>
			<div class="flex flex-wrap gap-2">
				<?php foreach ( $color_terms as $slug => $color ) : ?>
					<label class="cursor-pointer" title="<?php echo esc_attr( $color['name'] ); ?>">
						<input type="radio" name="filter_color" value="<?php echo esc_attr( $slug ); ?>" class="sr-only peer" <?php checked( $active_color, $slug ); ?> />
						<span class="block h-7 w-7 rounded-full ring-1 ring-border hover:ring-gold peer-checked:ring-2 peer-checked:ring-gold transition" style="background: <?php echo esc_attr( $color['hex'] ); ?>"></span>
					</label>
				<?php endforeach; ?>
			</div>
		</div>

		<?php if ( is_product_category() ) : ?>
			<?php $qo = get_queried_object(); ?>
			<input type="hidden" name="in_cat" value="<?php echo esc_attr( $qo->slug ); ?>" />
		<?php endif; ?>

		<div class="flex flex-col gap-3 pt-2">
			<button type="submit" class="btn-luxe justify-center"><?php esc_html_e( 'Apply filters', 'luxora' ); ?></button>
			<a href="<?php echo esc_url( $shop_url ); ?>" class="link-underline text-xs uppercase tracking-[0.18em] text-muted-foreground"><?php esc_html_e( 'Clear all', 'luxora' ); ?></a>
		</div>
	</form>
</aside>

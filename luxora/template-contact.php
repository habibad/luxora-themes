<?php
/**
 * Template Name: Contact
 *
 * Mirrors contact.tsx — atelier contact details + message form.
 *
 * @package Luxora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$c_address = luxora_opt( 'luxora_contact_address' );
$c_address = $c_address ? $c_address : "House 27, Road 113, Gulshan 2\nDhaka 1212";
$c_phone   = luxora_opt( 'luxora_contact_phone' );
$c_phone   = $c_phone ? $c_phone : '+880 1700-000 000';
$c_email   = luxora_opt( 'luxora_contact_email' );
$c_email   = $c_email ? $c_email : get_option( 'admin_email' );

$field = 'w-full bg-transparent border-b border-ink/30 focus:border-ink outline-none py-3 transition-colors';

$contact_info = array(
	array( 'map-pin', __( 'Atelier', 'luxora' ), nl2br( esc_html( $c_address ) ) ),
	array( 'phone', __( 'Phone', 'luxora' ), esc_html( $c_phone ) ),
	array( 'mail', __( 'Email', 'luxora' ), esc_html( $c_email ) ),
);
?>
<section class="container-luxe py-24 md:py-32 grid lg:grid-cols-2 gap-16 luxora-contact">
	<div data-reveal>
		<p class="eyebrow mb-6"><?php esc_html_e( 'In touch', 'luxora' ); ?></p>
		<h1 class="font-display text-5xl md:text-6xl leading-[1.05]"><?php esc_html_e( 'Speak with our atelier.', 'luxora' ); ?></h1>
		<p class="mt-6 font-serif text-lg text-muted-foreground">
			<?php esc_html_e( 'Whether you\'re considering a piece, ordering a gift, or arranging a private appointment — we are here.', 'luxora' ); ?>
		</p>

		<div class="mt-12 space-y-6 text-sm">
			<?php foreach ( $contact_info as $info ) : ?>
				<div class="flex gap-4">
					<span class="text-gold mt-0.5"><?php echo luxora_icon( $info[0], 'h-5 w-5' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<div>
						<p class="eyebrow mb-1"><?php echo esc_html( $info[1] ); ?></p>
						<p class="whitespace-pre-line"><?php echo wp_kses( $info[2], array( 'br' => array() ) ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<form class="bg-cream p-8 md:p-12 space-y-6 luxora-contact-form" data-reveal>
		<h2 class="font-display text-3xl"><?php esc_html_e( 'Write to us', 'luxora' ); ?></h2>

		<label class="block">
			<span class="eyebrow block mb-2"><?php esc_html_e( 'Name', 'luxora' ); ?></span>
			<input type="text" name="name" class="<?php echo esc_attr( $field ); ?>" required />
		</label>
		<label class="block">
			<span class="eyebrow block mb-2"><?php esc_html_e( 'Email', 'luxora' ); ?></span>
			<input type="email" name="email" class="<?php echo esc_attr( $field ); ?>" required />
		</label>
		<label class="block">
			<span class="eyebrow block mb-2"><?php esc_html_e( 'Subject', 'luxora' ); ?></span>
			<input type="text" name="subject" class="<?php echo esc_attr( $field ); ?>" />
		</label>
		<label class="block">
			<span class="eyebrow block mb-2"><?php esc_html_e( 'Message', 'luxora' ); ?></span>
			<textarea rows="4" name="message" class="<?php echo esc_attr( $field ); ?> resize-none" required></textarea>
		</label>

		<button type="submit" class="btn-luxe w-full justify-center"><?php esc_html_e( 'Send message', 'luxora' ); ?></button>
		<p class="luxora-contact-msg text-sm text-muted-foreground" role="status" aria-live="polite"></p>
	</form>
</section>

<?php
get_footer();

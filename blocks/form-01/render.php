<?php
/**
 * Server-side rendering for `zenyx/form-01`.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$breadcrumb_home     = isset( $attributes['breadcrumbHome'] ) ? (string) $attributes['breadcrumbHome'] : '';
$breadcrumb_home_url   = isset( $attributes['breadcrumbHomeUrl'] ) ? trim( (string) $attributes['breadcrumbHomeUrl'] ) : '';
$breadcrumb_home_url   = '' !== $breadcrumb_home_url ? esc_url( $breadcrumb_home_url ) : esc_url( home_url( '/' ) );
$breadcrumb_current = isset( $attributes['breadcrumbCurrent'] ) ? (string) $attributes['breadcrumbCurrent'] : '';
$hero_title         = isset( $attributes['heroTitle'] ) ? (string) $attributes['heroTitle'] : '';
$hero_description   = isset( $attributes['heroDescription'] ) ? (string) $attributes['heroDescription'] : '';
$cf7_shortcode      = isset( $attributes['cf7Shortcode'] ) ? trim( (string) $attributes['cf7Shortcode'] ) : '';
$bottom_title       = isset( $attributes['bottomTitle'] ) ? (string) $attributes['bottomTitle'] : '';

$schedule_label = isset( $attributes['scheduleLabel'] ) ? (string) $attributes['scheduleLabel'] : '';
$schedule_text  = isset( $attributes['scheduleText'] ) ? (string) $attributes['scheduleText'] : '';
$location_label = isset( $attributes['locationLabel'] ) ? (string) $attributes['locationLabel'] : '';
$location_text  = isset( $attributes['locationText'] ) ? (string) $attributes['locationText'] : '';
$phone_label    = isset( $attributes['phoneLabel'] ) ? (string) $attributes['phoneLabel'] : '';
$phone_text     = isset( $attributes['phoneText'] ) ? (string) $attributes['phoneText'] : '';
$email_label    = isset( $attributes['emailLabel'] ) ? (string) $attributes['emailLabel'] : '';
$email_text     = isset( $attributes['emailText'] ) ? (string) $attributes['emailText'] : '';

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'mwm-form-01 w-full bg-protagonista',
	)
);

$has_cf7_shortcode = '' !== $cf7_shortcode && ( false !== strpos( $cf7_shortcode, '[contact-form-7' ) || false !== strpos( $cf7_shortcode, '[contact-form' ) );

$contact_items = array(
	array(
		'label' => $schedule_label,
		'text'  => $schedule_text,
	),
	array(
		'label' => $location_label,
		'text'  => $location_text,
	),
	array(
		'type'  => 'phone',
		'label' => $phone_label,
		'text'  => $phone_text,
	),
	array(
		'type'  => 'email',
		'label' => $email_label,
		'text'  => $email_text,
	),
);

/**
 * @param string $text Raw contact text.
 * @return string Sanitized email or empty.
 */
$mwm_form_01_email_from_text = static function ( $text ) {
	$text = trim( wp_strip_all_tags( (string) $text ) );
	if ( '' === $text ) {
		return '';
	}
	$email = sanitize_email( $text );
	return is_email( $email ) ? $email : '';
};

/**
 * @param string $text Raw contact text.
 * @return string Digits for tel: href or empty.
 */
$mwm_form_01_tel_href_from_text = static function ( $text ) {
	$text = trim( wp_strip_all_tags( (string) $text ) );
	if ( '' === $text ) {
		return '';
	}
	$tel = preg_replace( '/\s+/', '', $text );
	if ( ! preg_match( '/\d{3,}/', $tel ) ) {
		return '';
	}
	return $tel;
};
?>

<section data-light <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> style="padding-top: var(--header-height);">
	<div class="mwm-form-01__top pt-[12px] pb-16 md:pb-20 lg:pb-[120px]">
		<div class="mwm-max-1">
			<div class="mwm-form-01__breadcrumbs flex pb-6 md:pb-10 lg:pb-[80px] items-center gap-3 text-sm text-neutral-light">
				<?php if ( '' !== trim( $breadcrumb_home ) ) : ?>
					<a class="mwm-form-01__breadcrumb-link text-neutral-light no-underline transition-colors hover:text-acento" href="<?php echo esc_url( $breadcrumb_home_url ); ?>"><?php echo esc_html( $breadcrumb_home ); ?></a>
				<?php endif; ?>
				<?php if ( '' !== trim( $breadcrumb_current ) ) : ?>
					<span><?php echo esc_html( $breadcrumb_current ); ?></span>
				<?php endif; ?>
			</div>
			<div class="mwm-form-01__grid grid grid-cols-1 items-end gap-10 lg:grid-cols-2 lg:gap-6">
				<div class="mwm-form-01__intro flex flex-col gap-8 lg:gap-10">
					

					<div class="mwm-form-01__copy flex flex-col gap-4">
						<?php if ( '' !== trim( $hero_title ) ) : ?>
							<h2 class="mwm-form-01__title font-heading text-display-l leading-[1.1] uppercase text-white">
								<?php echo nl2br( esc_html( $hero_title ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</h2>
						<?php endif; ?>

						<?php if ( '' !== trim( $hero_description ) ) : ?>
							<p class="mwm-form-01__description max-w-[620px] text-body-l leading-[1.45] text-neutral-light">
								<?php echo esc_html( $hero_description ); ?>
							</p>
						<?php endif; ?>
					</div>
				</div>

				<div class="mwm-form-01__form-shell rounded-none border bg-linear-to-b from-white/20 to-white/5 p-5 backdrop-blur-[20px] md:p-7 lg:p-9">
					<div class="mwm-form-01__form-inner flex flex-col gap-6">
						<div class="mwm-form-01__cf7 text-white">
							<?php if ( $has_cf7_shortcode ) : ?>
								<?php echo do_shortcode( $cf7_shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CF7 markup from trusted shortcode. ?>
							<?php else : ?>
								<div class="mwm-form-01__cf7-fallback border border-dashed border-white/35 p-4 text-sm leading-[1.45] text-neutral-light">
									<?php echo esc_html__( 'Configura el campo "Shortcode CF7" en el bloque para mostrar el formulario de Contact Form 7.', 'zenyx' ); ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="mwm-form-01__bottom py-16 md:py-20 lg:pt-[120px] lg:pb-[200px]">
		<div class="mwm-max-1">
			<div class="mwm-form-01__bottom-inner flex flex-col gap-10 lg:gap-16">
				<?php if ( '' !== trim( $bottom_title ) ) : ?>
					<h3 class="mwm-form-01__bottom-title font-heading text-display-s leading-[1.2] text-white">
						<?php echo esc_html( $bottom_title ); ?>
					</h3>
				<?php endif; ?>

				<div class="mwm-form-01__contact-grid grid grid-cols-1 gap-x-6 gap-y-10 md:grid-cols-2">
					<?php foreach ( $contact_items as $item ) : ?>
						<?php
						$item_label = isset( $item['label'] ) ? (string) $item['label'] : '';
						$item_text  = isset( $item['text'] ) ? (string) $item['text'] : '';
						$item_type  = isset( $item['type'] ) ? (string) $item['type'] : '';
						$item_email = ( 'email' === $item_type ) ? $mwm_form_01_email_from_text( $item_text ) : '';
						$item_phone = ( 'phone' === $item_type ) ? $mwm_form_01_tel_href_from_text( $item_text ) : '';
						?>
						<div class="mwm-form-01__contact-item grid grid-cols-1 items-start gap-5 md:grid-cols-2 md:gap-6">
							<div class="mwm-form-01__meta flex items-center gap-4">
								<?php if ( '' !== trim( $item_label ) ) : ?>
									<p class="mwm-form-01__meta-label shrink-0 text-base text-acento"><?php echo esc_html( $item_label ); ?></p>
								<?php endif; ?>
								<span class="mwm-form-01__meta-line h-px w-full bg-neutral-light"></span>
							</div>
							<div class="mwm-form-01__meta-content text-subheading leading-[1.35] text-neutral-light">
								<?php if ( '' !== $item_email ) : ?>
									<a
										href="<?php echo esc_url( 'mailto:' . $item_email ); ?>"
										class="text-inherit no-underline transition-opacity hover:opacity-70"
									><?php echo esc_html( $item_text ); ?></a>
								<?php elseif ( '' !== $item_phone ) : ?>
									<a
										href="<?php echo esc_url( 'tel:' . $item_phone ); ?>"
										class="text-inherit no-underline hover:underline"
									><?php echo esc_html( $item_text ); ?></a>
								<?php else : ?>
									<?php echo nl2br( esc_html( $item_text ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>

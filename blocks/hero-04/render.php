<?php
/**
 * Server-side rendering for `zenyx/hero-04`.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading                = isset( $attributes['heading'] ) ? (string) $attributes['heading'] : '';
$lead                   = isset( $attributes['lead'] ) ? (string) $attributes['lead'] : '';
$supporting_text        = isset( $attributes['supportingText'] ) ? (string) $attributes['supportingText'] : '';
$button_text            = isset( $attributes['buttonText'] ) ? (string) $attributes['buttonText'] : '';
$button_url             = isset( $attributes['buttonUrl'] ) ? (string) $attributes['buttonUrl'] : '';
$opens_in_new_tab       = ! empty( $attributes['opensInNewTab'] );
$image_id               = isset( $attributes['imageId'] ) ? (int) $attributes['imageId'] : 0;
$image_url              = isset( $attributes['imageUrl'] ) ? (string) $attributes['imageUrl'] : '';
$image_alt              = isset( $attributes['imageAlt'] ) ? (string) $attributes['imageAlt'] : '';
$show_breadcrumbs       = ! isset( $attributes['showBreadcrumbs'] ) || ! empty( $attributes['showBreadcrumbs'] );
$breadcrumbs_archive    = isset( $attributes['breadcrumbsArchiveLabel'] ) ? trim( (string) $attributes['breadcrumbsArchiveLabel'] ) : '';
$breadcrumbs_archive    = '' !== $breadcrumbs_archive ? $breadcrumbs_archive : __( 'Casos de exito', 'zenyx' );

if ( '' === $image_url && $image_id > 0 ) {
	$image_url = (string) wp_get_attachment_image_url( $image_id, 'full' );
}

if ( '' === $image_alt && $image_id > 0 ) {
	$image_alt = (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true );
}

if ( '' === $image_url && is_singular() ) {
	$fallback_image_id = (int) get_post_thumbnail_id( get_the_ID() );
	if ( $fallback_image_id > 0 ) {
		$image_url = (string) wp_get_attachment_image_url( $fallback_image_id, 'full' );
		if ( '' === $image_alt ) {
			$image_alt = (string) get_post_meta( $fallback_image_id, '_wp_attachment_image_alt', true );
		}
	}
}

$home_url       = home_url( '/' );
$archive_url    = get_post_type_archive_link( 'caso_exito' );
$archive_url    = $archive_url ? $archive_url : '';
$current_label  = '';

if ( is_singular( 'caso_exito' ) ) {
	$current_label = get_the_title();
}

if ( '' === trim( (string) $current_label ) ) {
	$current_label = wp_strip_all_tags( $heading );
}

$decor_id = wp_unique_id( 'mwm-hero-04-decor-' );

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'mwm-hero-04 relative w-full overflow-hidden bg-neutral-light',
	)
);
?>

<section data-dark <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> style="padding-top: calc(var(--header-height, 68px));">
	<div class="mwm-max-1">
		<div class="mwm-hero-04__shell relative isolate flex min-h-[620px] w-full flex-col gap-8 pb-[35px] pt-3 lg:min-h-[768px]">
			<div class="mwm-hero-04__bg-decor pointer-events-none absolute left-0 bottom-0 z-0" aria-hidden="true">
				<svg class="mwm-hero-04__bg-decor-svg" width="718" height="383" viewBox="0 0 718 383" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
					<g opacity="1">
						<path d="M717.674 0V152.686L487.013 383.331H229.297L459.977 152.686H0.467773L0 0H717.674Z" fill="url(#<?php echo esc_attr( $decor_id ); ?>)" style="mix-blend-mode:plus-darker"></path>
					</g>
					<defs>
						<linearGradient id="<?php echo esc_attr( $decor_id ); ?>" x1="717.674" y1="139.666" x2="0" y2="139.666" gradientUnits="userSpaceOnUse">
							<stop stop-color="#083B51"></stop>
							<stop offset="1" stop-color="#083B51" stop-opacity="0"></stop>
						</linearGradient>
					</defs>
				</svg>
			</div>
			<?php if ( $show_breadcrumbs ) : ?>
				<nav class="mwm-hero-04__breadcrumbs relative z-10 flex flex-wrap items-center gap-3" aria-label="<?php esc_attr_e( 'Migas de pan', 'zenyx' ); ?>">
					<a class="mwm-hero-04__breadcrumb-link no-underline hover:underline" href="<?php echo esc_url( $home_url ); ?>">
						<?php esc_html_e( 'Home', 'zenyx' ); ?>
					</a>
					<?php if ( '' !== $archive_url ) : ?>
						<a class="mwm-hero-04__breadcrumb-link no-underline hover:underline" href="<?php echo esc_url( $archive_url ); ?>">
							<?php echo esc_html( $breadcrumbs_archive ); ?>
						</a>
					<?php else : ?>
						<span class="mwm-hero-04__breadcrumb-current"><?php echo esc_html( $breadcrumbs_archive ); ?></span>
					<?php endif; ?>
					<span class="mwm-hero-04__breadcrumb-current"><?php echo esc_html( $current_label ); ?></span>
				</nav>
			<?php endif; ?>

			<div class="mwm-hero-04__content relative z-10 grid min-h-0 flex-1 grid-cols-1 gap-6 lg:grid-cols-2 lg:gap-6">
				<div class="mwm-hero-04__left flex min-h-0 flex-1 flex-col justify-between gap-8 lg:gap-6">
					<div class="mwm-hero-04__copy flex flex-col gap-6">
						<?php if ( '' !== trim( wp_strip_all_tags( $heading ) ) ) : ?>
							<h1 class="mwm-hero-04__title max-w-[636px] text-[2rem] font-heading leading-[1.2] text-protagonista md:text-5xl">
								<?php echo wp_kses_post( $heading ); ?>
							</h1>
						<?php endif; ?>

						<?php if ( '' !== trim( wp_strip_all_tags( $lead ) ) ) : ?>
							<p class="mwm-hero-04__lead max-w-[636px] text-xl leading-[1.2] text-protagonista">
								<?php echo wp_kses_post( $lead ); ?>
							</p>
						<?php endif; ?>
					</div>

					<div class="mwm-hero-04__bottom flex flex-col items-start gap-6 md:flex-row md:items-end md:gap-6">
						<?php if ( '' !== trim( wp_strip_all_tags( $supporting_text ) ) ) : ?>
							<p class="mwm-hero-04__supporting max-w-[306px] text-base leading-[1.2] text-protagonista">
								<?php echo wp_kses_post( $supporting_text ); ?>
							</p>
						<?php endif; ?>

						<?php if ( '' !== trim( $button_text ) && function_exists( 'mwm_render_button' ) ) : ?>
							<div class="mwm-hero-04__cta-wrap flex flex-col items-start gap-1.5">
								<?php
								if ( '' !== trim( $button_url ) ) {
									mwm_render_button(
										array(
											'text'          => $button_text,
											'url'           => $button_url,
											'variant'       => 'primary',
											'icon'          => 'arrow-right',
											'icon_position' => 'after',
											'size'          => 'md',
											'target'        => $opens_in_new_tab ? '_blank' : '',
											'class'         => 'mwm-hero-04__cta',
										)
									);
								} else {
									mwm_render_button(
										array(
											'text'          => $button_text,
											'as'            => 'button',
											'variant'       => 'primary',
											'icon'          => 'arrow-right',
											'icon_position' => 'after',
											'size'          => 'md',
											'class'         => 'mwm-hero-04__cta',
											'disabled'      => true,
											'aria_disabled' => true,
										)
									);
								}
								?>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<div class="mwm-hero-04__right flex min-h-0 items-end justify-end">
					<div class="mwm-hero-04__media-wrap relative aspect-square w-full max-w-[419px] overflow-hidden bg-protagonista">
						<?php if ( '' !== trim( $image_url ) ) : ?>
							<img class="mwm-hero-04__media h-full w-full object-cover" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" />
						<?php else : ?>
							<div class="mwm-hero-04__media-placeholder h-full w-full"></div>
						<?php endif; ?>
						<div class="mwm-hero-04__media-corner" aria-hidden="true"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

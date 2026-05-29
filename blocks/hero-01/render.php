<?php
/**
 * Server-side rendering for `zenyx/hero-01`.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$default_stats = array(
	array(
		'value'       => '1M+',
		'description' => '+83.333EUR / mes de facturacion',
	),
	array(
		'value'       => '35%',
		'description' => 'De rentabilidad media total',
	),
	array(
		'value'       => '+20h',
		'description' => 'De tus horas semanales libres de la operativa',
	),
);

$heading     = isset( $attributes['heading'] ) ? (string) $attributes['heading'] : '';
$subheading  = isset( $attributes['subheading'] ) ? (string) $attributes['subheading'] : '';
$description = isset( $attributes['description'] ) ? (string) $attributes['description'] : '';

$button_text  = isset( $attributes['buttonText'] ) ? (string) $attributes['buttonText'] : '';
$button_url   = isset( $attributes['buttonUrl'] ) ? (string) $attributes['buttonUrl'] : '';
$opens_new    = ! empty( $attributes['opensInNewTab'] );
$overlay_safe = 0.8;

$background_media_type = isset( $attributes['backgroundMediaType'] ) ? (string) $attributes['backgroundMediaType'] : 'video';
$background_media_type = in_array( $background_media_type, array( 'image', 'video' ), true ) ? $background_media_type : 'video';
$background_image_id   = isset( $attributes['backgroundImageId'] ) ? (int) $attributes['backgroundImageId'] : 0;
$background_image_url  = isset( $attributes['backgroundImageUrl'] ) ? (string) $attributes['backgroundImageUrl'] : '';
$background_image_alt  = isset( $attributes['backgroundImageAlt'] ) ? (string) $attributes['backgroundImageAlt'] : '';
$background_video_id   = isset( $attributes['backgroundVideoId'] ) ? (int) $attributes['backgroundVideoId'] : 0;
$background_video_url  = isset( $attributes['backgroundVideoUrl'] ) ? (string) $attributes['backgroundVideoUrl'] : '';

$clip_media_type = isset( $attributes['clipMediaType'] ) ? (string) $attributes['clipMediaType'] : 'image';
$clip_media_type = in_array( $clip_media_type, array( 'image', 'video' ), true ) ? $clip_media_type : 'image';
$clip_image_id   = isset( $attributes['clipImageId'] ) ? (int) $attributes['clipImageId'] : 0;
$clip_image_url  = isset( $attributes['clipImageUrl'] ) ? (string) $attributes['clipImageUrl'] : '';
$clip_image_alt  = isset( $attributes['clipImageAlt'] ) ? (string) $attributes['clipImageAlt'] : '';
$clip_video_id   = isset( $attributes['clipVideoId'] ) ? (int) $attributes['clipVideoId'] : 0;
$clip_video_url  = isset( $attributes['clipVideoUrl'] ) ? (string) $attributes['clipVideoUrl'] : '';

if ( '' === $background_image_url && $background_image_id > 0 ) {
	$background_image_url = (string) wp_get_attachment_image_url( $background_image_id, 'full' );
}
if ( '' === $background_image_alt && $background_image_id > 0 ) {
	$background_image_alt = (string) get_post_meta( $background_image_id, '_wp_attachment_image_alt', true );
}
if ( '' === $background_video_url && $background_video_id > 0 ) {
	$background_video_url = (string) wp_get_attachment_url( $background_video_id );
}

if ( '' === $clip_image_url && $clip_image_id > 0 ) {
	$clip_image_url = (string) wp_get_attachment_image_url( $clip_image_id, 'full' );
}
if ( '' === $clip_image_alt && $clip_image_id > 0 ) {
	$clip_image_alt = (string) get_post_meta( $clip_image_id, '_wp_attachment_image_alt', true );
}
if ( '' === $clip_video_url && $clip_video_id > 0 ) {
	$clip_video_url = (string) wp_get_attachment_url( $clip_video_id );
}

$has_background_media = ( 'video' === $background_media_type ) ? '' !== trim( $background_video_url ) : '' !== trim( $background_image_url );
$has_clip_media       = ( 'video' === $clip_media_type ) ? '' !== trim( $clip_video_url ) : '' !== trim( $clip_image_url );

$text_column_classes = 'mwm-hero-01__text-column flex min-h-0 min-w-0 flex-1 flex-col-reverse justify-between gap-6 self-stretch pt-0 lg:flex-col lg:pt-3';
if ( $has_clip_media ) {
	$text_column_classes .= ' lg:min-h-[308px]';
}

$stats_raw = isset( $attributes['stats'] ) && is_array( $attributes['stats'] ) ? $attributes['stats'] : array();
$stats     = array();

for ( $i = 0; $i < 3; $i++ ) {
	$raw_item = isset( $stats_raw[ $i ] ) && is_array( $stats_raw[ $i ] ) ? $stats_raw[ $i ] : array();

	$stats[] = array(
		'value'       => isset( $raw_item['value'] ) ? (string) $raw_item['value'] : (string) $default_stats[ $i ]['value'],
		'description' => isset( $raw_item['description'] ) ? (string) $raw_item['description'] : (string) $default_stats[ $i ]['description'],
	);
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'mwm-hero-01 relative isolate w-full overflow-hidden',
	)
);
?>

<section data-dark <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> style="padding-top: calc(var(--header-height, 68px));">
	<div class="mwm-hero-01__bg absolute inset-0 -z-10 overflow-hidden" aria-hidden="true">
		<?php if ( $has_background_media ) : ?>
			<?php if ( 'video' === $background_media_type ) : ?>
				<video class="mwm-hero-01__bg-media h-full w-full object-cover" autoplay muted loop playsinline>
					<source src="<?php echo esc_url( $background_video_url ); ?>" type="video/mp4" />
				</video>
			<?php else : ?>
				<img
					class="mwm-hero-01__bg-media h-full w-full object-cover"
					src="<?php echo esc_url( $background_image_url ); ?>"
					alt="<?php echo esc_attr( $background_image_alt ); ?>"
				/>
			<?php endif; ?>
		<?php endif; ?>
		<div class="mwm-hero-01__overlay absolute inset-0 bg-neutral-light" style="<?php echo esc_attr( 'opacity:' . $overlay_safe . ';' ); ?>"></div>
	</div>

		<div class="mwm-max-1">
		<div class="mwm-hero-01__shell flex w-full min-h-0 flex-1 flex-col">
			<div class="mwm-hero-01__content flex min-h-0 flex-1 flex-col justify-between gap-8 self-stretch py-10 lg:justify-around lg:gap-6 lg:py-[35px]">
				<div class="mwm-hero-01__top flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-center lg:gap-6 lg:self-stretch">
					<?php if ( '' !== trim( wp_strip_all_tags( $heading ) ) ) : ?>
						<div class="mwm-hero-01__heading-col flex min-w-0 flex-1 basis-0 lg:max-w-[636px]">
							<h1 class="mwm-hero-01__heading w-full text-left text-[2.125rem] font-heading uppercase leading-[1.2] text-protagonista md:text-5xl lg:text-[64px]">
								<?php echo wp_kses_post( $heading ); ?>
							</h1>
						</div>
					<?php endif; ?>

					<div class="mwm-hero-01__media-row flex min-w-0 flex-1 flex-col gap-6 lg:flex-row lg:items-end lg:justify-start lg:gap-6 lg:self-stretch">
						<?php if ( $has_clip_media ) : ?>
							<div class="mwm-hero-01__clip-media aspect-306/308 w-full max-w-[306px] shrink-0 overflow-hidden">
								<?php if ( 'video' === $clip_media_type ) : ?>
									<video class="h-full w-full object-cover" autoplay muted loop playsinline>
										<source src="<?php echo esc_url( $clip_video_url ); ?>" type="video/mp4" />
									</video>
								<?php else : ?>
									<img
										class="h-full w-full object-cover"
										src="<?php echo esc_url( $clip_image_url ); ?>"
										alt="<?php echo esc_attr( $clip_image_alt ); ?>"
									/>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<div class="<?php echo esc_attr( $text_column_classes ); ?>">
							<?php if ( '' !== trim( wp_strip_all_tags( $subheading ) ) ) : ?>
								<p class="mwm-hero-01__subheading max-w-[306px] text-lg font-medium leading-[1.2] text-protagonista md:text-xl lg:text-2xl">
									<?php echo wp_kses_post( $subheading ); ?>
								</p>
							<?php endif; ?>

							<?php if ( '' !== trim( $button_text ) && function_exists( 'mwm_render_button' ) ) : ?>
								<div class="mwm-hero-01__cta-wrap flex w-full max-w-[306px] flex-col justify-end gap-1.5">
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
												'target'        => $opens_new ? '_blank' : '',
												'class'         => 'mwm-hero-01__cta',
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
												'class'         => 'mwm-hero-01__cta',
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
				</div>

				<div class="mwm-hero-01__bottom flex flex-col gap-6 lg:gap-6">
					<div class="mwm-hero-01__desc-row hidden flex-col gap-4 lg:flex lg:flex-row lg:items-end lg:gap-6">
						<?php if ( '' !== trim( wp_strip_all_tags( $description ) ) ) : ?>
							<p class="mwm-hero-01__description max-w-[306px] text-base font-medium leading-[1.2] text-protagonista">
								<?php echo wp_kses_post( $description ); ?>
							</p>
						<?php endif; ?>
						<div class="hidden min-h-[57px] shrink-0 lg:block lg:w-[306px]" aria-hidden="true"></div>
						<div class="hidden min-h-[57px] flex-1 lg:block" aria-hidden="true"></div>
					</div>

					<div class="mwm-hero-01__stats-wrap max-w-[966px]">
						<div class="mwm-hero-01__stats">
							<div class="mwm-hero-01__stats-track">
								<?php foreach ( $stats as $stat_item ) : ?>
									<?php
									$stat_value = isset( $stat_item['value'] ) ? (string) $stat_item['value'] : '';
									$stat_desc  = isset( $stat_item['description'] ) ? (string) $stat_item['description'] : '';
									?>
									<div class="mwm-hero-01__stat flex max-w-[306px] flex-col gap-1.5">
										<?php if ( '' !== trim( wp_strip_all_tags( $stat_value ) ) ) : ?>
											<p class="mwm-hero-01__stat-value text-5xl font-normal leading-[1.2] text-white md:text-[64px]">
												<?php echo wp_kses_post( $stat_value ); ?>
											</p>
										<?php endif; ?>
										<?php if ( '' !== trim( wp_strip_all_tags( $stat_desc ) ) ) : ?>
											<p class="mwm-hero-01__stat-desc text-base font-medium leading-[1.2] text-protagonista">
												<?php echo wp_kses_post( $stat_desc ); ?>
											</p>
										<?php endif; ?>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

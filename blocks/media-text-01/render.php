<?php
/**
 * Server-side rendering for `zenyx/media-text-01`.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 *
 * @package zenyx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'mwm_media_text_01_normalize_slide_row' ) ) {
	/**
	 * Normaliza un slide desde atributos (array o legacy).
	 *
	 * @param array $row Slide crudo.
	 * @return array<string, mixed>
	 */
	function mwm_media_text_01_normalize_slide_row( $row ) {
		if ( ! is_array( $row ) ) {
			$row = array();
		}
		$type = isset( $row['mediaType'] ) ? (string) $row['mediaType'] : 'image';
		$type = in_array( $type, array( 'image', 'video' ), true ) ? $type : 'image';

		return array(
			'panelTitle'     => isset( $row['panelTitle'] ) ? (string) $row['panelTitle'] : '',
			'panelBody'      => isset( $row['panelBody'] ) ? (string) $row['panelBody'] : '',
			'mediaType'      => $type,
			'mediaImageId'   => isset( $row['mediaImageId'] ) ? (int) $row['mediaImageId'] : 0,
			'mediaImageUrl'  => isset( $row['mediaImageUrl'] ) ? (string) $row['mediaImageUrl'] : '',
			'mediaImageAlt'  => isset( $row['mediaImageAlt'] ) ? (string) $row['mediaImageAlt'] : '',
			'mediaVideoId'   => isset( $row['mediaVideoId'] ) ? (int) $row['mediaVideoId'] : 0,
			'mediaVideoUrl'  => isset( $row['mediaVideoUrl'] ) ? (string) $row['mediaVideoUrl'] : '',
		);
	}
}

if ( ! function_exists( 'mwm_media_text_01_resolve_slide_media' ) ) {
	/**
	 * Resuelve URLs de un slide para salida.
	 *
	 * @param array $slide Slide normalizado.
	 * @return array<string, mixed>
	 */
	function mwm_media_text_01_resolve_slide_media( $slide ) {
		$media_image_id  = (int) ( $slide['mediaImageId'] ?? 0 );
		$media_image_url = (string) ( $slide['mediaImageUrl'] ?? '' );
		$media_image_alt = (string) ( $slide['mediaImageAlt'] ?? '' );
		$media_video_id  = (int) ( $slide['mediaVideoId'] ?? 0 );
		$media_video_url = (string) ( $slide['mediaVideoUrl'] ?? '' );

		if ( '' === $media_image_url && $media_image_id > 0 ) {
			$media_image_url = (string) wp_get_attachment_image_url( $media_image_id, 'full' );
		}

		if ( '' === $media_image_alt && $media_image_id > 0 ) {
			$media_image_alt = (string) get_post_meta( $media_image_id, '_wp_attachment_image_alt', true );
		}

		if ( '' === $media_video_url && $media_video_id > 0 ) {
			$media_video_url = (string) wp_get_attachment_url( $media_video_id );
		}

		$slide['mediaImageUrl'] = $media_image_url;
		$slide['mediaImageAlt'] = $media_image_alt;
		$slide['mediaVideoUrl'] = $media_video_url;

		return $slide;
	}
}

$section_heading = isset( $attributes['sectionHeading'] ) ? (string) $attributes['sectionHeading'] : '';

$raw_slides = isset( $attributes['slides'] ) && is_array( $attributes['slides'] ) ? array_values( $attributes['slides'] ) : array();

if ( empty( $raw_slides ) ) {
	$legacy_title = isset( $attributes['panelTitle'] ) ? (string) $attributes['panelTitle'] : '';
	$legacy_body  = isset( $attributes['panelBody'] ) ? (string) $attributes['panelBody'] : '';
	$raw_slides   = array(
		array(
			'panelTitle'     => $legacy_title,
			'panelBody'      => $legacy_body,
			'mediaType'      => isset( $attributes['mediaType'] ) ? (string) $attributes['mediaType'] : 'image',
			'mediaImageId'   => isset( $attributes['mediaImageId'] ) ? (int) $attributes['mediaImageId'] : 0,
			'mediaImageUrl'  => isset( $attributes['mediaImageUrl'] ) ? (string) $attributes['mediaImageUrl'] : '',
			'mediaImageAlt'  => isset( $attributes['mediaImageAlt'] ) ? (string) $attributes['mediaImageAlt'] : '',
			'mediaVideoId'   => isset( $attributes['mediaVideoId'] ) ? (int) $attributes['mediaVideoId'] : 0,
			'mediaVideoUrl'  => isset( $attributes['mediaVideoUrl'] ) ? (string) $attributes['mediaVideoUrl'] : '',
		),
	);
}

$slides = array();
foreach ( $raw_slides as $row ) {
	$slides[] = mwm_media_text_01_resolve_slide_media( mwm_media_text_01_normalize_slide_row( $row ) );
}

if ( empty( $slides ) ) {
	$slides[] = mwm_media_text_01_resolve_slide_media( mwm_media_text_01_normalize_slide_row( array() ) );
}

$slide_count = count( $slides );
$slide_frac  = $slide_count > 0 ? ( 100 / $slide_count ) : 100;

$has_section_heading = '' !== trim( wp_strip_all_tags( $section_heading ) );

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class'                   => 'mwm-media-text-01 w-full bg-neutral-light',
		'data-mwm-media-text-01-root' => '1',
		'data-slide-count'        => (string) (int) $slide_count,
	)
);
?>

<section data-dark <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="mwm-media-text-01__pin flex min-h-dvh w-full flex-col justify-center">
		<div class="mwm-max-1 flex flex-col gap-10 pt-[56px] pb-[90px] lg:gap-[150px] lg:pt-[72px] lg:pb-[220px]">
			<?php if ( $has_section_heading ) : ?>
				<div class="mwm-media-text-01__heading-wrap flex flex-col gap-2.5 px-4 sm:px-[110px] lg:px-[220px]">
					<h2 class="mwm-media-text-01__section-heading text-center text-[2rem] font-heading leading-[1.2] text-protagonista md:text-[40px]">
						<?php echo wp_kses_post( $section_heading ); ?>
					</h2>
				</div>
			<?php endif; ?>

			<div class="mwm-media-text-01__row grid grid-cols-1 w-full md:grid-cols-2 md:min-h-[calc(100dvh-var(--wp-admin--admin-bar--height,0px)-var(--mwm-media-text-01-pin-offset,180px)-var(--mwm-media-text-01-pin-gap-bottom,96px))] md:max-h-[630px]" data-mwm-media-text-01-pin-target>
				<div class="mwm-media-text-01__media-col hidden w-full flex-col bg-white p-6 md:flex md:min-h-0 md:flex-1">
					<div
						class="mwm-media-text-01__media-viewport relative h-[280px] w-full overflow-hidden bg-neutral-light md:min-h-[582px] md:h-full"
						data-mwm-media-text-01-media-viewport
					>
						<div
							class="mwm-media-text-01__media-track flex h-full min-h-[280px] flex-row will-change-transform"
							data-mwm-media-text-01-media-track
							style="<?php echo esc_attr( 'width: ' . (int) ( $slide_count * 100 ) . '%;' ); ?>"
						>
							<?php foreach ( $slides as $slide ) : ?>
								<?php
								$media_type      = $slide['mediaType'];
								$media_image_url = $slide['mediaImageUrl'];
								$media_image_alt = $slide['mediaImageAlt'];
								$media_video_url = $slide['mediaVideoUrl'];
								$has_media       = ( 'video' === $media_type ) ? '' !== trim( $media_video_url ) : '' !== trim( $media_image_url );
								?>
								<div
									class="mwm-media-text-01__media-slide relative box-border flex h-full min-h-[280px] shrink-0 flex-col overflow-hidden bg-neutral-light"
									style="<?php echo esc_attr( 'flex: 0 0 ' . $slide_frac . '%; width: ' . $slide_frac . '%;' ); ?>"
								>
									<div class="mwm-media-text-01__media-shell relative flex min-h-[280px] h-full w-full flex-1 flex-col overflow-hidden bg-neutral-light md:min-h-[582px] md:h-full">
										<?php if ( $has_media && 'video' === $media_type ) : ?>
											<video
												class="mwm-media-text-01__media h-full w-full min-h-0 flex-1 object-cover mix-blend-luminosity"
												autoplay
												muted
												loop
												playsinline
											>
												<source src="<?php echo esc_url( $media_video_url ); ?>" type="video/mp4" />
											</video>
										<?php elseif ( $has_media && 'image' === $media_type ) : ?>
											<img
												class="mwm-media-text-01__media h-full w-full min-h-0 flex-1 object-cover mix-blend-luminosity"
												src="<?php echo esc_url( $media_image_url ); ?>"
												alt="<?php echo esc_attr( $media_image_alt ); ?>"
											/>
										<?php endif; ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>

				<div
					class="mwm-media-text-01__panel mwm-media-text-01__panel-col box-border hidden min-h-[280px] w-full flex-col bg-protagonista p-6 md:flex md:min-h-0 md:flex-1 md:gap-0 md:py-6"
					data-light
				>
					<div
						class="mwm-media-text-01__panel-title-wrap relative h-[72px] w-full shrink-0 overflow-hidden md:h-[140px]"
						data-mwm-media-text-01-title-viewport
					>
						<div
							class="mwm-media-text-01__title-track flex w-full flex-col will-change-transform"
							data-mwm-media-text-01-title-track
						>
							<?php foreach ( $slides as $slide ) : ?>
								<?php $panel_title = $slide['panelTitle']; ?>
								<div class="mwm-media-text-01__title-slide box-border flex h-[72px] w-full shrink-0 items-start justify-start md:h-[140px]">
									<h3 class="mwm-media-text-01__panel-title m-0 w-full min-w-0 text-[32px] font-heading leading-[1.2] text-neutral-light">
										<?php echo wp_kses_post( $panel_title ); ?>
									</h3>
								</div>
							<?php endforeach; ?>
						</div>
					</div>

					<div
						class="mwm-media-text-01__panel-body relative flex mt-auto min-h-[100px] w-full min-w-0 basis-0 flex-col overflow-hidden"
						data-mwm-media-text-01-body-viewport
					>
						<div
							class="mwm-media-text-01__body-track flex w-full flex-col will-change-transform"
							data-mwm-media-text-01-body-track
						>
							<?php foreach ( $slides as $slide ) : ?>
								<?php $panel_body = $slide['panelBody']; ?>
								<div
									class="mwm-media-text-01__body-slide box-border flex w-full min-w-0 shrink-0 flex-col justify-end overflow-hidden text-xl leading-[1.2] text-white [&_a]:text-white [&_a]:underline"
									data-mwm-media-text-01-body-slide
								>
									<?php echo wp_kses_post( $panel_body ); ?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>

			<div
				class="mwm-media-text-01__mobile-cards swiper md:!hidden"
				data-mwm-media-text-01-mobile-swiper
			>
				<div class="swiper-wrapper">
					<?php foreach ( $slides as $slide ) : ?>
					<?php
					$panel_title     = $slide['panelTitle'];
					$panel_body      = $slide['panelBody'];
					$media_type      = $slide['mediaType'];
					$media_image_url = $slide['mediaImageUrl'];
					$media_image_alt = $slide['mediaImageAlt'];
					$media_video_url = $slide['mediaVideoUrl'];
					$has_media       = ( 'video' === $media_type ) ? '' !== trim( $media_video_url ) : '' !== trim( $media_image_url );
					?>
					<article class="mwm-media-text-01__mobile-card swiper-slide flex h-auto flex-col overflow-hidden">
						<?php if ( $has_media && 'video' === $media_type ) : ?>
							<div class="mwm-media-text-01__mobile-card-media relative aspect-[4/3] w-full shrink-0 overflow-hidden bg-neutral-light">
								<video
									class="mwm-media-text-01__media mwm-media-text-01__mobile-card-video absolute inset-0 h-full w-full object-cover mix-blend-luminosity"
									autoplay
									muted
									loop
									playsinline
								>
									<source src="<?php echo esc_url( $media_video_url ); ?>" type="video/mp4" />
								</video>
							</div>
						<?php elseif ( $has_media && 'image' === $media_type ) : ?>
							<div class="mwm-media-text-01__mobile-card-media relative aspect-[4/3] w-full shrink-0 overflow-hidden bg-neutral-light">
								<img
									class="mwm-media-text-01__media mwm-media-text-01__mobile-card-img h-full w-full object-cover mix-blend-luminosity"
									src="<?php echo esc_url( $media_image_url ); ?>"
									alt="<?php echo esc_attr( $media_image_alt ); ?>"
									loading="lazy"
									decoding="async"
								/>
							</div>
						<?php endif; ?>
						<div
							class="mwm-media-text-01__mobile-card-content box-border flex flex-col bg-protagonista p-6 text-white"
							data-light
						>
							<?php if ( '' !== trim( wp_strip_all_tags( $panel_title ) ) ) : ?>
								<h3 class="mwm-media-text-01__mobile-card-title m-0 text-[20px] font-heading leading-[1.2] text-neutral-light">
									<?php echo wp_kses_post( $panel_title ); ?>
								</h3>
							<?php endif; ?>
							<?php if ( '' !== trim( wp_strip_all_tags( $panel_body ) ) ) : ?>
								<div class="mwm-media-text-01__mobile-card-body mt-4 text-[16px] leading-[1.3] [&_a]:text-white [&_a]:underline">
									<?php echo wp_kses_post( $panel_body ); ?>
								</div>
							<?php endif; ?>
						</div>
					</article>
					<?php endforeach; ?>
				</div>
				<?php if ( $slide_count > 1 ) : ?>
					<div
						class="mwm-slider-casos-01__nav mt-6 flex gap-2 justify-center"
						data-mwm-media-text-01-nav
					>
						<button
							type="button"
							class="mwm-slider-casos-01__btn-prev inline-flex h-10 w-10 cursor-pointer items-center justify-center text-protagonista transition-opacity hover:opacity-70 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-30"
							data-mwm-media-text-01-nav-prev
							aria-label="<?php esc_attr_e( 'Caso anterior', 'zenyx' ); ?>"
						>
							<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
								<path d="M11.9518 7L4 15M4 15L11.9518 23M4 15H26" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
							</svg>
						</button>
						<button
							type="button"
							class="mwm-slider-casos-01__btn-next inline-flex h-10 w-10 cursor-pointer items-center justify-center text-protagonista transition-opacity hover:opacity-70 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-30"
							data-mwm-media-text-01-nav-next
							aria-label="<?php esc_attr_e( 'Caso siguiente', 'zenyx' ); ?>"
						>
							<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
								<path d="M18.0482 23L26 15M26 15L18.0482 7M26 15L4 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
							</svg>
						</button>
					</div>
				<?php endif; ?>
			</div>

		</div>
	</div>
</section>

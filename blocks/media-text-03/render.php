<?php
/**
 * Server-side rendering for `zenyx/media-text-03`.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$media_type       = isset( $attributes['mediaType'] ) ? (string) $attributes['mediaType'] : 'image';
$media_type       = in_array( $media_type, array( 'image', 'video' ), true ) ? $media_type : 'image';
$media_image_id   = isset( $attributes['mediaImageId'] ) ? (int) $attributes['mediaImageId'] : 0;
$media_image_url  = isset( $attributes['mediaImageUrl'] ) ? (string) $attributes['mediaImageUrl'] : '';
$media_image_alt  = isset( $attributes['mediaImageAlt'] ) ? (string) $attributes['mediaImageAlt'] : '';
$media_video_id   = isset( $attributes['mediaVideoId'] ) ? (int) $attributes['mediaVideoId'] : 0;
$media_video_url  = isset( $attributes['mediaVideoUrl'] ) ? (string) $attributes['mediaVideoUrl'] : '';

if ( '' === $media_image_url && $media_image_id > 0 ) {
	$media_image_url = (string) wp_get_attachment_image_url( $media_image_id, 'full' );
}

if ( '' === $media_image_alt && $media_image_id > 0 ) {
	$media_image_alt = (string) get_post_meta( $media_image_id, '_wp_attachment_image_alt', true );
}

if ( '' === $media_video_url && $media_video_id > 0 ) {
	$media_video_url = (string) wp_get_attachment_url( $media_video_id );
}

$has_media = ( 'video' === $media_type ) ? '' !== trim( $media_video_url ) : '' !== trim( $media_image_url );

$stats_raw = isset( $attributes['stats'] ) && is_array( $attributes['stats'] ) ? $attributes['stats'] : array();
$stats     = array();

foreach ( $stats_raw as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}
	$variant = isset( $row['variant'] ) ? (string) $row['variant'] : 'single';
	$variant = in_array( $variant, array( 'single', 'double' ), true ) ? $variant : 'single';
	$stats[] = array(
		'variant'   => $variant,
		'primary'   => isset( $row['primary'] ) ? (string) $row['primary'] : '',
		'secondary' => isset( $row['secondary'] ) ? (string) $row['secondary'] : '',
	);
}

$visible_stats = array();
foreach ( $stats as $item ) {
	$t = isset( $item['primary'] ) ? (string) $item['primary'] : '';
	$s = isset( $item['secondary'] ) ? (string) $item['secondary'] : '';
	$v = isset( $item['variant'] ) ? (string) $item['variant'] : 'single';
	if ( 'double' === $v ) {
		if ( '' !== trim( wp_strip_all_tags( $t ) ) || '' !== trim( wp_strip_all_tags( $s ) ) ) {
			$visible_stats[] = $item;
		}
	} elseif ( '' !== trim( wp_strip_all_tags( $t ) ) ) {
		$visible_stats[] = $item;
	}
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'mwm-media-text-03 w-full overflow-hidden bg-neutral-light py-[120px]',
	)
);
?>

<section data-dark <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="mwm-max-1 flex flex-col gap-6 lg:flex-row lg:items-center">
		<div class="mwm-media-text-03__media-col w-full min-w-0 shrink-0 lg:max-w-[636px] lg:pr-[110px]">
			<div class="mwm-media-text-03__media-shell relative flex lg:max-h-[212px] w-full flex-col overflow-hidden bg-neutral-light">
				<?php if ( $has_media && 'video' === $media_type ) : ?>
					<video
						class="mwm-media-text-03__media h-full w-full min-h-0 flex-1 object-cover mix-blend-luminosity"
						autoplay
						muted
						loop
						playsinline
					>
						<source src="<?php echo esc_url( $media_video_url ); ?>" type="video/mp4" />
					</video>
				<?php elseif ( $has_media && 'image' === $media_type ) : ?>
					<img
						class="mwm-media-text-03__media h-full w-full min-h-0 flex-1 object-cover mix-blend-luminosity"
						src="<?php echo esc_url( $media_image_url ); ?>"
						alt="<?php echo esc_attr( $media_image_alt ); ?>"
					/>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( ! empty( $visible_stats ) ) : ?>
			<div class="mwm-media-text-03__stats grid min-w-0 flex-1 grid-cols-1 gap-6 py-10 xl:grid-cols-2">
				<?php foreach ( $visible_stats as $stat ) : ?>
					<?php
					$variant = isset( $stat['variant'] ) ? (string) $stat['variant'] : 'single';
					$primary = isset( $stat['primary'] ) ? (string) $stat['primary'] : '';
					$second  = isset( $stat['secondary'] ) ? (string) $stat['secondary'] : '';
					$is_dbl  = 'double' === $variant;
					$mod     = $is_dbl ? 'mwm-media-text-03__stat--double' : 'mwm-media-text-03__stat--single';
					?>
					<div class="mwm-media-text-03__stat <?php echo esc_attr( $mod ); ?> min-w-0">
						<?php if ( $is_dbl ) : ?>
							<div class="mwm-media-text-03__stat-inner flex flex-col gap-3">
								<?php if ( '' !== trim( wp_strip_all_tags( $primary ) ) ) : ?>
									<div class="mwm-media-text-03__stat-primary text-left text-[24px] leading-[1.2] text-acento">
										<?php echo wp_kses_post( $primary ); ?>
									</div>
								<?php endif; ?>
								<?php if ( '' !== trim( wp_strip_all_tags( $second ) ) ) : ?>
									<div class="mwm-media-text-03__stat-secondary text-left text-base leading-[1.2] text-inherit md:pr-6">
										<?php echo wp_kses_post( $second ); ?>
									</div>
								<?php endif; ?>
							</div>
						<?php else : ?>
							<div class="mwm-media-text-03__stat-primary text-left text-[24px] leading-[1.2] text-acento">
								<?php echo wp_kses_post( $primary ); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

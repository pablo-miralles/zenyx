<?php
/**
 * Server-side rendering for `zenyx/gallery-01`.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading     = isset( $attributes['heading'] ) ? (string) $attributes['heading'] : '';
$description = isset( $attributes['description'] ) ? (string) $attributes['description'] : '';
$raw_images  = isset( $attributes['images'] ) && is_array( $attributes['images'] ) ? array_values( $attributes['images'] ) : array();

$empty_image = array(
	'imageId'  => 0,
	'imageUrl' => '',
	'imageAlt' => '',
);

$images = array();
foreach ( $raw_images as $item ) {
	$current  = is_array( $item ) ? $item : array();
	$merged   = array_merge( $empty_image, $current );
	$image_id = absint( $merged['imageId'] );
	$image_url = (string) $merged['imageUrl'];
	$image_alt = (string) $merged['imageAlt'];

	if ( $image_id > 0 && '' === $image_url ) {
		$image_url = (string) wp_get_attachment_image_url( $image_id, 'large' );
	}
	if ( $image_id > 0 && '' === $image_alt ) {
		$image_alt = (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true );
	}

	if ( '' !== $image_url ) {
		$images[] = array(
			'url' => $image_url,
			'alt' => $image_alt,
		);
	}
}

$has_text = '' !== trim( wp_strip_all_tags( $heading ) ) || '' !== trim( wp_strip_all_tags( $description ) );

if ( ! $has_text && empty( $images ) ) {
	return;
}

$gallery_patterns = array(
	array(
		'class'    => 'mwm-gallery-01__item--pattern-1',
		'top_px'   => 0,
		'height_px'=> 554,
	),
	array(
		'class'    => 'mwm-gallery-01__item--pattern-2',
		'top_px'   => 1560,
		'height_px'=> 408,
	),
	array(
		'class'    => 'mwm-gallery-01__item--pattern-3',
		'top_px'   => 109,
		'height_px'=> 234,
	),
	array(
		'class'    => 'mwm-gallery-01__item--pattern-4',
		'top_px'   => 1128,
		'height_px'=> 234,
	),
	array(
		'class'    => 'mwm-gallery-01__item--pattern-5',
		'top_px'   => 560,
		'height_px'=> 229,
	),
	array(
		'class'    => 'mwm-gallery-01__item--pattern-6',
		'top_px'   => 896,
		'height_px'=> 394,
	),
	array(
		'class'    => 'mwm-gallery-01__item--pattern-7',
		'top_px'   => 1504,
		'height_px'=> 312,
	),
);

$gallery_repeat_order = array( 0, 2, 4, 5, 3, 6, 1 );
$gallery_cycle_height = 1968;
$gallery_tail_height  = 832;
$gallery_aspect_h     = 0;
$gallery_items        = array();

foreach ( $images as $index => $img ) {
	if ( $index < count( $gallery_patterns ) ) {
		$pattern     = $gallery_patterns[ $index ];
		$cycle_index = 0;
	} else {
		$repeat_index = $index - count( $gallery_patterns );
		$pattern      = $gallery_patterns[ $gallery_repeat_order[ $repeat_index % count( $gallery_repeat_order ) ] ];
		$cycle_index  = 1 + (int) floor( $repeat_index / count( $gallery_repeat_order ) );
	}

	$top_px        = (int) $pattern['top_px'] + ( $cycle_index * $gallery_cycle_height );
	$bottom_px     = $top_px + (int) $pattern['height_px'];

	$gallery_aspect_h = max( $gallery_aspect_h, $bottom_px + $gallery_tail_height );

	$gallery_items[] = array(
		'url'       => $img['url'],
		'alt'       => $img['alt'],
		'class'     => $pattern['class'],
		'top_px'    => $top_px,
	);
}

if ( $gallery_aspect_h < 700 ) {
	$gallery_aspect_h = 700;
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'mwm-gallery-01 w-full bg-neutral-light',
	)
);
?>

<section data-dark <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $has_text ) : ?>
		<div class="mwm-gallery-01__text sticky top-0 z-10 flex h-screen items-center justify-center">
			<div class="mwm-max-1 flex flex-col items-center gap-6">
				<?php if ( '' !== trim( wp_strip_all_tags( $heading ) ) ) : ?>
					<div class="mwm-gallery-01__heading-wrap w-full max-w-[636px]">
						<h2 class="mwm-gallery-01__heading text-center font-heading text-display-m text-protagonista">
							<?php echo wp_kses_post( $heading ); ?>
						</h2>
					</div>
				<?php endif; ?>
				<?php if ( '' !== trim( wp_strip_all_tags( $description ) ) ) : ?>
					<div class="mwm-gallery-01__desc-wrap w-full max-w-[636px]">
						<div class="mwm-gallery-01__desc text-center text-body-l text-protagonista">
							<?php echo wp_kses_post( $description ); ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $images ) ) : ?>
		<div
			class="mwm-gallery-01__collage relative z-20"
			style="<?php echo esc_attr( '--mwm-gallery-aspect-h: ' . (string) $gallery_aspect_h ); ?>"
		>
			<?php foreach ( $gallery_items as $img ) : ?>
				<div
					class="mwm-gallery-01__item <?php echo esc_attr( $img['class'] ); ?> overflow-hidden"
					style="<?php echo esc_attr( '--mwm-gallery-item-top: ' . (string) ( ( $img['top_px'] / $gallery_aspect_h ) * 100 ) . '%' ); ?>"
				>
					<img
						src="<?php echo esc_url( $img['url'] ); ?>"
						alt="<?php echo esc_attr( $img['alt'] ); ?>"
						class="h-full w-full object-cover"
						loading="lazy"
					/>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>

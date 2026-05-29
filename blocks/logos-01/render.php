<?php
/**
 * Server-side rendering for `zenyx/logos-01`.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading   = isset( $attributes['heading'] ) ? (string) $attributes['heading'] : '';
$raw_items = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? array_values( $attributes['items'] ) : array();

$empty_item = array(
	'imageId'  => 0,
	'imageUrl' => '',
	'imageAlt' => '',
);

$items = array();
foreach ( $raw_items as $item ) {
	$current   = is_array( $item ) ? $item : array();
	$merged    = array_merge( $empty_item, $current );
	$image_id  = absint( $merged['imageId'] );
	$image_url = (string) $merged['imageUrl'];
	$image_alt = (string) $merged['imageAlt'];

	if ( $image_id > 0 && '' === $image_url ) {
		$image_url = (string) wp_get_attachment_image_url( $image_id, 'full' );
	}
	if ( $image_id > 0 && '' === $image_alt ) {
		$image_alt = (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true );
	}

	if ( '' !== $image_url ) {
		$items[] = array(
			'id'  => $image_id,
			'url' => $image_url,
			'alt' => $image_alt,
		);
	}
}

$has_heading = '' !== trim( wp_strip_all_tags( $heading ) );

if ( ! $has_heading && empty( $items ) ) {
	return;
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'mwm-logos-01 w-full bg-neutral-light',
	)
);
?>

<section data-dark <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="mwm-max-1 flex flex-col-reverse gap-16 py-16 lg:gap-20 lg:py-[120px]">
		<?php if ( ! empty( $items ) ) : ?>
			<ul class="mwm-logos-01__grid grid list-none grid-cols-2 gap-6 p-0 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5" role="list">
				<?php foreach ( $items as $logo ) : ?>
					<?php
					$image_id = isset( $logo['id'] ) ? absint( $logo['id'] ) : 0;
					$url      = isset( $logo['url'] ) ? (string) $logo['url'] : '';
					$alt      = isset( $logo['alt'] ) ? (string) $logo['alt'] : '';
					if ( '' === $url ) {
						continue;
					}
					$is_decorative = '' === trim( $alt );
					$css_url       = esc_url( $url, array( 'http', 'https' ) );
					$mask_style    = '--mwm-logos-src: url(' . $css_url . ');';
					$img_attr      = array(
						'class'         => 'mwm-logos-01__sizer',
						'loading'       => 'lazy',
						'decoding'      => 'async',
						'alt'           => '',
						'aria-hidden'   => 'true',
					);
					?>
					<li class="mwm-logos-01__cell flex min-h-[60px] items-center justify-center">
						<div
							class="mwm-logos-01__logo relative inline-block max-w-full"
							<?php if ( $is_decorative ) : ?>
								aria-hidden="true"
							<?php else : ?>
								role="img"
								aria-label="<?php echo esc_attr( $alt ); ?>"
							<?php endif; ?>
						>
							<?php
							if ( $image_id > 0 ) {
								echo wp_get_attachment_image( $image_id, 'full', false, $img_attr ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							} else {
								printf(
									'<img src="%1$s" alt="" class="mwm-logos-01__sizer" loading="lazy" decoding="async" aria-hidden="true" />',
									$css_url
								);
							}
							?>
							<div
								class="mwm-logos-01__tint absolute inset-0"
								style="<?php echo esc_attr( $mask_style ); ?>"
								aria-hidden="true"
							></div>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( $has_heading ) : ?>
			<div class="mwm-logos-01__heading-wrap flex justify-center px-0">
				<p class="mwm-logos-01__heading max-w-[636px] text-center font-body text-xl text-inherit lg:text-[20px]">
					<?php echo wp_kses_post( $heading ); ?>
				</p>
			</div>
		<?php endif; ?>
	</div>
</section>

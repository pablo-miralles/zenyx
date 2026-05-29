<?php
/**
 * Server-side rendering for `zenyx/benefits-01`.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = isset( $attributes['heading'] ) ? (string) $attributes['heading'] : '';

$image_id  = isset( $attributes['imageId'] ) ? absint( $attributes['imageId'] ) : 0;
$image_url = isset( $attributes['imageUrl'] ) ? (string) $attributes['imageUrl'] : '';
$image_alt = isset( $attributes['imageAlt'] ) ? (string) $attributes['imageAlt'] : '';

if ( $image_id > 0 && '' === $image_url ) {
	$image_url = (string) wp_get_attachment_image_url( $image_id, 'large' );
}
if ( $image_id > 0 && '' === $image_alt ) {
	$image_alt = (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true );
}

$raw_items = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? array_values( $attributes['items'] ) : array();

$empty_item = array(
	'contentType' => 'text',
	'text'        => '',
	'variant'     => 'default',
	'imageId'     => 0,
	'imageUrl'    => '',
	'imageAlt'    => '',
);

$items = array();
for ( $i = 0; $i < 6; $i++ ) {
	$row    = isset( $raw_items[ $i ] ) && is_array( $raw_items[ $i ] ) ? $raw_items[ $i ] : array();
	$merged = array_merge( $empty_item, $row );
	$ctype  = (string) $merged['contentType'];
	if ( 'image' !== $ctype ) {
		$ctype = 'text';
	}
	$var = (string) $merged['variant'];
	if ( 'accent' !== $var ) {
		$var = 'default';
	}
	$item_image_id  = isset( $merged['imageId'] ) ? absint( $merged['imageId'] ) : 0;
	$item_image_url = isset( $merged['imageUrl'] ) ? (string) $merged['imageUrl'] : '';
	$item_image_alt = isset( $merged['imageAlt'] ) ? (string) $merged['imageAlt'] : '';
	if ( $item_image_id > 0 && '' === $item_image_url ) {
		$item_image_url = (string) wp_get_attachment_image_url( $item_image_id, 'large' );
	}
	if ( $item_image_id > 0 && '' === $item_image_alt ) {
		$item_image_alt = (string) get_post_meta( $item_image_id, '_wp_attachment_image_alt', true );
	}
	$items[] = array(
		'contentType' => $ctype,
		'text'        => (string) $merged['text'],
		'variant'     => $var,
		'imageId'     => $item_image_id,
		'imageUrl'    => $item_image_url,
		'imageAlt'    => $item_image_alt,
	);
}

$has_heading = '' !== trim( wp_strip_all_tags( $heading ) );
$has_visual  = '' !== $image_url;
$has_item    = false;
foreach ( $items as $it ) {
	if ( 'image' === $it['contentType'] ) {
		if ( '' !== $it['imageUrl'] ) {
			$has_item = true;
			break;
		}
	} elseif ( '' !== trim( $it['text'] ) ) {
		$has_item = true;
		break;
	}
}

if ( ! $has_heading && ! $has_visual && ! $has_item ) {
	return;
}

/*
 * Alto lógico del collage (1366:H): misma interpolación que gallery-01.
 * Cuenta piezas = tarjetas con contenido (texto o imagen) + imagen central si existe (máx. 7).
 */
$filled_card_count = 0;
foreach ( $items as $it ) {
	if ( 'image' === $it['contentType'] && '' !== $it['imageUrl'] ) {
		$filled_card_count++;
	} elseif ( 'text' === $it['contentType'] && '' !== trim( $it['text'] ) ) {
		$filled_card_count++;
	}
}
$piece_count = $filled_card_count + ( $has_visual ? 1 : 0 );
if ( $piece_count < 1 ) {
	$piece_count = 1;
}

$min_canvas       = 700;
$max_canvas       = 2800;
$step_extra       = (int) round( ( $max_canvas - $min_canvas ) / 6 );
$benefits_aspect_h = $max_canvas;
if ( 1 === $piece_count ) {
	$benefits_aspect_h = $min_canvas;
} elseif ( $piece_count <= 7 ) {
	$benefits_aspect_h = (int) round(
		$min_canvas + ( $max_canvas - $min_canvas ) * ( $piece_count - 1 ) / 6
	);
} else {
	$benefits_aspect_h = $max_canvas + ( $piece_count - 7 ) * $step_extra;
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'mwm-benefits-01 w-full bg-protagonista',
	)
);

$collage_style = '--mwm-benefits-aspect-h: ' . (string) $benefits_aspect_h;
?>

<section data-light <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $has_heading ) : ?>
		<div class="mwm-benefits-01__text sticky top-0 z-10 flex h-screen items-center justify-center">
			<div class="mwm-max-1 flex flex-col items-center gap-6">
				<div class="mwm-benefits-01__heading-wrap w-full max-w-[636px]">
					<h2 class="mwm-benefits-01__heading text-center font-heading text-display-m text-inherit">
						<?php echo wp_kses_post( $heading ); ?>
					</h2>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<div
		class="mwm-benefits-01__collage relative z-20"
		style="<?php echo esc_attr( $collage_style ); ?>"
	>
		<?php
		for ( $i = 0; $i < 6; $i++ ) :
			$slot  = $i + 1;
			$item  = $items[ $i ];
			$is_image_slot = 'image' === $item['contentType'];

			if ( $is_image_slot ) {
				$item_url = $item['imageUrl'];
				$item_alt = $item['imageAlt'];
				$is_empty  = '' === $item_url;
				$card_mod  = ' mwm-benefits-01__card--media';
				$bg_class  = 'bg-white';
			} else {
				$text_raw  = $item['text'];
				$is_accent = 'accent' === $item['variant'];
				$is_empty  = '' === trim( $text_raw );
				$card_mod  = '';
				$bg_class  = $is_accent ? 'bg-acento' : 'bg-white';
				$txt_class = $is_accent ? 'text-white' : 'text-protagonista';
			}
			?>
			<div
				class="mwm-benefits-01__card mwm-benefits-01__card-clip flex flex-col <?php echo $is_empty ? '' : esc_attr( $bg_class ); ?> mwm-benefits-01__card--<?php echo esc_attr( (string) $slot ); ?><?php echo esc_attr( $card_mod ); ?><?php echo $is_empty ? ' mwm-benefits-01__card--empty' : ''; ?>"
				<?php echo $is_empty ? ' aria-hidden="true"' : ''; ?>
			>
				<?php if ( ! $is_empty && $is_image_slot ) : ?>
					<div class="mwm-benefits-01__card-media min-h-0 flex-1">
						<img
							src="<?php echo esc_url( $item_url ); ?>"
							alt="<?php echo esc_attr( $item_alt ); ?>"
							class="mwm-benefits-01__card-img h-full w-full object-cover"
							loading="lazy"
						/>
					</div>
				<?php elseif ( ! $is_empty ) : ?>
					<div class="mwm-benefits-01__card-inner flex min-h-0 flex-1 flex-col justify-center px-5">
						<p class="mwm-benefits-01__card-text m-0 text-left font-body text-base lg:text-subheading <?php echo esc_attr( $txt_class ); ?>">
							<?php echo esc_html( $text_raw ); ?>
						</p>
					</div>
				<?php endif; ?>
			</div>
			<?php
		endfor;
		?>

		<?php if ( $has_visual ) : ?>
			<div class="mwm-benefits-01__visual">
				<img
					src="<?php echo esc_url( $image_url ); ?>"
					alt="<?php echo esc_attr( $image_alt ); ?>"
					class="mwm-benefits-01__visual-img h-full w-full object-cover"
					loading="lazy"
				/>
			</div>
		<?php endif; ?>
	</div>
</section>

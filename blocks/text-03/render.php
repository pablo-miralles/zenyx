<?php
/**
 * Server-side rendering for `zenyx/text-03`.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = isset( $attributes['heading'] ) ? (string) $attributes['heading'] : '';
$intro   = isset( $attributes['intro'] ) ? (string) $attributes['intro'] : '';

$items_raw = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : array();
$items     = array();

foreach ( $items_raw as $raw ) {
	if ( ! is_array( $raw ) ) {
		continue;
	}
	$items[] = array(
		'title' => isset( $raw['title'] ) ? (string) $raw['title'] : '',
		'body'  => isset( $raw['body'] ) ? (string) $raw['body'] : '',
	);
}

$has_heading = '' !== trim( wp_strip_all_tags( $heading ) );
$has_intro   = '' !== trim( wp_strip_all_tags( $intro ) );

$visible_items = array();
foreach ( $items as $item ) {
	$t = isset( $item['title'] ) ? (string) $item['title'] : '';
	$b = isset( $item['body'] ) ? (string) $item['body'] : '';
	if ( '' !== trim( wp_strip_all_tags( $t ) ) || '' !== trim( wp_strip_all_tags( $b ) ) ) {
		$visible_items[] = $item;
	}
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'mwm-text-03 relative w-full overflow-hidden bg-neutral-light py-[120px]',
	)
);

// Gradiente del SVG: --mwm-text-03-gradient-color en theme CSS según data-light / data-dark
// (p. ej. group-section-color-transition alterna atributos; sin JS extra en el bloque).
$gradient_id = wp_unique_id( 'mwm-text-03-grad-' );

?>

<section data-dark <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<div class="mwm-max-1 relative z-1 flex flex-col gap-[120px]">
		<div
			class="mwm-text-03__svg-wrap pointer-events-none absolute bottom-0 right-0 w-full max-w-[min(100%,1076px)]" aria-hidden="true">
			<svg width="1076" height="837" viewBox="0 0 1076 837" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path opacity="0.1" d="M230.673 697.044L640.626 697.965V836.889H0V697.044L204.817 492.258H435.474L230.673 697.044ZM873.978 149.152V285.816L667.52 492.26H436.847L643.321 285.816H232.028L231.61 149.152H873.978ZM1076 81.8477L1010.53 147.627H928.416V0H1076V81.8477Z" fill="url(#<?php echo esc_attr( $gradient_id ); ?>)"/>
				<defs>
				<linearGradient id="<?php echo esc_attr( $gradient_id ); ?>" x1="538" y1="0" x2="538" y2="836.889" gradientUnits="userSpaceOnUse">
				<stop stop-color="var(--mwm-text-03-gradient-color, #083b51)" stop-opacity="0.8"/>
				<stop offset="1" stop-color="var(--mwm-text-03-gradient-color, #083b51)" stop-opacity="0.3"/>
				</linearGradient>
				</defs>
			</svg>

		</div>
		<?php if ( $has_heading || $has_intro ) : ?>
			<div class="flex max-w-[636px] flex-col gap-6">
				<?php if ( $has_heading ) : ?>
					<h2 class="mwm-text-03__heading w-full text-left text-[32px] font-heading leading-[1.2] text-inherit">
						<?php echo wp_kses_post( $heading ); ?>
					</h2>
				<?php endif; ?>
				<?php if ( $has_intro ) : ?>
					<div class="mwm-text-03__intro w-full text-left text-xl leading-[1.2] text-inherit">
						<?php echo wp_kses_post( $intro ); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $visible_items ) ) : ?>
			<div class="mwm-text-03__grid grid grid-cols-1 gap-x-6 gap-y-20 lg:grid-cols-2">
				<?php foreach ( $visible_items as $item ) : ?>
					<?php
					$title = isset( $item['title'] ) ? (string) $item['title'] : '';
					$body  = isset( $item['body'] ) ? (string) $item['body'] : '';
					$has_t = '' !== trim( wp_strip_all_tags( $title ) );
					$has_b = '' !== trim( wp_strip_all_tags( $body ) );
					if ( ! $has_t && ! $has_b ) {
						continue;
					}
					?>
					<div class="mwm-text-03__item flex flex-col gap-5 md:flex-row md:items-start">
						<?php if ( $has_t ) : ?>
							<h3 class="mwm-text-03__item-title min-w-0 flex-1 pr-0 text-left text-[24px] !font-body font-medium text-acento md:pr-6">
								<?php echo wp_kses_post( $title ); ?>
							</h3>
						<?php endif; ?>
						<?php if ( $has_b ) : ?>
							<div class="mwm-text-03__item-body min-w-0 flex-1 pr-0 text-left text-base leading-[1.2] text-inherit md:pr-6">
								<?php echo wp_kses_post( $body ); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

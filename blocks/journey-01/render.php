<?php
/**
 * Server-side rendering for `zenyx/journey-01`.
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

$heading = isset( $attributes['heading'] ) ? (string) $attributes['heading'] : '';

$raw_items = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? array_values( $attributes['items'] ) : array();

$items = array();
foreach ( $raw_items as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}
	$text = isset( $row['text'] ) ? (string) $row['text'] : '';
	if ( '' === trim( $text ) ) {
		continue;
	}
	$placement = isset( $row['placement'] ) && 'below' === $row['placement'] ? 'below' : 'above';
	$items[]   = array(
		'text'      => $text,
		'placement' => $placement,
	);
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class'                 => 'mwm-journey-01 flex min-h-screen min-h-[100dvh] w-full flex-col justify-center bg-protagonista overflow-hidden py-8 md:py-12',
		'data-mwm-journey-root' => '1',
	)
);
?>

<section data-light <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="mwm-journey-01__inner flex w-full flex-col-reverse items-stretch gap-10 md:gap-14 lg:gap-[60px]">
		<?php if ( count( $items ) > 0 ) : ?>
			<div class="mwm-journey-01__timeline w-full">
				<div
					class="mwm-journey-01__viewport relative min-h-[min(50vh,560px)] h-[50vh] w-full md:min-h-[55vh] md:h-[55vh]"
					data-mwm-journey-viewport
				>
					<div
						class="pointer-events-none absolute left-1/2 top-1/2 z-10 -translate-x-1/2 -translate-y-1/2"
						aria-hidden="true"
					>
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mwm-journey-01__marker block h-6 w-6">
							<path d="M13.3539 24H0V0H24V13.3061L13.3539 24Z" fill="#FE7756" />
						</svg>
					</div>

					<div
						class="mwm-journey-01__track relative z-1 flex h-full w-max flex-row will-change-transform"
						data-mwm-journey-track
					>
						<?php foreach ( $items as $item ) : ?>
							<?php
							$placement = $item['placement'];
							$text      = $item['text'];
							?>
							<div class="mwm-journey-01__col relative z-1 flex h-full w-[85vw] shrink-0 flex-col sm:w-[75vw] md:w-[649px]">
								<?php if ( 'above' === $placement ) : ?>
									<div class="mwm-journey-01__cell mwm-journey-01__cell--above flex flex-1 flex-col items-start justify-end">
										<p class="mwm-journey-01__text w-full max-w-[329px] pb-8 text-left font-body text-base leading-[1.3] text-white md:pb-[88px] md:text-2xl md:leading-normal">
											<?php echo esc_html( $text ); ?>
										</p>
									</div>
									<div class="mwm-journey-01__spacer flex-1" aria-hidden="true"></div>
								<?php else : ?>
									<div class="mwm-journey-01__spacer flex-1" aria-hidden="true"></div>
									<div class="mwm-journey-01__cell mwm-journey-01__cell--below flex flex-1 flex-col items-start justify-start">
										<p class="mwm-journey-01__text w-full max-w-[329px] pt-8 text-left font-body text-base leading-[1.3] text-white md:pt-[88px] md:text-2xl md:leading-normal">
											<?php echo esc_html( $text ); ?>
										</p>
									</div>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( '' !== trim( wp_strip_all_tags( $heading ) ) ) : ?>
			<div class="mwm-journey-01__heading-wrap flex w-full flex-col items-center justify-center max-w-[856px] mx-auto px-[20px] lg:px-[35px]">
				<h2 class="mwm-journey-01__heading w-full text-center font-heading text-[clamp(1.5rem,4vw,2.5rem)] leading-[1.2] text-inherit">
					<?php echo esc_html( $heading ); ?>
				</h2>
			</div>
		<?php endif; ?>
	</div>
</section>

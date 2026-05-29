<?php
/**
 * Server-side rendering for `zenyx/cards-01`.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading       = isset( $attributes['heading'] ) ? (string) $attributes['heading'] : '';
$button_text   = isset( $attributes['buttonText'] ) ? (string) $attributes['buttonText'] : '';
$button_url    = isset( $attributes['buttonUrl'] ) ? (string) $attributes['buttonUrl'] : '';
$opens_new     = ! empty( $attributes['opensInNewTab'] );
$center_header = ! empty( $attributes['centerHeader'] );
$card_text_size  = ( isset( $attributes['cardTextSize'] ) && 'large' === $attributes['cardTextSize'] ) ? 'large' : 'normal';
$card_title_index_class = 'large' === $card_text_size ? 'text-[24px] leading-normal' : 'text-[20px] leading-normal';
$card_body_class        = 'large' === $card_text_size ? 'text-[20px] leading-normal' : 'text-[16px] leading-normal';

$cards_raw = isset( $attributes['cards'] ) && is_array( $attributes['cards'] ) ? $attributes['cards'] : array();
$cards     = array();

foreach ( $cards_raw as $raw ) {
	if ( ! is_array( $raw ) ) {
		continue;
	}
	$cards[] = array(
		'title' => isset( $raw['title'] ) ? (string) $raw['title'] : '',
		'body'  => isset( $raw['body'] ) ? (string) $raw['body'] : '',
	);
}

$visible_card_count = 0;
foreach ( $cards as $c ) {
	$t = isset( $c['title'] ) ? (string) $c['title'] : '';
	$b = isset( $c['body'] ) ? (string) $c['body'] : '';
	if ( '' !== trim( wp_strip_all_tags( $t ) ) || '' !== trim( wp_strip_all_tags( $b ) ) ) {
		$visible_card_count++;
	}
}

$n_cols            = min( max( $visible_card_count, 1 ), 4 );
$grid_cols_classes = array(
	1 => 'grid-cols-1',
	2 => 'grid-cols-1 sm:grid-cols-2',
	3 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
	4 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
);
$grid_class        = 'mwm-cards-01__grid grid items-start gap-6 justify-items-center ' . $grid_cols_classes[ $n_cols ];

$header_classes = 'mwm-cards-01__header flex flex-col gap-6';
if ( $center_header ) {
	$header_classes .= ' w-full items-center text-center lg:flex-row lg:flex-wrap lg:items-center lg:justify-center lg:gap-8';
} else {
	$header_classes .= ' lg:flex-row lg:items-end lg:justify-between lg:gap-6';
}

$wrapper_classes = 'mwm-cards-01 w-full bg-protagonista py-[120px]';
if ( 'large' === $card_text_size ) {
	$wrapper_classes .= ' mwm-cards-01--text-large';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => $wrapper_classes,
	)
);

$heading_html = (string) $heading;
if ( '' !== trim( $heading_html ) ) {
	$heading_html = preg_replace( '#</p>\s*<p[^>]*>#i', '<br />', $heading_html );
	$heading_html = preg_replace( '#<p[^>]*>#i', '', $heading_html );
	$heading_html = preg_replace( '#</p>#i', '', $heading_html );
}
?>

<section data-light <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="mwm-max-1 flex flex-col gap-20">
		<div class="<?php echo esc_attr( $header_classes ); ?>">
			<?php if ( '' !== trim( wp_strip_all_tags( $heading ) ) ) : ?>
				<?php
				$heading_wrap_class = $center_header
					? 'mwm-cards-01__heading-wrap min-w-0 w-full max-w-[636px] mx-auto'
					: 'mwm-cards-01__heading-wrap min-w-0 flex-1 lg:max-w-[636px]';
				$heading_text_class = 'mwm-cards-01__heading text-[1.75rem] font-heading leading-[1.2] text-inherit md:text-3xl lg:text-[40px]';
				?>
				<div class="<?php echo esc_attr( $heading_wrap_class ); ?>">
					<h2 class="<?php echo esc_attr( $heading_text_class ); ?>">
						<?php echo wp_kses_post( $heading_html ); ?>
					</h2>
				</div>
			<?php endif; ?>

			<?php if ( '' !== trim( $button_text ) && '' !== trim( $button_url ) && function_exists( 'mwm_render_button' ) ) : ?>
				<?php
				$cta_wrap_class = $center_header
					? 'mwm-cards-01__cta-wrap flex w-full shrink-0 flex-col items-center justify-center lg:w-auto lg:max-w-[636px]'
					: 'mwm-cards-01__cta-wrap flex w-full shrink-0 flex-col items-stretch justify-end lg:w-auto lg:max-w-[636px] lg:items-end';
				?>
				<div class="<?php echo esc_attr( $cta_wrap_class ); ?>">
					<?php
					mwm_render_button(
						array(
							'text'          => $button_text,
							'url'           => $button_url,
							'variant'       => 'primary',
							'icon'          => 'arrow-right',
							'icon_position' => 'after',
							'size'          => 'md',
							'target'        => $opens_new ? '_blank' : '',
							'class'         => 'mwm-cards-01__cta',
						)
					);
					?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $visible_card_count > 0 ) : ?>
			<div class="<?php echo esc_attr( $grid_class ); ?>" role="list">
				<?php foreach ( $cards as $card_index => $card ) : ?>
					<?php
					$title      = $card['title'];
					$body       = $card['body'];
					$has_title = '' !== trim( wp_strip_all_tags( $title ) );
					$has_body  = '' !== trim( wp_strip_all_tags( $body ) );
					if ( ! $has_title && ! $has_body ) {
						continue;
					}
					$index_label = sprintf( '(%02d)', (int) $card_index + 1 );
					?>
					<div class="mwm-cards-01__card-wrap w-full h-full" role="listitem">
						<div class="mwm-cards-01__card outline-none transition-colors duration-300 focus-visible:ring-2 focus-visible:ring-white/80 focus-visible:ring-offset-2 focus-visible:ring-offset-protagonista" tabindex="0">
							<div class="mwm-cards-01__clip-media w-full min-h-0 shrink-0 overflow-hidden">
								<div class="mwm-cards-01__surface aspect-square relative flex min-h-0 w-full flex-col overflow-hidden">
								<div class="mwm-cards-01__copy relative flex flex-1 flex-col px-5 pt-5 <?php echo $has_title ? 'min-h-0' : 'min-h-20'; ?>">
										<?php if ( $has_title ) : ?>
											<div class="mwm-cards-01__title-wrap relative z-2 shrink-0">
												<div class="mwm-cards-01__title max-w-[266px] text-left font-heading text-protagonista <?php echo esc_attr( $card_title_index_class ); ?>">
													<?php echo wp_kses_post( $title ); ?>
												</div>
											</div>
										<?php endif; ?>

										<?php if ( $has_body ) : ?>
											<div class="mwm-cards-01__body-wrap">
												<div class="mwm-cards-01__body max-w-[266px] text-left text-neutral-light <?php echo esc_attr( $card_body_class ); ?>">
													<?php echo wp_kses_post( $body ); ?>
												</div>
											</div>
										<?php endif; ?>
									</div>

									<div class="mwm-cards-01__index mt-auto flex w-full shrink-0 px-5 pb-5 pt-2 font-heading">
										<p class="mwm-cards-01__index-text w-full max-w-[266px] text-left <?php echo esc_attr( $card_title_index_class ); ?>">
											<?php echo esc_html( $index_label ); ?>
										</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
/**
 * Server-side rendering for `zenyx/levels-01`.
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

$heading_html = $heading;
if ( '' !== trim( $heading_html ) ) {
	$heading_html = preg_replace( '#</p>\s*<p[^>]*>#i', '<br />', $heading_html );
	$heading_html = preg_replace( '#<p[^>]*>#i', '', $heading_html );
	$heading_html = preg_replace( '#</p>#i', '', $heading_html );
}

$levels_raw = isset( $attributes['levels'] ) && is_array( $attributes['levels'] ) ? $attributes['levels'] : array();
$levels     = array();

foreach ( $levels_raw as $raw ) {
	if ( ! is_array( $raw ) ) {
		continue;
	}
	$variant = isset( $raw['panelVariant'] ) ? (string) $raw['panelVariant'] : 'light';
	if ( ! in_array( $variant, array( 'light', 'dark', 'accent' ), true ) ) {
		$variant = 'light';
	}
	$levels[] = array(
		'panelVariant'   => $variant,
		'levelTitle'     => isset( $raw['levelTitle'] ) ? (string) $raw['levelTitle'] : '',
		'desdeLabel'     => isset( $raw['desdeLabel'] ) ? (string) $raw['desdeLabel'] : '',
		'price'          => isset( $raw['price'] ) ? (string) $raw['price'] : '',
		'paraQuienTitle' => isset( $raw['paraQuienTitle'] ) ? (string) $raw['paraQuienTitle'] : '',
		'paraQuienBody'  => isset( $raw['paraQuienBody'] ) ? (string) $raw['paraQuienBody'] : '',
		'situacionTitle' => isset( $raw['situacionTitle'] ) ? (string) $raw['situacionTitle'] : '',
		'situacionBody'  => isset( $raw['situacionBody'] ) ? (string) $raw['situacionBody'] : '',
		'objetivoTitle'  => isset( $raw['objetivoTitle'] ) ? (string) $raw['objetivoTitle'] : '',
		'objetivoBody'   => isset( $raw['objetivoBody'] ) ? (string) $raw['objetivoBody'] : '',
	);
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'mwm-levels-01 w-full bg-neutral-light pt-[96px] pb-[120px] md:py-[120px]',
	)
);
?>

<section data-dark <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="mwm-max-1 flex flex-col gap-20">
		<?php if ( '' !== trim( wp_strip_all_tags( $heading ) ) || '' !== trim( wp_strip_all_tags( $intro ) ) ) : ?>
			<header class="mwm-levels-01__header flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between lg:gap-6">
				<?php if ( '' !== trim( wp_strip_all_tags( $heading ) ) ) : ?>
					<div class="mwm-levels-01__heading-wrap min-w-0 flex-1 lg:max-w-[636px]">
						<h2 class="mwm-levels-01__heading text-[1.75rem] font-heading leading-[1.2] text-protagonista md:text-3xl lg:text-[40px]">
							<?php echo wp_kses_post( $heading_html ); ?>
						</h2>
					</div>
				<?php endif; ?>
				<?php if ( '' !== trim( wp_strip_all_tags( $intro ) ) ) : ?>
					<div class="mwm-levels-01__intro-wrap min-w-0 w-full shrink-0 lg:max-w-[416px] lg:pr-6">
						<div class="mwm-levels-01__intro text-lg leading-normal text-protagonista lg:text-xl">
							<?php echo wp_kses_post( $intro ); ?>
						</div>
					</div>
				<?php endif; ?>
			</header>
		<?php endif; ?>

		<?php if ( ! empty( $levels ) ) : ?>
			<div class="mwm-levels-01__rows flex flex-col gap-10">
				<?php foreach ( $levels as $index => $level ) : ?>
					<?php
					$surface_class = 'mwm-levels-01__panel-surface mwm-levels-01__panel-surface--' . $level['panelVariant'];
					$text_on_panel = ( 'light' === $level['panelVariant'] ) ? 'text-protagonista' : 'text-white';
					?>
					<div class="mwm-levels-01__row flex flex-col gap-6 bg-white lg:flex-row lg:items-stretch lg:gap-6" data-level-index="<?php echo esc_attr( (string) $index ); ?>">
						<div class="mwm-levels-01__panel-wrap flex min-h-0 min-w-0 flex-1 items-stretch p-6">
							<div class="<?php echo esc_attr( $surface_class . ' mwm-levels-01__panel-surface-inner relative flex min-h-[258px] w-full flex-col justify-between overflow-hidden' ); ?>">
								<?php if ( '' !== trim( wp_strip_all_tags( $level['levelTitle'] ) ) ) : ?>
									<div class="mwm-levels-01__panel-title relative z-20 px-5 pt-5">
										<div class="mwm-levels-01__panel-title-text text-xl font-medium uppercase leading-normal <?php echo esc_attr( $text_on_panel ); ?>">
											<?php echo wp_kses_post( $level['levelTitle'] ); ?>
										</div>
									</div>
								<?php endif; ?>
								<div class="mwm-levels-01__panel-price relative z-20 flex flex-col gap-1 px-5 pb-5 pr-[86px] pt-5">
									<?php if ( '' !== trim( wp_strip_all_tags( $level['desdeLabel'] ) ) ) : ?>
										<p class="mwm-levels-01__panel-desde text-sm leading-normal <?php echo esc_attr( $text_on_panel ); ?>">
											<?php echo wp_kses_post( $level['desdeLabel'] ); ?>
										</p>
									<?php endif; ?>
									<?php if ( '' !== trim( wp_strip_all_tags( $level['price'] ) ) ) : ?>
										<p class="mwm-levels-01__panel-amount text-xl font-medium leading-normal <?php echo esc_attr( $text_on_panel ); ?>">
											<?php echo wp_kses_post( $level['price'] ); ?>
										</p>
									<?php endif; ?>
								</div>
							</div>
						</div>

						<div class="mwm-levels-01__col mwm-levels-01__col--lead flex min-h-0 min-w-0 flex-1 flex-col gap-3 px-6 pb-6 pt-0 lg:px-0 lg:pb-6 lg:pt-6">
							<?php if ( '' !== trim( wp_strip_all_tags( $level['paraQuienTitle'] ) ) ) : ?>
								<p class="mwm-levels-01__label text-base font-medium leading-normal text-acento">
									<?php echo wp_kses_post( $level['paraQuienTitle'] ); ?>
								</p>
							<?php endif; ?>
							<?php if ( '' !== trim( wp_strip_all_tags( $level['paraQuienBody'] ) ) ) : ?>
								<div class="mwm-levels-01__lead text-xl font-medium leading-normal text-protagonista">
									<?php echo wp_kses_post( $level['paraQuienBody'] ); ?>
								</div>
							<?php endif; ?>
						</div>

						<div class="mwm-levels-01__col flex min-h-0 min-w-0 flex-1 flex-col gap-3 px-6 pb-6 pt-0 lg:px-0 lg:pb-6 lg:pt-6">
							<?php if ( '' !== trim( wp_strip_all_tags( $level['situacionTitle'] ) ) ) : ?>
								<p class="mwm-levels-01__label text-base font-medium leading-normal text-acento">
									<?php echo wp_kses_post( $level['situacionTitle'] ); ?>
								</p>
							<?php endif; ?>
							<?php if ( '' !== trim( wp_strip_all_tags( $level['situacionBody'] ) ) ) : ?>
								<div class="mwm-levels-01__body text-base leading-normal text-protagonista">
									<?php echo wp_kses_post( $level['situacionBody'] ); ?>
								</div>
							<?php endif; ?>
						</div>

						<div class="mwm-levels-01__col flex min-h-0 min-w-0 flex-1 flex-col gap-3 px-6 pb-6 pt-0 lg:pr-6 lg:pt-6">
							<?php if ( '' !== trim( wp_strip_all_tags( $level['objetivoTitle'] ) ) ) : ?>
								<p class="mwm-levels-01__label text-base font-medium leading-normal text-acento">
									<?php echo wp_kses_post( $level['objetivoTitle'] ); ?>
								</p>
							<?php endif; ?>
							<?php if ( '' !== trim( wp_strip_all_tags( $level['objetivoBody'] ) ) ) : ?>
								<div class="mwm-levels-01__body text-base leading-normal text-protagonista">
									<?php echo wp_kses_post( $level['objetivoBody'] ); ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

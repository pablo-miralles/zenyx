<?php
/**
 * Server-side rendering for `zenyx/accordion-01`.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = isset( $attributes['heading'] ) ? (string) $attributes['heading'] : '';

$items_raw = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : array();
$items     = array();

foreach ( $items_raw as $raw ) {
	if ( ! is_array( $raw ) ) {
		continue;
	}
	$question = isset( $raw['question'] ) ? (string) $raw['question'] : '';
	$paras    = array();
	if ( isset( $raw['paragraphs'] ) && is_array( $raw['paragraphs'] ) ) {
		foreach ( $raw['paragraphs'] as $p ) {
			$paras[] = is_string( $p ) ? $p : '';
		}
	}
	$items[] = array(
		'question'   => $question,
		'paragraphs' => $paras,
	);
}

$has_heading = '' !== trim( wp_strip_all_tags( $heading ) );

$visible_items = array();
foreach ( $items as $item ) {
	$q = isset( $item['question'] ) ? (string) $item['question'] : '';
	$paras = isset( $item['paragraphs'] ) && is_array( $item['paragraphs'] ) ? $item['paragraphs'] : array();
	$has_paras = false;
	foreach ( $paras as $p ) {
		if ( '' !== trim( wp_strip_all_tags( (string) $p ) ) ) {
			$has_paras = true;
			break;
		}
	}
	if ( '' !== trim( wp_strip_all_tags( $q ) ) || $has_paras ) {
		$visible_items[] = $item;
	}
}

if ( ! $has_heading && empty( $visible_items ) ) {
	return;
}

$block_id = wp_unique_id( 'mwm-acc-' );

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'mwm-accordion-01 w-full overflow-hidden bg-protagonista py-[120px] text-white',
	)
);
?>

<section data-light <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<div class="mwm-max-1 flex flex-col gap-20">
		<?php if ( $has_heading ) : ?>
			<div class="mwm-accordion-01__heading-wrap flex justify-center">
				<h2 class="mwm-accordion-01__heading max-w-[636px] text-center font-heading text-[32px] leading-[1.2] text-white md:text-[40px]">
					<?php echo wp_kses_post( $heading ); ?>
				</h2>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $visible_items ) ) : ?>
			<div class="mwm-accordion-01__list mx-auto flex w-full max-w-[856px] flex-col gap-6">
				<?php
				foreach ( $visible_items as $index => $item ) :
					$question   = isset( $item['question'] ) ? (string) $item['question'] : '';
					$paragraphs = isset( $item['paragraphs'] ) && is_array( $item['paragraphs'] ) ? $item['paragraphs'] : array();
					$btn_id     = $block_id . '-btn-' . $index;
					$panel_id   = $block_id . '-panel-' . $index;
					?>
					<div
						class="mwm-accordion-01__item border-b border-neutral-light pb-[18px]"
						data-mwm-accordion-item
					>
						<div class="mwm-accordion-01__trigger-row flex w-full items-center gap-3">
							<button
								type="button"
								class="mwm-accordion-01__trigger cursor-pointer group flex w-full min-w-0 flex-1 items-center gap-3 text-left"
								id="<?php echo esc_attr( $btn_id ); ?>"
								aria-expanded="false"
								aria-controls="<?php echo esc_attr( $panel_id ); ?>"
								data-mwm-accordion-trigger
							>
								<h3 class="mwm-accordion-01__question min-w-0 flex-1 text-xl !font-body !font-medium leading-[1.2] text-white md:text-[20px]">
									<?php echo wp_kses_post( $question ); ?>
								</h3>
								<span class="mwm-accordion-01__icon-wrap relative h-6 w-6 shrink-0 text-[#FE7756]" aria-hidden="true">
									<svg
										class="mwm-accordion-01__icon-svg block h-6 w-6"
										width="24"
										height="24"
										viewBox="0 0 24 24"
										fill="none"
										xmlns="http://www.w3.org/2000/svg"
										focusable="false"
									>
										<rect x="1.5" y="1.5" width="21" height="21" stroke="currentColor" stroke-width="1" />
										<path
											class="mwm-accordion-01__icon-h"
											d="M18 12H6"
											stroke="currentColor"
											stroke-linejoin="round"
										/>
										<path
											class="mwm-accordion-01__icon-v"
											d="M12.0049 18.0059L12.0049 6.00586"
											stroke="currentColor"
											stroke-linejoin="round"
										/>
									</svg>
								</span>
							</button>
						</div>

						<div
							class="mwm-accordion-01__panel"
							id="<?php echo esc_attr( $panel_id ); ?>"
							role="region"
							aria-labelledby="<?php echo esc_attr( $btn_id ); ?>"
							aria-hidden="true"
							inert
							data-mwm-accordion-panel
						>
							<div class="mwm-accordion-01__panel-inner pt-3 pr-9">
								<?php
								foreach ( $paragraphs as $p ) {
									$p = (string) $p;
									if ( '' === trim( wp_strip_all_tags( $p ) ) ) {
										continue;
									}
									?>
									<div class="mwm-accordion-01__paragraph mb-3 text-base font-normal leading-[1.2] text-neutral-light last:mb-0">
										<?php echo wp_kses_post( $p ); ?>
									</div>
									<?php
								}
								?>
							</div>
						</div>
					</div>
					<?php
				endforeach;
				?>
			</div>
		<?php endif; ?>
	</div>
</section>

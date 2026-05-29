<?php
/**
 * Server-side rendering for `zenyx/podcast-info-01`.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$left_kicker  = isset( $attributes['leftKicker'] ) ? (string) $attributes['leftKicker'] : '';
$right_kicker = isset( $attributes['rightKicker'] ) ? (string) $attributes['rightKicker'] : '';

$empty_row = array( 'text' => '' );

$raw_left   = isset( $attributes['leftItems'] ) && is_array( $attributes['leftItems'] ) ? array_values( $attributes['leftItems'] ) : array();
$raw_topics = isset( $attributes['topicItems'] ) && is_array( $attributes['topicItems'] ) ? array_values( $attributes['topicItems'] ) : array();

$left_items   = array();
$topic_items  = array();

foreach ( $raw_left as $item ) {
	$merged = array_merge( $empty_row, is_array( $item ) ? $item : array() );
	$left_items[] = array(
		'text' => isset( $merged['text'] ) ? (string) $merged['text'] : '',
	);
}

foreach ( $raw_topics as $item ) {
	$merged = array_merge( $empty_row, is_array( $item ) ? $item : array() );
	$topic_items[] = array(
		'text' => isset( $merged['text'] ) ? (string) $merged['text'] : '',
	);
}

$has_left_kicker  = '' !== trim( wp_strip_all_tags( $left_kicker ) );
$has_right_kicker = '' !== trim( wp_strip_all_tags( $right_kicker ) );

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'mwm-podcast-info-01 w-full bg-neutral-light py-[120px]',
	)
);
?>

<section data-dark <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="mwm-max-1 flex flex-col gap-12 lg:flex-row lg:justify-between lg:gap-8">
		<div class="mwm-podcast-info-01__left flex min-w-0 max-w-[636px] flex-1 flex-col justify-between gap-12">
			<?php if ( $has_left_kicker ) : ?>
				<div class="mwm-podcast-info-01__left-kicker-wrap flex flex-col gap-3">
					<p class="mwm-podcast-info-01__left-kicker text-left text-xl leading-normal text-acento">
						<?php echo wp_kses_post( $left_kicker ); ?>
					</p>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $left_items ) ) : ?>
				<div class="mwm-podcast-info-01__grid grid grid-cols-1 gap-6 sm:grid-cols-2">
					<?php foreach ( $left_items as $row ) : ?>
						<?php
						$cell_text = isset( $row['text'] ) ? (string) $row['text'] : '';
						if ( '' === trim( wp_strip_all_tags( $cell_text ) ) ) {
							continue;
						}
						$clip_id = wp_unique_id( 'mwm-podcast-info-01-check-' );
						?>
						<div class="mwm-podcast-info-01__cell flex flex-col gap-5 text-protagonista">
							<svg
								class="mwm-podcast-info-01__check-icon h-6 w-6 shrink-0"
								width="24"
								height="24"
								viewBox="0 0 24 24"
								fill="none"
								xmlns="http://www.w3.org/2000/svg"
								aria-hidden="true"
								focusable="false"
							>
								<g clip-path="url(#<?php echo esc_attr( $clip_id ); ?>)">
									<path
										d="M6.75 9.00002L10.044 13.611C10.1796 13.8009 10.3569 13.9571 10.5623 14.0677C10.7677 14.1783 10.9958 14.2403 11.2289 14.249C11.462 14.2577 11.694 14.2128 11.9071 14.1178C12.1202 14.0228 12.3086 13.8802 12.458 13.701L23.25 0.749023"
										stroke="currentColor"
										stroke-width="1.5"
										stroke-linecap="round"
										stroke-linejoin="round"
									/>
									<path
										d="M21.75 10.5V20.25C21.75 21.0456 21.4339 21.8087 20.8713 22.3713C20.3087 22.9339 19.5456 23.25 18.75 23.25H3.75C2.95435 23.25 2.19129 22.9339 1.62868 22.3713C1.06607 21.8087 0.75 21.0456 0.75 20.25V5.25C0.75 4.45435 1.06607 3.69129 1.62868 3.12868C2.19129 2.56607 2.95435 2.25 3.75 2.25H16.5"
										stroke="currentColor"
										stroke-width="1.5"
										stroke-linecap="round"
										stroke-linejoin="round"
									/>
								</g>
								<defs>
									<clipPath id="<?php echo esc_attr( $clip_id ); ?>">
										<rect width="24" height="24" fill="white" />
									</clipPath>
								</defs>
							</svg>
							<div class="mwm-podcast-info-01__cell-text text-left text-2xl leading-normal text-protagonista">
								<?php echo wp_kses_post( $cell_text ); ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="mwm-podcast-info-01__card relative flex w-full justify-between min-h-[416px] max-w-[416px] shrink-0 flex-col gap-6 overflow-hidden bg-white p-6">
			<?php if ( $has_right_kicker ) : ?>
				<div class="mwm-podcast-info-01__card-header relative z-10 flex flex-col gap-3">
					<p class="mwm-podcast-info-01__right-kicker text-left text-xl leading-normal text-acento">
						<?php echo wp_kses_post( $right_kicker ); ?>
					</p>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $topic_items ) ) : ?>
				<div class="mwm-podcast-info-01__topics relative z-10 flex flex-col gap-6">
					<?php foreach ( $topic_items as $row ) : ?>
						<?php
						$t_text = isset( $row['text'] ) ? (string) $row['text'] : '';
						if ( '' === trim( wp_strip_all_tags( $t_text ) ) ) {
							continue;
						}
						?>
						<div class="mwm-podcast-info-01__topic-row flex flex-col gap-5">
							<div class="mwm-podcast-info-01__topic-text text-left text-2xl leading-normal text-protagonista">
								<?php echo wp_kses_post( $t_text ); ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<svg
				class="mwm-podcast-info-01__corner pointer-events-none absolute bottom-0 right-0 h-[172px] w-[172px] shrink-0"
				width="172"
				height="172"
				viewBox="0 0 172 172"
				fill="none"
				xmlns="http://www.w3.org/2000/svg"
				aria-hidden="true"
				focusable="false"
				preserveAspectRatio="xMidYMid meet"
			>
				<path d="M0 172L172 0V172H0Z" fill="var(--color-neutral-light)" />
			</svg>
		</div>
	</div>
</section>

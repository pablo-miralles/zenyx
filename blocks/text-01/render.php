<?php
/**
 * Server-side rendering for `zenyx/text-01`.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = isset( $attributes['heading'] ) ? (string) $attributes['heading'] : '';
$lead    = isset( $attributes['lead'] ) ? (string) $attributes['lead'] : '';

$default_columns = array(
	array(
		'title' => 'Construcción',
		'body'  => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean nec pulvinar urna, id tincidunt risus. Integer luctus scelerisque nisi nec maximus.',
	),
	array(
		'title' => 'Crecimiento',
		'body'  => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean nec pulvinar urna, id tincidunt risus. Integer luctus scelerisque nisi nec maximus.',
	),
	array(
		'title' => 'Expansión',
		'body'  => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean nec pulvinar urna, id tincidunt risus. Integer luctus scelerisque nisi nec maximus.',
	),
);

$raw_columns = isset( $attributes['columns'] ) && is_array( $attributes['columns'] ) ? array_values( $attributes['columns'] ) : array();
$columns     = array();

for ( $i = 0; $i < 3; $i++ ) {
	$item        = isset( $raw_columns[ $i ] ) && is_array( $raw_columns[ $i ] ) ? $raw_columns[ $i ] : array();
	$def         = $default_columns[ $i ];
	$columns[] = array(
		'title' => isset( $item['title'] ) ? (string) $item['title'] : (string) $def['title'],
		'body'  => isset( $item['body'] ) ? (string) $item['body'] : (string) $def['body'],
	);
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'mwm-text-01 w-full bg-[#083b51] py-[120px]',
	)
);
?>

<section data-light <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="mwm-max-1 flex flex-col gap-16 lg:gap-[120px]">
		<?php if ( '' !== trim( wp_strip_all_tags( $heading ) ) || '' !== trim( wp_strip_all_tags( $lead ) ) ) : ?>
			<div class="mwm-text-01__intro flex flex-col items-center gap-10">
				<?php if ( '' !== trim( wp_strip_all_tags( $heading ) ) ) : ?>
					<div class="mwm-text-01__heading-wrap w-full max-w-[648px]">
						<h2 class="mwm-text-01__heading text-center text-[2rem] font-heading leading-[1.2] text-neutral-light md:text-4xl">
							<?php echo wp_kses_post( $heading ); ?>
						</h2>
					</div>
				<?php endif; ?>
				<?php if ( '' !== trim( wp_strip_all_tags( $lead ) ) ) : ?>
					<div class="mwm-text-01__lead-wrap w-full max-w-[416px] text-center text-lg leading-[1.3] text-acento md:text-xl">
						<div class="mwm-text-01__lead">
							<?php echo wp_kses_post( $lead ); ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="mwm-text-01__columns grid grid-cols-1 gap-6 md:grid-cols-3 md:gap-6">
			<?php foreach ( $columns as $col ) : ?>
				<?php
				$col_title = $col['title'];
				$col_body  = $col['body'];
				?>
				<div class="mwm-text-01__column flex flex-col items-center gap-5 px-6">
					<?php if ( '' !== trim( wp_strip_all_tags( $col_title ) ) ) : ?>
						<div class="mwm-text-01__column-title-wrap w-full max-w-[368px]">
							<h3 class="mwm-text-01__column-title text-center text-2xl font-medium leading-tight text-white">
								<?php echo wp_kses_post( $col_title ); ?>
							</h3>
						</div>
					<?php endif; ?>
					<?php if ( '' !== trim( wp_strip_all_tags( $col_body ) ) ) : ?>
						<div class="mwm-text-01__column-body-wrap w-full max-w-[368px] text-center text-base leading-normal text-neutral-light">
							<div class="mwm-text-01__column-body">
								<?php echo wp_kses_post( $col_body ); ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php
/**
 * Listado del archive de casos de éxito: filas de 3 cards.
 *
 * @package zenyx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! have_posts() ) {
	?>
	<section
		id="casos"
		data-dark
		class="w-full bg-neutral-light"
		aria-label="<?php esc_attr_e( 'Casos de exito', THEME_TEXT_DOMAIN ); ?>"
	>
		<div class="mwm-max-1 py-16 lg:py-[120px]">
			<p class="text-center font-body text-body-l text-protagonista/80">
				<?php esc_html_e( 'No hay casos de exito publicados todavia.', THEME_TEXT_DOMAIN ); ?>
			</p>
		</div>
	</section>
	<?php
	return;
}

$post_ids = array();
while ( have_posts() ) {
	the_post();
	$post_ids[] = get_the_ID();
}

$rows = array_chunk( $post_ids, 3 );
?>
<section
	id="casos"
	data-dark
	class="w-full bg-neutral-light"
	aria-label="<?php esc_attr_e( 'Casos de exito', THEME_TEXT_DOMAIN ); ?>"
>
	<div class="mwm-max-1 flex flex-col gap-12 py-16 lg:gap-10 lg:py-[120px]">
		<?php
		foreach ( $rows as $row_index => $row_ids ) :
			$cards_in_row     = count( $row_ids );
			$default_active_i = mwm_casos_row_default_active_index( $row_index, $cards_in_row );
			?>
			<div
				class="mwm-card-caso-row flex flex-col gap-6 lg:flex-row lg:items-stretch lg:gap-6"
				data-mwm-caso-row
				data-mwm-caso-row-index="<?php echo esc_attr( (string) (int) $row_index ); ?>"
			>
				<?php
				foreach ( $row_ids as $index => $post_id ) {
					get_template_part(
						'template-parts/card-caso',
						null,
						array(
							'post_id'   => (int) $post_id,
							'is_active' => ( (int) $index === (int) $default_active_i ),
						)
					);
				}
				?>
			</div>
		<?php endforeach; ?>
	</div>
</section>

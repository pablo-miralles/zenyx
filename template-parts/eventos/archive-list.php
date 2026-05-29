<?php
/**
 * Listado del archive de eventos: filas alternas (media / texto).
 *
 * @package zenyx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$list_title = get_option( 'mwm_eventos_archive_list_title' );
$list_title = is_string( $list_title ) ? trim( $list_title ) : '';
if ( '' === $list_title ) {
	$list_title = __( 'Últimos eventos Zenyx', THEME_TEXT_DOMAIN );
}

if ( ! have_posts() ) {
	?>
	<section
		id="eventos-lista"
		data-light
		class="w-full bg-protagonista"
		aria-label="<?php esc_attr_e( 'Eventos', THEME_TEXT_DOMAIN ); ?>"
	>
		<div class="mwm-max-1 flex flex-col gap-12 py-16 lg:gap-[60px] lg:py-[120px]">
			<h2 class="max-w-[636px] font-heading text-[1.75rem] leading-[1.2] text-neutral-light md:text-3xl lg:text-[40px]">
				<?php echo esc_html( $list_title ); ?>
			</h2>
			<p class="text-center font-body text-body-l text-neutral-light/80">
				<?php esc_html_e( 'No hay eventos publicados todavía.', THEME_TEXT_DOMAIN ); ?>
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
?>
<section
	id="eventos-lista"
	data-light
	class="w-full bg-protagonista"
	aria-label="<?php esc_attr_e( 'Eventos', THEME_TEXT_DOMAIN ); ?>"
>
	<div class="mwm-max-1 flex flex-col gap-12 py-16 lg:gap-[60px] lg:py-[120px]">
		<h2 class="max-w-[636px] font-heading text-[1.75rem] leading-[1.2] text-neutral-light md:text-3xl lg:text-[40px]">
			<?php echo esc_html( $list_title ); ?>
		</h2>

		<div class="flex flex-col gap-6">
			<?php
			foreach ( $post_ids as $index => $post_id ) {
				get_template_part(
					'template-parts/eventos/card-evento',
					null,
					array(
						'post_id'     => (int) $post_id,
						'media_first' => ( 0 === ( (int) $index % 2 ) ),
					)
				);
			}
			?>
		</div>

		<?php
		global $wp_query;
		if ( isset( $wp_query->max_num_pages ) && (int) $wp_query->max_num_pages > 1 ) {
			the_posts_pagination(
				array(
					'mid_size'  => 2,
					'prev_text' => __( 'Anterior', THEME_TEXT_DOMAIN ),
					'next_text' => __( 'Siguiente', THEME_TEXT_DOMAIN ),
				)
			);
		}
		?>
	</div>
</section>

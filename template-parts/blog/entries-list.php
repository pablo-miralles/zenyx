<?php
/**
 * Blog entries section: category filters, grid, load more.
 *
 * @package zenyx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$categories = get_categories(
	array(
		'taxonomy'   => 'category',
		'hide_empty' => true,
	)
);

$initial_cat_ids = mwm_blog_entries_context_category_ids();
$initial_query   = new WP_Query( mwm_blog_entries_query_args( 1, $initial_cat_ids ) );
$max_pages       = (int) $initial_query->max_num_pages;
$has_more        = $max_pages > 1;
?>
<section
	data-light
	id="articulos"
	class="w-full bg-protagonista"
	aria-label="<?php esc_attr_e( 'Articulos del blog', THEME_TEXT_DOMAIN ); ?>"
>
	<div
		id="mwm-entries-root"
		class="mwm-max-1 flex flex-col gap-12 py-16 lg:gap-20 lg:py-[120px]"
		data-max-pages="<?php echo esc_attr( (string) $max_pages ); ?>"
		data-posts-per-page="<?php echo esc_attr( (string) mwm_blog_entries_posts_per_page() ); ?>"
		data-initial-categories="<?php echo esc_attr( wp_json_encode( array_values( array_map( 'absint', $initial_cat_ids ) ) ) ); ?>"
	>
		<div class="flex flex-wrap items-start gap-6" role="group" aria-label="<?php esc_attr_e( 'Filtrar por categoria', THEME_TEXT_DOMAIN ); ?>">
			<button
				type="button"
				id="mwm-entries-filter-all"
				class="mwm-entries-filter mwm-entries-filter--all font-heading text-body-m leading-[1.2] text-acento rounded-sm px-1 py-1 underline decoration-transparent underline-offset-4 transition hover:decoration-acento focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-white"
				aria-pressed="<?php echo empty( $initial_cat_ids ) ? 'true' : 'false'; ?>"
				data-mwm-all="1"
			>
				<?php esc_html_e( 'Todas las categorias', THEME_TEXT_DOMAIN ); ?>
			</button>

			<?php foreach ( $categories as $cat ) : ?>
				<button
					type="button"
					class="mwm-entries-filter cursor-pointer font-heading text-body-m leading-[1.2] text-acento rounded-sm px-1 py-1 underline decoration-transparent underline-offset-4 transition hover:decoration-acento focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-white"
					aria-pressed="<?php echo in_array( (int) $cat->term_id, $initial_cat_ids, true ) ? 'true' : 'false'; ?>"
					data-cat-id="<?php echo esc_attr( (string) (int) $cat->term_id ); ?>"
				>
					<?php echo esc_html( '#' . $cat->name ); ?>
				</button>
			<?php endforeach; ?>
		</div>

		<div id="mwm-entries-status" class="sr-only" aria-live="polite" aria-atomic="true"></div>

		<div
			id="mwm-entries-grid"
			class="grid w-full grid-cols-1 justify-items-end gap-x-6 gap-y-12 md:grid-cols-2 md:justify-items-stretch lg:gap-y-[72px] xl:grid-cols-3"
		>
			<?php
			if ( $initial_query->have_posts() ) {
				echo mwm_blog_entries_render_cards_html( $initial_query ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- card HTML from template parts.
			} else {
				echo '<p class="col-span-full font-body text-center text-white/80">' . esc_html__( 'No hay entradas todavia.', THEME_TEXT_DOMAIN ) . '</p>';
			}
			?>
		</div>

		<div class="flex justify-center pt-4">
			<button
				type="button"
				id="mwm-entries-load-more"
				class="mwm-btn mwm-btn--header-primary mwm-btn--md font-heading disabled:pointer-events-none disabled:opacity-50"
				aria-busy="false"
				<?php echo $has_more ? '' : ' hidden'; ?>
			>
				<span class="mwm-entries-load-more__label"><?php esc_html_e( 'Cargar mas', THEME_TEXT_DOMAIN ); ?></span>
				<span class="mwm-entries-load-more__loading hidden" aria-hidden="true"><?php esc_html_e( 'Cargando...', THEME_TEXT_DOMAIN ); ?></span>
			</button>
		</div>
	</div>
</section>

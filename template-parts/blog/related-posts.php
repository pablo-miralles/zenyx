<?php
/**
 * Single post: related posts section ("Artículos más destacados").
 *
 * @package zenyx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = get_the_ID();
if ( ! $post_id ) {
	return;
}

$related_query = mwm_get_related_posts_query( $post_id, 3 );

if ( ! $related_query instanceof WP_Query || (int) $related_query->found_posts < 1 ) {
	return;
}

$heading_id = 'mwm-related-posts-heading';
?>
<section
	data-light
	class="w-full bg-protagonista"
	aria-labelledby="<?php echo esc_attr( $heading_id ); ?>"
>
	<div class="mwm-max-1 flex flex-col gap-20 px-4 py-[120px] sm:px-6 lg:px-[35px]">
		<h2
			id="<?php echo esc_attr( $heading_id ); ?>"
			class="max-w-[636px] font-heading text-display-m text-neutral-light"
		>
			<?php esc_html_e( 'Artículos más destacados', THEME_TEXT_DOMAIN ); ?>
		</h2>

		<div
			class="grid w-full grid-cols-1 justify-items-end gap-x-6 gap-y-12 md:grid-cols-2 md:justify-items-stretch lg:gap-y-[72px] xl:grid-cols-3"
		>
			<?php
			echo mwm_blog_entries_render_cards_html( $related_query ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- card HTML from template parts.
			?>
		</div>
	</div>
</section>

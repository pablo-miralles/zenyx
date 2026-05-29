<?php
/**
 * Post card for blog grid (entries-list maquette).
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

$cats     = get_the_category( $post_id );
$cat_name = '';
$cat_link = '';
if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
	$first     = $cats[0];
	$cat_name  = $first->name;
	$term_link = get_term_link( $first );
	$cat_link  = is_wp_error( $term_link ) ? '' : $term_link;
}

$thumb_url = '';
if ( has_post_thumbnail( $post_id ) ) {
	$thumb_url = get_the_post_thumbnail_url( $post_id, 'large' );
}

$media_classes = 'mwm-card-post__media relative z-0 h-[min(19.25rem,85vw)] w-[min(19.125rem,100%)] shrink-0 bg-blend-luminosity bg-cover bg-center bg-no-repeat';
if ( '' !== $thumb_url ) {
	$media_classes .= ' bg-neutral-light transition-colors duration-300 ease-out group-hover:bg-transparent group-focus-within:bg-transparent';
}
?>
<article <?php post_class( 'mwm-card-post group relative flex w-full max-w-[26rem] flex-col items-end gap-6' ); ?>>
	<div class="flex w-full items-center md:pr-[110px]">
		<div
			class="<?php echo esc_attr( $media_classes ); ?>"
			<?php if ( '' !== $thumb_url ) : ?>
				style="background-image:url(<?php echo esc_url( $thumb_url ); ?>);"
				aria-hidden="true"
			<?php endif; ?>
		>
			<?php if ( '' === $thumb_url ) : ?>
				<div class="flex h-full w-full items-center justify-center bg-neutral-light font-body text-sm text-protagonista/50" aria-hidden="true">
					<?php esc_html_e( 'Sin imagen', THEME_TEXT_DOMAIN ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<div class="flex w-full flex-col items-start justify-end gap-6 self-stretch pr-0 md:pr-10">
		<div class="flex w-full flex-col items-start gap-5 self-stretch">
			<?php if ( '' !== $cat_name && '' !== $cat_link ) : ?>
				<div class="flex max-w-[106px] items-center gap-3">
					<a
						class="relative z-2 font-heading text-body-m leading-[1.2] text-neutral-light line-clamp-2 hover:text-acento-hover no-underline transition duration-300 ease-out"
						href="<?php echo esc_url( $cat_link ); ?>"
					>
						<?php echo esc_html( '#' . $cat_name ); ?>
					</a>
				</div>
			<?php elseif ( '' !== $cat_name ) : ?>
				<div class="flex max-w-[106px] items-center gap-3">
					<span class="font-heading text-body-m leading-[1.2] text-neutral-light line-clamp-2">
						<?php echo esc_html( '#' . $cat_name ); ?>
					</span>
				</div>
			<?php endif; ?>

			<div class="flex w-full items-start gap-2.5">
				<h2 class="min-h-[calc(3*1.2*var(--text-body-l))] w-full font-body text-body-l leading-[1.2] text-white">
					<a
						class="mwm-card-post__link-stretch no-underline block w-full"
						href="<?php echo esc_url( get_permalink() ); ?>"
					>
						<span class="line-clamp-3"><?php echo esc_html( get_the_title() ); ?></span>
					</a>
				</h2>
			</div>
		</div>

		<div class="pointer-events-none flex w-full items-start">
			<span
				class="mwm-card-post__cta inline-flex items-center gap-3 font-heading text-body-m leading-none text-acento"
				aria-hidden="true"
			>
				<span><?php esc_html_e( 'Leer artículo ', THEME_TEXT_DOMAIN ); ?></span>
			</span>
		</div>
	</div>
</article>

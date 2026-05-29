<?php
/**
 * Single blog post template.
 *
 * @package zenyx
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>

	<main class="mwm-main-container">
		<?php get_template_part( 'template-parts/blog/hero-single' ); ?>

		<section
			data-dark
			class="w-full bg-neutral-light"
			aria-label="<?php esc_attr_e( 'Contenido del articulo', THEME_TEXT_DOMAIN ); ?>"
		>
			<article <?php post_class( 'mwm-max-1 px-4 py-[120px] sm:px-6 lg:px-[35px]' ); ?> id="post-<?php the_ID(); ?>">
				<div class="mx-auto max-w-[856px]">
					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="mwm-single-featured w-full overflow-hidden">
							<?php
							the_post_thumbnail(
								'large',
								array(
									'class'   => 'h-auto w-full max-h-[min(481px,70vh)] object-cover',
									'loading' => 'eager',
									'decoding' => 'async',
								)
							);
							?>
						</figure>
					<?php endif; ?>

					<div class="mwm-single-content entry-content pt-[40px] lg:pt-[80px] font-body text-protagonista [&_a]:text-acento [&_a]:underline hover:[&_a]:text-acento-hover">
						<?php
						the_content();

						wp_link_pages(
							array(
								'before' => '<nav class="post-nav-links mt-8 font-body text-sm" aria-label="' . esc_attr__( 'Paginas del articulo', THEME_TEXT_DOMAIN ) . '"><span class="font-medium">' . esc_html__( 'Paginas:', THEME_TEXT_DOMAIN ) . '</span> ',
								'after'  => '</nav>',
							)
						);
						?>
					</div>

					<div class="mt-20 flex flex-col items-start gap-2">
						<span id="mwm-single-copy-status" class="sr-only" role="status" aria-live="polite" aria-atomic="true"></span>
						<?php
						mwm_render_button(
							array(
								'text'            => __( 'Copiar articulo', THEME_TEXT_DOMAIN ),
								'as'              => 'button',
								'variant'         => 'dark',
								'icon'            => 'copy',
								'icon_position'   => 'after',
								'size'            => 'md',
								'type'            => 'button',
								'class'           => 'mwm-single-copy-btn',
								'aria_label'      => __( 'Copiar enlace del articulo al portapapeles', THEME_TEXT_DOMAIN ),
								'data_attributes' => array(
									'copy-url' => get_permalink(),
								),
							)
						);
						?>
					</div>
				</div>
			</article>
		</section>

		<?php get_template_part( 'template-parts/blog/related-posts' ); ?>

		<?php get_template_part( 'template-parts/blog/cta-home' ); ?>

	</main>

	<?php
endwhile;

get_footer();

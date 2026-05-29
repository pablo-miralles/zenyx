<?php
/**
 * Template Name: Legal
 *
 * Plantilla para páginas de texto legal (política de privacidad, aviso legal, cookies, etc.).
 *
 * @package zenyx
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>

<main class="mwm-main-container pt-header">
	<section
		data-dark
		class="w-full bg-neutral-light"
		aria-label="<?php esc_attr_e( 'Contenido legal', THEME_TEXT_DOMAIN ); ?>"
	>
		<article <?php post_class( 'mwm-legal mwm-max-1 py-[60px] md:py-[100px] lg:py-[120px]' ); ?> id="post-<?php the_ID(); ?>">
			<div class="mx-auto flex max-w-[856px] flex-col gap-8">

				<header class="flex flex-col gap-4">
					<h1 class="font-heading text-[32px] font-light leading-tight text-protagonista md:text-[48px]">
						<?php the_title(); ?>
					</h1>
					<?php
					$modified = get_the_modified_date( 'd/m/Y' );
					if ( $modified ) :
						?>
					<p class="text-sm font-light text-protagonista">
						<?php
						printf(
							/* translators: %s: date */
							esc_html__( 'Última actualización: %s', THEME_TEXT_DOMAIN ),
							esc_html( $modified )
						);
						?>
					</p>
					<?php endif; ?>
				</header>

				<div class="mwm-legal-content mwm-single-content entry-content font-body text-protagonista [&_a]:text-acento [&_a]:underline hover:[&_a]:text-acento-hover">
					<?php the_content(); ?>
				</div>

			</div>
		</article>
	</section>
</main>

	<?php
endwhile;

get_footer();

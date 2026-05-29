<?php
/**
 * Single template for Casos de exito CPT.
 *
 * @package zenyx
 */

get_header();
?>

<main class="mwm-main-container">
	<?php the_content(); ?>
	<?php get_template_part( 'template-parts/casos-exito/cta' ); ?>
</main>

<?php
get_footer();

<?php
/**
 * Archive template for Casos de exito CPT.
 *
 * @package zenyx
 */

get_header();
?>

<main class="mwm-main-container">
	<?php get_template_part( 'template-parts/casos-exito/hero-archive' ); ?>
	<?php get_template_part( 'template-parts/casos-exito/archive-list' ); ?>
	<?php get_template_part( 'template-parts/casos-exito/cta' ); ?>
</main>

<?php
get_footer();

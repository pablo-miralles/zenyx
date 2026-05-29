<?php
/**
 * Archive template for evento CPT.
 *
 * @package zenyx
 */

get_header();
?>

<main class="mwm-main-container">
	<?php get_template_part( 'template-parts/eventos/hero-archive' ); ?>
	<?php get_template_part( 'template-parts/eventos/archive-list' ); ?>
	<?php get_template_part( 'template-parts/eventos/marquee-archive' ); ?>
	<?php get_template_part( 'template-parts/eventos/cta-archive' ); ?>
</main>

<?php
get_footer();

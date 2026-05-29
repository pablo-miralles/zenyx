<?php
/**
 * Category archive (same layout as blog index: hero + entries grid + filters).
 *
 * @package zenyx
 */

get_header();
?>

<main class="mwm-main-container">
	<?php get_template_part( 'template-parts/blog/hero-blog' ); ?>
	<?php get_template_part( 'template-parts/blog/entries-list' ); ?>
</main>

<?php
get_footer();

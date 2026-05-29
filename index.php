<?php
/**
 * Blog posts index (latest posts on front or "Posts page").
 *
 * @package zenyx
 */

get_header();
?>

<main class="mwm-main-container">
	<?php get_template_part( 'template-parts/blog/hero-blog' ); ?>
	<?php get_template_part( 'template-parts/blog/entries-list' ); ?>
	<?php get_template_part( 'template-parts/blog/cta-home' ); ?>
</main>

<?php
get_footer();

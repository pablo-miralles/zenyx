<?php
/**
 * Single post hero (breadcrumb, title, excerpt, category link).
 *
 * @package zenyx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$home_url = home_url( '/' );

$posts_page_id = (int) get_option( 'page_for_posts' );
$blog_url      = '';
if ( $posts_page_id > 0 ) {
	$blog_url = get_permalink( $posts_page_id );
} else {
	$archive = get_post_type_archive_link( 'post' );
	if ( $archive ) {
		$blog_url = $archive;
	}
}

if ( has_excerpt() ) {
	$excerpt_text = get_the_excerpt();
} else {
	$raw = get_post_field( 'post_content', get_the_ID() );
	$excerpt_text = $raw
		? wp_trim_words( wp_strip_all_tags( $raw ), 40, '…' )
		: '';
}

$categories = get_the_category();
$first_cat  = ( ! empty( $categories ) && ! is_wp_error( $categories ) ) ? $categories[0] : null;

$hero_uid = 'mwm-single-hero-' . (int) get_the_ID();
$grad_id  = 'mwm_single_hero_grad_' . (int) get_the_ID();
?>
<section
	data-dark
	class="mwm-single-hero overflow-hidden bg-neutral-light pt-header pb-10 lg:min-h-[700px] lg:pb-9"
	aria-labelledby="<?php echo esc_attr( $hero_uid ); ?>-title"
>
	<div class="mwm-max-1">
		<div class="relative">
			<svg
				class="pointer-events-none absolute left-0 top-20 z-1 w-[min(100%,56.25rem)] max-w-[90vw] opacity-10 md:left-[35px] md:top-19"
				width="900"
				height="700"
				viewBox="0 0 900 700"
				fill="none"
				xmlns="http://www.w3.org/2000/svg"
				aria-hidden="true"
				focusable="false"
			>
				<g>
					<path
						d="M192.942 583.03L535.84 583.8V700H0V583.03L171.316 411.74H364.244L192.942 583.03ZM731.021 124.754V239.064L558.333 411.74H365.391L538.093 239.064H194.074L193.725 124.754H731.021ZM900.001 68.46L845.242 123.479H776.557V0H900.001V68.46Z"
						fill="url(#<?php echo esc_attr( $grad_id ); ?>)"
						style="mix-blend-mode:plus-darker"
					/>
				</g>
				<defs>
					<linearGradient
						id="<?php echo esc_attr( $grad_id ); ?>"
						x1="450"
						y1="0"
						x2="450"
						y2="700"
						gradientUnits="userSpaceOnUse"
					>
						<stop stop-color="#083B51" stop-opacity="0" />
						<stop offset="1" stop-color="#083B51" />
					</linearGradient>
				</defs>
			</svg>

			<div class="relative z-2 mwm-max-1 flex min-h-[min(700px,90svh)] flex-col justify-between gap-10 px-4 pt-3 pb-8 sm:px-6 lg:gap-14 lg:px-[35px] lg:pb-0">
				<nav class="w-full shrink-0" aria-label="<?php esc_attr_e( 'Migas de pan', THEME_TEXT_DOMAIN ); ?>">
					<ol class="flex flex-wrap items-center gap-3 text-sm text-protagonista">
						<li>
							<a class="no-underline hover:underline" href="<?php echo esc_url( $home_url ); ?>">
								<?php esc_html_e( 'Home', THEME_TEXT_DOMAIN ); ?>
							</a>
						</li>
						<?php if ( $blog_url ) : ?>
							<li>
								<a class="no-underline hover:underline" href="<?php echo esc_url( $blog_url ); ?>">
									<?php esc_html_e( 'Blog', THEME_TEXT_DOMAIN ); ?>
								</a>
							</li>
						<?php endif; ?>
						<li class="font-medium" aria-current="page">
							<?php esc_html_e( 'Artículo', THEME_TEXT_DOMAIN ); ?>
						</li>
					</ol>
				</nav>

				<div class="flex w-full flex-col items-center gap-6 text-center lg:mx-auto lg:max-w-[636px]">
					<h1 id="<?php echo esc_attr( $hero_uid ); ?>-title" class="font-heading text-display-l w-full leading-[1.2] text-protagonista">
						<?php echo esc_html( get_the_title() ); ?>
					</h1>
					<?php if ( '' !== $excerpt_text ) : ?>
						<p class="font-body text-body-l w-full max-w-[636px] leading-[1.2] text-protagonista">
							<?php echo esc_html( wp_strip_all_tags( $excerpt_text ) ); ?>
						</p>
					<?php endif; ?>
				</div>

				<?php if ( $first_cat ) : ?>
					<div class="flex w-full shrink-0 flex-col items-center text-center lg:mx-auto lg:max-w-[636px]">
						<a
							class="font-body text-body-m text-acento no-underline transition-colors hover:text-acento-hover hover:underline"
							href="<?php echo esc_url( get_category_link( $first_cat->term_id ) ); ?>"
						>
							<?php echo esc_html( '#' . $first_cat->name ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>

	<?php
	$footer_email = get_option( 'mwm_foot_email' ) ?: get_theme_mod( 'mwm_foot_email' );
	$footer_phone = get_option( 'mwm_foot_phone' ) ?: get_theme_mod( 'mwm_foot_phone' );
	$footer_text_1 = get_option( 'mwm_footer_text_1' );
	$footer_text_2 = get_option( 'mwm_footer_text_2' );
	$footer_text_1 = ! empty( $footer_text_1 ) ? $footer_text_1 : __( 'Powered by Balinot Tech Consulting', THEME_TEXT_DOMAIN );
	$footer_text_2 = ! empty( $footer_text_2 ) ? $footer_text_2 : 'Zenyx [CURRENT_YEAR] ©';
	$footer_text_2 = mwm_format_footer_text_2( $footer_text_2 );

	$social_links = array(
		'linkedin' => array(
			'url'  => get_option( 'mwm_footer_linkedin_url' ),
			'icon' => get_template_directory() . '/assets/images/icons/linkedin.php',
			'name' => __( 'LinkedIn', THEME_TEXT_DOMAIN ),
		),
		'instagram' => array(
			'url'  => get_option( 'mwm_footer_instagram_url' ),
			'icon' => get_template_directory() . '/assets/images/icons/instagram.php',
			'name' => __( 'Instagram', THEME_TEXT_DOMAIN ),
		),
		'youtube' => array(
			'url'  => get_option( 'mwm_footer_youtube_url' ),
			'icon' => get_template_directory() . '/assets/images/icons/youtube.php',
			'name' => __( 'YouTube', THEME_TEXT_DOMAIN ),
		),
	);
	?>

	<div data-light data-footer-parallax class="footer-wrap relative overflow-hidden">
	<footer class="flex flex-col bg-protagonista" id="colophon" aria-label="<?php esc_attr_e( 'Pie de página', THEME_TEXT_DOMAIN ); ?>">
		<div
			id="footer-bg"
			data-footer-parallax-inner
			class="min-h-0 flex w-full flex-col justify-between bg-protagonista py-8 sm:pt-[64px] sm:pb-[35px] lg:min-h-screen lg:h-screen lg:pt-[80px]"
		>
		<div class="mwm-max-1 flex min-h-0 w-full flex-1 flex-col justify-between gap-8 lg:gap-16">
			<div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_306px] lg:items-stretch lg:gap-6">
				<div class="flex min-w-0 flex-col">
					<nav aria-label="<?php esc_attr_e( 'Footer menu 1', THEME_TEXT_DOMAIN ); ?>">
						<?php
						wp_nav_menu( array(
							'theme_location' => 'FooterMenu1',
							'container'      => false,
							'menu_class'     => 'm-0 list-none p-0 [&_li]:py-2',
							'menu_id'        => 'footer-menu-1',
							'fallback_cb'    => false,
						) );
						?>
					</nav>
					<div class="mt-2 flex flex-col">
						<?php if ( $footer_email ) : ?>
							<p class="m-0 py-2 text-base text-[#FE7756]">
								<a href="<?php echo esc_url( 'mailto:' . $footer_email ); ?>" class="text-[#FE7756] no-underline transition-opacity hover:opacity-70"><?php echo esc_html( $footer_email ); ?></a>
							</p>
						<?php endif; ?>
						<?php if ( $footer_phone ) : ?>
							<p class="m-0 py-2 text-base text-[#FE7756]">
								<a href="<?php echo esc_url( 'tel:' . preg_replace( '/\s+/', '', $footer_phone ) ); ?>" class="text-[#FE7756] no-underline transition-opacity hover:opacity-70"><?php echo esc_html( $footer_phone ); ?></a>
							</p>
						<?php endif; ?>
					</div>
				</div>

				<div class="flex min-w-0 flex-col">
					<nav aria-label="<?php esc_attr_e( 'Footer menu 2', THEME_TEXT_DOMAIN ); ?>">
						<?php
						wp_nav_menu( array(
							'theme_location' => 'FooterMenu2',
							'container'      => false,
							'menu_class'     => 'm-0 list-none p-0 [&_li]:py-2',
							'menu_id'        => 'footer-menu-2',
							'fallback_cb'    => false,
						) );
						?>
					</nav>
				</div>

				<div class="flex min-w-0 flex-col">
					<nav aria-label="<?php esc_attr_e( 'Footer legal', THEME_TEXT_DOMAIN ); ?>">
						<?php
						wp_nav_menu( array(
							'theme_location' => 'FooterMenu3',
							'container'      => false,
							'menu_class'     => 'm-0 list-none p-0 [&_li]:py-2',
							'menu_id'        => 'footer-menu-3',
							'fallback_cb'    => false,
						) );
						?>
					</nav>
				</div>

				<div class="flex min-w-0 flex-col justify-between gap-8 lg:w-[306px]">
					<div class="flex flex-col">
						<?php if ( trim( wp_strip_all_tags( $footer_text_1 ) ) ) : ?>
							<p class="footer-text-1 m-0 py-2 text-base text-[#C1D9E4] [&_a]:text-inherit [&_a]:no-underline [&_a]:transition-opacity hover:[&_a]:opacity-70"><?php echo wp_kses_post( $footer_text_1 ); ?></p>
						<?php endif; ?>
						<?php if ( $footer_text_2 ) : ?>
							<p class="m-0 py-2 text-base text-[#C1D9E4]"><?php echo esc_html( $footer_text_2 ); ?></p>
						<?php endif; ?>
					</div>
					<div class="flex items-center gap-4" aria-label="<?php esc_attr_e( 'Redes sociales', THEME_TEXT_DOMAIN ); ?>">
						<?php foreach ( $social_links as $social ) : ?>
							<?php if ( ! empty( $social['url'] ) && is_readable( $social['icon'] ) ) : ?>
								<a href="<?php echo esc_url( $social['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $social['name'] ); ?>" class="inline-flex h-5 w-5 items-center justify-center text-[#C1D9E4] transition-opacity hover:opacity-70">
									<?php require $social['icon']; ?>
								</a>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

				<div class="mt-auto w-full shrink-0 overflow-hidden">
					<a
						href="<?php echo esc_url( home_url( '/' ) ); ?>"
						class="mwm-footer__logo block leading-none no-underline transition-opacity"
						aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
					>
						<span class="sr-only"><?php bloginfo( 'name' ); ?></span>
						<?php
						$footer_logo_path = get_template_directory() . '/assets/images/footer-logo.php';
						if ( is_readable( $footer_logo_path ) ) {
							ob_start();
							require $footer_logo_path;
							$footer_logo = ob_get_clean();
							if ( is_string( $footer_logo ) ) {
								$footer_logo = preg_replace( '/<svg/', '<svg class="h-auto w-full"', $footer_logo, 1 );
								echo $footer_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
						}
						?>
					</a>
				</div>
		</div>
		</div>
	</footer>
	<div
		data-footer-parallax-dark
		class="footer-wrap__dark pointer-events-none absolute inset-0 z-1 bg-[#04202c] opacity-0 w-full h-full"
		aria-hidden="true"
	></div>
	</div>

	<?php wp_footer(); ?>

</body>

</html>

<!DOCTYPE html>
<?php
$header_campus_text = get_option( 'mwm_header_campus_text' ) ?: __( 'Campus', THEME_TEXT_DOMAIN );
$header_campus_url  = get_option( 'mwm_header_campus_url' ) ?: '#';
$header_contact_text = get_option( 'mwm_header_contact_text' ) ?: __( 'Contacto', THEME_TEXT_DOMAIN );
$header_contact_url  = get_option( 'mwm_header_contact_url' ) ?: '#';
?>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name=viewport content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex, nofollow">
	
	<?php wp_head(); ?>

</head>
<body <?php body_class( ); ?>>

	<?php wp_body_open(); ?>

	<header class="mwm-header fixed left-0 right-0 z-100" style="top: var(--wp-admin--admin-bar--height, 0px);" id="mwm-header">
		<div class="mwm-header__shell">
			<div class="mwm-max-1">
			<div class="mwm-header__bar">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mwm-header__logo" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
					<span class="sr-only"><?php bloginfo( 'name' ); ?></span>
					<?php get_template_part( 'template-parts/header/logo' ); ?>
				</a>

				<div class="mwm-header__desktop hidden lg:flex">
					<nav class="mwm-header-nav" aria-label="<?php esc_attr_e( 'Navegación principal', THEME_TEXT_DOMAIN ); ?>">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'HeaderMenu',
								'container'      => false,
								'menu_class'     => 'mwm-header-nav__list',
								'menu_id'        => 'header-menu',
								'depth'          => 2,
								'fallback_cb'    => false,
								'walker'         => new MWM_Walker_Nav_Menu(),
							)
						);
						?>
					</nav>

					<div class="mwm-header__actions">
						<?php
						mwm_render_button(
							array(
								'text'          => $header_campus_text,
								'url'           => $header_campus_url,
								'variant'       => 'header-outline',
								'size'          => 'sm',
								'icon'          => 'campus',
								'icon_position' => 'before',
								'aria_label'    => $header_campus_text,
							)
						);

						mwm_render_button(
							array(
								'text'       => $header_contact_text,
								'url'        => $header_contact_url,
								'variant'    => 'header-primary',
								'size'       => 'sm',
								'aria_label' => $header_contact_text,
							)
						);
						?>
					</div>
				</div>

				<button
					type="button"
					id="mwm-mobile-toggle"
					class="mwm-burger relative z-[110] cursor-pointer ml-[12px] shrink-0 lg:hidden"
					aria-controls="mwm-mobile-menu"
					aria-expanded="false"
					aria-label="<?php esc_attr_e( 'Abrir menú', THEME_TEXT_DOMAIN ); ?>"
					data-label-open="<?php esc_attr_e( 'Abrir menú', THEME_TEXT_DOMAIN ); ?>"
					data-label-close="<?php esc_attr_e( 'Cerrar menú', THEME_TEXT_DOMAIN ); ?>"
				>
					<span class="mwm-burger__line mwm-burger__line--1"></span>
					<span class="mwm-burger__line mwm-burger__line--2"></span>
					<span class="mwm-burger__line mwm-burger__line--3"></span>
				</button>
			</div>
			</div>
		</div>
	</header>

	<!-- Menú móvil: debajo del header (el burger sigue encima para abrir/cerrar) -->
	<div
		id="mwm-mobile-backdrop"
		class="mwm-mobile-backdrop lg:hidden"
		aria-hidden="true"
	></div>
	<div
		id="mwm-mobile-menu"
		class="mwm-mobile-drawer lg:hidden"
		role="dialog"
		aria-modal="false"
		aria-labelledby="mwm-mobile-menu-title"
		aria-hidden="true"
	>
		<span id="mwm-mobile-menu-title" class="sr-only"><?php esc_html_e( 'Menú principal', THEME_TEXT_DOMAIN ); ?></span>
		<nav class="mwm-mobile-nav" aria-label="<?php esc_attr_e( 'Navegación principal (móvil)', THEME_TEXT_DOMAIN ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'HeaderMenu',
					'container'      => false,
					'menu_class'     => 'mwm-mobile-nav__list',
					'menu_id'        => 'header-menu-mobile',
					'depth'          => 2,
					'fallback_cb'    => false,
					'walker'         => new MWM_Walker_Nav_Menu(),
				)
			);
			?>
		</nav>
		<div class="mwm-mobile-drawer__cta">
			<?php
			mwm_render_button(
				array(
					'text'          => $header_campus_text,
					'url'           => $header_campus_url,
					'variant'       => 'header-outline',
					'size'          => 'sm',
					'icon'          => 'campus',
					'icon_position' => 'before',
					'aria_label'    => $header_campus_text,
				)
			);
			mwm_render_button(
				array(
					'text'       => $header_contact_text,
					'url'        => $header_contact_url,
					'variant'    => 'header-primary',
					'size'       => 'sm',
					'aria_label' => $header_contact_text,
				)
			);
			?>
		</div>
	</div>

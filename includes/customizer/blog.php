<?php
/**
 * Blog archive — Customizer (panel Hero / CTA).
 *
 * @package zenyx
 */

if ( ! function_exists( 'mwm_customize_blog_register_hero' ) ) {
	/**
	 * @param WP_Customize_Manager $wp_customize Manager.
	 * @param string               $section      Section ID.
	 */
	function mwm_customize_blog_register_hero( $wp_customize, $section ) {
		$wp_customize->add_setting(
			'mwm_blog_hero_title',
			array(
				'type'              => 'option',
				'default'           => __( 'Blog sobre escalar agencias', THEME_TEXT_DOMAIN ),
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'mwm_blog_hero_title',
			array(
				'label'   => __( 'Titulo del hero', THEME_TEXT_DOMAIN ),
				'section' => $section,
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'mwm_blog_hero_lead',
			array(
				'type'              => 'option',
				'default'           => __( 'Aqui tienes el blog para duenos de agencias que no se conforman con su facturacion.', THEME_TEXT_DOMAIN ),
				'sanitize_callback' => 'sanitize_textarea_field',
			)
		);
		$wp_customize->add_control(
			'mwm_blog_hero_lead',
			array(
				'label'   => __( 'Parrafo principal', THEME_TEXT_DOMAIN ),
				'section' => $section,
				'type'    => 'textarea',
			)
		);

		$wp_customize->add_setting(
			'mwm_blog_hero_subline',
			array(
				'type'              => 'option',
				'default'           => __( 'Consejos, mentalidad, procesos y estrategias para aprender a escalar desde dentro.', THEME_TEXT_DOMAIN ),
				'sanitize_callback' => 'sanitize_textarea_field',
			)
		);
		$wp_customize->add_control(
			'mwm_blog_hero_subline',
			array(
				'label'   => __( 'Linea destacada (color acento)', THEME_TEXT_DOMAIN ),
				'section' => $section,
				'type'    => 'textarea',
			)
		);

		$wp_customize->add_setting(
			'mwm_blog_hero_cta_text',
			array(
				'type'              => 'option',
				'default'           => __( 'Ver articulos', THEME_TEXT_DOMAIN ),
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'mwm_blog_hero_cta_text',
			array(
				'label'   => __( 'Texto del boton CTA', THEME_TEXT_DOMAIN ),
				'section' => $section,
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'mwm_blog_hero_cta_url',
			array(
				'type'              => 'option',
				'default'           => '#articulos',
				'sanitize_callback' => 'mwm_sanitize_url_or_hash',
			)
		);
		$wp_customize->add_control(
			'mwm_blog_hero_cta_url',
			array(
				'label'       => __( 'Enlace del CTA', THEME_TEXT_DOMAIN ),
				'description' => __( 'Por defecto ancla a la lista de entradas en la misma pagina (#articulos).', THEME_TEXT_DOMAIN ),
				'section'     => $section,
				'type'        => 'url',
			)
		);
	}
}

if ( ! function_exists( 'mwm_customize_register_blog' ) ) {
	/**
	 * @param WP_Customize_Manager $wp_customize Manager.
	 */
	function mwm_customize_register_blog( $wp_customize ) {
		$wp_customize->add_panel(
			'mwm_blog_archive_panel',
			array(
				'title'       => __( 'Blog (archivo)', THEME_TEXT_DOMAIN ),
				'description' => __( 'Hero y CTA del listado y del single de entradas.', THEME_TEXT_DOMAIN ),
				'priority'    => 162,
			)
		);

		$wp_customize->add_section(
			'mwm_blog_hero_section',
			array(
				'title' => __( 'Hero', THEME_TEXT_DOMAIN ),
				'panel' => 'mwm_blog_archive_panel',
			)
		);

		$wp_customize->add_section(
			'mwm_blog_archive_cta_section',
			array(
				'title' => __( 'CTA', THEME_TEXT_DOMAIN ),
				'panel' => 'mwm_blog_archive_panel',
			)
		);

		mwm_customize_blog_register_hero( $wp_customize, 'mwm_blog_hero_section' );

		if ( function_exists( 'mwm_customize_register_cta_block' ) ) {
			mwm_customize_register_cta_block(
				$wp_customize,
				'mwm_blog_archive_cta_section',
				'mwm_blog_archive_cta',
				array(
					'heading'       => __( '¿Te gustaría aplicar esto con ayuda y no solo leerlo?', THEME_TEXT_DOMAIN ),
					'description'   => '',
					'theme'         => 'oscuro',
					'button_text'   => __( 'Descubre cómo podemos ayudarte', THEME_TEXT_DOMAIN ),
					'button_url'    => home_url( '/contacto/' ),
					'opens_new_tab' => false,
				)
			);
		}
	}
	add_action( 'customize_register', 'mwm_customize_register_blog' );
}

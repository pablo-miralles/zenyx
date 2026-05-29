<?php
/**
 * Casos de exito — Customizer (panel Hero / CTA).
 *
 * @package zenyx
 */

if ( ! function_exists( 'mwm_customize_casos_register_hero' ) ) {
	/**
	 * @param WP_Customize_Manager $wp_customize Manager.
	 * @param string               $section      Section ID.
	 */
	function mwm_customize_casos_register_hero( $wp_customize, $section ) {
		$wp_customize->add_setting(
			'mwm_casos_archive_hero_title',
			array(
				'type'              => 'option',
				'default'           => __( 'Casos de exito', THEME_TEXT_DOMAIN ),
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'mwm_casos_archive_hero_title',
			array(
				'label'   => __( 'Titulo del hero', THEME_TEXT_DOMAIN ),
				'section' => $section,
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'mwm_casos_archive_hero_lead',
			array(
				'type'              => 'option',
				'default'           => __( 'Historias reales de crecimiento, procesos y resultados en agencias.', THEME_TEXT_DOMAIN ),
				'sanitize_callback' => 'sanitize_textarea_field',
			)
		);
		$wp_customize->add_control(
			'mwm_casos_archive_hero_lead',
			array(
				'label'   => __( 'Parrafo principal', THEME_TEXT_DOMAIN ),
				'section' => $section,
				'type'    => 'textarea',
			)
		);

		$wp_customize->add_setting(
			'mwm_casos_archive_hero_subline',
			array(
				'type'              => 'option',
				'default'           => __( 'Descubre como otras agencias desbloquearon su siguiente nivel.', THEME_TEXT_DOMAIN ),
				'sanitize_callback' => 'sanitize_textarea_field',
			)
		);
		$wp_customize->add_control(
			'mwm_casos_archive_hero_subline',
			array(
				'label'   => __( 'Linea destacada (color acento)', THEME_TEXT_DOMAIN ),
				'section' => $section,
				'type'    => 'textarea',
			)
		);

		$wp_customize->add_setting(
			'mwm_casos_archive_hero_cta_text',
			array(
				'type'              => 'option',
				'default'           => __( 'Ver casos', THEME_TEXT_DOMAIN ),
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'mwm_casos_archive_hero_cta_text',
			array(
				'label'   => __( 'Texto del boton CTA', THEME_TEXT_DOMAIN ),
				'section' => $section,
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'mwm_casos_archive_hero_cta_url',
			array(
				'type'              => 'option',
				'default'           => '#casos',
				'sanitize_callback' => 'mwm_sanitize_url_or_hash',
			)
		);
		$wp_customize->add_control(
			'mwm_casos_archive_hero_cta_url',
			array(
				'label'       => __( 'Enlace del CTA', THEME_TEXT_DOMAIN ),
				'description' => __( 'Por defecto ancla a la lista de casos en la misma pagina (#casos).', THEME_TEXT_DOMAIN ),
				'section'     => $section,
				'type'        => 'url',
			)
		);
	}
}

if ( ! function_exists( 'mwm_customize_register_casos' ) ) {
	/**
	 * @param WP_Customize_Manager $wp_customize Manager.
	 */
	function mwm_customize_register_casos( $wp_customize ) {
		$wp_customize->add_panel(
			'mwm_casos_archive_panel',
			array(
				'title'       => __( 'Casos de exito (archivo)', THEME_TEXT_DOMAIN ),
				'description' => __( 'Hero y CTA del archive y single de casos de exito.', THEME_TEXT_DOMAIN ),
				'priority'    => 163,
			)
		);

		$wp_customize->add_section(
			'mwm_casos_archive_hero_section',
			array(
				'title' => __( 'Hero', THEME_TEXT_DOMAIN ),
				'panel' => 'mwm_casos_archive_panel',
			)
		);

		$wp_customize->add_section(
			'mwm_casos_archive_cta_section',
			array(
				'title' => __( 'CTA', THEME_TEXT_DOMAIN ),
				'panel' => 'mwm_casos_archive_panel',
			)
		);

		mwm_customize_casos_register_hero( $wp_customize, 'mwm_casos_archive_hero_section' );

		if ( function_exists( 'mwm_customize_register_cta_block' ) ) {
			mwm_customize_register_cta_block(
				$wp_customize,
				'mwm_casos_archive_cta_section',
				'mwm_casos_archive_cta',
				array(
					'heading'       => __( '¿Quieres ser el próximo caso de éxito? Escríbenos y te ayudamos', THEME_TEXT_DOMAIN ),
					'description'   => '',
					'theme'         => 'claro',
					'button_text'   => __( 'Descubre el programa Libertad', THEME_TEXT_DOMAIN ),
					'button_url'    => home_url( '/programa-libertad/' ),
					'opens_new_tab' => false,
				)
			);
		}
	}
	add_action( 'customize_register', 'mwm_customize_register_casos' );
}

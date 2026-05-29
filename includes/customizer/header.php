<?php
/**
 * Header customizer settings.
 */

if ( ! function_exists( 'mwm_customize_register_header' ) ) {
	function mwm_customize_register_header( $wp_customize ) {
		$wp_customize->add_section(
			'mwm_header_section',
			array(
				'title'       => __( 'Header', THEME_TEXT_DOMAIN ),
				'description' => __( 'Configuracion del header principal.', THEME_TEXT_DOMAIN ),
				'priority'    => 155,
			)
		);

		$wp_customize->add_setting(
			'mwm_header_campus_text',
			array(
				'type'              => 'option',
				'default'           => __( 'Campus', THEME_TEXT_DOMAIN ),
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'mwm_header_campus_text',
			array(
				'label'       => __( 'Texto boton Campus', THEME_TEXT_DOMAIN ),
				'section'     => 'mwm_header_section',
				'type'        => 'text',
				'input_attrs' => array(
					'placeholder' => __( 'Campus', THEME_TEXT_DOMAIN ),
				),
			)
		);

		$wp_customize->add_setting(
			'mwm_header_campus_url',
			array(
				'type'              => 'option',
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			'mwm_header_campus_url',
			array(
				'label'       => __( 'Enlace boton Campus', THEME_TEXT_DOMAIN ),
				'section'     => 'mwm_header_section',
				'type'        => 'url',
				'input_attrs' => array(
					'placeholder' => 'https://...',
				),
			)
		);

		$wp_customize->add_setting(
			'mwm_header_contact_text',
			array(
				'type'              => 'option',
				'default'           => __( 'Contacto', THEME_TEXT_DOMAIN ),
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'mwm_header_contact_text',
			array(
				'label'       => __( 'Texto boton Contacto', THEME_TEXT_DOMAIN ),
				'section'     => 'mwm_header_section',
				'type'        => 'text',
				'input_attrs' => array(
					'placeholder' => __( 'Contacto', THEME_TEXT_DOMAIN ),
				),
			)
		);

		$wp_customize->add_setting(
			'mwm_header_contact_url',
			array(
				'type'              => 'option',
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			'mwm_header_contact_url',
			array(
				'label'       => __( 'Enlace boton Contacto', THEME_TEXT_DOMAIN ),
				'section'     => 'mwm_header_section',
				'type'        => 'url',
				'input_attrs' => array(
					'placeholder' => 'https://...',
				),
			)
		);
	}
	add_action( 'customize_register', 'mwm_customize_register_header' );
}

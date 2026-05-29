<?php
/**
 * Eventos archive — Customizer (panel Hero / Carrusel / CTA).
 *
 * @package zenyx
 */

if ( ! function_exists( 'mwm_sanitize_eventos_hero_media_type' ) ) {
	/**
	 * @param string $value Raw value.
	 * @return string
	 */
	function mwm_sanitize_eventos_hero_media_type( $value ) {
		$value = (string) $value;
		return in_array( $value, array( 'video', 'image' ), true ) ? $value : 'video';
	}
}

if ( ! function_exists( 'mwm_eventos_archive_get_carousel_image_ids_legacy' ) ) {
	/**
	 * Migración desde controles fijos (imagen 1…12).
	 *
	 * @return array<int>
	 */
	function mwm_eventos_archive_get_carousel_image_ids_legacy() {
		$ids = array();

		for ( $i = 1; $i <= 12; $i++ ) {
			$id = absint( get_option( 'mwm_eventos_archive_carousel_image_' . $i ) );
			if ( $id < 1 || ! wp_attachment_is_image( $id ) ) {
				continue;
			}
			$ids[] = $id;
		}

		return $ids;
	}
}

if ( ! function_exists( 'mwm_eventos_archive_get_carousel_image_ids' ) ) {
	/**
	 * IDs de imagen del carrusel (galería del Customizer).
	 *
	 * @return array<int>
	 */
	function mwm_eventos_archive_get_carousel_image_ids() {
		$stored = get_option( 'mwm_eventos_archive_carousel_image_ids', '' );
		$ids    = mwm_parse_image_ids_json( $stored );

		if ( ! empty( $ids ) ) {
			return $ids;
		}

		return mwm_eventos_archive_get_carousel_image_ids_legacy();
	}
}

if ( ! function_exists( 'mwm_eventos_archive_carousel_duration_seconds' ) ) {
	/**
	 * Duración del marquee según número de imágenes (~4s por slide, acotado 20–120s).
	 *
	 * @param int $image_count Número de imágenes en el carrusel.
	 * @return int
	 */
	function mwm_eventos_archive_carousel_duration_seconds( $image_count ) {
		$image_count = max( 0, (int) $image_count );

		if ( $image_count < 1 ) {
			return 40;
		}

		$duration = $image_count * 4;

		return max( 20, min( 120, $duration ) );
	}
}

if ( ! function_exists( 'mwm_customize_eventos_register_hero' ) ) {
	/**
	 * @param WP_Customize_Manager $wp_customize Manager.
	 * @param string               $section      Section ID.
	 */
	function mwm_customize_eventos_register_hero( $wp_customize, $section ) {
		$wp_customize->add_setting(
			'mwm_eventos_archive_hero_title',
			array(
				'type'              => 'option',
				'default'           => __( "Eventos para agencias\nde marketing", THEME_TEXT_DOMAIN ),
				'sanitize_callback' => 'sanitize_textarea_field',
			)
		);
		$wp_customize->add_control(
			'mwm_eventos_archive_hero_title',
			array(
				'label'       => __( 'Titulo del hero (una o varias lineas)', THEME_TEXT_DOMAIN ),
				'description' => __( 'Usa una linea nueva para partir el titulo en dos bloques.', THEME_TEXT_DOMAIN ),
				'section'     => $section,
				'type'        => 'textarea',
			)
		);

		$wp_customize->add_setting(
			'mwm_eventos_archive_hero_lead',
			array(
				'type'              => 'option',
				'default'           => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean nec pulvinar urna, id tincidunt risus. Integer luctus scelerisque nisi nec maximus.', THEME_TEXT_DOMAIN ),
				'sanitize_callback' => 'sanitize_textarea_field',
			)
		);
		$wp_customize->add_control(
			'mwm_eventos_archive_hero_lead',
			array(
				'label'   => __( 'Parrafo descriptivo', THEME_TEXT_DOMAIN ),
				'section' => $section,
				'type'    => 'textarea',
			)
		);

		$wp_customize->add_setting(
			'mwm_eventos_archive_hero_bg_type',
			array(
				'type'              => 'option',
				'default'           => 'video',
				'sanitize_callback' => 'mwm_sanitize_eventos_hero_media_type',
			)
		);
		$wp_customize->add_control(
			'mwm_eventos_archive_hero_bg_type',
			array(
				'label'   => __( 'Tipo de medio de fondo', THEME_TEXT_DOMAIN ),
				'section' => $section,
				'type'    => 'select',
				'choices' => array(
					'video' => __( 'Video', THEME_TEXT_DOMAIN ),
					'image' => __( 'Imagen', THEME_TEXT_DOMAIN ),
				),
			)
		);

		$wp_customize->add_setting(
			'mwm_eventos_archive_hero_bg_video_id',
			array(
				'type'              => 'option',
				'default'           => 0,
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				'mwm_eventos_archive_hero_bg_video_id',
				array(
					'label'     => __( 'Video de fondo (MP4)', THEME_TEXT_DOMAIN ),
					'section'   => $section,
					'mime_type' => 'video',
					'settings'  => 'mwm_eventos_archive_hero_bg_video_id',
				)
			)
		);

		$wp_customize->add_setting(
			'mwm_eventos_archive_hero_bg_image_id',
			array(
				'type'              => 'option',
				'default'           => 0,
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				'mwm_eventos_archive_hero_bg_image_id',
				array(
					'label'     => __( 'Imagen de fondo', THEME_TEXT_DOMAIN ),
					'section'   => $section,
					'mime_type' => 'image',
					'settings'  => 'mwm_eventos_archive_hero_bg_image_id',
				)
			)
		);

		$wp_customize->add_setting(
			'mwm_eventos_archive_hero_clip_type',
			array(
				'type'              => 'option',
				'default'           => 'video',
				'sanitize_callback' => 'mwm_sanitize_eventos_hero_media_type',
			)
		);
		$wp_customize->add_control(
			'mwm_eventos_archive_hero_clip_type',
			array(
				'label'       => __( 'Tipo de medio en la figura (recorte)', THEME_TEXT_DOMAIN ),
				'description' => __( 'Debe coincidir con el archivo elegido abajo (video o imagen). Si solo subes uno, se muestra aunque el otro control este vacio.', THEME_TEXT_DOMAIN ),
				'section'     => $section,
				'type'        => 'select',
				'choices'     => array(
					'video' => __( 'Video', THEME_TEXT_DOMAIN ),
					'image' => __( 'Imagen', THEME_TEXT_DOMAIN ),
				),
			)
		);

		$wp_customize->add_setting(
			'mwm_eventos_archive_hero_clip_video_id',
			array(
				'type'              => 'option',
				'default'           => 0,
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				'mwm_eventos_archive_hero_clip_video_id',
				array(
					'label'     => __( 'Video en la figura (MP4)', THEME_TEXT_DOMAIN ),
					'section'   => $section,
					'mime_type' => 'video',
					'settings'  => 'mwm_eventos_archive_hero_clip_video_id',
				)
			)
		);

		$wp_customize->add_setting(
			'mwm_eventos_archive_hero_clip_image_id',
			array(
				'type'              => 'option',
				'default'           => 0,
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				'mwm_eventos_archive_hero_clip_image_id',
				array(
					'label'     => __( 'Imagen en la figura', THEME_TEXT_DOMAIN ),
					'section'   => $section,
					'mime_type' => 'image',
					'settings'  => 'mwm_eventos_archive_hero_clip_image_id',
				)
			)
		);

		$wp_customize->add_setting(
			'mwm_eventos_archive_list_title',
			array(
				'type'              => 'option',
				'default'           => __( 'Últimos eventos Zenyx', THEME_TEXT_DOMAIN ),
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'mwm_eventos_archive_list_title',
			array(
				'label'   => __( 'Titulo del listado (debajo del hero)', THEME_TEXT_DOMAIN ),
				'section' => $section,
				'type'    => 'text',
			)
		);
	}
}

if ( ! function_exists( 'mwm_customize_eventos_register_carousel' ) ) {
	/**
	 * @param WP_Customize_Manager $wp_customize Manager.
	 * @param string               $section      Section ID.
	 */
	function mwm_customize_eventos_register_carousel( $wp_customize, $section ) {
		$wp_customize->add_setting(
			'mwm_eventos_archive_carousel_title',
			array(
				'type'              => 'option',
				'default'           => __( 'Así se vive los eventos para agencias de Zenyx', THEME_TEXT_DOMAIN ),
				'sanitize_callback' => 'wp_kses_post',
			)
		);
		$wp_customize->add_control(
			'mwm_eventos_archive_carousel_title',
			array(
				'label'   => __( 'Titular', THEME_TEXT_DOMAIN ),
				'section' => $section,
				'type'    => 'textarea',
			)
		);

		$wp_customize->add_setting(
			'mwm_eventos_archive_carousel_lead',
			array(
				'type'              => 'option',
				'default'           => '',
				'sanitize_callback' => 'wp_kses_post',
			)
		);
		$wp_customize->add_control(
			'mwm_eventos_archive_carousel_lead',
			array(
				'label'   => __( 'Texto introductorio', THEME_TEXT_DOMAIN ),
				'section' => $section,
				'type'    => 'textarea',
			)
		);

		$wp_customize->add_setting(
			'mwm_eventos_archive_carousel_center',
			array(
				'type'              => 'option',
				'default'           => true,
				'sanitize_callback' => 'mwm_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			'mwm_eventos_archive_carousel_center',
			array(
				'label'   => __( 'Centrar titular y texto', THEME_TEXT_DOMAIN ),
				'section' => $section,
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'mwm_eventos_archive_carousel_image_ids',
			array(
				'type'              => 'option',
				'default'           => '[]',
				'sanitize_callback' => 'mwm_sanitize_image_ids_json',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			new MWM_Customize_Image_Gallery_Control(
				$wp_customize,
				'mwm_eventos_archive_carousel_image_ids',
				array(
					'label'       => __( 'Imágenes del carrusel', THEME_TEXT_DOMAIN ),
					'description' => __( 'Añade todas las imágenes que quieras y arrástralas para definir el orden del carrusel.', THEME_TEXT_DOMAIN ),
					'section'     => $section,
					'settings'    => 'mwm_eventos_archive_carousel_image_ids',
				)
			)
		);
	}
}

if ( ! function_exists( 'mwm_eventos_archive_migrate_carousel_images' ) ) {
	/**
	 * Migra imágenes del esquema antiguo (12 slots) a la galería JSON.
	 */
	function mwm_eventos_archive_migrate_carousel_images() {
		$ids = mwm_parse_image_ids_json( get_option( 'mwm_eventos_archive_carousel_image_ids', '[]' ) );
		if ( ! empty( $ids ) ) {
			return;
		}

		$legacy = mwm_eventos_archive_get_carousel_image_ids_legacy();
		if ( empty( $legacy ) ) {
			return;
		}

		update_option(
			'mwm_eventos_archive_carousel_image_ids',
			mwm_sanitize_image_ids_json( $legacy )
		);
	}
	add_action( 'customize_register', 'mwm_eventos_archive_migrate_carousel_images', 20 );
}

if ( ! function_exists( 'mwm_customize_register_eventos' ) ) {
	/**
	 * @param WP_Customize_Manager $wp_customize Manager.
	 */
	function mwm_customize_register_eventos( $wp_customize ) {
		$wp_customize->add_panel(
			'mwm_eventos_archive_panel',
			array(
				'title'       => __( 'Eventos (archivo)', THEME_TEXT_DOMAIN ),
				'description' => __( 'Hero, carrusel y CTA del listado de eventos.', THEME_TEXT_DOMAIN ),
				'priority'    => 164,
			)
		);

		$wp_customize->add_section(
			'mwm_eventos_archive_hero_section',
			array(
				'title' => __( 'Hero', THEME_TEXT_DOMAIN ),
				'panel' => 'mwm_eventos_archive_panel',
			)
		);

		$wp_customize->add_section(
			'mwm_eventos_archive_carousel_section',
			array(
				'title' => __( 'Carrusel', THEME_TEXT_DOMAIN ),
				'panel' => 'mwm_eventos_archive_panel',
			)
		);

		$wp_customize->add_section(
			'mwm_eventos_archive_cta_section',
			array(
				'title' => __( 'CTA', THEME_TEXT_DOMAIN ),
				'panel' => 'mwm_eventos_archive_panel',
			)
		);

		mwm_customize_eventos_register_hero( $wp_customize, 'mwm_eventos_archive_hero_section' );
		mwm_customize_eventos_register_carousel( $wp_customize, 'mwm_eventos_archive_carousel_section' );

		if ( function_exists( 'mwm_customize_register_cta_block' ) ) {
			mwm_customize_register_cta_block(
				$wp_customize,
				'mwm_eventos_archive_cta_section',
				'mwm_eventos_archive_cta',
				array(
					'heading'       => __( 'Apúntate al próximo evento', THEME_TEXT_DOMAIN ),
					'description'   => '',
					'theme'         => 'claro',
					'button_text'   => __( 'Escribirnos es el primer paso', THEME_TEXT_DOMAIN ),
					'button_url'    => '',
					'opens_new_tab' => true,
				)
			);
		}
	}
	add_action( 'customize_register', 'mwm_customize_register_eventos' );
}

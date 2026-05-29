<?php
/**
 * CPT Eventos + metas (fecha, lugar, URL vídeo) + taxonomía Categoría.
 * Sin vista individual; con archivo.
 *
 * @package zenyx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MWM_EVENTO_POST_TYPE', 'evento' );
define( 'MWM_EVENTO_FECHA_META', 'mwm_evento_fecha' );
define( 'MWM_EVENTO_LUGAR_META', 'mwm_evento_lugar' );
define( 'MWM_EVENTO_VIDEO_URL_META', 'mwm_evento_video_url' );
define( 'MWM_EVENTO_CATEGORIA_TAX', 'evento_categoria' );

/**
 * Registra el tipo de contenido.
 */
function mwm_register_eventos() {
	$labels = array(
		'name'                  => _x( 'Eventos', 'post type general name', THEME_TEXT_DOMAIN ),
		'singular_name'         => _x( 'Evento', 'post type singular name', THEME_TEXT_DOMAIN ),
		'menu_name'             => _x( 'Eventos', 'admin menu', THEME_TEXT_DOMAIN ),
		'name_admin_bar'        => _x( 'Evento', 'add new on admin bar', THEME_TEXT_DOMAIN ),
		'add_new'               => _x( 'Añadir nuevo', 'evento', THEME_TEXT_DOMAIN ),
		'add_new_item'          => __( 'Añadir nuevo evento', THEME_TEXT_DOMAIN ),
		'new_item'              => __( 'Nuevo evento', THEME_TEXT_DOMAIN ),
		'edit_item'             => __( 'Editar evento', THEME_TEXT_DOMAIN ),
		'view_item'             => __( 'Ver evento', THEME_TEXT_DOMAIN ),
		'all_items'             => __( 'Todos los eventos', THEME_TEXT_DOMAIN ),
		'search_items'          => __( 'Buscar eventos', THEME_TEXT_DOMAIN ),
		'parent_item_colon'     => __( 'Evento superior:', THEME_TEXT_DOMAIN ),
		'not_found'             => __( 'No se encontraron eventos.', THEME_TEXT_DOMAIN ),
		'not_found_in_trash'    => __( 'No hay eventos en la papelera.', THEME_TEXT_DOMAIN ),
		'featured_image'        => __( 'Imagen destacada', THEME_TEXT_DOMAIN ),
		'set_featured_image'    => __( 'Establecer imagen destacada', THEME_TEXT_DOMAIN ),
		'remove_featured_image' => __( 'Quitar imagen destacada', THEME_TEXT_DOMAIN ),
		'use_featured_image'    => __( 'Usar como imagen destacada', THEME_TEXT_DOMAIN ),
		'archives'              => __( 'Archivo de eventos', THEME_TEXT_DOMAIN ),
		'insert_into_item'      => __( 'Insertar en el evento', THEME_TEXT_DOMAIN ),
		'uploaded_to_this_item' => __( 'Subidos a este evento', THEME_TEXT_DOMAIN ),
		'filter_items_list'     => __( 'Filtrar lista de eventos', THEME_TEXT_DOMAIN ),
		'items_list_navigation' => __( 'Navegación de lista de eventos', THEME_TEXT_DOMAIN ),
		'items_list'            => __( 'Lista de eventos', THEME_TEXT_DOMAIN ),
	);

	$args = array(
		'labels'              => $labels,
		'public'              => true,
		// Debe ser true para que el archive responda en front; los singles se bloquean con template_redirect.
		'publicly_queryable'  => true,
		'exclude_from_search' => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'show_in_rest'        => true,
		'rest_base'           => 'eventos',
		'query_var'           => true,
		'rewrite'             => array(
			'slug'       => 'eventos',
			'with_front' => false,
		),
		'capability_type'     => 'post',
		'has_archive'         => true,
		'hierarchical'        => false,
		'menu_position'       => 22,
		'menu_icon'           => 'dashicons-calendar-alt',
		'supports'            => array( 'title', 'thumbnail', 'revisions' ),
	);

	register_post_type( MWM_EVENTO_POST_TYPE, $args );
}
add_action( 'init', 'mwm_register_eventos', 0 );

/**
 * Taxonomía Categoría (solo eventos).
 */
function mwm_register_evento_categoria_taxonomy() {
	$labels = array(
		'name'              => _x( 'Categorías', 'taxonomy general name', THEME_TEXT_DOMAIN ),
		'singular_name'     => _x( 'Categoría', 'taxonomy singular name', THEME_TEXT_DOMAIN ),
		'search_items'      => __( 'Buscar categorías', THEME_TEXT_DOMAIN ),
		'all_items'         => __( 'Todas las categorías', THEME_TEXT_DOMAIN ),
		'parent_item'       => __( 'Categoría superior', THEME_TEXT_DOMAIN ),
		'parent_item_colon' => __( 'Categoría superior:', THEME_TEXT_DOMAIN ),
		'edit_item'         => __( 'Editar categoría', THEME_TEXT_DOMAIN ),
		'update_item'       => __( 'Actualizar categoría', THEME_TEXT_DOMAIN ),
		'add_new_item'      => __( 'Añadir nueva categoría', THEME_TEXT_DOMAIN ),
		'new_item_name'     => __( 'Nombre de la nueva categoría', THEME_TEXT_DOMAIN ),
		'menu_name'         => __( 'Categoría', THEME_TEXT_DOMAIN ),
	);

	$args = array(
		'labels'            => $labels,
		'hierarchical'      => true,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_in_rest'      => true,
		'rewrite'           => array(
			'slug'         => 'eventos/categoria',
			'with_front'   => false,
			'hierarchical' => true,
		),
	);

	register_taxonomy( MWM_EVENTO_CATEGORIA_TAX, array( MWM_EVENTO_POST_TYPE ), $args );
}
add_action( 'init', 'mwm_register_evento_categoria_taxonomy', 1 );

/**
 * Sanitiza el texto de fecha del evento (formato libre en pantalla).
 *
 * @param mixed $value Valor enviado.
 * @return string Cadena vacía o texto sanitizado.
 */
function mwm_evento_sanitize_fecha( $value ) {
	return is_string( $value ) ? sanitize_text_field( $value ) : '';
}

/**
 * Registra metadatos (REST + plantillas).
 */
function mwm_register_evento_metas() {
	register_post_meta(
		MWM_EVENTO_POST_TYPE,
		MWM_EVENTO_FECHA_META,
		array(
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'mwm_evento_sanitize_fecha',
			'show_in_rest'      => true,
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		MWM_EVENTO_POST_TYPE,
		MWM_EVENTO_LUGAR_META,
		array(
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		MWM_EVENTO_POST_TYPE,
		MWM_EVENTO_VIDEO_URL_META,
		array(
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'esc_url_raw',
			'show_in_rest'      => true,
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'mwm_register_evento_metas', 2 );

/**
 * Metabox datos del evento.
 */
function mwm_evento_add_meta_boxes() {
	add_meta_box(
		'mwm_evento_data',
		__( 'Datos del evento', THEME_TEXT_DOMAIN ),
		'mwm_evento_data_metabox',
		MWM_EVENTO_POST_TYPE,
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'mwm_evento_add_meta_boxes' );

/**
 * Contenido del metabox.
 *
 * @param WP_Post $post Post actual.
 */
function mwm_evento_data_metabox( $post ) {
	wp_nonce_field( 'mwm_evento_data_save', 'mwm_evento_data_nonce' );

	$fecha = get_post_meta( $post->ID, MWM_EVENTO_FECHA_META, true );
	$lugar = get_post_meta( $post->ID, MWM_EVENTO_LUGAR_META, true );
	$video = get_post_meta( $post->ID, MWM_EVENTO_VIDEO_URL_META, true );
	?>
	<p>
		<label for="mwm_evento_fecha"><strong><?php esc_html_e( 'Fecha del evento', THEME_TEXT_DOMAIN ); ?></strong></label><br />
		<span class="description"><?php esc_html_e( 'Texto libre (ej. 11-13 FEB. 2026).', THEME_TEXT_DOMAIN ); ?></span><br />
		<input
			type="text"
			id="mwm_evento_fecha"
			name="mwm_evento_fecha"
			value="<?php echo esc_attr( $fecha ); ?>"
			class="large-text"
			autocomplete="off"
			placeholder="<?php esc_attr_e( '11-13 FEB. 2026', THEME_TEXT_DOMAIN ); ?>"
		/>
	</p>
	<p>
		<label for="mwm_evento_lugar"><strong><?php esc_html_e( 'Lugar', THEME_TEXT_DOMAIN ); ?></strong></label><br />
		<input
			type="text"
			id="mwm_evento_lugar"
			name="mwm_evento_lugar"
			value="<?php echo esc_attr( $lugar ); ?>"
			class="large-text"
			autocomplete="off"
		/>
	</p>
	<p>
		<label for="mwm_evento_video_url"><strong><?php esc_html_e( 'URL del vídeo', THEME_TEXT_DOMAIN ); ?></strong></label><br />
		<span class="description"><?php esc_html_e( 'Enlace (YouTube, Vimeo o archivo .mp4)', THEME_TEXT_DOMAIN ); ?></span><br />
		<input
			type="url"
			id="mwm_evento_video_url"
			name="mwm_evento_video_url"
			value="<?php echo esc_attr( $video ); ?>"
			class="large-text code"
			placeholder="https://"
			autocomplete="off"
		/>
	</p>
	<?php
}

/**
 * Guarda metadatos del evento.
 *
 * @param int     $post_id ID del post.
 * @param WP_Post $post    Objeto post.
 */
function mwm_evento_save_data( $post_id, $post ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! isset( $_POST['mwm_evento_data_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mwm_evento_data_nonce'] ) ), 'mwm_evento_data_save' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( MWM_EVENTO_POST_TYPE !== $post->post_type ) {
		return;
	}

	$fecha = isset( $_POST['mwm_evento_fecha'] )
		? mwm_evento_sanitize_fecha( wp_unslash( $_POST['mwm_evento_fecha'] ) )
		: '';
	if ( '' !== $fecha ) {
		update_post_meta( $post_id, MWM_EVENTO_FECHA_META, $fecha );
	} else {
		delete_post_meta( $post_id, MWM_EVENTO_FECHA_META );
	}

	if ( isset( $_POST['mwm_evento_lugar'] ) ) {
		$lugar = sanitize_text_field( wp_unslash( $_POST['mwm_evento_lugar'] ) );
		if ( '' !== $lugar ) {
			update_post_meta( $post_id, MWM_EVENTO_LUGAR_META, $lugar );
		} else {
			delete_post_meta( $post_id, MWM_EVENTO_LUGAR_META );
		}
	}

	$video = isset( $_POST['mwm_evento_video_url'] )
		? esc_url_raw( wp_unslash( $_POST['mwm_evento_video_url'] ) )
		: '';
	if ( '' !== $video ) {
		update_post_meta( $post_id, MWM_EVENTO_VIDEO_URL_META, $video );
	} else {
		delete_post_meta( $post_id, MWM_EVENTO_VIDEO_URL_META );
	}
}
add_action( 'save_post_' . MWM_EVENTO_POST_TYPE, 'mwm_evento_save_data', 10, 2 );

/**
 * Sin vista individual: las URLs de ficha devuelven 404 (el archive sigue activo en /eventos/).
 */
function mwm_evento_block_single_view() {
	if ( ! is_singular( MWM_EVENTO_POST_TYPE ) ) {
		return;
	}
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();
}
add_action( 'template_redirect', 'mwm_evento_block_single_view' );

/**
 * Regenera enlaces permanentes al activar el tema.
 */
function mwm_eventos_rewrite_flush() {
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'mwm_eventos_rewrite_flush' );

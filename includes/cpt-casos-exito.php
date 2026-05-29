<?php
/**
 * CPT Casos de éxito + metas (Pre título, URL vídeo).
 *
 * @package zenyx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MWM_CASO_EXITO_POST_TYPE', 'caso_exito' );
define( 'MWM_CASO_EXITO_PRE_TITULO_META', 'mwm_pre_titulo' );
define( 'MWM_CASO_EXITO_VIDEO_URL_META', 'mwm_caso_video_url' );
define( 'MWM_CASO_EXITO_DISABLE_SINGLE_META', 'mwm_caso_disable_single' );

/**
 * Indica si el caso tiene deshabilitado el single.
 *
 * @param int $post_id ID del caso.
 * @return bool
 */
function mwm_caso_exito_single_disabled( $post_id ) {
	$post_id = absint( $post_id );
	if ( $post_id < 1 ) {
		return false;
	}

	return (bool) get_post_meta( $post_id, MWM_CASO_EXITO_DISABLE_SINGLE_META, true );
}

/**
 * Marcado de bloques por defecto para el editor (hero + contenido + slider relacionados).
 *
 * @return string
 */
function mwm_caso_exito_get_default_block_markup() {
	return trim(
		"<!-- wp:zenyx/hero-04 /-->\n<!-- wp:zenyx/caso-exito-content-01 /-->\n<!-- wp:zenyx/slider-casos-01 /-->"
	);
}

/**
 * Si el cuerpo sigue vacío al guardar, rellena con los bloques por defecto (borradores / casos legacy).
 * No actúa si ya hay bloques o texto en `post_content`.
 *
 * @param int     $post_id ID del post.
 * @param WP_Post $post    Objeto post.
 * @param bool    $update  Si es actualización.
 */
function mwm_caso_exito_maybe_seed_block_template( $post_id, $post, $update ) {
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}
	if ( ! $post instanceof WP_Post || MWM_CASO_EXITO_POST_TYPE !== $post->post_type ) {
		return;
	}
	$content = (string) get_post_field( 'post_content', $post_id, 'raw' );
	if ( '' !== trim( $content ) ) {
		return;
	}

	$markup = mwm_caso_exito_get_default_block_markup();
	remove_action( 'save_post_' . MWM_CASO_EXITO_POST_TYPE, 'mwm_caso_exito_maybe_seed_block_template', 5 );
	wp_update_post(
		array(
			'ID'           => $post_id,
			'post_content' => wp_slash( $markup ),
		)
	);
	add_action( 'save_post_' . MWM_CASO_EXITO_POST_TYPE, 'mwm_caso_exito_maybe_seed_block_template', 5, 3 );
}

/**
 * Registra el tipo de contenido.
 */
function mwm_register_casos_exito() {
	$labels = array(
		'name'                  => _x( 'Casos de éxito', 'post type general name', 'zenyx' ),
		'singular_name'         => _x( 'Caso de éxito', 'post type singular name', 'zenyx' ),
		'menu_name'             => _x( 'Casos de éxito', 'admin menu', 'zenyx' ),
		'name_admin_bar'        => _x( 'Caso de éxito', 'add new on admin bar', 'zenyx' ),
		'add_new'               => _x( 'Añadir nuevo', 'caso_exito', 'zenyx' ),
		'add_new_item'          => __( 'Añadir nuevo caso de éxito', 'zenyx' ),
		'new_item'              => __( 'Nuevo caso de éxito', 'zenyx' ),
		'edit_item'             => __( 'Editar caso de éxito', 'zenyx' ),
		'view_item'             => __( 'Ver caso de éxito', 'zenyx' ),
		'all_items'             => __( 'Todos los casos de éxito', 'zenyx' ),
		'search_items'          => __( 'Buscar casos de éxito', 'zenyx' ),
		'parent_item_colon'     => __( 'Caso de éxito superior:', 'zenyx' ),
		'not_found'             => __( 'No se encontraron casos de éxito.', 'zenyx' ),
		'not_found_in_trash'    => __( 'No hay casos de éxito en la papelera.', 'zenyx' ),
		'featured_image'        => __( 'Imagen destacada', 'zenyx' ),
		'set_featured_image'    => __( 'Establecer imagen destacada', 'zenyx' ),
		'remove_featured_image' => __( 'Quitar imagen destacada', 'zenyx' ),
		'use_featured_image'    => __( 'Usar como imagen destacada', 'zenyx' ),
		'archives'              => __( 'Archivo de casos de éxito', 'zenyx' ),
		'insert_into_item'      => __( 'Insertar en el caso de éxito', 'zenyx' ),
		'uploaded_to_this_item' => __( 'Subidos a este caso de éxito', 'zenyx' ),
		'filter_items_list'     => __( 'Filtrar lista de casos de éxito', 'zenyx' ),
		'items_list_navigation' => __( 'Navegación de lista de casos de éxito', 'zenyx' ),
		'items_list'            => __( 'Lista de casos de éxito', 'zenyx' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_nav_menus'  => true,
		'show_in_admin_bar'  => true,
		'show_in_rest'       => true,
		'rest_base'          => 'casos-exito',
		'query_var'          => true,
		'rewrite'            => array(
			'slug'       => 'casos-exito',
			'with_front' => false,
		),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 20,
		'menu_icon'          => 'dashicons-awards',
		'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
		/*
		 * Plantilla inicial del editor de bloques (nuevos casos de éxito).
		 * Mismo orden que la maqueta: hero 04 → contenido caso → slider relacionados.
		 */
		'template'           => array(
			array( 'zenyx/hero-04', array() ),
			array( 'zenyx/caso-exito-content-01', array() ),
			array( 'zenyx/slider-casos-01', array() ),
		),
	);

	register_post_type( MWM_CASO_EXITO_POST_TYPE, $args );
}
add_action( 'init', 'mwm_register_casos_exito', 0 );

add_action( 'save_post_' . MWM_CASO_EXITO_POST_TYPE, 'mwm_caso_exito_maybe_seed_block_template', 5, 3 );

/**
 * Meta "Pre título" (REST + plantillas).
 */
function mwm_register_caso_exito_pre_titulo_meta() {
	register_post_meta(
		MWM_CASO_EXITO_POST_TYPE,
		MWM_CASO_EXITO_PRE_TITULO_META,
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
}
add_action( 'init', 'mwm_register_caso_exito_pre_titulo_meta', 1 );

/**
 * Meta URL de vídeo (botón Play en listados / archive).
 */
function mwm_register_caso_exito_video_url_meta() {
	register_post_meta(
		MWM_CASO_EXITO_POST_TYPE,
		MWM_CASO_EXITO_VIDEO_URL_META,
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
add_action( 'init', 'mwm_register_caso_exito_video_url_meta', 1 );

/**
 * Meta booleano para deshabilitar el single y ocultar CTAs al detalle.
 */
function mwm_register_caso_exito_disable_single_meta() {
	register_post_meta(
		MWM_CASO_EXITO_POST_TYPE,
		MWM_CASO_EXITO_DISABLE_SINGLE_META,
		array(
			'type'              => 'boolean',
			'single'            => true,
			'sanitize_callback' => 'rest_sanitize_boolean',
			'show_in_rest'      => true,
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'mwm_register_caso_exito_disable_single_meta', 1 );

/**
 * Metabox en el editor (clásico y bloques).
 */
function mwm_caso_exito_add_meta_boxes() {
	add_meta_box(
		'mwm_caso_exito_pre_titulo',
		__( 'Pre título', 'zenyx' ),
		'mwm_caso_exito_pre_titulo_metabox',
		MWM_CASO_EXITO_POST_TYPE,
		'side',
		'high'
	);

	add_meta_box(
		'mwm_caso_exito_video_url',
		__( 'URL del vídeo', 'zenyx' ),
		'mwm_caso_exito_video_url_metabox',
		MWM_CASO_EXITO_POST_TYPE,
		'side',
		'default'
	);

	add_meta_box(
		'mwm_caso_exito_single_settings',
		__( 'Single', 'zenyx' ),
		'mwm_caso_exito_single_settings_metabox',
		MWM_CASO_EXITO_POST_TYPE,
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'mwm_caso_exito_add_meta_boxes' );

/**
 * Contenido del metabox Pre título.
 *
 * @param WP_Post $post Post actual.
 */
function mwm_caso_exito_pre_titulo_metabox( $post ) {
	wp_nonce_field( 'mwm_caso_exito_pre_titulo_save', 'mwm_caso_exito_pre_titulo_nonce' );
	$value = get_post_meta( $post->ID, MWM_CASO_EXITO_PRE_TITULO_META, true );
	?>
	<p>
		<label for="mwm_pre_titulo" class="screen-reader-text"><?php esc_html_e( 'Pre título', 'zenyx' ); ?></label>
		<input
			type="text"
			id="mwm_pre_titulo"
			name="mwm_pre_titulo"
			value="<?php echo esc_attr( $value ); ?>"
			class="large-text"
			autocomplete="off"
		/>
	</p>
	<?php
}

/**
 * Guarda el meta Pre título.
 *
 * @param int     $post_id ID del post.
 * @param WP_Post $post    Objeto post.
 */
function mwm_caso_exito_save_pre_titulo( $post_id, $post ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! isset( $_POST['mwm_caso_exito_pre_titulo_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mwm_caso_exito_pre_titulo_nonce'] ) ), 'mwm_caso_exito_pre_titulo_save' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( MWM_CASO_EXITO_POST_TYPE !== $post->post_type ) {
		return;
	}
	$value = isset( $_POST['mwm_pre_titulo'] )
		? sanitize_text_field( wp_unslash( $_POST['mwm_pre_titulo'] ) )
		: '';
	update_post_meta( $post_id, MWM_CASO_EXITO_PRE_TITULO_META, $value );
}
add_action( 'save_post_' . MWM_CASO_EXITO_POST_TYPE, 'mwm_caso_exito_save_pre_titulo', 10, 2 );

/**
 * Contenido del metabox URL de vídeo.
 *
 * @param WP_Post $post Post actual.
 */
function mwm_caso_exito_video_url_metabox( $post ) {
	wp_nonce_field( 'mwm_caso_exito_video_url_save', 'mwm_caso_exito_video_url_nonce' );
	$value = get_post_meta( $post->ID, MWM_CASO_EXITO_VIDEO_URL_META, true );
	?>
	<p>
		<label for="mwm_caso_video_url"><?php esc_html_e( 'Enlace (YouTube, Vimeo o archivo .mp4)', 'zenyx' ); ?></label>
		<input
			type="url"
			id="mwm_caso_video_url"
			name="mwm_caso_video_url"
			value="<?php echo esc_attr( $value ); ?>"
			class="large-text code"
			placeholder="https://"
			autocomplete="off"
		/>
	</p>
	<?php
}

/**
 * Contenido del metabox de ajustes del single.
 *
 * @param WP_Post $post Post actual.
 */
function mwm_caso_exito_single_settings_metabox( $post ) {
	wp_nonce_field( 'mwm_caso_exito_single_settings_save', 'mwm_caso_exito_single_settings_nonce' );
	$disabled = mwm_caso_exito_single_disabled( $post->ID );
	?>
	<p>
		<label for="mwm_caso_disable_single">
			<input
				type="checkbox"
				id="mwm_caso_disable_single"
				name="mwm_caso_disable_single"
				value="1"
				<?php checked( $disabled ); ?>
			/>
			<?php esc_html_e( 'Deshabilitar single y ocultar "Ver caso".', 'zenyx' ); ?>
		</label>
	</p>
	<?php
}

/**
 * Guarda el meta URL de vídeo.
 *
 * @param int     $post_id ID del post.
 * @param WP_Post $post    Objeto post.
 */
function mwm_caso_exito_save_video_url( $post_id, $post ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! isset( $_POST['mwm_caso_exito_video_url_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mwm_caso_exito_video_url_nonce'] ) ), 'mwm_caso_exito_video_url_save' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( MWM_CASO_EXITO_POST_TYPE !== $post->post_type ) {
		return;
	}
	$value = isset( $_POST['mwm_caso_video_url'] )
		? esc_url_raw( wp_unslash( $_POST['mwm_caso_video_url'] ) )
		: '';
	update_post_meta( $post_id, MWM_CASO_EXITO_VIDEO_URL_META, $value );
}
add_action( 'save_post_' . MWM_CASO_EXITO_POST_TYPE, 'mwm_caso_exito_save_video_url', 10, 2 );

/**
 * Guarda el meta booleano para deshabilitar el single.
 *
 * @param int     $post_id ID del post.
 * @param WP_Post $post    Objeto post.
 */
function mwm_caso_exito_save_single_settings( $post_id, $post ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! isset( $_POST['mwm_caso_exito_single_settings_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mwm_caso_exito_single_settings_nonce'] ) ), 'mwm_caso_exito_single_settings_save' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( MWM_CASO_EXITO_POST_TYPE !== $post->post_type ) {
		return;
	}

	$disabled = isset( $_POST['mwm_caso_disable_single'] ) ? 1 : 0;
	update_post_meta( $post_id, MWM_CASO_EXITO_DISABLE_SINGLE_META, $disabled );
}
add_action( 'save_post_' . MWM_CASO_EXITO_POST_TYPE, 'mwm_caso_exito_save_single_settings', 10, 2 );

/**
 * Si el caso tiene el single deshabilitado, la URL de detalle devuelve 404.
 */
function mwm_caso_exito_block_disabled_single_view() {
	if ( ! is_singular( MWM_CASO_EXITO_POST_TYPE ) || is_preview() ) {
		return;
	}

	$post_id = (int) get_queried_object_id();
	if ( $post_id < 1 || ! mwm_caso_exito_single_disabled( $post_id ) ) {
		return;
	}

	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();
}
add_action( 'template_redirect', 'mwm_caso_exito_block_disabled_single_view' );

if ( ! function_exists( 'mwm_casos_row_default_active_index' ) ) {
	/**
	 * Índice de card activa por defecto en el archive (patrón cada 3 filas).
	 *
	 * Filas 0,3,6… → primera card (0).
	 * Filas 1,4,7… → última card.
	 * Filas 2,5,8… → segunda card.
	 *
	 * @param int $row_index     Índice de fila (0-based).
	 * @param int $cards_in_row Número de cards en esa fila (1–3).
	 * @return int Índice dentro de la fila (0-based).
	 */
	function mwm_casos_row_default_active_index( $row_index, $cards_in_row ) {
		$cards_in_row = max( 1, min( 3, (int) $cards_in_row ) );
		$mod          = absint( $row_index ) % 3;

		if ( 1 === $mod ) {
			return $cards_in_row - 1;
		}
		if ( 2 === $mod ) {
			return min( 1, $cards_in_row - 1 );
		}

		return 0;
	}
}

/**
 * Regenera enlaces permanentes al activar el tema (el CPT ya está registrado en `init`).
 */
function mwm_casos_exito_rewrite_flush() {
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'mwm_casos_exito_rewrite_flush' );

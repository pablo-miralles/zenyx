<?php
/**
 * CPT Podcasts + metas (episodio, fecha, duración, Spotify, YouTube).
 *
 * Archivo público; sin vista individual ni taxonomías.
 *
 * @package zenyx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MWM_PODCAST_POST_TYPE', 'podcast' );
define( 'MWM_PODCAST_EPISODE_META', 'mwm_podcast_episode' );
define( 'MWM_PODCAST_EPISODE_DATE_META', 'mwm_podcast_episode_date' );
define( 'MWM_PODCAST_DURATION_META', 'mwm_podcast_duration' );
define( 'MWM_PODCAST_SPOTIFY_URL_META', 'mwm_podcast_spotify_url' );
define( 'MWM_PODCAST_YOUTUBE_URL_META', 'mwm_podcast_youtube_url' );

/**
 * Registra el tipo de contenido.
 */
function mwm_register_podcasts() {
	$labels = array(
		'name'                  => _x( 'Podcasts', 'post type general name', THEME_TEXT_DOMAIN ),
		'singular_name'         => _x( 'Podcast', 'post type singular name', THEME_TEXT_DOMAIN ),
		'menu_name'             => _x( 'Podcasts', 'admin menu', THEME_TEXT_DOMAIN ),
		'name_admin_bar'        => _x( 'Podcast', 'add new on admin bar', THEME_TEXT_DOMAIN ),
		'add_new'               => _x( 'Añadir nuevo', 'podcast', THEME_TEXT_DOMAIN ),
		'add_new_item'          => __( 'Añadir nuevo podcast', THEME_TEXT_DOMAIN ),
		'new_item'              => __( 'Nuevo podcast', THEME_TEXT_DOMAIN ),
		'edit_item'             => __( 'Editar podcast', THEME_TEXT_DOMAIN ),
		'view_item'             => __( 'Ver podcast', THEME_TEXT_DOMAIN ),
		'all_items'             => __( 'Todos los podcasts', THEME_TEXT_DOMAIN ),
		'search_items'          => __( 'Buscar podcasts', THEME_TEXT_DOMAIN ),
		'parent_item_colon'     => __( 'Podcast superior:', THEME_TEXT_DOMAIN ),
		'not_found'             => __( 'No se encontraron podcasts.', THEME_TEXT_DOMAIN ),
		'not_found_in_trash'    => __( 'No hay podcasts en la papelera.', THEME_TEXT_DOMAIN ),
		'featured_image'        => __( 'Imagen destacada', THEME_TEXT_DOMAIN ),
		'set_featured_image'    => __( 'Establecer imagen destacada', THEME_TEXT_DOMAIN ),
		'remove_featured_image' => __( 'Quitar imagen destacada', THEME_TEXT_DOMAIN ),
		'use_featured_image'    => __( 'Usar como imagen destacada', THEME_TEXT_DOMAIN ),
		'archives'              => __( 'Archivo de podcasts', THEME_TEXT_DOMAIN ),
		'insert_into_item'      => __( 'Insertar en el podcast', THEME_TEXT_DOMAIN ),
		'uploaded_to_this_item' => __( 'Subidos a este podcast', THEME_TEXT_DOMAIN ),
		'filter_items_list'     => __( 'Filtrar lista de podcasts', THEME_TEXT_DOMAIN ),
		'items_list_navigation' => __( 'Navegación de lista de podcasts', THEME_TEXT_DOMAIN ),
		'items_list'            => __( 'Lista de podcasts', THEME_TEXT_DOMAIN ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => false,
		'exclude_from_search' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_nav_menus'  => true,
		'show_in_admin_bar'  => true,
		'show_in_rest'       => true,
		'rest_base'          => 'podcasts',
		'query_var'          => true,
		'rewrite'            => array(
			'slug'       => 'podcasts',
			'with_front' => false,
		),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => 21,
		'menu_icon'          => 'dashicons-microphone',
		'supports'           => array( 'title', 'thumbnail', 'revisions' ),
	);

	register_post_type( MWM_PODCAST_POST_TYPE, $args );
}
add_action( 'init', 'mwm_register_podcasts', 0 );

/**
 * Sanitiza fecha de episodio (Y-m-d).
 *
 * @param mixed $value Valor enviado.
 * @return string Cadena vacía o Y-m-d válida.
 */
function mwm_podcast_sanitize_episode_date( $value ) {
	$value = is_string( $value ) ? sanitize_text_field( $value ) : '';
	if ( '' === $value ) {
		return '';
	}
	$d = \DateTime::createFromFormat( 'Y-m-d', $value );
	if ( $d && $d->format( 'Y-m-d' ) === $value ) {
		return $value;
	}
	return '';
}

/**
 * Registra metadatos (REST + plantillas).
 */
function mwm_register_podcast_metas() {
	register_post_meta(
		MWM_PODCAST_POST_TYPE,
		MWM_PODCAST_EPISODE_META,
		array(
			'type'              => 'integer',
			'single'            => true,
			'sanitize_callback' => function ( $value ) {
				return absint( $value );
			},
			'show_in_rest'      => true,
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		MWM_PODCAST_POST_TYPE,
		MWM_PODCAST_EPISODE_DATE_META,
		array(
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'mwm_podcast_sanitize_episode_date',
			'show_in_rest'      => true,
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		MWM_PODCAST_POST_TYPE,
		MWM_PODCAST_DURATION_META,
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
		MWM_PODCAST_POST_TYPE,
		MWM_PODCAST_SPOTIFY_URL_META,
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

	register_post_meta(
		MWM_PODCAST_POST_TYPE,
		MWM_PODCAST_YOUTUBE_URL_META,
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
add_action( 'init', 'mwm_register_podcast_metas', 1 );

/**
 * Metabox datos del episodio.
 */
function mwm_podcast_add_meta_boxes() {
	add_meta_box(
		'mwm_podcast_episode_data',
		__( 'Datos del episodio', THEME_TEXT_DOMAIN ),
		'mwm_podcast_episode_data_metabox',
		MWM_PODCAST_POST_TYPE,
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'mwm_podcast_add_meta_boxes' );

/**
 * Contenido del metabox.
 *
 * @param WP_Post $post Post actual.
 */
function mwm_podcast_episode_data_metabox( $post ) {
	wp_nonce_field( 'mwm_podcast_episode_data_save', 'mwm_podcast_episode_data_nonce' );

	$episode   = get_post_meta( $post->ID, MWM_PODCAST_EPISODE_META, true );
	$ep_date   = get_post_meta( $post->ID, MWM_PODCAST_EPISODE_DATE_META, true );
	$duration  = get_post_meta( $post->ID, MWM_PODCAST_DURATION_META, true );
	$spotify   = get_post_meta( $post->ID, MWM_PODCAST_SPOTIFY_URL_META, true );
	$youtube   = get_post_meta( $post->ID, MWM_PODCAST_YOUTUBE_URL_META, true );
	?>
	<p>
		<label for="mwm_podcast_episode"><strong><?php esc_html_e( 'Número de episodio', THEME_TEXT_DOMAIN ); ?></strong></label><br />
		<input
			type="number"
			id="mwm_podcast_episode"
			name="mwm_podcast_episode"
			value="<?php echo esc_attr( '' !== $episode ? (string) (int) $episode : '' ); ?>"
			class="small-text"
			min="1"
			step="1"
			autocomplete="off"
		/>
	</p>
	<p>
		<label for="mwm_podcast_episode_date"><strong><?php esc_html_e( 'Fecha', THEME_TEXT_DOMAIN ); ?></strong></label><br />
		<input
			type="date"
			id="mwm_podcast_episode_date"
			name="mwm_podcast_episode_date"
			value="<?php echo esc_attr( $ep_date ); ?>"
			class="regular-text"
			autocomplete="off"
		/>
	</p>
	<p>
		<label for="mwm_podcast_duration"><strong><?php esc_html_e( 'Duración', THEME_TEXT_DOMAIN ); ?></strong></label><br />
		<input
			type="text"
			id="mwm_podcast_duration"
			name="mwm_podcast_duration"
			value="<?php echo esc_attr( $duration ); ?>"
			class="large-text"
			placeholder="<?php esc_attr_e( '1h 16 min', THEME_TEXT_DOMAIN ); ?>"
			autocomplete="off"
		/>
	</p>
	<p>
		<label for="mwm_podcast_spotify_url"><strong><?php esc_html_e( 'Enlace a Spotify', THEME_TEXT_DOMAIN ); ?></strong></label><br />
		<input
			type="url"
			id="mwm_podcast_spotify_url"
			name="mwm_podcast_spotify_url"
			value="<?php echo esc_attr( $spotify ); ?>"
			class="large-text code"
			placeholder="https://"
			autocomplete="off"
		/>
	</p>
	<p>
		<label for="mwm_podcast_youtube_url"><strong><?php esc_html_e( 'Enlace a YouTube', THEME_TEXT_DOMAIN ); ?></strong></label><br />
		<input
			type="url"
			id="mwm_podcast_youtube_url"
			name="mwm_podcast_youtube_url"
			value="<?php echo esc_attr( $youtube ); ?>"
			class="large-text code"
			placeholder="https://"
			autocomplete="off"
		/>
	</p>
	<?php
}

/**
 * Guarda metadatos del episodio.
 *
 * @param int     $post_id ID del post.
 * @param WP_Post $post    Objeto post.
 */
function mwm_podcast_save_episode_data( $post_id, $post ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! isset( $_POST['mwm_podcast_episode_data_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mwm_podcast_episode_data_nonce'] ) ), 'mwm_podcast_episode_data_save' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( MWM_PODCAST_POST_TYPE !== $post->post_type ) {
		return;
	}

	if ( isset( $_POST['mwm_podcast_episode'] ) && '' !== $_POST['mwm_podcast_episode'] ) {
		update_post_meta( $post_id, MWM_PODCAST_EPISODE_META, absint( wp_unslash( $_POST['mwm_podcast_episode'] ) ) );
	} else {
		delete_post_meta( $post_id, MWM_PODCAST_EPISODE_META );
	}

	$date_val = isset( $_POST['mwm_podcast_episode_date'] )
		? mwm_podcast_sanitize_episode_date( wp_unslash( $_POST['mwm_podcast_episode_date'] ) )
		: '';
	if ( '' !== $date_val ) {
		update_post_meta( $post_id, MWM_PODCAST_EPISODE_DATE_META, $date_val );
	} else {
		delete_post_meta( $post_id, MWM_PODCAST_EPISODE_DATE_META );
	}

	if ( isset( $_POST['mwm_podcast_duration'] ) ) {
		$dur = sanitize_text_field( wp_unslash( $_POST['mwm_podcast_duration'] ) );
		if ( '' !== $dur ) {
			update_post_meta( $post_id, MWM_PODCAST_DURATION_META, $dur );
		} else {
			delete_post_meta( $post_id, MWM_PODCAST_DURATION_META );
		}
	}

	$spotify = isset( $_POST['mwm_podcast_spotify_url'] )
		? esc_url_raw( wp_unslash( $_POST['mwm_podcast_spotify_url'] ) )
		: '';
	if ( '' !== $spotify ) {
		update_post_meta( $post_id, MWM_PODCAST_SPOTIFY_URL_META, $spotify );
	} else {
		delete_post_meta( $post_id, MWM_PODCAST_SPOTIFY_URL_META );
	}

	$youtube = isset( $_POST['mwm_podcast_youtube_url'] )
		? esc_url_raw( wp_unslash( $_POST['mwm_podcast_youtube_url'] ) )
		: '';
	if ( '' !== $youtube ) {
		update_post_meta( $post_id, MWM_PODCAST_YOUTUBE_URL_META, $youtube );
	} else {
		delete_post_meta( $post_id, MWM_PODCAST_YOUTUBE_URL_META );
	}
}
add_action( 'save_post_' . MWM_PODCAST_POST_TYPE, 'mwm_podcast_save_episode_data', 10, 2 );

/**
 * Regenera enlaces permanentes al activar el tema.
 */
function mwm_podcasts_rewrite_flush() {
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'mwm_podcasts_rewrite_flush' );

<?php
/**
 * Blog entries grid: query helpers and AJAX loader.
 *
 * @package zenyx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'mwm_blog_entries_posts_per_page' ) ) {
	/**
	 * Posts per page for blog grid / load more.
	 *
	 * @return int
	 */
	function mwm_blog_entries_posts_per_page() {
		return (int) apply_filters( 'mwm_blog_entries_posts_per_page', 9 );
	}
}

if ( ! function_exists( 'mwm_blog_entries_sanitize_category_ids' ) ) {
	/**
	 * @param mixed $raw Raw input (array of IDs or JSON string).
	 * @return int[]
	 */
	function mwm_blog_entries_sanitize_category_ids( $raw ) {
		if ( is_string( $raw ) && '' !== $raw ) {
			$decoded = json_decode( wp_unslash( $raw ), true );
			$raw     = is_array( $decoded ) ? $decoded : array();
		}
		if ( ! is_array( $raw ) ) {
			return array();
		}
		return array_values( array_filter( array_map( 'absint', $raw ) ) );
	}
}

if ( ! function_exists( 'mwm_blog_entries_query_args' ) ) {
	/**
	 * @param int   $paged Page number.
	 * @param int[] $category_ids Category term IDs (OR).
	 * @return array
	 */
	function mwm_blog_entries_query_args( $paged, $category_ids ) {
		$paged = max( 1, (int) $paged );
		$args  = array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => mwm_blog_entries_posts_per_page(),
			'paged'               => $paged,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => false,
		);
		$ids = array_filter( array_map( 'absint', (array) $category_ids ) );
		if ( ! empty( $ids ) ) {
			$args['category__in'] = $ids;
		}
		return $args;
	}
}

if ( ! function_exists( 'mwm_blog_entries_context_category_ids' ) ) {
	/**
	 * Category term IDs for the current request (e.g. category archive).
	 *
	 * @return int[]
	 */
	function mwm_blog_entries_context_category_ids() {
		if ( ! is_category() ) {
			return array();
		}
		$term_id = (int) get_queried_object_id();
		return $term_id > 0 ? array( $term_id ) : array();
	}
}

if ( ! function_exists( 'mwm_get_related_posts_query' ) ) {
	/**
	 * Posts relacionados: misma categoría que el post actual (excluido), hasta $limit.
	 * Si hay menos de $limit en esa categoría, completa con publicaciones recientes (excluyendo ya mostradas).
	 *
	 * @param int $post_id Post ID actual.
	 * @param int $limit   Número máximo de entradas (por defecto 3).
	 * @return WP_Query
	 */
	function mwm_get_related_posts_query( $post_id, $limit = 3 ) {
		$post_id = absint( $post_id );
		$limit   = max( 1, (int) $limit );

		$cats = wp_get_post_categories(
			$post_id,
			array(
				'fields' => 'ids',
			)
		);

		$args = array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $limit,
			'post__not_in'        => array( $post_id ),
			'ignore_sticky_posts' => true,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'no_found_rows'       => false,
		);

		if ( ! empty( $cats ) ) {
			$args['category__in'] = $cats;
		}

		$query = new WP_Query( $args );

		if ( (int) $query->post_count >= $limit || empty( $cats ) ) {
			return $query;
		}

		$have_ids = wp_list_pluck( $query->posts, 'ID' );
		$need     = $limit - count( $have_ids );
		if ( $need < 1 ) {
			return $query;
		}

		$backfill = new WP_Query(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => $need,
				'post__not_in'        => array_merge( array( $post_id ), $have_ids ),
				'ignore_sticky_posts' => true,
				'orderby'             => 'date',
				'order'               => 'DESC',
			)
		);

		$merged_ids = array_merge( $have_ids, wp_list_pluck( $backfill->posts, 'ID' ) );
		$merged_ids = array_slice( array_values( array_unique( array_map( 'absint', $merged_ids ) ) ), 0, $limit );

		if ( empty( $merged_ids ) ) {
			return $query;
		}

		return new WP_Query(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'post__in'            => $merged_ids,
				'orderby'             => 'post__in',
				'posts_per_page'      => count( $merged_ids ),
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);
	}
}

if ( ! function_exists( 'mwm_blog_entries_render_cards_html' ) ) {
	/**
	 * @param WP_Query $query Query with posts.
	 * @return string
	 */
	function mwm_blog_entries_render_cards_html( $query ) {
		if ( ! $query instanceof WP_Query ) {
			return '';
		}
		ob_start();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				get_template_part( 'template-parts/card-post' );
			}
		}
		wp_reset_postdata();
		return ob_get_clean();
	}
}

if ( ! function_exists( 'mwm_ajax_blog_entries' ) ) {
	/**
	 * AJAX: return HTML fragment for blog cards.
	 */
	function mwm_ajax_blog_entries() {
		check_ajax_referer( 'mwm_blog_entries', 'nonce' );

		$paged = isset( $_POST['paged'] ) ? absint( wp_unslash( $_POST['paged'] ) ) : 1;
		$paged = max( 1, $paged );

		$category_ids = array();
		if ( isset( $_POST['categories'] ) ) {
			$category_ids = mwm_blog_entries_sanitize_category_ids( wp_unslash( $_POST['categories'] ) );
		}

		$q = new WP_Query( mwm_blog_entries_query_args( $paged, $category_ids ) );

		$html    = mwm_blog_entries_render_cards_html( $q );
		$has_more = (int) $q->max_num_pages > $paged;

		wp_send_json_success(
			array(
				'html'      => $html,
				'has_more'  => $has_more,
				'next_page' => $paged + 1,
				'found'     => (int) $q->found_posts,
			)
		);
	}
	add_action( 'wp_ajax_mwm_blog_entries', 'mwm_ajax_blog_entries' );
	add_action( 'wp_ajax_nopriv_mwm_blog_entries', 'mwm_ajax_blog_entries' );
}

<?php
/**
 * Banner query functionality
 *
 * @since      1.0.0
 *
 * @package    Festival_Banner
 * @subpackage Festival_Banner/includes
 */

/**
 * Banner query functionality.
 *
 * Handles all database queries for fetching banners.
 *
 * @package    Festival_Banner
 * @subpackage Festival_Banner/includes
 * @author     justSohel <thesohelrana.me@gmail.com>
 */
class Festival_Banner_Query {

	/**
	 * Get active banners for the current page.
	 *
	 * @since  1.0.0
	 * @param  int $page_id The current page ID (0 for homepage).
	 * @return array Array of banner objects.
	 */
	public static function get_active_banners( $page_id = 0 ) {
		// Try to get from cache first.
		$cache_key = 'festival_banner_active_' . $page_id;
		$banners   = get_transient( $cache_key );

		if ( false !== $banners ) {
			return $banners;
		}

		// Query published banners.
		$args = array(
			'post_type'      => 'festival_banner',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		$query   = new WP_Query( $args );
		$banners = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$banner_id = get_the_ID();

				// Check if banner should display on this page.
				if ( ! self::should_display_on_page( $banner_id, $page_id ) ) {
					continue;
				}

				// Check if banner is within schedule.
				if ( ! self::is_within_schedule( $banner_id ) ) {
					continue;
				}

				// Get banner data.
				$banner_data = self::get_banner_data( $banner_id );

				if ( $banner_data ) {
					$banners[] = $banner_data;
				}
			}
			wp_reset_postdata();
		}

		// Group banners by position and keep only newest per position.
		$banners = self::filter_by_position( $banners );

		// Cache for 1 hour.
		// set_transient( $cache_key, $banners, HOUR_IN_SECONDS );
		set_transient( $cache_key, $banners, HOUR_IN_SECONDS );

		return $banners;
	}

	/**
	 * Get banner data by ID.
	 *
	 * @since  1.0.0
	 * @param  int $banner_id The banner post ID.
	 * @return object|false Banner data object or false.
	 */
	public static function get_banner_data( $banner_id ) {
		$post = get_post( $banner_id );

		if ( ! $post ) {
			return false;
		}

		// Get all meta data.
		$banner = new stdClass();

		$banner->ID                = $post->ID;
		$banner->title             = $post->post_title;
		$banner->content           = get_post_meta( $banner_id, '_fb_content', true );
		$banner->cta_text          = get_post_meta( $banner_id, '_fb_cta_text', true );
		$banner->cta_url           = get_post_meta( $banner_id, '_fb_cta_url', true );
		$banner->cta_new_tab       = get_post_meta( $banner_id, '_fb_cta_new_tab', true );
		$banner->position          = get_post_meta( $banner_id, '_fb_position', true );
		$banner->floating_position = get_post_meta( $banner_id, '_fb_floating_position', true );
		$banner->side_position     = get_post_meta( $banner_id, '_fb_side_position', true );
		$banner->modal_delay       = get_post_meta( $banner_id, '_fb_modal_delay', true );
		$banner->display_type      = get_post_meta( $banner_id, '_fb_display_type', true );
		$banner->specific_pages    = get_post_meta( $banner_id, '_fb_specific_pages', true );
		$banner->start_date        = get_post_meta( $banner_id, '_fb_start_date', true );
		$banner->end_date          = get_post_meta( $banner_id, '_fb_end_date', true );
		$banner->is_recurring      = get_post_meta( $banner_id, '_fb_is_recurring', true );
		$banner->is_dismissible    = get_post_meta( $banner_id, '_fb_is_dismissible', true );
		$banner->animation         = get_post_meta( $banner_id, '_fb_animation', true );
		$banner->bg_color          = get_post_meta( $banner_id, '_fb_bg_color', true );
		$banner->text_color        = get_post_meta( $banner_id, '_fb_text_color', true );
		$banner->cta_bg_color      = get_post_meta( $banner_id, '_fb_cta_bg_color', true );
		$banner->cta_text_color    = get_post_meta( $banner_id, '_fb_cta_text_color', true );

		// Set defaults.
		$banner->position          = $banner->position ? $banner->position : 'top_bar';
		$banner->floating_position = $banner->floating_position ? $banner->floating_position : 'bottom_right';
		$banner->side_position     = $banner->side_position ? $banner->side_position : 'right';
		$banner->modal_delay       = $banner->modal_delay ? $banner->modal_delay : 3;
		$banner->display_type      = $banner->display_type ? $banner->display_type : 'all_pages';
		$banner->specific_pages    = is_array( $banner->specific_pages ) ? $banner->specific_pages : array();
		$banner->animation         = $banner->animation ? $banner->animation : 'fade';
		$banner->bg_color          = $banner->bg_color ? $banner->bg_color : '#000000';
		$banner->text_color        = $banner->text_color ? $banner->text_color : '#ffffff';
		$banner->cta_bg_color      = $banner->cta_bg_color ? $banner->cta_bg_color : '#ffffff';
		$banner->cta_text_color    = $banner->cta_text_color ? $banner->cta_text_color : '#000000';
		// $banner->is_dismissible    = ( '' === $banner->is_dismissible ) ? true : $banner->is_dismissible;

		return $banner;
	}

	/**
	 * Check if banner should display on the current page.
	 *
	 * @since  1.0.0
	 * @param  int $banner_id The banner post ID.
	 * @param  int $page_id   The current page ID.
	 * @return bool True if should display, false otherwise.
	 */
	private static function should_display_on_page( $banner_id, $page_id ) {
		$display_type   = get_post_meta( $banner_id, '_fb_display_type', true );
		$specific_pages = get_post_meta( $banner_id, '_fb_specific_pages', true );

		// Default to all pages.
		if ( empty( $display_type ) ) {
			$display_type = 'all_pages';
		}

		switch ( $display_type ) {
			case 'all_pages':
				return true;

			case 'homepage_only':
				return ( 0 === $page_id || is_front_page() );

			case 'specific_pages':
				if ( ! is_array( $specific_pages ) ) {
					return false;
				}
				return in_array( $page_id, $specific_pages, true );

			default:
				return true;
		}
	}

	/**
	 * Check if banner is within its schedule.
	 *
	 * @since  1.0.0
	 * @param  int $banner_id The banner post ID.
	 * @return bool True if within schedule, false otherwise.
	 */
	private static function is_within_schedule( $banner_id ) {
		$start_date = get_post_meta( $banner_id, '_fb_start_date', true );
		$end_date   = get_post_meta( $banner_id, '_fb_end_date', true );

		// No schedule = always active.
		if ( empty( $start_date ) && empty( $end_date ) ) {
			return true;
		}

		$now = current_time( 'timestamp' );

		// Check start date.
		if ( ! empty( $start_date ) ) {
			$start_timestamp = strtotime( $start_date );
			if ( $now < $start_timestamp ) {
				return false; // Not started yet.
			}
		}

		// Check end date.
		if ( ! empty( $end_date ) ) {
			$end_timestamp = strtotime( $end_date );
			if ( $now > $end_timestamp ) {
				return false; // Already expired.
			}
		}

		return true;
	}

	/**
	 * Filter banners by position (keep only newest per position).
	 *
	 * @since  1.0.0
	 * @param  array $banners Array of banner objects.
	 * @return array Filtered banners.
	 */
	private static function filter_by_position( $banners ) {
		$filtered = array();
		$positions_used = array();

		foreach ( $banners as $banner ) {
			$position = $banner->position;

			// For side position with "both", we allow it.
			// For other positions, only keep first (newest) occurrence.
			if ( 'side' === $position && 'both' === $banner->side_position ) {
				// Allow side banners with "both".
				$filtered[] = $banner;
				continue;
			}

			// For floating, allow one per corner.
			if ( 'floating' === $position ) {
				$key = 'floating_' . $banner->floating_position;
				if ( ! isset( $positions_used[ $key ] ) ) {
					$filtered[] = $banner;
					$positions_used[ $key ] = true;
				}
				continue;
			}

			// For other positions, only one per position type.
			if ( ! isset( $positions_used[ $position ] ) ) {
				$filtered[] = $banner;
				$positions_used[ $position ] = true;
			}
		}

		return $filtered;
	}

	/**
	 * Clear all banner caches.
	 *
	 * @since 1.0.0
	 */
	public static function clear_all_caches() {
		global $wpdb;

		// Delete all festival banner transients.
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} 
				WHERE option_name LIKE %s 
				OR option_name LIKE %s",
				$wpdb->esc_like( '_transient_festival_banner_' ) . '%',
				$wpdb->esc_like( '_transient_timeout_festival_banner_' ) . '%'
			)
		);
	}

	/**
	 * Get expired recurring banners.
	 *
	 * Used in admin notices.
	 *
	 * @since  1.0.0
	 * @return array Array of post IDs.
	 */
	public static function get_expired_recurring_banners() {
		$args = array(
			'post_type'      => 'festival_banner',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'   => '_fb_is_recurring',
					'value' => '1',
				),
				array(
					'key'     => '_fb_end_date',
					'value'   => current_time( 'mysql' ),
					'compare' => '<',
					'type'    => 'DATETIME',
				),
			),
			'fields'         => 'ids',
		);

		$query = new WP_Query( $args );
		return $query->posts;
	}
}
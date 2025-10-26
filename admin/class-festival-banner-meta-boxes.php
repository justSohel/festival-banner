<?php
/**
 * Meta boxes functionality for the admin area.
 *
 * @since      1.0.0
 *
 * @package    Festival_Banner
 * @subpackage Festival_Banner/admin
 */

/**
 * Meta boxes functionality.
 *
 * Registers and handles all meta boxes for the banner post type.
 *
 * @package    Festival_Banner
 * @subpackage Festival_Banner/admin
 * @author     justSohel <thesohelrana.me@gmail.com>
 */
class Festival_Banner_Meta_Boxes {

	/**
	 * Initialize the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_festival_banner', array( $this, 'save_meta_boxes' ), 10, 2 );
	}

	/**
	 * Register all meta boxes.
	 *
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {
		// Content meta box.
		add_meta_box(
			'festival_banner_content',
			__( 'Banner Content', 'festival-banner' ),
			array( $this, 'render_content_meta_box' ),
			'festival_banner',
			'normal',
			'high'
		);

		// CTA meta box.
		add_meta_box(
			'festival_banner_cta',
			__( 'Call-to-Action Button', 'festival-banner' ),
			array( $this, 'render_cta_meta_box' ),
			'festival_banner',
			'normal',
			'high'
		);

		// Display settings meta box.
		add_meta_box(
			'festival_banner_display',
			__( 'Display Settings', 'festival-banner' ),
			array( $this, 'render_display_meta_box' ),
			'festival_banner',
			'side',
			'high'
		);

		// Schedule meta box.
		add_meta_box(
			'festival_banner_schedule',
			__( 'Schedule', 'festival-banner' ),
			array( $this, 'render_schedule_meta_box' ),
			'festival_banner',
			'side',
			'default'
		);

		// Appearance meta box.
		add_meta_box(
			'festival_banner_appearance',
			__( 'Appearance', 'festival-banner' ),
			array( $this, 'render_appearance_meta_box' ),
			'festival_banner',
			'side',
			'default'
		);

		// Behavior meta box.
		add_meta_box(
			'festival_banner_behavior',
			__( 'Behavior', 'festival-banner' ),
			array( $this, 'render_behavior_meta_box' ),
			'festival_banner',
			'side',
			'low'
		);
	}

	/**
	 * Render content meta box.
	 *
	 * @since 1.0.0
	 * @param WP_Post $post The post object.
	 */
	public function render_content_meta_box( $post ) {
		wp_nonce_field( 'festival_banner_meta_box', 'festival_banner_meta_box_nonce' );
		require_once FESTIVAL_BANNER_PLUGIN_DIR . 'admin/partials/meta-box-content.php';
	}

	/**
	 * Render CTA meta box.
	 *
	 * @since 1.0.0
	 * @param WP_Post $post The post object.
	 */
	public function render_cta_meta_box( $post ) {
		require_once FESTIVAL_BANNER_PLUGIN_DIR . 'admin/partials/meta-box-cta.php';
	}

	/**
	 * Render display settings meta box.
	 *
	 * @since 1.0.0
	 * @param WP_Post $post The post object.
	 */
	public function render_display_meta_box( $post ) {
		require_once FESTIVAL_BANNER_PLUGIN_DIR . 'admin/partials/meta-box-display.php';
	}

	/**
	 * Render schedule meta box.
	 *
	 * @since 1.0.0
	 * @param WP_Post $post The post object.
	 */
	public function render_schedule_meta_box( $post ) {
		require_once FESTIVAL_BANNER_PLUGIN_DIR . 'admin/partials/meta-box-schedule.php';
	}

	/**
	 * Render appearance meta box.
	 *
	 * @since 1.0.0
	 * @param WP_Post $post The post object.
	 */
	public function render_appearance_meta_box( $post ) {
		require_once FESTIVAL_BANNER_PLUGIN_DIR . 'admin/partials/meta-box-appearance.php';
	}

	/**
	 * Render behavior meta box.
	 *
	 * @since 1.0.0
	 * @param WP_Post $post The post object.
	 */
	public function render_behavior_meta_box( $post ) {
		require_once FESTIVAL_BANNER_PLUGIN_DIR . 'admin/partials/meta-box-behavior.php';
	}

	/**
	 * Save all meta boxes data.
	 *
	 * @since 1.0.0
	 * @param int     $post_id The post ID.
	 * @param WP_Post $post    The post object.
	 */
	public function save_meta_boxes( $post_id, $post ) {
		// Verify nonce.
		if ( ! $this->verify_nonce() ) {
			return;
		}

		// Check if not an autosave.
		if ( $this->is_autosave( $post_id ) ) {
			return;
		}

		// Check user permissions.
		if ( ! $this->check_permissions( $post_id ) ) {
			return;
		}

		// Load validation class.
		require_once FESTIVAL_BANNER_PLUGIN_DIR . 'admin/class-festival-banner-validation.php';

		// Save content.
		$this->save_content_fields( $post_id );

		// Save CTA fields.
		$this->save_cta_fields( $post_id );

		// Save display settings.
		$this->save_display_fields( $post_id );

		// Save schedule.
		$this->save_schedule_fields( $post_id );

		// Save appearance.
		$this->save_appearance_fields( $post_id );

		// Save behavior.
		$this->save_behavior_fields( $post_id );

		// Clear ALL banner caches after saving.
		Festival_Banner_Query::clear_all_caches();
	}

	/**
	 * Verify nonce.
	 *
	 * @since  1.0.0
	 * @return bool True if nonce is valid, false otherwise.
	 */
	private function verify_nonce() {
		if ( ! isset( $_POST['festival_banner_meta_box_nonce'] ) ) {
			return false;
		}

		return wp_verify_nonce( 
			sanitize_text_field( wp_unslash( $_POST['festival_banner_meta_box_nonce'] ) ),
			'festival_banner_meta_box' 
		);
	}

	/**
	 * Check if this is an autosave.
	 *
	 * @since  1.0.0
	 * @param  int $post_id The post ID.
	 * @return bool True if autosave, false otherwise.
	 */
	private function is_autosave( $post_id ) {
		return defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;
	}

	/**
	 * Check user permissions.
	 *
	 * @since  1.0.0
	 * @param  int $post_id The post ID.
	 * @return bool True if user has permission, false otherwise.
	 */
	private function check_permissions( $post_id ) {
		return current_user_can( 'edit_post', $post_id );
	}

	/**
	 * Save content fields.
	 *
	 * @since 1.0.0
	 * @param int $post_id The post ID.
	 */
	private function save_content_fields( $post_id ) {
		if ( isset( $_POST['fb_content'] ) ) {
			$content = wp_kses_post( wp_unslash( $_POST['fb_content'] ) );
			update_post_meta( $post_id, '_fb_content', $content );
		}
	}

	/**
	 * Save CTA fields.
	 *
	 * @since 1.0.0
	 * @param int $post_id The post ID.
	 */
	private function save_cta_fields( $post_id ) {
		// CTA text.
		if ( isset( $_POST['fb_cta_text'] ) ) {
			$cta_text = sanitize_text_field( wp_unslash( $_POST['fb_cta_text'] ) );
			update_post_meta( $post_id, '_fb_cta_text', $cta_text );
		}

		// CTA URL.
		if ( isset( $_POST['fb_cta_url'] ) ) {
			$cta_url = esc_url_raw( wp_unslash( $_POST['fb_cta_url'] ) );
			update_post_meta( $post_id, '_fb_cta_url', $cta_url );
		}

		// CTA new tab.
		$cta_new_tab = isset( $_POST['fb_cta_new_tab'] ) ? true : false;
		update_post_meta( $post_id, '_fb_cta_new_tab', $cta_new_tab );
	}

	/**
	 * Save display fields.
	 *
	 * @since 1.0.0
	 * @param int $post_id The post ID.
	 */
	private function save_display_fields( $post_id ) {
		// Position.
		if ( isset( $_POST['fb_position'] ) ) {
			$position = sanitize_text_field( wp_unslash( $_POST['fb_position'] ) );
			$allowed  = array( 'top_bar', 'bottom_bar', 'floating', 'modal', 'side' );
			if ( in_array( $position, $allowed, true ) ) {
				update_post_meta( $post_id, '_fb_position', $position );
			}
		}

		// Floating position.
		if ( isset( $_POST['fb_floating_position'] ) ) {
			$floating_pos = sanitize_text_field( wp_unslash( $_POST['fb_floating_position'] ) );
			$allowed      = array( 'top_left', 'top_right', 'bottom_left', 'bottom_right' );
			if ( in_array( $floating_pos, $allowed, true ) ) {
				update_post_meta( $post_id, '_fb_floating_position', $floating_pos );
			}
		}

		// Side position.
		if ( isset( $_POST['fb_side_position'] ) ) {
			$side_pos = sanitize_text_field( wp_unslash( $_POST['fb_side_position'] ) );
			$allowed  = array( 'left', 'right', 'both' );
			if ( in_array( $side_pos, $allowed, true ) ) {
				update_post_meta( $post_id, '_fb_side_position', $side_pos );
			}
		}

		// Modal delay.
		if ( isset( $_POST['fb_modal_delay'] ) ) {
			$modal_delay = absint( $_POST['fb_modal_delay'] );
			// Limit between 0 and 60 seconds.
			$modal_delay = max( 0, min( 60, $modal_delay ) );
			update_post_meta( $post_id, '_fb_modal_delay', $modal_delay );
		}

		// Display type.
		if ( isset( $_POST['fb_display_type'] ) ) {
			$display_type = sanitize_text_field( wp_unslash( $_POST['fb_display_type'] ) );
			$allowed      = array( 'all_pages', 'homepage_only', 'specific_pages' );
			if ( in_array( $display_type, $allowed, true ) ) {
				update_post_meta( $post_id, '_fb_display_type', $display_type );
			}
		}

		// Specific pages.
		if ( isset( $_POST['fb_specific_pages'] ) && is_array( $_POST['fb_specific_pages'] ) ) {
			$specific_pages = array_map( 'absint', wp_unslash( $_POST['fb_specific_pages'] ) );
			update_post_meta( $post_id, '_fb_specific_pages', $specific_pages );
		} else {
			update_post_meta( $post_id, '_fb_specific_pages', array() );
		}
	}

	/**
	 * Save schedule fields.
	 *
	 * @since 1.0.0
	 * @param int $post_id The post ID.
	 */
	private function save_schedule_fields( $post_id ) {
		// Enable schedule.
		$enable_schedule = isset( $_POST['fb_enable_schedule'] ) ? true : false;

		if ( $enable_schedule ) {
			// Start date.
			if ( isset( $_POST['fb_start_date'] ) ) {
				$start_date = sanitize_text_field( wp_unslash( $_POST['fb_start_date'] ) );
				// Validate date format.
				if ( Festival_Banner_Validation::validate_datetime( $start_date ) ) {
					update_post_meta( $post_id, '_fb_start_date', $start_date );
				}
			}

			// End date.
			if ( isset( $_POST['fb_end_date'] ) ) {
				$end_date = sanitize_text_field( wp_unslash( $_POST['fb_end_date'] ) );
				// Validate date format.
				if ( Festival_Banner_Validation::validate_datetime( $end_date ) ) {
					update_post_meta( $post_id, '_fb_end_date', $end_date );
				}
			}
		} else {
			// Clear schedule if disabled.
			delete_post_meta( $post_id, '_fb_start_date' );
			delete_post_meta( $post_id, '_fb_end_date' );
		}

		// Recurring.
		$is_recurring = isset( $_POST['fb_is_recurring'] ) ? true : false;
		update_post_meta( $post_id, '_fb_is_recurring', $is_recurring );

		// Recurring year.
		if ( isset( $_POST['fb_recurring_year'] ) ) {
			$recurring_year = absint( $_POST['fb_recurring_year'] );
			if ( $recurring_year > 0 ) {
				update_post_meta( $post_id, '_fb_recurring_year', $recurring_year );
			}
		} else {
			// Set current year by default.
			update_post_meta( $post_id, '_fb_recurring_year', (int) gmdate( 'Y' ) );
		}
	}

	/**
	 * Save appearance fields.
	 *
	 * @since 1.0.0
	 * @param int $post_id The post ID.
	 */
	private function save_appearance_fields( $post_id ) {
		// Background color.
		if ( isset( $_POST['fb_bg_color'] ) ) {
			$bg_color = sanitize_hex_color( wp_unslash( $_POST['fb_bg_color'] ) );
			if ( $bg_color ) {
				update_post_meta( $post_id, '_fb_bg_color', $bg_color );
			}
		}

		// Text color.
		if ( isset( $_POST['fb_text_color'] ) ) {
			$text_color = sanitize_hex_color( wp_unslash( $_POST['fb_text_color'] ) );
			if ( $text_color ) {
				update_post_meta( $post_id, '_fb_text_color', $text_color );
			}
		}

		// CTA background color.
		if ( isset( $_POST['fb_cta_bg_color'] ) ) {
			$cta_bg_color = sanitize_hex_color( wp_unslash( $_POST['fb_cta_bg_color'] ) );
			if ( $cta_bg_color ) {
				update_post_meta( $post_id, '_fb_cta_bg_color', $cta_bg_color );
			}
		}

		// CTA text color.
		if ( isset( $_POST['fb_cta_text_color'] ) ) {
			$cta_text_color = sanitize_hex_color( wp_unslash( $_POST['fb_cta_text_color'] ) );
			if ( $cta_text_color ) {
				update_post_meta( $post_id, '_fb_cta_text_color', $cta_text_color );
			}
		}

		// Animation.
		if ( isset( $_POST['fb_animation'] ) ) {
			$animation = sanitize_text_field( wp_unslash( $_POST['fb_animation'] ) );
			$allowed   = array( 'fade', 'slide', 'none' );
			if ( in_array( $animation, $allowed, true ) ) {
				update_post_meta( $post_id, '_fb_animation', $animation );
			}
		}
	}

	/**
	 * Save behavior fields.
	 *
	 * @since 1.0.0
	 * @param int $post_id The post ID.
	 */
	private function save_behavior_fields( $post_id ) {
		// Dismissible.
		$is_dismissible = isset( $_POST['fb_is_dismissible'] ) ? true : false;
		
		// Force dismissible for modal position.
		$position = get_post_meta( $post_id, '_fb_position', true );
		if ( 'modal' === $position ) {
			$is_dismissible = true;
		}
		
		update_post_meta( $post_id, '_fb_is_dismissible', $is_dismissible );
	}
}
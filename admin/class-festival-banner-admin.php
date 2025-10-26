<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Festival_Banner
 * @subpackage Festival_Banner/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Festival_Banner
 * @subpackage Festival_Banner/admin
 * @author     Your Name <email@example.com>
 */
class Festival_Banner_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		// Initialize list table customization.
		$this->init_list_table();

		// Initialize meta boxes.
		$this->init_meta_boxes();

		// Clear cache on banner trash/delete.
		add_action( 'trashed_post', array( $this, 'clear_cache_on_delete' ) );
		add_action( 'deleted_post', array( $this, 'clear_cache_on_delete' ) );
		add_action( 'untrashed_post', array( $this, 'clear_cache_on_delete' ) );
	}

	/**
	 * Initialize list table customization.
	 *
	 * @since 1.0.0
	 */
	private function init_list_table() {
		require_once FESTIVAL_BANNER_PLUGIN_DIR . 'admin/class-festival-banner-list-table.php';
		new Festival_Banner_List_Table();
	}

	/**
	 * Initialize meta boxes.
	 *
	 * @since 1.0.0
	 */
	private function init_meta_boxes() {
		require_once FESTIVAL_BANNER_PLUGIN_DIR . 'admin/class-festival-banner-meta-boxes.php';
		new Festival_Banner_Meta_Boxes();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 1.0.0
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_styles( $hook_suffix ) {
		// Only load on our plugin pages.
		if ( ! $this->is_plugin_page( $hook_suffix ) ) {
			return;
		}

		wp_enqueue_style(
			$this->plugin_name,
			FESTIVAL_BANNER_PLUGIN_URL . 'admin/css/admin-styles.css',
			array( 'wp-color-picker' ),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 1.0.0
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_scripts( $hook_suffix ) {
		// Only load on our plugin pages.
		if ( ! $this->is_plugin_page( $hook_suffix ) ) {
			return;
		}

		wp_enqueue_script(
			$this->plugin_name,
			FESTIVAL_BANNER_PLUGIN_URL . 'admin/js/admin-scripts.js',
			array( 'jquery', 'wp-color-picker', 'jquery-ui-datepicker' ),
			$this->version,
			false
		);

		// Localize script with data.
		wp_localize_script(
			$this->plugin_name,
			'festivalBannerAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'festival_banner_admin_nonce' ),
			)
		);
	}

	/**
	 * Check if current page is a plugin page.
	 *
	 * @since  1.0.0
	 * @param  string $hook_suffix The current admin page.
	 * @return bool True if plugin page, false otherwise.
	 */
	private function is_plugin_page( $hook_suffix ) {
		$plugin_pages = array(
			'post.php',
			'post-new.php',
			'edit.php',
		);

		// Check if we're on a festival_banner post type page.
		$screen = get_current_screen();
		if ( $screen && 'festival_banner' === $screen->post_type ) {
			return true;
		}

		return false;
	}

	/**
	 * Register the custom post type.
	 *
	 * @since 1.0.0
	 */
	public function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Festival Banners', 'Post Type General Name', 'festival-banner' ),
			'singular_name'         => _x( 'Festival Banner', 'Post Type Singular Name', 'festival-banner' ),
			'menu_name'             => __( 'Festival Banners', 'festival-banner' ),
			'name_admin_bar'        => __( 'Festival Banner', 'festival-banner' ),
			'archives'              => __( 'Banner Archives', 'festival-banner' ),
			'attributes'            => __( 'Banner Attributes', 'festival-banner' ),
			'parent_item_colon'     => __( 'Parent Banner:', 'festival-banner' ),
			'all_items'             => __( 'All Banners', 'festival-banner' ),
			'add_new_item'          => __( 'Add New Banner', 'festival-banner' ),
			'add_new'               => __( 'Add New', 'festival-banner' ),
			'new_item'              => __( 'New Banner', 'festival-banner' ),
			'edit_item'             => __( 'Edit Banner', 'festival-banner' ),
			'update_item'           => __( 'Update Banner', 'festival-banner' ),
			'view_item'             => __( 'View Banner', 'festival-banner' ),
			'view_items'            => __( 'View Banners', 'festival-banner' ),
			'search_items'          => __( 'Search Banner', 'festival-banner' ),
			'not_found'             => __( 'No banners found', 'festival-banner' ),
			'not_found_in_trash'    => __( 'No banners found in Trash', 'festival-banner' ),
			'featured_image'        => __( 'Banner Image', 'festival-banner' ),
			'set_featured_image'    => __( 'Set banner image', 'festival-banner' ),
			'remove_featured_image' => __( 'Remove banner image', 'festival-banner' ),
			'use_featured_image'    => __( 'Use as banner image', 'festival-banner' ),
			'insert_into_item'      => __( 'Insert into banner', 'festival-banner' ),
			'uploaded_to_this_item' => __( 'Uploaded to this banner', 'festival-banner' ),
			'items_list'            => __( 'Banners list', 'festival-banner' ),
			'items_list_navigation' => __( 'Banners list navigation', 'festival-banner' ),
			'filter_items_list'     => __( 'Filter banners list', 'festival-banner' ),
		);

		$args = array(
			'label'               => __( 'Festival Banner', 'festival-banner' ),
			'description'         => __( 'Festival promotional banners', 'festival-banner' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 28,
			'menu_icon'           => 'dashicons-megaphone',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
			'show_in_rest'        => false,
			'rewrite'             => false,
		);

		register_post_type( 'festival_banner', $args );
	}

	/**
	 * Add custom admin menu items.
	 *
	 * @since 1.0.0
	 */
	public function add_admin_menu() {
		// Currently, all menu items are handled by the CPT registration.
		// This method is a placeholder for future custom menu items.
		// Settings page will be added in Phase 2.
	}

	/**
	 * Display admin notices.
	 *
	 * @since 1.0.0
	 */
	public function display_admin_notices() {
		// Check for conflicting banners.
		$this->check_conflicting_banners();

		// Check for recurring banners ready for next year.
		$this->check_recurring_banners();

		// Display success messages after bulk actions.
		$this->display_bulk_action_notices();
	}

	/**
	 * Check for conflicting banners and display warning.
	 *
	 * @since 1.0.0
	 */
	private function check_conflicting_banners() {
		// Only show on the banners list page.
		$screen = get_current_screen();
		if ( ! $screen || 'edit-festival_banner' !== $screen->id ) {
			return;
		}

		// Query for active banners grouped by position.
		$args = array(
			'post_type'      => 'festival_banner',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => '_fb_start_date',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => '_fb_start_date',
					'value'   => current_time( 'mysql' ),
					'compare' => '<=',
					'type'    => 'DATETIME',
				),
			),
		);

		$banners = get_posts( $args );

		// Group by position.
		$positions = array();
		foreach ( $banners as $banner ) {
			$position = get_post_meta( $banner->ID, '_fb_position', true );
			if ( ! isset( $positions[ $position ] ) ) {
				$positions[ $position ] = 0;
			}
			$positions[ $position ]++;
		}

		// Check for conflicts.
		foreach ( $positions as $position => $count ) {
			if ( $count > 1 ) {
				$position_labels = array(
					'top_bar'    => __( 'Top Bar', 'festival-banner' ),
					'bottom_bar' => __( 'Bottom Bar', 'festival-banner' ),
					'floating'   => __( 'Floating', 'festival-banner' ),
					'modal'      => __( 'Modal', 'festival-banner' ),
					'side'       => __( 'Side Banner', 'festival-banner' ),
				);

				$position_label = isset( $position_labels[ $position ] ) ? $position_labels[ $position ] : $position;

				echo '<div class="notice notice-warning is-dismissible">';
				echo '<p>';
				printf(
					/* translators: 1: Position name, 2: Number of banners */
					esc_html__( 'Warning: %1$d banners are active for %2$s position. Only the newest will display.', 'festival-banner' ),
					absint( $count ),
					esc_html( $position_label )
				);
				echo ' <a href="' . esc_url( admin_url( 'edit.php?post_type=festival_banner' ) ) . '">' . esc_html__( 'Manage Banners', 'festival-banner' ) . '</a>';
				echo '</p>';
				echo '</div>';
				break; // Only show one warning.
			}
		}
	}

	/**
	 * Check for expired recurring banners.
	 *
	 * @since 1.0.0
	 */
	private function check_recurring_banners() {
		// Only show on the banners list page.
		$screen = get_current_screen();
		if ( ! $screen || 'edit-festival_banner' !== $screen->id ) {
			return;
		}

		// Query for expired recurring banners.
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
		);

		$expired_recurring = get_posts( $args );

		if ( ! empty( $expired_recurring ) ) {
			$count = count( $expired_recurring );
			echo '<div class="notice notice-info is-dismissible">';
			echo '<p>';
			printf(
				/* translators: %d: Number of expired recurring banners */
				esc_html( _n( '%d recurring banner has expired and is ready for next year.', '%d recurring banners have expired and are ready for next year.', $count, 'festival-banner' ) ),
				absint( $count )
			);
			echo ' <a href="' . esc_url( admin_url( 'edit.php?post_type=festival_banner' ) ) . '">' . esc_html__( 'Review Banners', 'festival-banner' ) . '</a>';
			echo '</p>';
			echo '</div>';
		}
	}

	/**
	 * Display bulk action success notices.
	 *
	 * @since 1.0.0
	 */
	private function display_bulk_action_notices() {
		// Check for bulk action messages.
		if ( ! isset( $_GET['bulk_action'] ) || ! isset( $_GET['changed'] ) ) {
			return;
		}

		$action  = sanitize_text_field( wp_unslash( $_GET['bulk_action'] ) );
		$changed = absint( $_GET['changed'] );

		$messages = array(
			'activated'   => _n( '%d banner activated.', '%d banners activated.', $changed, 'festival-banner' ),
			'deactivated' => _n( '%d banner deactivated.', '%d banners deactivated.', $changed, 'festival-banner' ),
			'duplicated'  => _n( '%d banner duplicated.', '%d banners duplicated.', $changed, 'festival-banner' ),
		);

		if ( isset( $messages[ $action ] ) ) {
			echo '<div class="notice notice-success is-dismissible">';
			echo '<p>' . sprintf( esc_html( $messages[ $action ] ), absint( $changed ) ) . '</p>';
			echo '</div>';
		}
	}

	/**
	 * Add custom row actions to banner list.
	 *
	 * @since 1.0.0
	 * @param array   $actions An array of row action links.
	 * @param WP_Post $post    The post object.
	 * @return array Modified actions array.
	 */
	public function add_row_actions( $actions, $post ) {
		// Only for our post type.
		if ( 'festival_banner' !== $post->post_type ) {
			return $actions;
		}

		// Add duplicate action.
		$duplicate_url = wp_nonce_url(
			admin_url( 'admin.php?action=duplicate_festival_banner&post=' . $post->ID ),
			'duplicate_festival_banner_' . $post->ID
		);

		$actions['duplicate'] = '<a href="' . esc_url( $duplicate_url ) . '">' . esc_html__( 'Duplicate', 'festival-banner' ) . '</a>';

		// Add "Create Next Year" for expired recurring banners.
		$is_recurring = get_post_meta( $post->ID, '_fb_is_recurring', true );
		$end_date     = get_post_meta( $post->ID, '_fb_end_date', true );

		if ( $is_recurring && $end_date && strtotime( $end_date ) < current_time( 'timestamp' ) ) {
			$next_year_url = wp_nonce_url(
				admin_url( 'admin.php?action=create_next_year_banner&post=' . $post->ID ),
				'create_next_year_banner_' . $post->ID
			);

			$actions['create_next_year'] = '<a href="' . esc_url( $next_year_url ) . '">' . esc_html__( 'Create Next Year', 'festival-banner' ) . '</a>';
		}

		return $actions;
	}

	/**
	 * Register custom bulk actions.
	 *
	 * @since 1.0.0
	 * @param array $bulk_actions An array of bulk actions.
	 * @return array Modified bulk actions array.
	 */
	public function register_bulk_actions( $bulk_actions ) {
		$bulk_actions['activate_banners']   = __( 'Activate', 'festival-banner' );
		$bulk_actions['deactivate_banners'] = __( 'Deactivate', 'festival-banner' );
		$bulk_actions['duplicate_banners']  = __( 'Duplicate', 'festival-banner' );

		return $bulk_actions;
	}

	/**
	 * Handle custom bulk actions.
	 *
	 * @since 1.0.0
	 * @param string $redirect_to The redirect URL.
	 * @param string $doaction    The action being taken.
	 * @param array  $post_ids    The items to take the action on.
	 * @return string The redirect URL.
	 */
	public function handle_bulk_actions( $redirect_to, $doaction, $post_ids ) {
		$changed = 0;

		switch ( $doaction ) {
			case 'activate_banners':
				foreach ( $post_ids as $post_id ) {
					wp_update_post(
						array(
							'ID'          => $post_id,
							'post_status' => 'publish',
						)
					);
					$changed++;
				}
				$redirect_to = add_query_arg( 'bulk_action', 'activated', $redirect_to );
				break;

			case 'deactivate_banners':
				foreach ( $post_ids as $post_id ) {
					wp_update_post(
						array(
							'ID'          => $post_id,
							'post_status' => 'draft',
						)
					);
					$changed++;
				}
				$redirect_to = add_query_arg( 'bulk_action', 'deactivated', $redirect_to );
				break;

			case 'duplicate_banners':
				foreach ( $post_ids as $post_id ) {
					$this->duplicate_banner( $post_id );
					$changed++;
				}
				$redirect_to = add_query_arg( 'bulk_action', 'duplicated', $redirect_to );
				break;
		}

		if ( $changed > 0 ) {
			$redirect_to = add_query_arg( 'changed', $changed, $redirect_to );
		}

		return $redirect_to;
	}

	/**
	 * Duplicate a banner.
	 *
	 * @since 1.0.0
	 * @param int $post_id The post ID to duplicate.
	 * @return int|bool New post ID on success, false on failure.
	 */
	private function duplicate_banner( $post_id ) {
		$post = get_post( $post_id );

		if ( ! $post ) {
			return false;
		}

		// Create new post.
		$new_post_id = wp_insert_post(
			array(
				'post_title'  => $post->post_title . ' (Copy)',
				'post_type'   => $post->post_type,
				'post_status' => 'draft',
			)
		);

		if ( is_wp_error( $new_post_id ) ) {
			return false;
		}

		// Copy all meta data.
		$meta_data = get_post_meta( $post_id );
		foreach ( $meta_data as $key => $values ) {
			foreach ( $values as $value ) {
				add_post_meta( $new_post_id, $key, maybe_unserialize( $value ) );
			}
		}

		return $new_post_id;
	}

	/**
	 * Clear cache when banner is trashed, deleted, or restored.
	 *
	 * @since 1.0.0
	 * @param int $post_id The post ID.
	 */
	public function clear_cache_on_delete( $post_id ) {
		// Check if it's a festival banner.
		if ( 'festival_banner' !== get_post_type( $post_id ) ) {
			return;
		}

		// Clear all banner caches.
		Festival_Banner_Query::clear_all_caches();
	}
}
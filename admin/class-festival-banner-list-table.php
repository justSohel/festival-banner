<?php
/**
 * Custom list table functionality for the admin area.
 *
 * @since      1.0.0
 *
 * @package    Festival_Banner
 * @subpackage Festival_Banner/admin
 */

/**
 * Custom list table functionality.
 *
 * Adds custom columns, filters, and sorting to the banners list table.
 *
 * @package    Festival_Banner
 * @subpackage Festival_Banner/admin
 * @author     justSohel <thesohelrana.me@gmail.com>
 */
class Festival_Banner_List_Table {

	/**
	 * Initialize the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Add custom columns.
		add_filter( 'manage_festival_banner_posts_columns', array( $this, 'add_custom_columns' ) );
		add_action( 'manage_festival_banner_posts_custom_column', array( $this, 'render_custom_columns' ), 10, 2 );

		// Make columns sortable.
		add_filter( 'manage_edit-festival_banner_sortable_columns', array( $this, 'make_columns_sortable' ) );

		// Add custom filters.
		add_action( 'restrict_manage_posts', array( $this, 'add_custom_filters' ) );
		add_filter( 'parse_query', array( $this, 'filter_by_custom_fields' ) );
	}

	/**
	 * Add custom columns to the list table.
	 *
	 * @since  1.0.0
	 * @param  array $columns Existing columns.
	 * @return array Modified columns.
	 */
	public function add_custom_columns( $columns ) {
		// Remove date column (we'll add it back at the end).
		$date = $columns['date'];
		unset( $columns['date'] );

		// Add custom columns.
		$columns['position']    = __( 'Position', 'festival-banner' );
		$columns['status']      = __( 'Status', 'festival-banner' );
		$columns['display']     = __( 'Display', 'festival-banner' );
		$columns['schedule']    = __( 'Schedule', 'festival-banner' );
		$columns['dismissible'] = __( 'Dismissible', 'festival-banner' );
		$columns['date']        = $date;

		return $columns;
	}

	/**
	 * Render custom column content.
	 *
	 * @since 1.0.0
	 * @param string $column  The column name.
	 * @param int    $post_id The post ID.
	 */
	public function render_custom_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'position':
				$this->render_position_column( $post_id );
				break;

			case 'status':
				$this->render_status_column( $post_id );
				break;

			case 'display':
				$this->render_display_column( $post_id );
				break;

			case 'schedule':
				$this->render_schedule_column( $post_id );
				break;

			case 'dismissible':
				$this->render_dismissible_column( $post_id );
				break;
		}
	}

	/**
	 * Render position column.
	 *
	 * @since 1.0.0
	 * @param int $post_id The post ID.
	 */
	private function render_position_column( $post_id ) {
		$position = get_post_meta( $post_id, '_fb_position', true );

		$positions = array(
			'top_bar'    => array(
				'label' => __( 'Top Bar', 'festival-banner' ),
				'icon'  => '⬆️',
			),
			'bottom_bar' => array(
				'label' => __( 'Bottom Bar', 'festival-banner' ),
				'icon'  => '⬇️',
			),
			'floating'   => array(
				'label' => __( 'Floating', 'festival-banner' ),
				'icon'  => '📌',
			),
			'modal'      => array(
				'label' => __( 'Modal', 'festival-banner' ),
				'icon'  => '🔲',
			),
			'side'       => array(
				'label' => __( 'Side Banner', 'festival-banner' ),
				'icon'  => '◀️',
			),
		);

		if ( isset( $positions[ $position ] ) ) {
			echo '<span title="' . esc_attr( $positions[ $position ]['label'] ) . '">';
			echo esc_html( $positions[ $position ]['icon'] ) . ' ';
			echo esc_html( $positions[ $position ]['label'] );
			echo '</span>';
		} else {
			echo '—';
		}
	}

	/**
	 * Render status column.
	 *
	 * @since 1.0.0
	 * @param int $post_id The post ID.
	 */
	private function render_status_column( $post_id ) {
		$post_status = get_post_status( $post_id );
		$status      = $this->get_banner_status( $post_id );

		$status_config = array(
			'active'    => array(
				'label' => __( 'Active', 'festival-banner' ),
				'color' => '#10b981',
				'icon'  => '✓',
			),
			'scheduled' => array(
				'label' => __( 'Scheduled', 'festival-banner' ),
				'color' => '#3b82f6',
				'icon'  => '🕐',
			),
			'expired'   => array(
				'label' => __( 'Expired', 'festival-banner' ),
				'color' => '#6b7280',
				'icon'  => '⏹',
			),
			'draft'     => array(
				'label' => __( 'Draft', 'festival-banner' ),
				'color' => '#f59e0b',
				'icon'  => '📝',
			),
		);

		if ( isset( $status_config[ $status ] ) ) {
			$config = $status_config[ $status ];
			echo '<span style="display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; color: white; background-color: ' . esc_attr( $config['color'] ) . ';">';
			echo esc_html( $config['icon'] ) . ' ' . esc_html( $config['label'] );
			echo '</span>';
		}
	}

	/**
	 * Get banner status.
	 *
	 * @since  1.0.0
	 * @param  int $post_id The post ID.
	 * @return string The banner status (active, scheduled, expired, draft).
	 */
	private function get_banner_status( $post_id ) {
		$post_status = get_post_status( $post_id );

		if ( 'publish' !== $post_status ) {
			return 'draft';
		}

		$start_date = get_post_meta( $post_id, '_fb_start_date', true );
		$end_date   = get_post_meta( $post_id, '_fb_end_date', true );
		$now        = current_time( 'timestamp' );

		// No schedule = always active.
		if ( empty( $start_date ) && empty( $end_date ) ) {
			return 'active';
		}

		// Check if scheduled for future.
		if ( ! empty( $start_date ) && strtotime( $start_date ) > $now ) {
			return 'scheduled';
		}

		// Check if expired.
		if ( ! empty( $end_date ) && strtotime( $end_date ) < $now ) {
			return 'expired';
		}

		return 'active';
	}

	/**
	 * Render display column.
	 *
	 * @since 1.0.0
	 * @param int $post_id The post ID.
	 */
	private function render_display_column( $post_id ) {
		$display_type = get_post_meta( $post_id, '_fb_display_type', true );

		if ( 'all_pages' === $display_type || empty( $display_type ) ) {
			echo '<span style="color: #059669;">🌐 ' . esc_html__( 'Site-wide', 'festival-banner' ) . '</span>';
		} elseif ( 'homepage_only' === $display_type ) {
			echo '<span style="color: #0891b2;">🏠 ' . esc_html__( 'Homepage', 'festival-banner' ) . '</span>';
		} elseif ( 'specific_pages' === $display_type ) {
			$pages = get_post_meta( $post_id, '_fb_specific_pages', true );
			if ( is_array( $pages ) && ! empty( $pages ) ) {
				$count = count( $pages );
				printf(
					/* translators: %d: Number of pages */
					esc_html( _n( '📄 %d Page', '📄 %d Pages', $count, 'festival-banner' ) ),
					absint( $count )
				);
			} else {
				echo '📄 ' . esc_html__( 'Specific Pages', 'festival-banner' );
			}
		} else {
			echo '—';
		}
	}

	/**
	 * Render schedule column.
	 *
	 * @since 1.0.0
	 * @param int $post_id The post ID.
	 */
	private function render_schedule_column( $post_id ) {
		$start_date   = get_post_meta( $post_id, '_fb_start_date', true );
		$end_date     = get_post_meta( $post_id, '_fb_end_date', true );
		$is_recurring = get_post_meta( $post_id, '_fb_is_recurring', true );

		if ( empty( $start_date ) && empty( $end_date ) ) {
			echo '<span style="color: #6b7280;">' . esc_html__( 'No schedule', 'festival-banner' ) . '</span>';
			return;
		}

		$date_format = get_option( 'date_format' );

		if ( ! empty( $start_date ) ) {
			echo '<strong>' . esc_html__( 'Start:', 'festival-banner' ) . '</strong> ';
			echo esc_html( date_i18n( $date_format, strtotime( $start_date ) ) );
			echo '<br>';
		}

		if ( ! empty( $end_date ) ) {
			echo '<strong>' . esc_html__( 'End:', 'festival-banner' ) . '</strong> ';
			echo esc_html( date_i18n( $date_format, strtotime( $end_date ) ) );
		}

		if ( $is_recurring ) {
			echo '<br><span style="color: #7c3aed;">🔄 ' . esc_html__( 'Recurring', 'festival-banner' ) . '</span>';
		}
	}

	/**
	 * Render dismissible column.
	 *
	 * @since 1.0.0
	 * @param int $post_id The post ID.
	 */
	private function render_dismissible_column( $post_id ) {
		$is_dismissible = get_post_meta( $post_id, '_fb_is_dismissible', true );
		$position       = get_post_meta( $post_id, '_fb_position', true );

		// Modal is always dismissible.
		if ( 'modal' === $position ) {
			echo '<span style="color: #10b981; font-size: 18px;" title="' . esc_attr__( 'Yes (required for modals)', 'festival-banner' ) . '">✓</span>';
		} elseif ( $is_dismissible ) {
			echo '<span style="color: #10b981; font-size: 18px;" title="' . esc_attr__( 'Yes', 'festival-banner' ) . '">✓</span>';
		} else {
			echo '<span style="color: #ef4444; font-size: 18px;" title="' . esc_attr__( 'No', 'festival-banner' ) . '">✗</span>';
		}
	}

	/**
	 * Make columns sortable.
	 *
	 * @since  1.0.0
	 * @param  array $columns Sortable columns.
	 * @return array Modified columns.
	 */
	public function make_columns_sortable( $columns ) {
		$columns['position'] = 'position';
		$columns['status']   = 'status';

		return $columns;
	}

	/**
	 * Add custom filter dropdowns.
	 *
	 * @since 1.0.0
	 * @param string $post_type The post type.
	 */
	public function add_custom_filters( $post_type ) {
		if ( 'festival_banner' !== $post_type ) {
			return;
		}

		// Position filter.
		$this->render_position_filter();

		// Status filter.
		$this->render_status_filter();

		// Display type filter.
		$this->render_display_filter();
	}

	/**
	 * Render position filter dropdown.
	 *
	 * @since 1.0.0
	 */
	private function render_position_filter() {
		$selected = isset( $_GET['filter_position'] ) ? sanitize_text_field( wp_unslash( $_GET['filter_position'] ) ) : '';

		$positions = array(
			'top_bar'    => __( 'Top Bar', 'festival-banner' ),
			'bottom_bar' => __( 'Bottom Bar', 'festival-banner' ),
			'floating'   => __( 'Floating', 'festival-banner' ),
			'modal'      => __( 'Modal', 'festival-banner' ),
			'side'       => __( 'Side Banner', 'festival-banner' ),
		);

		echo '<select name="filter_position">';
		echo '<option value="">' . esc_html__( 'All Positions', 'festival-banner' ) . '</option>';
		foreach ( $positions as $value => $label ) {
			printf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $value ),
				selected( $selected, $value, false ),
				esc_html( $label )
			);
		}
		echo '</select>';
	}

	/**
	 * Render status filter dropdown.
	 *
	 * @since 1.0.0
	 */
	private function render_status_filter() {
		$selected = isset( $_GET['filter_status'] ) ? sanitize_text_field( wp_unslash( $_GET['filter_status'] ) ) : '';

		$statuses = array(
			'active'    => __( 'Active', 'festival-banner' ),
			'scheduled' => __( 'Scheduled', 'festival-banner' ),
			'expired'   => __( 'Expired', 'festival-banner' ),
		);

		echo '<select name="filter_status">';
		echo '<option value="">' . esc_html__( 'All Statuses', 'festival-banner' ) . '</option>';
		foreach ( $statuses as $value => $label ) {
			printf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $value ),
				selected( $selected, $value, false ),
				esc_html( $label )
			);
		}
		echo '</select>';
	}

	/**
	 * Render display type filter dropdown.
	 *
	 * @since 1.0.0
	 */
	private function render_display_filter() {
		$selected = isset( $_GET['filter_display'] ) ? sanitize_text_field( wp_unslash( $_GET['filter_display'] ) ) : '';

		$display_types = array(
			'all_pages'      => __( 'Site-wide', 'festival-banner' ),
			'homepage_only'  => __( 'Homepage Only', 'festival-banner' ),
			'specific_pages' => __( 'Specific Pages', 'festival-banner' ),
		);

		echo '<select name="filter_display">';
		echo '<option value="">' . esc_html__( 'All Display Types', 'festival-banner' ) . '</option>';
		foreach ( $display_types as $value => $label ) {
			printf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $value ),
				selected( $selected, $value, false ),
				esc_html( $label )
			);
		}
		echo '</select>';
	}

	/**
	 * Filter posts by custom fields.
	 *
	 * @since 1.0.0
	 * @param WP_Query $query The WP_Query instance.
	 */
	public function filter_by_custom_fields( $query ) {
		global $pagenow;

		if ( ! is_admin() || 'edit.php' !== $pagenow || ! isset( $query->query_vars['post_type'] ) || 'festival_banner' !== $query->query_vars['post_type'] ) {
			return;
		}

		$meta_query = array();

		// Filter by position.
		if ( isset( $_GET['filter_position'] ) && ! empty( $_GET['filter_position'] ) ) {
			$meta_query[] = array(
				'key'   => '_fb_position',
				'value' => sanitize_text_field( wp_unslash( $_GET['filter_position'] ) ),
			);
		}

		// Filter by display type.
		if ( isset( $_GET['filter_display'] ) && ! empty( $_GET['filter_display'] ) ) {
			$meta_query[] = array(
				'key'   => '_fb_display_type',
				'value' => sanitize_text_field( wp_unslash( $_GET['filter_display'] ) ),
			);
		}

		// Filter by status (more complex - requires date comparisons).
		if ( isset( $_GET['filter_status'] ) && ! empty( $_GET['filter_status'] ) ) {
			$status = sanitize_text_field( wp_unslash( $_GET['filter_status'] ) );
			$now    = current_time( 'mysql' );

			switch ( $status ) {
				case 'scheduled':
					$meta_query[] = array(
						'key'     => '_fb_start_date',
						'value'   => $now,
						'compare' => '>',
						'type'    => 'DATETIME',
					);
					break;

				case 'expired':
					$meta_query[] = array(
						'key'     => '_fb_end_date',
						'value'   => $now,
						'compare' => '<',
						'type'    => 'DATETIME',
					);
					break;

				case 'active':
					// Active means: published AND (no schedule OR within schedule).
					$meta_query['relation'] = 'OR';
					$meta_query[]           = array(
						'key'     => '_fb_start_date',
						'compare' => 'NOT EXISTS',
					);
					$meta_query[]           = array(
						'relation' => 'AND',
						array(
							'key'     => '_fb_start_date',
							'value'   => $now,
							'compare' => '<=',
							'type'    => 'DATETIME',
						),
						array(
							'relation' => 'OR',
							array(
								'key'     => '_fb_end_date',
								'compare' => 'NOT EXISTS',
							),
							array(
								'key'     => '_fb_end_date',
								'value'   => $now,
								'compare' => '>=',
								'type'    => 'DATETIME',
							),
						),
					);
					break;
			}
		}

		if ( ! empty( $meta_query ) ) {
			$query->set( 'meta_query', $meta_query );
		}
	}
}
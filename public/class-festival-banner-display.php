<?php
/**
 * Banner display functionality
 *
 * @since      1.0.0
 *
 * @package    Festival_Banner
 * @subpackage Festival_Banner/public
 */

/**
 * Banner display functionality.
 *
 * Handles rendering of banners on the frontend.
 *
 * @package    Festival_Banner
 * @subpackage Festival_Banner/public
 * @author     justSohel <thesohelrana.me@gmail.com>
 */
class Festival_Banner_Display {

	/**
	 * Render all banners.
	 *
	 * @since 1.0.0
	 * @param array $banners Array of banner objects.
	 */
	public static function render_banners( $banners ) {
		if ( empty( $banners ) ) {
			return;
		}

		// Group banners by position.
		$grouped = self::group_by_position( $banners );

		// Render each position group.
		foreach ( $grouped as $position => $position_banners ) {
			foreach ( $position_banners as $banner ) {
				self::render_banner( $banner );
			}
		}
	}

	/**
	 * Render a single banner.
	 *
	 * @since 1.0.0
	 * @param object $banner Banner data object.
	 */
	public static function render_banner( $banner ) {
		// Get banner classes.
		$classes = self::get_banner_classes( $banner );

		// Get banner inline styles.
		$styles = self::get_banner_styles( $banner );

		// Load appropriate template.
		$template = self::get_template_path( $banner->position );

		if ( file_exists( $template ) ) {
			include $template;
		}
	}

	/**
	 * Get banner CSS classes.
	 *
	 * @since  1.0.0
	 * @param  object $banner Banner data object.
	 * @return string Space-separated CSS classes.
	 */
	private static function get_banner_classes( $banner ) {
		$classes = array(
			'fb-banner',
			'fb-banner--' . $banner->position,
		);

		// Add animation class.
		if ( $banner->animation && 'none' !== $banner->animation ) {
			$classes[] = 'fb-banner--' . $banner->animation;
		}

		// Add dismissible class.
		if ( $banner->is_dismissible ) {
			$classes[] = 'fb-banner--dismissible';
		}

		// Add position-specific classes.
		switch ( $banner->position ) {
			case 'floating':
				$classes[] = 'fb-banner--floating-' . $banner->floating_position;
				break;

			case 'side':
				if ( 'both' === $banner->side_position ) {
					$classes[] = 'fb-banner--side-both';
				} else {
					$classes[] = 'fb-banner--side-' . $banner->side_position;
				}
				break;

			case 'modal':
				$classes[] = 'fb-banner--modal';
				break;
		}

		return implode( ' ', $classes );
	}

	/**
	 * Get banner inline styles.
	 *
	 * @since  1.0.0
	 * @param  object $banner Banner data object.
	 * @return string Inline CSS styles.
	 */
	private static function get_banner_styles( $banner ) {
		$styles = array();

		// Background color.
		if ( ! empty( $banner->bg_color ) ) {
			$styles[] = 'background-color: ' . esc_attr( $banner->bg_color );
		}

		// Text color.
		if ( ! empty( $banner->text_color ) ) {
			$styles[] = 'color: ' . esc_attr( $banner->text_color );
		}

		// Floating position (add as inline style for better specificity).
		if ( 'floating' === $banner->position ) {
			$position_map = array(
				'top_left'     => 'top: 20px; left: 20px;',
				'top_right'    => 'top: 20px; right: 20px;',
				'bottom_left'  => 'bottom: 20px; left: 20px;',
				'bottom_right' => 'bottom: 20px; right: 20px;',
			);
			
			if ( isset( $position_map[ $banner->floating_position ] ) ) {
				$styles[] = $position_map[ $banner->floating_position ];
			}
		}

		return implode( '; ', $styles );
	}

	/**
	 * Get template path for position.
	 *
	 * @since  1.0.0
	 * @param  string $position Banner position.
	 * @return string Template file path.
	 */
	private static function get_template_path( $position ) {
		$template_name = 'banner-' . str_replace( '_', '-', $position ) . '.php';
		return FESTIVAL_BANNER_PLUGIN_DIR . 'public/partials/' . $template_name;
	}

	/**
	 * Group banners by position.
	 *
	 * @since  1.0.0
	 * @param  array $banners Array of banner objects.
	 * @return array Grouped banners.
	 */
	private static function group_by_position( $banners ) {
		$grouped = array();

		foreach ( $banners as $banner ) {
			$position = $banner->position;

			// Special handling for side banners with "both".
			if ( 'side' === $position && 'both' === $banner->side_position ) {
				// Add to both left and right.
				if ( ! isset( $grouped['side_left'] ) ) {
					$grouped['side_left'] = array();
				}
				if ( ! isset( $grouped['side_right'] ) ) {
					$grouped['side_right'] = array();
				}
				
				// Clone banner for both sides.
				$left_banner = clone $banner;
				$left_banner->side_position = 'left';
				$grouped['side_left'][] = $left_banner;

				$right_banner = clone $banner;
				$right_banner->side_position = 'right';
				$grouped['side_right'][] = $right_banner;
			} else {
				if ( ! isset( $grouped[ $position ] ) ) {
					$grouped[ $position ] = array();
				}
				$grouped[ $position ][] = $banner;
			}
		}

		return $grouped;
	}

	/**
	 * Render modal backdrop.
	 *
	 * @since 1.0.0
	 * @param object $banner Banner data object.
	 */
	public static function render_modal_backdrop( $banner ) {
		?>
		<div class="fb-modal-backdrop" id="fb-modal-backdrop-<?php echo esc_attr( $banner->ID ); ?>" data-banner-id="<?php echo esc_attr( $banner->ID ); ?>" data-delay="<?php echo esc_attr( $banner->modal_delay ); ?>" style="display: none;">
		<?php
	}

	/**
	 * Close modal backdrop.
	 *
	 * @since 1.0.0
	 */
	public static function close_modal_backdrop() {
		?>
		</div>
		<?php
	}
}
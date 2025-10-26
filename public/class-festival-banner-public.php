<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Festival_Banner
 * @subpackage Festival_Banner/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Festival_Banner
 * @subpackage Festival_Banner/public
 * @author     justSohel <thesohelrana.me@gmail.com>
 */
class Festival_Banner_Public {

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
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		// Only load if there are active banners.
		if ( ! $this->should_load_assets() ) {
			return;
		}

		wp_enqueue_style(
			$this->plugin_name,
			FESTIVAL_BANNER_PLUGIN_URL . 'public/css/public-styles.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		// Only load if there are active banners.
		if ( ! $this->should_load_assets() ) {
			return;
		}

		wp_enqueue_script(
			$this->plugin_name,
			FESTIVAL_BANNER_PLUGIN_URL . 'public/js/public-scripts.js',
			array( 'jquery' ),
			$this->version,
			true
		);

		// Pass data to JavaScript.
		wp_localize_script(
			$this->plugin_name,
			'festivalBannerData',
			array(
				'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
				'dismissedKey'  => 'festival_banner_dismissed',
			)
		);
	}

	/**
	 * Display banners on the frontend.
	 *
	 * @since 1.0.0
	 */
	public function display_banners() {
		// Get current page ID.
		$page_id = get_queried_object_id();

		// Get active banners for this page.
		$banners = Festival_Banner_Query::get_active_banners( $page_id );

		if ( empty( $banners ) ) {
			return;
		}

		// Load display class.
		require_once FESTIVAL_BANNER_PLUGIN_DIR . 'public/class-festival-banner-display.php';

		// Render banners.
		Festival_Banner_Display::render_banners( $banners );
	}

	/**
	 * Check if assets should be loaded.
	 *
	 * @since  1.0.0
	 * @return bool True if should load, false otherwise.
	 */
	private function should_load_assets() {
		// Don't load in admin.
		if ( is_admin() ) {
			return false;
		}

		// Get current page ID.
		$page_id = get_queried_object_id();

		// Check if there are active banners for this page.
		$banners = Festival_Banner_Query::get_active_banners( $page_id );

		return ! empty( $banners );
	}
}
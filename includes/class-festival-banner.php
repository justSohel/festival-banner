<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Festival_Banner
 * @subpackage Festival_Banner/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Festival_Banner
 * @subpackage Festival_Banner/includes
 * @author     Your Name <email@example.com>
 */
class Festival_Banner {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    Festival_Banner_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( defined( 'FESTIVAL_BANNER_VERSION' ) ) {
			$this->version = FESTIVAL_BANNER_VERSION;
		} else {
			$this->version = '1.0.0';
		}

		$this->plugin_name = 'festival-banner';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Festival_Banner_Loader. Orchestrates the hooks of the plugin.
	 * - Festival_Banner_i18n. Defines internationalization functionality.
	 * - Festival_Banner_Query. Handles banner database queries.
	 * - Festival_Banner_Admin. Defines all hooks for the admin area.
	 * - Festival_Banner_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once FESTIVAL_BANNER_PLUGIN_DIR . 'includes/class-festival-banner-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once FESTIVAL_BANNER_PLUGIN_DIR . 'includes/class-festival-banner-i18n.php';

		/**
		 * The class responsible for banner database queries.
		 */
		require_once FESTIVAL_BANNER_PLUGIN_DIR . 'includes/class-festival-banner-query.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once FESTIVAL_BANNER_PLUGIN_DIR . 'admin/class-festival-banner-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once FESTIVAL_BANNER_PLUGIN_DIR . 'public/class-festival-banner-public.php';

		$this->loader = new Festival_Banner_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Festival_Banner_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function set_locale() {
		$plugin_i18n = new Festival_Banner_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Festival_Banner_Admin( $this->get_plugin_name(), $this->get_version() );

		// Enqueue admin styles and scripts.
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Register custom post type.
		$this->loader->add_action( 'init', $plugin_admin, 'register_post_type' );

		// Add custom admin menu.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu' );

		// Display admin notices.
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'display_admin_notices' );

		// Handle custom row actions.
		$this->loader->add_filter( 'post_row_actions', $plugin_admin, 'add_row_actions', 10, 2 );

		// Handle bulk actions.
		$this->loader->add_filter( 'bulk_actions-edit-festival_banner', $plugin_admin, 'register_bulk_actions' );
		$this->loader->add_filter( 'handle_bulk_actions-edit-festival_banner', $plugin_admin, 'handle_bulk_actions', 10, 3 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function define_public_hooks() {
		$plugin_public = new Festival_Banner_Public( $this->get_plugin_name(), $this->get_version() );

		// Enqueue public styles and scripts.
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Display banners on frontend.
		$this->loader->add_action( 'wp_footer', $plugin_public, 'display_banners' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since  1.0.0
	 * @return string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since  1.0.0
	 * @return Festival_Banner_Loader Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since  1.0.0
	 * @return string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
<?php
/**
 * Fired during plugin activation
 *
 * @since      1.0.0
 *
 * @package    Festival_Banner
 * @subpackage Festival_Banner/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Festival_Banner
 * @subpackage Festival_Banner/includes
 * @author     justSohel <thesohelrana.me@gmail.com>
 */
class Festival_Banner_Activator {

	/**
	 * Activate the plugin.
	 *
	 * Checks system requirements and sets up initial plugin data.
	 *
	 * @since 1.0.0
	 */
	public static function activate() {
		// Check WordPress version requirement.
		self::check_wordpress_version();

		// Check PHP version requirement.
		self::check_php_version();

		// Set plugin activation timestamp.
		self::set_activation_time();

		// Set plugin version.
		self::set_plugin_version();

		// Set default options.
		self::set_default_options();

		// Flush rewrite rules.
		self::flush_rewrite_rules();
	}

	/**
	 * Check WordPress version requirement.
	 *
	 * @since 1.0.0
	 */
	private static function check_wordpress_version() {
		global $wp_version;

		$required_wp_version = '5.8';

		if ( version_compare( $wp_version, $required_wp_version, '<' ) ) {
			deactivate_plugins( FESTIVAL_BANNER_PLUGIN_BASENAME );
			wp_die(
				sprintf(
					/* translators: 1: Required WordPress version, 2: Current WordPress version */
					esc_html__( 'Festival Banner requires WordPress version %1$s or higher. You are running version %2$s. Please upgrade WordPress and try again.', 'festival-banner' ),
					esc_html( $required_wp_version ),
					esc_html( $wp_version )
				),
				esc_html__( 'Plugin Activation Error', 'festival-banner' ),
				array( 'back_link' => true )
			);
		}
	}

	/**
	 * Check PHP version requirement.
	 *
	 * @since 1.0.0
	 */
	private static function check_php_version() {
		$required_php_version = '7.4';

		if ( version_compare( PHP_VERSION, $required_php_version, '<' ) ) {
			deactivate_plugins( FESTIVAL_BANNER_PLUGIN_BASENAME );
			wp_die(
				sprintf(
					/* translators: 1: Required PHP version, 2: Current PHP version */
					esc_html__( 'Festival Banner requires PHP version %1$s or higher. You are running version %2$s. Please upgrade PHP and try again.', 'festival-banner' ),
					esc_html( $required_php_version ),
					esc_html( PHP_VERSION )
				),
				esc_html__( 'Plugin Activation Error', 'festival-banner' ),
				array( 'back_link' => true )
			);
		}
	}

	/**
	 * Set plugin activation timestamp.
	 *
	 * @since 1.0.0
	 */
	private static function set_activation_time() {
		$option_name = 'festival_banner_activated_time';

		// Only set if not already set (preserve original activation time).
		if ( false === get_option( $option_name ) ) {
			add_option( $option_name, time() );
		}
	}

	/**
	 * Set plugin version in database.
	 *
	 * @since 1.0.0
	 */
	private static function set_plugin_version() {
		update_option( 'festival_banner_version', FESTIVAL_BANNER_VERSION );
	}

	/**
	 * Set default plugin options.
	 *
	 * @since 1.0.0
	 */
	private static function set_default_options() {
		$default_options = array(
			'cache_duration' => 3600, // 1 hour in seconds
			'enable_caching' => true,
		);

		// Only set if option doesn't exist (don't override existing settings).
		if ( false === get_option( 'festival_banner_options' ) ) {
			add_option( 'festival_banner_options', $default_options );
		}
	}

	/**
	 * Flush rewrite rules.
	 *
	 * Ensures custom post type URLs work properly.
	 *
	 * @since 1.0.0
	 */
	private static function flush_rewrite_rules() {
		// Register the custom post type.
		self::register_post_type();

		// Flush rewrite rules.
		flush_rewrite_rules();
	}

	/**
	 * Register custom post type.
	 *
	 * Temporary registration for rewrite rules flush.
	 * The actual registration happens in the admin class.
	 *
	 * @since 1.0.0
	 */
	private static function register_post_type() {
		$labels = array(
			'name'          => _x( 'Festival Banners', 'Post Type General Name', 'festival-banner' ),
			'singular_name' => _x( 'Festival Banner', 'Post Type Singular Name', 'festival-banner' ),
		);

		$args = array(
			'label'               => __( 'Festival Banner', 'festival-banner' ),
			'labels'              => $labels,
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 28,
			'menu_icon'           => 'dashicons-megaphone',
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'supports'            => array( 'title' ),
			'has_archive'         => false,
			'rewrite'             => false,
		);

		register_post_type( 'festival_banner', $args );
	}
}
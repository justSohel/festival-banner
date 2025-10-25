<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Festival_Banner
 * @subpackage Festival_Banner/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Festival_Banner
 * @subpackage Festival_Banner/includes
 * @author     Your Name <email@example.com>
 */
class Festival_Banner_Deactivator {

	/**
	 * Deactivate the plugin.
	 *
	 * Performs cleanup tasks on plugin deactivation.
	 * Note: User data (banners and settings) are preserved.
	 *
	 * @since 1.0.0
	 */
	public static function deactivate() {
		// Clear all transient caches.
		self::clear_all_caches();

		// Flush rewrite rules.
		self::flush_rewrite_rules();

		// Log deactivation time (optional for future analytics).
		self::log_deactivation_time();
	}

	/**
	 * Clear all plugin transient caches.
	 *
	 * @since 1.0.0
	 */
	private static function clear_all_caches() {
		global $wpdb;

		// Delete all transients with our plugin prefix.
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} 
				WHERE option_name LIKE %s 
				OR option_name LIKE %s",
				$wpdb->esc_like( '_transient_festival_banner_' ) . '%',
				$wpdb->esc_like( '_transient_timeout_festival_banner_' ) . '%'
			)
		);

		// Clear object cache if available.
		if ( function_exists( 'wp_cache_flush' ) ) {
			wp_cache_flush();
		}
	}

	/**
	 * Flush WordPress rewrite rules.
	 *
	 * Cleans up custom post type rewrite rules.
	 *
	 * @since 1.0.0
	 */
	private static function flush_rewrite_rules() {
		flush_rewrite_rules();
	}

	/**
	 * Log deactivation timestamp.
	 *
	 * Useful for tracking plugin usage patterns.
	 *
	 * @since 1.0.0
	 */
	private static function log_deactivation_time() {
		update_option( 'festival_banner_deactivated_time', time() );
	}
}
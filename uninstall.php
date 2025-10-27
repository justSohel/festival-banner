<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * @since      1.0.0
 *
 * @package    Festival_Banner
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Delete all festival banner posts.
 */
function festival_banner_delete_all_posts() {
	global $wpdb;

	// Get all banner post IDs.
	$banner_ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = %s",
			'festival_banner'
		)
	);

	// Delete each banner and its meta data.
	foreach ( $banner_ids as $banner_id ) {
		// Delete all post meta.
		$wpdb->delete(
			$wpdb->postmeta,
			array( 'post_id' => $banner_id ),
			array( '%d' )
		);

		// Delete the post.
		$wpdb->delete(
			$wpdb->posts,
			array( 'ID' => $banner_id ),
			array( '%d' )
		);
	}
}

/**
 * Delete all plugin options.
 */
function festival_banner_delete_options() {
	// Delete plugin options.
	delete_option( 'festival_banner_version' );
	delete_option( 'festival_banner_activated_time' );
	delete_option( 'festival_banner_deactivated_time' );
	delete_option( 'festival_banner_options' );
}

/**
 * Delete all plugin transients.
 */
function festival_banner_delete_transients() {
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
 * Multisite uninstall.
 */
function festival_banner_uninstall_multisite() {
	global $wpdb;

	// Get all blog IDs.
	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );

	foreach ( $blog_ids as $blog_id ) {
		switch_to_blog( $blog_id );
		festival_banner_uninstall_single_site();
		restore_current_blog();
	}
}

/**
 * Single site uninstall.
 */
function festival_banner_uninstall_single_site() {
	// Delete all posts.
	festival_banner_delete_all_posts();

	// Delete all options.
	festival_banner_delete_options();

	// Delete all transients.
	festival_banner_delete_transients();

	// Clear any cached data.
	wp_cache_flush();
}

/**
 * Main uninstall function.
 */
if ( is_multisite() ) {
	// Multisite installation.
	festival_banner_uninstall_multisite();
} else {
	// Single site installation.
	festival_banner_uninstall_single_site();
}
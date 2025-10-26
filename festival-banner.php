<?php
/**
 * Festival Banner
 *
 * @package           Festival_Banner
 * @author            Your Name
 * @copyright         2024 Your Name
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Festival Banner (DEV Claude)
 * Plugin URI:        https://example.com/festival-banner
 * Description:       Create eye-catching festival banners for e-commerce sales campaigns with multiple positions and advanced scheduling.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Your Name
 * Author URI:        https://example.com
 * Text Domain:       festival-banner
 * Domain Path:       /languages
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define( 'FESTIVAL_BANNER_VERSION', '1.0.0' );

/**
 * Plugin file path.
 */
define( 'FESTIVAL_BANNER_PLUGIN_FILE', __FILE__ );

/**
 * Plugin directory path.
 */
define( 'FESTIVAL_BANNER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL.
 */
define( 'FESTIVAL_BANNER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin basename.
 */
define( 'FESTIVAL_BANNER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-festival-banner-activator.php
 */
function activate_festival_banner() {
	require_once FESTIVAL_BANNER_PLUGIN_DIR . 'includes/class-festival-banner-activator.php';
	Festival_Banner_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-festival-banner-deactivator.php
 */
function deactivate_festival_banner() {
	require_once FESTIVAL_BANNER_PLUGIN_DIR . 'includes/class-festival-banner-deactivator.php';
	Festival_Banner_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_festival_banner' );
register_deactivation_hook( __FILE__, 'deactivate_festival_banner' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require FESTIVAL_BANNER_PLUGIN_DIR . 'includes/class-festival-banner.php';

/**
 * The class responsible for banner database queries.
 */
require_once FESTIVAL_BANNER_PLUGIN_DIR . 'includes/class-festival-banner-query.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
function run_festival_banner() {
	$plugin = new Festival_Banner();
	$plugin->run();
}

run_festival_banner();
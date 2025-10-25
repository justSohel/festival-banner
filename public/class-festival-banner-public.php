<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Festival_Banner
 * @subpackage Festival_Banner/public
 */

class Festival_Banner_Public {
	
	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function enqueue_styles() {
		// Placeholder - will implement later
	}

	public function enqueue_scripts() {
		// Placeholder - will implement later
	}

	public function display_banners() {
		// Placeholder - will implement later
	}
}
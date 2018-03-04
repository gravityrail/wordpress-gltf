<?php

/**
 * Fired during plugin activation
 *
 * @link       http://goldsounds.com
 * @since      1.0.0
 *
 * @package    WebXR
 * @subpackage WebXR/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    WebXR
 * @subpackage WebXR/includes
 * @author     Daniel Walmsley <goldsounds@gmail.com>
 */
class WebXR_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		require_once plugin_dir_path( __FILE__ ) . 'class-webxr.php';
		$plugin = new WebXR();
		$plugin->register_scene_post_type();
		flush_rewrite_rules();
	}

}

<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://goldsounds.com
 * @since      1.0.0
 *
 * @package    WebXR
 * @subpackage WebXR/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    WebXR
 * @subpackage WebXR/includes
 * @author     Daniel Walmsley <goldsounds@gmail.com>
 */
class WebXR_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}

}

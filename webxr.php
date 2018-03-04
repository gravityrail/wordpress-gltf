<?php

/**
 * The plugin bootstrap file
 *
 * @link              http://goldsounds.com
 * @since             1.0.0
 * @package           webxr
 *
 * @wordpress-plugin
 * Plugin Name:       WebXR
 * Plugin URI:        http://goldsounds.com/plugins/webxr
 * Description:       A plugin to render WordPress content in XR (VR or AR) scenes.
 * Version:           1.0
 * Author:            Daniel Walmsley
 * Author URI:        http://goldsounds.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       webxr
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-webxr-activator.php
 */
function activate_webxr() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-webxr-activator.php';
	WebXR_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-webxr-deactivator.php
 */
function deactivate_webxr() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-webxr-deactivator.php';
	WebXR_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_webxr' );
register_deactivation_hook( __FILE__, 'deactivate_webxr' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-webxr.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_webxr() {

	$plugin = new WebXR();
	$plugin->run();

}
run_webxr();

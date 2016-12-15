<?php

/**
 * The plugin bootstrap file
 *
 * @link              http://goldsounds.com
 * @since             1.0.0
 * @package           Gltf
 *
 * @wordpress-plugin
 * Plugin Name:       GLTF Media Type
 * Plugin URI:        http://goldsounds.com/plugins/gltf
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Daniel Walmsley
 * Author URI:        http://goldsounds.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gltf
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-gltf-activator.php
 */
function activate_gltf() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gltf-activator.php';
	Gltf_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-gltf-deactivator.php
 */
function deactivate_gltf() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gltf-deactivator.php';
	Gltf_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_gltf' );
register_deactivation_hook( __FILE__, 'deactivate_gltf' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-gltf.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_gltf() {

	$plugin = new Gltf();
	$plugin->run();

}
run_gltf();

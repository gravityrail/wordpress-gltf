<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://goldsounds.com
 * @since      1.0.0
 *
 * @package    Gltf
 * @subpackage Gltf/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Gltf
 * @subpackage Gltf/public
 * @author     Daniel Walmsley <goldsounds@gmail.com>
 */
class Gltf_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Gltf_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Gltf_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/gltf-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Gltf_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Gltf_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		require_once dirname( dirname( __FILE__ ) ) . '/includes/class-gltf-model-utils.php';
		// Gltf_Model_Utils::enqueue_common_gltf_scripts( $this->version );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'js/public.js', array( 'jquery', 'wp-api' ), $this->version, false );

	}

}

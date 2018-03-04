<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://goldsounds.com
 * @since      1.0.0
 *
 * @package    WebXR
 * @subpackage WebXR/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WebXR
 * @subpackage WebXR/admin
 * @author     Daniel Walmsley <goldsounds@gmail.com>
 */
class WebXR_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WebXR_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WebXR_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/webxr-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook ) {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/webxr-admin.js', array( 'jquery' ), $this->version, false );

		global $post;

		// if we're editing a webxr_scene, load the metabox js
		if ( ( $hook == 'post-new.php' || $hook == 'post.php' ) && 'webxr_scene' === $post->post_type ) {
			wp_enqueue_script( 'webxr-model', plugin_dir_url( dirname( __FILE__ ) ) . 'js/public.js', array( 'jquery', 'wp-api' ), $this->version, false );
			wp_enqueue_script( 'webxr-admin-select-model-metabox', plugin_dir_url( __FILE__ ) . 'js/webxr-admin-select-model-metabox.js', array( 'jquery', 'webxr-model' ), $this->version, false );
		}
	}

}

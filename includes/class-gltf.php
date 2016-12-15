<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://goldsounds.com
 * @since      1.0.0
 *
 * @package    Gltf
 * @subpackage Gltf/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Gltf
 * @subpackage Gltf/includes
 * @author     Daniel Walmsley <goldsounds@gmail.com>
 */
class Gltf {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Gltf_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	// @see https://github.com/KhronosGroup/glTF/tree/master/specification/1.0#mimetypes
	// @see https://github.com/KhronosGroup/glTF/tree/master/extensions/Khronos/KHR_binary_glTF#mime-type
	protected $gltf_mime_types = array( 
		'glb' => 'model/gltf.binary',
		'gltf' => 'model/gltf+json',
		'bin' => 'application/octet-stream',
		'glsl' => 'text/plain'
	);

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'gltf';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->register_media();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Gltf_Loader. Orchestrates the hooks of the plugin.
	 * - Gltf_i18n. Defines internationalization functionality.
	 * - Gltf_Admin. Defines all hooks for the admin area.
	 * - Gltf_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-gltf-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-gltf-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-gltf-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-gltf-public.php';

		$this->loader = new Gltf_Loader();

	}

	private function register_media() {
		add_filter( 'upload_mimes', array( $this, 'upload_mime_types' ) );
		add_filter( 'wp_mime_type_icon', array( $this, 'mime_types_icons' ), 10, 3 );
		add_shortcode( 'gltf_model', array( $this, 'model_shortcode' ) );
		// add three.js if the current post has our shortcode
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_model_render_script' ) );
	}

	public function upload_mime_types( $mimes ) {
		foreach( $this->gltf_mime_types as $ext => $type ) {
			$mimes[ $ext ] = $type;
		}

		return $mimes;
	}

	public function mime_types_icons( $icon, $mime, $post_id ) {
		if ( in_array( $mime, array_values( $this->gltf_mime_types ) ) ) {
			return plugin_dir_url( dirname( __FILE__ ) ) . 'admin/images/model-icon.png';
		}
	}

	public function model_shortcode( $atts ) {
		$a = shortcode_atts( array(
			'url' => '',
			// 'bar' => 'something else',
		), $atts );

		return "<div class=\"gltf-model\" style=\"height: 300px\" data-model=\"".htmlspecialchars($a['url'])."\">model goes here</div>";
	}

	public function enqueue_model_render_script() {
		global $post;
	    if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'gltf_model') ) {
	        wp_enqueue_script( 'threejs', plugin_dir_url( dirname( __FILE__ ) ) . 'js/three.min.js', null, $this->version, false );
	        wp_enqueue_script( 'gltf-loader', plugin_dir_url( dirname( __FILE__ ) ) . 'js/loaders/GLTFLoader.js', array( 'threejs' ), $this->version, false );
	        wp_enqueue_script( 'gltf-model-preview', plugin_dir_url( dirname( __FILE__ ) ) . 'js/gltf-model-preview.js', array( 'jquery', 'gltf-loader' ), $this->version, false );
	    }
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Gltf_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Gltf_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Gltf_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Gltf_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Gltf_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}

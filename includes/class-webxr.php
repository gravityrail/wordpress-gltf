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
 * @package    WebXR
 * @subpackage WebXR/includes
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
 * @package    WebXR
 * @subpackage WebXR/includes
 * @author     Daniel Walmsley <goldsounds@gmail.com>
 */
class WebXR {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      WebXR_Loader    $loader    Maintains and registers all hooks for the plugin.
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

		$this->plugin_name = 'webxr';
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
	 * - WebXR_Loader. Orchestrates the hooks of the plugin.
	 * - WebXR_i18n. Defines internationalization functionality.
	 * - WebXR_Admin. Defines all hooks for the admin area.
	 * - WebXR_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * Utility functions
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-webxr-model-utils.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-webxr-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-webxr-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-webxr-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-webxr-public.php';

		$this->loader = new WebXR_Loader();

	}

	private function register_media() {
		add_filter( 'upload_mimes', array( $this, 'upload_mime_types' ) );
		add_filter( 'wp_mime_type_icon', array( $this, 'mime_types_icons' ), 10, 3 );
		add_shortcode( 'webxr_model', array( $this, 'model_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'init', array( $this, 'register_scene_post_type' ) );
		add_action( 'add_meta_boxes_webxr_scene', array( $this, 'add_scene_metaboxes' ) );
		add_action( 'save_post_webxr_scene', array( $this, 'save_scene_metaboxes' ), 10, 3 );
		add_filter( 'single_template', array( $this, 'register_scene_template' ) );
		add_filter( 'the_content', array( $this, 'render_model_attachment' ) );

		// remove the sidebars before rendering a scene
		add_filter( 'body_class', function( $classes ) {
			global $post;

			if ( $post && $post->post_type == 'webxr_scene' && in_array( 'has-sidebar', $classes ) ) {
				$classes = array_diff( $classes, array('has-sidebar') );
			}

			return $classes;
		}, 99, 1 );
	}

	public function upload_mime_types( $mimes ) {
		foreach( WebXR_Model_Utils::$gltf_mime_types as $ext => $type ) {
			$mimes[ $ext ] = $type;
		}

		return $mimes;
	}

	public function mime_types_icons( $icon, $mime, $post_id ) {
		if ( WebXR_Model_Utils::is_gltf_model_type( $mime ) ) {
			return plugin_dir_url( dirname( __FILE__ ) ) . 'admin/images/model-icon.png';
		}
	}

	public function model_shortcode( $atts ) {
		$a = shortcode_atts( array(
			'url' => '',
			'scale' => '1.0'
		), $atts );

		return '<div class="webxr-model" style="height: 300px" data-scale="'.htmlspecialchars($a['scale']).'" data-model="'.htmlspecialchars($a['url']).'"></div>';
	}

	public function register_scene_post_type() {
		register_post_type( 'webxr_scene',
			array(
				'labels' => array(
					'name'          => __( 'Scenes' ),
					'singular_name' => __( 'Scene' )
				),
				'public'             => true,
				'has_archive'        => true,
				'publicly_queryable' => true,
				'show_in_rest'       => true,
				'rewrite'            => array('slug' => 'scenes'),
				'menu_icon'          => 'dashicons-star-filled',
				'rest_base'          => 'scene',
				'rest_controller_class' => 'WP_REST_Posts_Controller'
			)
		);
	}

	function add_scene_metaboxes() {
		add_meta_box( 'webxr_select_scene_model', __( '3D Models', 'webxr' ), array( $this, 'select_scene_model_callback' ), 'webxr_scene' );
	}

	function select_scene_model_callback( $post ) {
		wp_nonce_field( basename( __FILE__ ), 'webxr_nonce' );

		// Get WordPress' media upload URL
		$upload_link = esc_url( get_upload_iframe_src( 'image', $post->ID ) );

		?>
		<div class="scene-models">
			<a class="upload-model" href="<?php echo $upload_link ?>">
				<?php _e('Add 3D model') ?>
			</a>
		<?php

		$models = get_post_meta( $post->ID, '_webxr_model' );

		foreach( $models as $model ) {

			// See if there's a media id already saved as post meta
			$model_uuid = $model['id'];
			$model_attachment_id = $model['attachment_id'];
			$model_scale = $model['scale'];
			$model_scale = $model_scale ? $model_scale : 1.0;
			// TODO position

			// Get the image src
			$model_url = wp_get_attachment_url( $model_id );

			// For convenience, see if the array is valid
			?>

			<!-- Model container -->
			<div class="webxr-model-container" id="webxr-model-<?php echo $model_uuid; ?>">
				<div class="webxr-model" data-model="<?php echo $model_url; ?>" data-scale="<?php echo $model_scale; ?>" style="width: 300px; height: 300px;"></div>
				<!-- A hidden input to set and post the chosen model id -->
				<input class="model-id" name="model-id" type="hidden" value="<?php echo esc_attr( $model_attachment_id ); ?>" />
				<a class="delete-model <?php if ( ! $model_is_set  ) { echo 'hidden'; } ?>"
				href="#">
					<?php _e('Remove this 3D model') ?>
				</a>
				<p>
					<label for="model-scale">
						<?php _e( 'Model Scale', 'webxr'); ?>
						<input class="model-scale" name="model-scale" value="<?php echo esc_attr( $model_scale ); ?>" />
					</label>
				</p>
			</div>
			<?php
		}

		echo "</div>";
	}

	public function save_scene_metaboxes( $post_id, $post, $update ) {
		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST[ 'webxr_nonce' ] ) && wp_verify_nonce( $_POST[ 'webxr_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

		// Exits script depending on save status
		if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
			return;
		}

		if( isset( $_POST[ 'model-id' ] ) ) {
			update_post_meta( $post_id, '_webxr_model', $_POST[ 'model-id' ] );
		}
		if( isset( $_POST[ 'model-scale' ] ) ) {
			update_post_meta( $post_id, '_webxr_model_scale', $_POST[ 'model-scale' ] );
		}
	}

	public function register_scene_template( $single ) {
		global $wp_query, $post;

		/* Checks for single template by post type */
		if ($post->post_type == 'webxr_scene'){
			return dirname( dirname( __FILE__ ) ) . '/public/templates/single-webxr_scene.php';
		}

		return $single;
	}

	/**
	 * Intercept model attachment pages so that header/footer are rendered from the theme
	 */
	public function render_model_attachment( $content ) {
		$post = get_post();
		if ( $post && $post->post_type == 'attachment' && WebXR_Model_Utils::is_gltf_model_type( $post->post_mime_type ) ) {
			// render style tag
			$style_tag = <<<ENDSTYLE
			<style>
				.gltf-model-scene {
					width: 100%;
					height: auto;
					min-height: 300px;
				}
			</style>
ENDSTYLE;
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aframe-scene-builder.php';
			$scene = new AFrame_Scene_Builder( 'webxr-gltf-model-'.$post->ID, 'gltf-model-scene' );
			$scene
				->add_gltf_model( 'model', wp_get_attachment_url( $post->ID ) );

			// allow other code to inject stuff into the scene
			$scene = apply_filters( 'webxr_model_attachment_pre_render', $scene );

			return $style_tag . $scene->build();
		}
		return $content;
	}

	public function enqueue_scripts() {
		global $post;
		if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'webxr_model') ) {
			wp_enqueue_script( 'webxr-model', plugin_dir_url( dirname( __FILE__ ) ) . 'js/public.js', array( 'jquery', 'wp-api' ), $this->version, false );
		}
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WebXR_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new WebXR_i18n();

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

		$plugin_admin = new WebXR_Admin( $this->get_plugin_name(), $this->get_version() );

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

		$plugin_public = new WebXR_Public( $this->get_plugin_name(), $this->get_version() );

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
	 * @return    WebXR_Loader    Orchestrates the hooks of the plugin.
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

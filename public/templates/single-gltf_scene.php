<?php
/**
 * The template for displaying a single VR scene
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 * @since 1.0
 * @version 1.0
 */

// TODO: move this to somewhere it can be shared, so we can render it in response to mysite.com/mypost?format=json
function get_scene_as_json( $scene ) {

	$main_model_id = get_post_meta( $scene->ID, '_gltf_main_model', true );
	$main_model_url = wp_get_attachment_url( $main_model_id );

	$scene_obj = array(
		// TODO
		// - "main" object, cube, something?
		'main_model' => $main_model_url,
		'main_model_scale' => get_post_meta( $scene->ID, '_gltf_main_model_scale', true ),
		// - location, scale, lights?
		// - form of locomotion - am I walking? Flying?
		// - links? Are they just an object that can be "selected"? Or a special kind of object that always looks the same?
		//   or a particular "effect" applied to a whole object, e.g. glow
		// - external photosphere?
		// - nested object references and locations
		// - embedded media
		// - other scenes, embedded in this scene? e.g. a scene on a table. Clicking "jumps in"
	);
	return json_encode( $scene_obj );
}

get_header(); ?>

<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
				/* Start the Loop */
				while ( have_posts() ) : the_post();
					$scene_id = get_the_ID();
					$scene_dom_id = 'gltf-scene-'.$scene_id; ?>
					<div class="gltf-scene" id="<?php echo $scene_dom_id; ?>" style="height: 600px"></div>
					<script type="text/javascript">
						jQuery( function() {
							GltfSceneRenderer.render( '<?php echo $scene_dom_id; ?>', <?php echo $scene_id; ?> );
						} );
					</script>

					<?php
					the_post_navigation();
				endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->
</div><!-- .wrap -->

<?php get_footer();
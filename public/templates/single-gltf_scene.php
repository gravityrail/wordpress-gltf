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

	$scene_obj = array(
		// TODO
		// - "main" object, cube, something?
                'main_model' => get_metadata( 'gltf_scene', $scene->ID, '_gltf_main_model', true )
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
                                		$scene_dom_id = 'gltf-scene-'.get_the_ID();
                                		?>The VR Scene
                                		<div class="gltf-scene" id="<?php echo $scene_dom_id; ?>"></div>
                                		<script type="text/javascript">
                                			jQuery( function() {
                                				console.log("loading");
                                				var scene = <?php echo get_scene_as_json( get_post() ); ?>;
                                				var scene_dom_id = '<?php echo $scene_dom_id; ?>';

                                				GltfScene.render( scene_dom_id, scene );
                                			} );
                                		</script>

                                		<?php

                                        // get_template_part( 'template-parts/post/content', get_post_format() );

                                        // If comments are open or we have at least one comment, load up the comment template.
                                        // if ( comments_open() || get_comments_number() ) :
                                        //         comments_template();
                                        // endif;

                                        // the_post_navigation( array(
                                        //         'prev_text' => '<span class="screen-reader-text">' . __( 'Previous Post', 'twentyseventeen' ) . '</span><span aria-hidden="true" class="nav-subtitle">' . __( 'Previous', 'twentyseventeen' ) . '</span> <span class="nav-title"><span class="nav-title-icon-wrapper">' . twentyseventeen_get_svg( array( 'icon' => 'arrow-left' ) ) . '</span>%title</span>',
                                        //         'next_text' => '<span class="screen-reader-text">' . __( 'Next Post', 'twentyseventeen' ) . '</span><span aria-hidden="true" class="nav-subtitle">' . __( 'Next', 'twentyseventeen' ) . '</span> <span class="nav-title">%title<span class="nav-title-icon-wrapper">' . twentyseventeen_get_svg( array( 'icon' => 'arrow-right' ) ) . '</span></span>',
                                        // ) );

                                endwhile; // End of the loop.
                        ?>

                </main><!-- #main -->
        </div><!-- #primary -->
        <?php get_sidebar(); ?>
</div><!-- .wrap -->

<?php get_footer();
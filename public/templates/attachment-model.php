<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

get_header(); ?>

<div class="entry-attachment">
	<h1>3D Model</h1>
	<?php if ( has_excerpt() ) : ?>
		<div class="entry-caption">
			<?php the_excerpt(); ?>
		</div><!-- .entry-caption -->
	<?php endif; ?>
</div><!-- .entry-attachment -->

<?php get_footer();
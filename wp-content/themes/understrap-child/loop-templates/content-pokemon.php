<?php
/**
 * Post rendering content according to caller of get_template_part
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<article <?php post_class( ['g-col-4'] ); ?> id="post-<?php the_ID(); ?>">
	<div class="card">
		<div class="card-body">
			<?php 
			echo sprintf( '<a href="%s" rel="bookmark">%s</a>', esc_url( get_permalink() ), get_the_post_thumbnail( $post->ID, [350, 'auto'], [
				'class'	=> 'card-img-top'
			] ) );
			//echo get_the_post_thumbnail( $post->ID, [350, 'auto'] ); 

			understrap_link_pages();
			?>

		</div><!-- .entry-content -->

		<footer class="card-footer">
			<?php
			the_title(
				sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ),
				'</a></h2>'
			);
			
			understrap_entry_footer(); 
			
			?>

		</footer><!-- .entry-footer -->
	</div>
</article><!-- #post-<?php the_ID(); ?> -->

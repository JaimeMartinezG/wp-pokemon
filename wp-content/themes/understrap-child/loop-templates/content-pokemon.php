<?php
/**
 * Post rendering content according to caller of get_template_part
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<header class="entry-header">

		<?php
		the_title(
			sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ),
			'</a></h2>'
		);
		?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php 
		echo sprintf( '<a href="%s" rel="bookmark">%s</a>', esc_url( get_permalink() ), get_the_post_thumbnail( $post->ID, [350, 'auto'] ) );
		//echo get_the_post_thumbnail( $post->ID, [350, 'auto'] ); 

		understrap_link_pages();
		?>

	</div><!-- .entry-content -->

	<footer class="entry-footer">

		<?php understrap_entry_footer(); ?>

	</footer><!-- .entry-footer -->

</article><!-- #post-<?php the_ID(); ?> -->

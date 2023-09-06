<?php
/**
 * Single post partial template
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
	<div class="entry-content">
		<div class="row">
			<div class="col-6 col-xs-12 imagen">
				<?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>

			</div>
			<div class="col-6 col-xs-12 contenido">
				<?php
				the_title( '<h1 class="entry-title">', '</h1>' ); 
				the_content( );
				the_terms( get_the_ID(), 'type', __('Type', 'pokemon') . ': ', ' | ', '' );
				?>
				<div class="pokedex_id">
					<?php
					Pokemon_Helper::the_pokedex_id(get_the_ID());
					?>
					<div class="older_version" id="older_version_<?php the_ID(); ?>"><a href="#" class="show_oldest_version" data-id="<?php the_ID(); ?>"><?php _e( 'Show oldest version', 'pokemon' ); ?></a></div>
				</div>
			</div>
		</div>
		<?php
		
		understrap_link_pages();
		?>

	</div><!-- .entry-content -->

	<footer class="entry-footer">

		<?php understrap_entry_footer(); ?>

	</footer><!-- .entry-footer -->

</article><!-- #post-<?php the_ID(); ?> -->

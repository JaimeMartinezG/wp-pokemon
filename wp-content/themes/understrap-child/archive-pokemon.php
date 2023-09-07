<?php
/**
 * The template for displaying archive pages
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

$container = get_theme_mod( 'understrap_container_type' );
?>

<div class="wrapper" id="archive-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">
		<div id="search" class="row">
			<div class="col"></div>
			<div class="col">
				<label class="col"><?php _e( 'Filter', 'pokemon' ); ?></label>
				<?php
				$pokeAPI = new PokeAPI();
				$filter_from_poke_api = $pokeAPI->get_type_list( ['limit' => 5]);
				$items_for_select = array_map( function( $el ) {
					return [$el->name => Pokemon_Helper::humanize_pokemon_name( $el->name )];
				}, $filter_from_poke_api->results );

				Pokemon_Helper::create_select( $items_for_select, [
					'name'		=> 'filter_by_type',
					'id'		=> 'filter_by_type',
					'default'	=> __( 'Select one type', 'pokemon' ),
					'class'		=> [
						'form-select'
					]
				] );
				?>
			</div>
		</div>
		<div class="row">

			<?php
			// Do the left sidebar check and open div#primary.
			get_template_part( 'global-templates/left-sidebar-check' );
			?>

			<main class="site-main" id="main">

				<?php
				if ( have_posts() ) {
					?>
					<header class="page-header">
						<h1 class="page-title">Pokémons</h1>
						<?php
						the_archive_description( '<div class="taxonomy-description">', '</div>' );
						?>
					</header><!-- .page-header -->
					<?php
					// Start the loop.
					?>
					<div class="row row-cols-3" style="--bs-gutter-y: 1.5rem;">
						<?php
						while ( have_posts() ) {
							the_post();
							/*
							* Include the Post-Format-specific template for the content.
							* If you want to override this in a child theme, then include a file
							* called content-___.php (where ___ is the Post Format name) and that will be used instead.
							*/
							get_template_part( 'loop-templates/content', 'pokemon' );
			
						}
						?>
					</div>
					<?php
				} else {
					get_template_part( 'loop-templates/content', 'none' );
				}
				?>

			</main>

			<?php
			// Display the pagination component.
			understrap_pagination();

			// Do the right sidebar check and close div#primary.
			get_template_part( 'global-templates/right-sidebar-check' );
			?>

		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #archive-wrapper -->

<?php
get_footer();

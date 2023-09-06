<?php
/**
 * Template Name: Pokemon Generator
 */

 if( !defined( 'ABSPATH' ) ) die( 'Access not allowed' );

get_header();

$container = get_theme_mod( 'understrap_container_type' );

?>

<div class="wrapper" id="page-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<div class="row">

			<?php
			// Do the left sidebar check and open div#primary.
			get_template_part( 'global-templates/left-sidebar-check' );
			?>

			<main class="site-main" id="main">

				<?php
                //Test user role/access
                if( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
                    get_template_part( 'loop-templates/content-page-generate' );
                } else {
                    echo 'Página sin funcionalidad.';
                }
				?>

			</main>

			<?php
			// Do the right sidebar check and close div#primary.
			get_template_part( 'global-templates/right-sidebar-check' );
			?>

		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #page-wrapper -->

<?php
get_footer();
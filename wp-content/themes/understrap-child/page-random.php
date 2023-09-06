<?php
/**
 * Template Name: Random Pokemon
 */

if( !defined( 'ABSPATH' ) ) die( 'Access not allowed' );

 $the_pokemon = get_posts( [
    'post_type'     => 'pokemon',
    'orderby'         => 'rand',
    'posts_per_page'=> 1
] );

wp_safe_redirect( get_permalink( $the_pokemon[0]->ID ), 302 );
exit;
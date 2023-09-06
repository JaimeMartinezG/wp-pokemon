<?php
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style( 'chil-theme-custom-styles', get_stylesheet_directory_uri() . '/assets/scss/custom.css', [], null );
}, 15);
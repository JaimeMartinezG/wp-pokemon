<?php
/*
Plugin Name: Pokémon
Plugin Author: Jaime Martínez
Description: Pokémon managing and reviewing through PokéAPI
Version: 1.0.0
Text Domain: pokemon
*/

require 'classes/custom-post-type.php';
require 'classes/helper.php';
require 'classes/ajax.php';
require 'classes/poke-api.php';

class Pokemons {
    public function __construct() {
        add_action( 'wp_enqueue_scripts', [$this, 'scripts'], 10 );
    }

    public function scripts() {
        wp_enqueue_script( 'pokemon-main-script', plugins_url( 'pokemons', 'Pokemons' ) . '/assets/js/main-front.js', [], null, true );
        wp_localize_script( 'pokemon-main-script', 'wp', [
            'urls'  => [
                'admin_ajax'    => get_admin_url( null, 'admin-ajax.php' ),
                'nonce'         => wp_create_nonce( 'pokemon_ajax_nonce' )
            ]
        ] );
    }
}

new Pokemons();
<?php

final class PokemonAPI {
    public function __construct() {

        add_action( 'rest_api_init', [$this, 'api_endpoint_simple_list'] );
        add_action( 'rest_api_init', [$this, 'api_endpoint_full_list'] );

    }

    public function api_endpoint_simple_list() {
        
        register_rest_route( 'pokemon/v1', 'simple-list', [
            'methods'   => 'GET',
            'callback'  => [$this, 'cb_simple_list']
        ] );
        
    }

    public function api_endpoint_full_list() {
        
        register_rest_route( 'pokemon/v1', 'full-list', [
            'methods'   => 'GET',
            'callback'  => [$this, 'cb_full_list']
        ] );
        
    }

    public function cb_simple_list() {
        
        $q = new WP_Query( [
            'post_type'     => 'pokemon',
            'posts_per_page'=> -1,
        ] );

        $mapped_meta_data = $this->get_metadatas_for_pokemons( null, ['pokemon_pokedex_number'] );

        $pokemons = $q->get_posts();
        $result = [];
        if( $pokemons ) {
            foreach( $pokemons as $pokemon ) {
                
                $pokemon_pokedex_number = isset($mapped_meta_data[$pokemon->ID]['pokemon_pokedex_number']) ? $mapped_meta_data[$pokemon->ID]['pokemon_pokedex_number'] : null;
                $pokemon_name = $pokemon->post_title;

                if( $pokemon_pokedex_number ) {
                    $result[] = [
                        'ID'        => $pokemon_pokedex_number,
                        'name'      => $pokemon_name
                    ];
                }

            }
        }
        
        return $result;
        
    }

    public function cb_full_list() {
        
        $q = new WP_Query( [
            'post_type'     => 'pokemon',
            'posts_per_page'=> -1,
        ] );

        $mapped_meta_data = $this->get_metadatas_for_pokemons();
        $pokemons = $q->get_posts();

        $result = [];

        if( $pokemons ) {
            foreach( $pokemons as $pokemon ) {
                /**
                 * Auxiliar function that extract only the slug from the WP_Term object
                 */
                $lambda = function( WP_Term $el ) {
                    return $el->slug;
                };

                $meta_data = $mapped_meta_data[$pokemon->ID];

                $pokemon_weight                 = isset( $meta_data['pokemon_weight'] ) ? $meta_data['pokemon_weight'] : null;
                $pokemon_pokedex_number         = isset( $meta_data['pokemon_pokedex_number'] ) ? $meta_data['pokemon_pokedex_number'] : null;
                $pokemon_pokedex_game           = isset( $meta_data['pokemon_pokedex_game'] ) ? $meta_data['pokemon_pokedex_game'] : null;
                $pokemon_pokedex_older_number   = isset( $meta_data['pokemon_pokedex_older_number'] ) ? $meta_data['pokemon_pokedex_older_number'] : null;
                $pokemon_pokedex_older_game     = isset( $meta_data['pokemon_pokedex_older_game'] ) ? $meta_data['pokemon_pokedex_older_game'] : null;

                $_pokemon_types  = wp_get_post_terms( $pokemon->ID, 'type' );
                $pokemon_types = !is_a( $_pokemon_types, 'WP_Error' ) ? array_map( $lambda, $_pokemon_types ) : null;

                $_pokemon_color  = wp_get_post_terms( $pokemon->ID, 'color' );
                $pokemon_color = !is_a( $_pokemon_color, 'WP_Error' ) ? array_map( $lambda, $_pokemon_color ) : null;

                $pokemon_image = wp_get_attachment_image_url( get_post_thumbnail_id( $pokemon->ID ), 'full' );

                $result[] = [
                    'ID'            => $pokemon->ID,
                    'name'          => $pokemon->post_title,
                    'description'   => $pokemon->post_content,
                    'weight'        => $pokemon_weight,
                    'types'         => $pokemon_types ? implode( ',', $pokemon_types ) : null,
                    'color'         => $pokemon_color ? implode( ',', $pokemon_color ) : null,
                    'image'         => $pokemon_image
                ];
            }
        }

        return $result;
        
    }

    /**
     * Retrieve all metadata for all pokemon_post_type and return and
     * associative array with the post_id as master key.
     * 
     * @global WP_DB $wpdb
     */
    private function get_metadatas_for_pokemons( int|null $post_id = null, array|null $metas = null ) : array {
        global $wpdb;
        
        $sql = 'SELECT * 
                FROM ' . $wpdb->prefix . 'postmeta ';
        if($post_id) {
            $sql .= 'WHERE post_id = ' . $post_id . ' ';
        } else {
            $sql .= 'WHERE post_id IN ( 
                        SELECT ID 
                        FROM ' . $wpdb->prefix . 'posts 
                        WHERE post_type = "pokemon" 
                        AND post_status = "publish" 
            );';
        }
                

        $all_data = $wpdb->get_results($sql);

        $mapped_data = [];
        foreach( $all_data as $meta_obj ) {
            if( !$metas || ( $metas && in_array( $meta_obj->meta_key, $metas ) ) ) {
                $mapped_data[$meta_obj->post_id][$meta_obj->meta_key] = $meta_obj->meta_value;
            }
        }
        
        return $mapped_data;

    }
}

new PokemonAPI();
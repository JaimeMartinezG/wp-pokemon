<?php

class Pokemon_Helper {

    public static function the_pokedex_id( int $post_id ) {

        echo self::get_the_pokedex_id( $post_id );

    }

    public static function get_the_pokedex_id( int $post_id ) {
        $pokemon_pokedex_number = get_post_meta( $post_id, 'pokemon_pokedex_number', true );
        $pokemon_pokedex_game   = get_post_meta( $post_id, 'pokemon_pokedex_game', true );

        $result = null;

        if($pokemon_pokedex_number) {
            $result['pokemon_pokedex_number'] = '#' . self::format_pokedex_id( $pokemon_pokedex_number, 5 );
        }

        if($pokemon_pokedex_game) {
            $result['pokemon_pokedex_game'] = __( 'Game version', 'pokemon' ) . ': ' . $pokemon_pokedex_game;
        }

        return $result ? implode( ' | ', $result ) : false;
    }

    public static function the_oldest_pokedex_id( int $post_id ) {
        echo self::get_the_oldest_pokedex_id( $post_id );
    }

    public static function get_the_oldest_pokedex_id( int $post_id ) {
        $pokemon_pokedex_older_number   = get_post_meta( $post_id, 'pokemon_pokedex_older_number', true );
        $pokemon_pokedex_older_game     = get_post_meta( $post_id, 'pokemon_pokedex_older_game', true );

        $result = null;

        if($pokemon_pokedex_older_number) {
            $result['pokemon_pokedex_older_number'] = '#' . self::format_pokedex_id( $pokemon_pokedex_older_number, 5 );
        }
        if($pokemon_pokedex_older_game) {
            $result['pokemon_pokedex_older_game'] = __( 'Game version', 'pokemon' ) . ': ' . $pokemon_pokedex_older_game;
        }

        return $result ? implode( ' | ', $result ) : __( 'There aren\'t data.', 'pokemon' );
    }

    /**
     * Format Pokedex_id to 000XX.
     * 
     * @param int $pokedex_number Pokemon number (ID)
     * @param int $positions Total digits for fill.
     */
    public static function format_pokedex_id( int $pokedex_number, int $positions = 4 ) : string {
        //Digits
        $digits = strlen( $pokedex_number );
        
        $result = '';
        
        while($digits < ( $positions )) {
            $result .= '0';
            $digits ++;
        }

        $result .= $pokedex_number;

        return $result;
    }

    public static function mapeo_pokemons_local_pokeapi( $pokeapi_list, $local_list ) {
        //Normalize pokeapi_list items
        $mapped_pokeapi_list = self::format_pokeapi_list($pokeapi_list);

        //Normalize local stored items
        $mapped_local_list = self::format_local_list($local_list);

        //Get all pokeapi_lists discarding all local_stored
        $result = array_diff_key($mapped_pokeapi_list, $mapped_local_list);

        return $result;
    }

    /**
     * Normalize the object received from PokéAPI to an array with pairs
     * {pokemon_name} => {pokemon_api_url}
     */
    private static function format_pokeapi_list( $list ) {
        $result = [];
        foreach( $list as $object ) {
            $result[$object->name] = $object->url;
        }

        return $result;
    }

    /**
     * Normalize the object of local stored pokemons to an array with pairs
     * {pokemon_name} => {post_id}
     */
    private static function format_local_list( $list ) {
        $result = [];
        foreach( $list as $object ) {
            $result[$object->post_name] = $object->ID;
        }

        return $result;
    }

    /**
     * Select one item randomly from the normalized list
     */
    public static function select_the_one( ?array $mapped_list ) :? array {
        if( !$mapped_list ) {
            throw new WP_Error( 'not-mapped', 'We aren\'t received the list.' );
        }

        $the_one = array_rand( $mapped_list, 1 );

        $result = [$the_one => $mapped_list[$the_one]];

        return $result;
    }

    /**
     * Translate from PokéAPI names to human name: gallade-mega > Gallage Mega
     */
    public static function humanize_pokemon_name( string $name ) : string {
        $splitted_name = explode( '-', $name );
        if( is_array( $splitted_name ) ){
            $uppercase_items = array_map( function( $el ) {
                return ucwords( $el );
            }, $splitted_name );

            return implode( ' ', $uppercase_items );
        } else {
            return ucwords($name);
        }
        
    }

    public static function get_pokemon_info( $pokemon_info, $field ) {
        switch( $field ) {
            case 'color': 
                $game_indices = $pokemon_info->game_indices;
                $last_index = is_array( $game_indices) ? array_slice( $game_indices, -1, 1 ) : null;

                if( $last_index ) {
                    //Check if term exists in our WP; else, I proceed to create.
                    $the_term = get_term_by( 'slug', $last_index[0]->version->name, 'color' );
                    if( !$the_term ) {
                        //Create new term
                        $the_term = wp_insert_term( Pokemon_Helper::humanize_pokemon_name( $last_index[0]->version->name ), 'color', [
                            'slug'  => $last_index[0]->version->name
                        ] );
                    }
                    return is_a( $the_term, 'WP_Term' ) ? $the_term->term_id : $the_term['term_id'];
                }

                break;
            case 'current_game':
                $game_indices = $pokemon_info->game_indices;
                $last_index = is_array( $game_indices ) ? array_slice( $game_indices, -1, 1 ) : null;

                if( $last_index ) {
                    return $last_index[0]->version->name;
                }

                break;
            case 'older_number':
                $game_indices = $pokemon_info->game_indices;
                $first_index = is_array( $game_indices ) ? array_slice( $game_indices, 0, 1 ) : null;

                if( $first_index ) {
                    return $first_index[0]->game_index;
                }

                break;
            case 'older_game':
                $game_indices = $pokemon_info->game_indices;
                $first_index = is_array( $game_indices ) ? array_slice( $game_indices, 0, 1 ) : null;

                if( $first_index ) {
                    return $first_index[0]->version->name;
                }

                break;
            case 'type':
                $types = $pokemon_info->types;

                if( $types ) {
                    $return = [];
                    foreach( $types as $type ) {
                        //Check if term exists in our WP; else, I proceed to create.
                        $the_term = get_term_by( 'slug', $type->type->name, 'type' );
                        if(!$the_term) {
                            //Create new term
                            $the_term = wp_insert_term( Pokemon_Helper::humanize_pokemon_name( $type->type->name ), 'type', [
                                'slug'  => $type->type->name
                            ] );
                        }
                        $return[] = is_a( $the_term, 'WP_Term' ) ? $the_term->term_id : $the_term['term_id'];
                    }

                    if($return) {
                        return implode( ',', $return);
                    }
                }

                break;
            default:
                return $pokemon_info;
                break;
        }

        return false;
    }
}
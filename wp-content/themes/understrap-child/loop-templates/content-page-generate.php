<?php

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

?>
<div class="container">
    <div class="row">
        <div class="col">
            <pre>
            <?php
            //Pokemons stored in our DB
            $pokemons_local = get_posts( [
                'post_type'     => 'pokemon',
                'posts_per_page'=> -1
            ] );

            //var_dump( $pokemons_local );
            ?>
            </pre>
        </div>
        <div class="col">
            <pre>
            <?php
            //Lista de pokemons de la PokéAPI
            $PokeAPI = new PokeAPI();
            $pokeapi_list = $PokeAPI->get_pokemon_list(['limit' => 9999]);

            //Map both pokemon lists, local and PokéAPI. Return only pokemons that not are stored locally
            $mapped = Pokemon_Helper::mapeo_pokemons_local_pokeapi( $pokeapi_list->results, $pokemons_local );

            //var_dump( $mapped );
            
            ?>
            </pre>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <h2>The One</h2>
            <pre>
                <?php
                //Choose randomly one of the pokemons
                $the_one = Pokemon_Helper::select_the_one( $mapped );

                //Store data of the selected item in to local db, to pokemont CPT
                $pokemon_info = $PokeAPI->do_custom_request( $the_one[key( $the_one )] );

                $args_new_pokemon = [
                    'post_type'     => 'pokemon',
                    'post_status'   => 'publish',
                    'post_title'    => Pokemon_Helper::humanize_pokemon_name( $pokemon_info->name ),
                    'post_name'     => $pokemon_info->name,
                    'post_content'  => '',
                    'comment_status'    => 'closed',
                    'ping_status'   => 'closed',
                    'meta_input'    => [
                        'pokemon_weight'                => $pokemon_info->weight,
                        'pokemon_pokedex_number'        => $pokemon_info->id,
                        'pokemon_pokedex_game'          => Pokemon_Helper::get_pokemon_info( $pokemon_info, 'current_game' ),
                        'pokemon_pokedex_older_number'  => Pokemon_Helper::get_pokemon_info( $pokemon_info, 'older_number' ),
                        'pokemon_pokedex_older_game'    => Pokemon_Helper::get_pokemon_info( $pokemon_info, 'older_game'),
                    ]
                ];

                $color = Pokemon_Helper::get_pokemon_info( $pokemon_info, "color" );
                if( $color ) {
                    $args_new_pokemon['tax_input']['color'] = $color;
                }

                $types = Pokemon_Helper::get_pokemon_info( $pokemon_info, 'type' );
                if( $types ) {
                    $args_new_pokemon['tax_input']['type'] = $types;
                }
                

                $new_pokemon = wp_insert_post( $args_new_pokemon );

                if( !is_a( $new_pokemon, 'WP_Error' ) ) {
                    //Attach the media file
                    $image = media_sideload_image( 
                        $pokemon_info->sprites->other->{'official-artwork'}->front_default, 
                        $new_pokemon, null, 'id' );

                    set_post_thumbnail( $new_pokemon, $image );

                    $the_image = null;
                    if( !is_a( $image, 'WP_Error' ) ) {
                        $the_image = wp_get_attachment_image( $image, [350, 'auto'] );
                    }

                    $the_pokemon = get_post( $new_pokemon );

                    //Show all data of the Pokemon
                    ?>
                    
                    <h2><?php echo $the_pokemon->post_title; ?> is now in our DataBase</h2>
                    
                    <?php
                    echo $the_image;
                    ?>

                    <a href="<?php the_permalink( $new_pokemon ); ?>">Click here to go to detail page</a>

                    <?php
                }
                ?>
            </pre>
        </div>
    </div>
</div>

<?php

class Pokemon_CPT {

    public $cpt = 'pokemon';
    public $taxonomy_type = 'type';
    public $taxonomy_color = 'color';

    public function __construct() {
        add_action('init', [$this, 'cpt']);
        add_action('init', [$this, 'taxonomy_type']);
        add_action('init', [$this, 'taxonomy_color']);
        add_action("save_post_{$this->cpt}", [$this, 'save_post']);
    }

    public function cpt() {
        $labels = [];
        $args = [
            'label'         => __('Pokémon', 'pokemon'),
            'labels'        => $labels,
            'public'        => true,
            'hierarchical'  => false,
            'has_archive'   => 'pokemons',
            'supports'      => [
                'title',
                'editor',
                'thumbnail'
            ],
            'register_meta_box_cb'  => [$this, 'pokemon_metabox'],
            'taxonomies'    => [
                $this->taxonomy_type,
                $this->taxonomy_color
            ],
            'show_in_rest'  => true
        ];

        register_post_type( $this->cpt, $args );
    }
    public function taxonomy_type() {
        $args = [
            'labels'        => [
                'name'          => __('Pokemon types', 'pokemon'),
                'singular_name' => __('Pokemon type', 'pokemon'),
                'search_items'  => __('Search Pokemon types', 'pokemon'),
                'edit_item'     => __('Edit type', 'pokemon'),
                'add_new_item'  => __('Add new type', 'pokemon'),
                'new_item_name' => __('New type', 'pokemon')
            ],
            'public'        => true,
            'hierarchical'  => true,
        ];
        register_taxonomy( $this->taxonomy_type, $this->cpt, $args );
    }
    public function taxonomy_color() {
        $args = [
            'labels'        => [
                'name'          => __('Pokemon colors', 'pokemon'),
                'singular_name' => __('Pokemon color', 'pokemon'),
                'search_items'  => __('Search Pokemon colors', 'pokemon'),
                'edit_item'     => __('Edit color', 'pokemon'),
                'add_new_item'  => __('Add new color', 'pokemon'),
                'new_item_name' => __('New color', 'pokemon')
            ],
            'public'        => true,
            'hierarchical'  => true,
        ];
        register_taxonomy( $this->taxonomy_color, $this->cpt, $args );
    }

    /**
     * @var WP_Post $post
     */
    public function pokemon_metabox($post) {
        if($post->post_type === 'pokemon') {
            add_meta_box( 'pokemon-meta-box', __('Other data', 'pokemon'), [$this, 'render_pokemon_metabox'], $post->post_type, 'advanced', 'high' );
        }
    }

    /**
     * @var WP_Post $post
     */
    public function render_pokemon_metabox(WP_Post $post) {
        $upload_link = esc_url( get_upload_iframe_src( 'image', $post->ID ) );

        //Nonce
        wp_nonce_field( 'pokemon_extra_metabox', 'pokemon_extra_metabox_nonce' );

        $pokemon_weight = get_post_meta( $post->ID, 'pokemon_weight', true );
        $pokemon_pokedex_older_number = get_post_meta( $post->ID, 'pokemon_pokedex_older_number', true );
        $pokemon_pokedex_older_game = get_post_meta($post->ID, 'pokemon_pokedex_older_game', true);
        $pokemon_pokedex_number = get_post_meta( $post->ID, 'pokemon_pokedex_number', true );
        $pokemon_pokedex_game = get_post_meta( $post->ID, 'pokemon_pokedex_game', true );

        ?>
        
        <div id="pokemon_weight_wrapper">
            <label for="pokemon_weight"><?php _e('Weight', 'pokemon'); ?></label>
            <input type="text" name="pokemon_weight" id="pokemon_weight" value="<?php echo $pokemon_weight; ?>" />
        </div>
        <div id="pokemon_pokedex_older_number_wrapper">
            <label for="pokemon_pokedex_older_number"><?php _e('Pokedex older number', 'pokemon'); ?></label>
            <input type="text" name="pokemon_pokedex_older_number" id="pokemon_pokedex_older_number" value="<?php echo $pokemon_pokedex_older_number; ?>" />
        </div>
        <div id="pokemon_pokedex_older_game_wrapper">
            <label for="pokemon_pokedex_older_game"><?php _e('Pokedex older game', 'pokemon'); ?></label>
            <input type="text" name="pokemon_pokedex_older_game" id="pokemon_pokedex_older_game" value="<?php echo $pokemon_pokedex_older_game; ?>" />
        </div>
        <div id="pokemon_pokedex_number_wrapper">
            <label for="pokemon_pokedex_number"><?php _e('Pokedex number', 'pokemon'); ?></label>
            <input type="text" name="pokemon_pokedex_number" id="pokemon_pokedex_number" value="<?php echo $pokemon_pokedex_number; ?>" />
        </div>
        <div id="pokemon_pokedex_game_wrapper">
            <label for="pokemon_pokedex_game"><?php _e('Pokedex latest game', 'pokemon'); ?></label>
            <input type="text" name="pokemon_pokedex_game" id="pokemon_pokedex_game" value="<?php echo $pokemon_pokedex_game; ?>" />
        </div>
        <?php
    }

    public function save_post(int $post_id) {
        //Save all Pokémon custom params
        foreach($_POST as $key => $value) {
            if(preg_match('#^pokemon_#', $key)) {
                update_post_meta( $post_id, $key, $value );
            }
        }
        
    }
}

new Pokemon_CPT();
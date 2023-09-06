<?php

class Pokemon_AJAX {
    public function __construct() {
        add_action('wp_ajax_nopriv_get_oldest_data', [$this, 'fn_get_oldest_data']);
        add_action('wp_ajax_get_oldest_data', [$this, 'fn_get_oldest_data']);
    }

    public function fn_get_oldest_data() {
        //Check the ID received
        $the_id = (int)filter_input(INPUT_GET, 'id');
        if(!$the_id) {
            wp_send_json_error( ['response' => 'KO', 'message' => 'No ID received.'] );
            exit;
        }
        //Check the nonce
        if(!check_ajax_referer( 'pokemon_ajax_nonce', 'nonce', false )) {
            wp_send_json_error( ['response' => 'KO', 'message' => 'Security fail.'] );
            exit;
        }

        //All right. Let's go to retrieve the data
        

        $html['older_version_' . $the_id] = Pokemon_Helper::get_the_oldest_pokedex_id($the_id);

        wp_send_json_success($html);
        exit;
    }
}

new Pokemon_AJAX();
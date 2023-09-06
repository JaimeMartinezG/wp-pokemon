<?php

class PokeAPI {
    const URL = 'https://pokeapi.co/api/v2/';
    const CACHE_ROUTE = 'wp-content/uploads/poke-cache';


    public function do_custom_request( string $route, array $params = [], $return = 'array' ) {
        $c = curl_init( $route );
        curl_setopt( $c, CURLOPT_RETURNTRANSFER, true );
        
        $data = curl_exec( $c );

        curl_close( $c );

        if( $return === 'array' ) {
            return json_decode( $data );
        } else {
            return $data;
        }
        
    }

    private function do_request( string $route, int|string|null $id = null, array $params = [] ) {
        $url = $this::URL . $route . '/';
        if( $id && $id !== 'list' ) {
            $url .= $id . '/';
        }

        if( $params ) {
            $url .= '?' . http_build_query( $params );
        }

        $c = curl_init( $url );
        curl_setopt( $c, CURLOPT_RETURNTRANSFER, true );
        $data = curl_exec( $c );
        curl_close( $c );

        return $data;
    }
    public function get_pokemon( $pokemon_id ) {
        $data =  [
            'resource'      => 'pokemon',
            'resource_id'   => $pokemon_id,
            'data'          => null
        ];
        
        if( $this->check_object_cache( $data['resource'], $data['resource_id'] ) ) {
            
            $pokemon = $this->get_object_cache( $data );

        } else {

            $pokemon = $this->do_request( 'pokemon', $pokemon_id );
            $data['data'] = $pokemon;

            $this->set_object_cache( $data );

        }

        return json_decode( $pokemon );
    }

    public function get_pokemon_list( array $params = [] ) {
        $data = [
            'resource'      => 'pokemon',
            'resource_id'   => 'list',
            'data'          => null
        ];

        if( $this->check_object_cache( $data['resource'], $data['resource_id'] ) ) {

            $pokemon_list   = $this->get_object_cache( $data );

        } else {

            $pokemon_list   = $this->do_request( $data['resource'], $data['resource_id'], $params );
            $data['data']   = $pokemon_list;

            $this->set_object_cache( $data );

        }

        return json_decode( $pokemon_list );

    }

    private function get_object_cache( $data ) {
        return file_get_contents( ABSPATH . $this::CACHE_ROUTE . '/' . $data['resource'] . '/' . $data['resource_id'] . '.json');
    }

    private function set_object_cache( $data ) {

        if( !$this->check_basedir_cache() ) {
            mkdir( ABSPATH . $this::CACHE_ROUTE . '/', 0755);
        }

        if( !$this->check_dir_cache( $data['resource'] ) ) {
            mkdir( ABSPATH . $this::CACHE_ROUTE . '/' . $data['resource'] . '/', 0755 );
        }

        if( !$this->check_object_cache( $data['resource'], $data['resource_id'] ) ) {
            $f = fopen( ABSPATH . $this::CACHE_ROUTE . '/' . $data['resource'] . '/' . $data['resource_id'] . '.json', 'w' );
            fwrite( $f, $data['data'] );
            fclose( $f );
        }

    }

    private function check_basedir_cache() {

        $route = ABSPATH . $this::CACHE_ROUTE . '/';

        if( !file_exists( $route ) ) {
            return false;
        }

        return true;

    }

    private function check_dir_cache( $resource ) {

        $route = ABSPATH . $this::CACHE_ROUTE . '/' . $resource . '/';

        if( !file_exists( $route ) ) {

            return false;

        }

        return true;

    }

    private function check_object_cache( $resource, $id ) {

        //Check if exists the cached file with all data
        $route = ABSPATH . $this::CACHE_ROUTE . "/" . $resource . "/" . $id . ".json";

        if(!file_exists( $route )) {

            return false;

        } else {

            //If file exists, check the creation date; if is overdued, I delete de file and return false
            $_fileModified = filemtime( $route );
            $fileModified = new DateTime();
            $fileModified->setTimestamp( $_fileModified );
            
            //Caché for 24h
            $now = new DateTime();

            if( ( ( $now->getTimestamp() - $fileModified->getTimestamp() ) / 60 / 60 ) > 24 ) {
                //Delete the file

                return false;
            }
            
            return true;
        }
    }
}
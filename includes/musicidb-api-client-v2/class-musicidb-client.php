<?php
/**
 * MusicIDB API Client
 *
 * API for MusicIDB WordPress plugins
 */

namespace MusicIDB\Client;

use \MusicIDBIntegration;

/**
 * MusicIDB_Client Class 
 *
 * @category Class
 * @package  MusicIDB\Client
 */
class MusicIDB_Client {
    const API_BASE = 'http://api.musicidb.com/api';
    const USER_AGENT = 'MusicIDB/API-Client/php';
    const DEBUG_FILE = 'php://output';

    const RESPONSE_TYPE_EVENT = "Event";
    const RESPONSE_TYPE_VENUE = "Venue";
    const RESPONSE_TYPE_ARTIST = "Artist";
    const RESPONSE_TYPE_COMPANY = "PromoCompany";
    const RESPONSE_TYPE_META = "Meta";

    public static $PATCH = "PATCH";
    public static $POST = "POST";
    public static $GET = "GET";
    public static $HEAD = "HEAD";
    public static $OPTIONS = "OPTIONS";
    public static $PUT = "PUT";
    public static $DELETE = "DELETE";

    private $api_key;

    public function __construct() {
        $musicidb_integration = MusicIDBIntegration::get_instance();
        $this->api_key = $musicidb_integration->get_api_key();
    }

    public function call( $request, $method, $params = array() ) {

        $params['api_key'] = $this->api_key;

        return $this->make_call( $request, $method, $params );

    }

    /**
     * Make the HTTP call without passing the api_key parameter
     * This is used rarely, but is needed for certain calls (Example: Api_Key_Request)
     */
    public function call_without_api_key( $request, $method, $params = array() ) {

        return $this->make_call( $request, $method, $params );

    }

    private function make_call( $request, $method, $params = array() ) {

        $request_args = array();

        switch( $method ) {
            case self::$GET:
                // build the query string and
                // append to request url
                if( !empty( $params ) ) {
                    $query = '?' . build_query( $params );
                    $request .= $query;
                }

                break;
            case self::$PATCH:
            case self::$POST:
                $request_args = array(
                    'headers' => array(
                        'Content-type' => 'application/json',
                        'Accept' => 'application/json; charset=utf-8', 
                    ),
                    'body' => json_encode( $params )
                );
                break;

            default:
                throw new API_Exception( 'Invalid or unimplemented HTTP method: "' . $method . '"' );
                break;
        }

        $url = self::API_BASE . $request;

        $request_args = array_merge(
            $request_args,
            array(
                'method' => $method
            )
        );

        $response = wp_remote_request( $url, $request_args );

        if( is_wp_error( $response ) ) {
            throw new API_Exception( 'Error encountered: ' . $response->get_error_message() );
        }

        return $response;

    }

    public function get_api_key() {
        return $this->api_key;
    }

}

<?php
/**
 * MusicIDB API
 *
 * API for MusicIDB WordPress plugins
 */

namespace MusicIDB\Client;

class MusicIDB_API {

	private static $instance;

	const VENUE_REQUEST = 'venue';
	const EVENT_REQUEST = 'event';
	const ARTIST_REQUEST = 'artist';
	const API_KEY_REQUEST = 'api_key';

	private static $event_request;
	private static $venue_request;
	private static $artist_request;
	private static $api_key_request;

	private function __construct() {

		self::$event_request = new Event_Request();
		self::$venue_request = new Venue_Request();
		self::$artist_request = new Artist_Request();
		self::$api_key_request = new Api_Key_Request();

	}

	public static function get_instance() {

		if( null === self::$instance ) {
			self::$instance = new MusicIDB_API();
		} 

		return self::$instance;

	}

	/**
	 *  Make an API request
	 * 
	 * @param  string $type     	The type of request
	 * @param  string $action 		The request action
	 * @param  array  $args     	The request parameters
	 * 
	 * @return MusicIDB_Response 	The response object
	 */
	public static function request( $request_type, $action, $args = array() ) {

		$current_request = null;

		switch( strtolower($request_type) ) {
			case self::VENUE_REQUEST:
				$current_request = self::$venue_request;
				break;
			case self::EVENT_REQUEST:
				$current_request = self::$event_request;
				break;
			case self::API_KEY_REQUEST:
				$current_request = self::$api_key_request;
				break;
			case self::ARTIST_REQUEST:
				$current_request = self::$artist_request;
				break;
			default:
				throw new Api_Exception( 'Invalid request type: "' . $request_type . '"' );
				break;
		}

		if( null === $current_request ) {
			throw new Api_Exception( 'Request was null' );
		}

		if( false === method_exists($current_request, $action) ) {
			throw new Api_Exception( 'Invalid request action: "' . $action . '"' );
		}

		// finally call the method
		$response = false;

		if( !empty( $args ) ) {
			$response = $current_request->$action($args);
		} else {
			$response = $current_request->$action();
		}

		return $response;

	}

}
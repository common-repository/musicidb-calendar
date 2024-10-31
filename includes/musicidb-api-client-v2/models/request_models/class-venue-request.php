<?php

namespace MusicIDB\Client;

class Venue_Request extends MusicIDB_Request {

	const BASE_ENDPOINT = '/v2/venue';
	const EVENTS_ENDPOINT = 'events';
	const EVENTS_ARCHIVE_ENDPOINT = self::EVENTS_ENDPOINT . '/archive';

	const LABEL_LIST = 'list';
	const LABEL_SINGLE = 'single';
	const LABEL_EVENTS = 'events';

	/** 
	**	Request Validation Rules 
	**
	**	'method_identifier' => rules (
	**		'arg_name' => 'arg_type'
	** 	)
	**/
	const VALIDATION_RULES = array(
		self::LABEL_LIST => array(
			'limit' => array(
				'type' => MusicIDB_Request::TYPE_INT,
				'required' => false
			),
			'page' => array(
				'type' => MusicIDB_Request::TYPE_INT,
				'required' => false
			)
		),
		self::LABEL_SINGLE => array(
			'id' => array(
				'type' => MusicIDB_Request::TYPE_INT,
				'required' => true
			)
		),
		self::LABEL_EVENTS => array(
			'id' => array(
				'type' => MusicIDB_Request::TYPE_INT,
				'required' => true
			),
			'limit' => array(
				'type' => MusicIDB_Request::TYPE_INT,
				'required' => false
			),
			'page' => array(
				'type' => MusicIDB_Request::TYPE_INT,
				'required' => false
			),
			'order_direction' => array(
				'type' => MusicIDB_Request::TYPE_STRING,
				'required' => false
			)
		)
	);

	/** 
	**	endpoint: GET /v2/venue
	** 	Get all venues
	**
	**	@param array $args 	The api endpoint params
	**
	** 	@see http://api.musicidb.com/swagger#/Venue%20--%20v2/getVenuesv2
	** 	@return array An array of venues
	**/
	public function get_venues( $args ) {
		$validation_rules = self::VALIDATION_RULES[self::LABEL_LIST];
		$endpoint = self::BASE_ENDPOINT;

		$args = $this->sanitize_args( $args, $validation_rules );

		$response = $this->client->call( $endpoint, MusicIDB_Client::$GET, $args );
		$venue_response = new Venue_Response( $response, $args );
		
		return $venue_response;
	}

	/** 
	**	endpoint: GET /v2/venue/{venueId}
	** 	Get the specified venue
	**
	**	@param array $args 	The api endpoint params
	**
	** 	@see http://api.musicidb.com/swagger#/Venue%20--%20v2/getVenueByIdv2
	** 	@return array The specified venue
	**/
	public function get_venue( $args ) {
		$validation_rules = self::VALIDATION_RULES[self::LABEL_SINGLE];
		$endpoint = self::BASE_ENDPOINT;

		$args = $this->sanitize_args( $args, $validation_rules );
		$original_args = $args;

		$request = sprintf( '%s/%d', $endpoint, $args['id'] );
		unset( $args['id'] );

		$response = $this->client->call( $request, MusicIDB_Client::$GET, $args );

		$venue_response = new Venue_Response( $response, $original_args );
		
		return $venue_response;
	}

	/** 
	**	endpoint: GET /v2/venue/{venueId}/events
	** 	Get the specified venue
	**
	**	@param array $args 	The api endpoint params
	**
	** 	@see http://api.musicidb.com/swagger#/Venue%20--%20v2/getVenueByIdv2
	** 	@return array The specified venue
	**/
	public function get_venue_events( $args ) {
		$validation_rules = self::VALIDATION_RULES[self::LABEL_EVENTS];
		$endpoint = self::BASE_ENDPOINT;

		$args = $this->sanitize_args( $args, $validation_rules );
		$original_args = $args;

		$request = sprintf( '%s/%d/%s', $endpoint, $args['id'], self::EVENTS_ENDPOINT );
		unset( $args['id'] );

		$response = $this->client->call( $request, MusicIDB_Client::$GET, $args );
		$venue_response = new Venue_Response( $response, $original_args );
		
		return $venue_response;
	}

	/** 
	**	endpoint: GET /v2/venue/{venueId}/events/archive
	** 	Get the specified venue
	**
	**	@param array $args 	The api endpoint params
	**
	** 	@see http://api.musicidb.com/swagger#/Venue%20--%20v2/getVenueEventsArchivev2
	** 	@return array The specified venue
	**/
	public function get_venue_events_archive( $args ) {
		$validation_rules = self::VALIDATION_RULES[self::LABEL_EVENTS];
		$endpoint = self::BASE_ENDPOINT;

		$args = $this->sanitize_args( $args, $validation_rules );
		$original_args = $args;

		$request = sprintf( '%s/%d/%s', $endpoint, $args['id'], self::EVENTS_ARCHIVE_ENDPOINT );
		unset( $args['id'] );

		$response = $this->client->call( $request, MusicIDB_Client::$GET, $args );
		$venue_response = new Venue_Response( $response, $original_args );
		
		return $venue_response;
	}
	
}
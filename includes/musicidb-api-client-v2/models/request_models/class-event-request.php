<?php

namespace MusicIDB\Client;

class Event_Request extends MusicIDB_Request {

	const BASE_ENDPOINT = '/v2/event';
	const ARCHIVE_ENDPOINT = 'archive';
	const MEDIA_ENDPOINT = 'media';
	const FILTER_ENDPOINT = '/v2/query/event';

	const LABEL_UPCOMING = 'upcoming';
	const LABEL_PAST = 'past';
	const LABEL_SINGLE = 'single';
	const LABEL_MEDIA = 'media';
	const LABEL_FILTER = 'filter';

	/** 
	**	Request Validation Rules 
	**
	**	'method_identifier' => rules (
	**		'arg_name' => 'arg_type'
	** 	)
	**/
	const VALIDATION_RULES = array(
		self::LABEL_UPCOMING => array(
			'limit' => array(
				'type' => MusicIDB_Request::TYPE_INT,
				'required' => false
			),
			'page' => array(
				'type' => MusicIDB_Request::TYPE_INT,
				'required' => false
			)
		),
		self::LABEL_PAST => array(
			'limit' => array(
				'type' => MusicIDB_Request::TYPE_INT,
				'required' => false
			),
			'page' => array(
				'type' => MusicIDB_Request::TYPE_INT,
				'required' => false
			),
		),
		self::LABEL_SINGLE => array(
			'id' => array(
				'type' => MusicIDB_Request::TYPE_INT,
				'required' => true
			)
		),
		self::LABEL_MEDIA => array(
			'id' => array(
				'type' => MusicIDB_Request::TYPE_INT,
				'required' => true
			)
		),
		self::LABEL_FILTER => array(
			'page' => array(
				'type' => MusicIDB_Request::TYPE_INT,
				'required' => false,
			),
			'limit' => array(
				'type' => MusicIDB_Request::TYPE_INT,
				'required' => false,
			),
			'is_published' => array(
				'type' => MusicIDB_Request::TYPE_BOOL,
				'required' => false,
			),
			'is_delete' => array(
				'type' => MusicIDB_Request::TYPE_BOOL,
				'required' => false,
			),
			'is_featured' => array(
				'type' => MusicIDB_Request::TYPE_BOOL,
				'required' => false,
			),
			'start_date_is' => array(
				'type' => MusicIDB_Request::TYPE_STRING,
				'required' => false,
			),
			'start_date_before'=> array(
				'type' => MusicIDB_Request::TYPE_STRING,
				'required' => false,
			),
			'start_date_after'=> array(
				'type' => MusicIDB_Request::TYPE_STRING,
				'required' => false,
			),
			'venues'=> array(
				'type' => MusicIDB_Request::TYPE_STRING,
				'required' => false,
				'multiple' => true,
			),
			'artists'=> array(
				'type' => MusicIDB_Request::TYPE_STRING,
				'required' => false,
				'multiple' => true,
			),
			'events'=> array(
				'type' => MusicIDB_Request::TYPE_STRING,
				'required' => false,
				'multiple' => true,
			),
			'order_by'=> array(
				'type' => MusicIDB_Request::TYPE_STRING,
				'required' => false,
			),
			'order_direction'=> array(
				'type' => MusicIDB_Request::TYPE_STRING,
				'required' => false,
			),
			'genres'=> array(
				'type' => MusicIDB_Request::TYPE_STRING,
				'required' => false,
				'multiple' => true,
			),
			'event_type'=> array(
				'type' => MusicIDB_Request::TYPE_STRING,
				'required' => false,
				'multiple' => true,
			),
			'event_type_exclude'=> array(
				'type' => MusicIDB_Request::TYPE_STRING,
				'required' => false,
				'multiple' => true,
			),
  			'event_type_null'=> array(
				'type' => MusicIDB_Request::TYPE_BOOL,
				'required' => false,  				
  			)
		)
	);

	/** 
	**	endpoint: GET /v2/event
	** 	Get all events from today onward that are published and not festivals
	**
	**	@param array $args 	The api endpoint params
	**
	** 	@see http://api.musicidb.com/swagger#/Event%20--%20v2/getEventsv2
	** 	@return array An array of upcoming events
	**/
	public function get_upcoming( $args ) {
		$validation_rules = self::VALIDATION_RULES[self::LABEL_UPCOMING];
		$endpoint = self::BASE_ENDPOINT;

		$args = $this->sanitize_args( $args, $validation_rules );

		$response = $this->client->call( $endpoint, MusicIDB_Client::$GET, $args );
		$event_response = new Event_Response( $response, $args );
		
		return $event_response;
	}

	/** 
	**	endpoint: GET /v2/event/archive
	**  Get all past events
	**
	**	@param array $args 	The api endpoint params
	**
	** 	@see http://api.musicidb.com/swagger#/Event%20--%20v2/getArchivedEventsv2
	** 	@return array An array of past events
	**/
	public function get_past( $args ) {
		$validation_rules = self::VALIDATION_RULES[self::LABEL_PAST];
		$endpoint = self::BASE_ENDPOINT . self::ARCHIVE_ENDPOINT;

		$args = $this->sanitize_args( $args, $validation_rules );

		$response = $this->client->call( $endpoint, MusicIDB_Client::$GET, $args );
		$event_response = new Event_Response( $response, $args );

		return $event_response;
	}

	/** 
	**	endpoint: GET /v2/event/{eventId}
	**  Get specified event by eventId
	**
	**	@param array $args 	The api endpoint params
	**
	** 	@see http://api.musicidb.com/swagger#/Event%20--%20v2/getEventByIdv2
	** 	@return Event 	The specified event
	**/
	public function get_event( $args ) {
		$validation_rules = self::VALIDATION_RULES[self::LABEL_SINGLE];

		$args = $this->sanitize_args( $args, $validation_rules );
		$original_args = $args;
		
		$request = self::BASE_ENDPOINT . '/' . $args['id'];
		unset( $args['id'] );

		$response = $this->client->call( $request, MusicIDB_Client::$GET, $args );
		$event_response = new Event_Response( $response, $original_args );

		return $event_response;
	}

	/**
	 * endpoint: GET /v2/event/{eventId}/media
	 * retrieve media for artists performing at a specific event
	 *
	 * This will return an array of artist objects including media information
	 * unlike /v2/artist, which omits media and event count. If possible, media
	 * should be returned in /v2/artist also
	 * 
	 * 	@see http://api.musicidb.com/swagger#/Event -- v2/getEventMediav2
	 * 	@return array An array of artists, including their media & event count
	 */
	public function get_event_artist_media( $args ) {
		$validation_rules = self::VALIDATION_RULES[self::LABEL_MEDIA];

		$args = $this->sanitize_args( $args, $validation_rules );
		$original_args = $args;

		$request = sprintf( '%s/%d/%s', self::BASE_ENDPOINT, $args['id'], self::MEDIA_ENDPOINT );
		unset( $args['id'] );

		$response = $this->client->call( $request, MusicIDB_Client::$GET, $args );
		$event_media_response = new Event_Media_Response( $response, $original_args );

		return $event_media_response;
	}

	/** 
	**	endpoint: POST /v2/query/event
	**	Submit a custom query to filter your events
	**
	** 	@param array $args 	Associative array of query args
	**
	**	@see http://api.musicidb.com/swagger#/Event%20--%20v2/queryEventsv2
	**	@return array An array of filtered events
	**/
	public function filter_query( $args ) {
		$validation_rules = self::VALIDATION_RULES[self::LABEL_FILTER];
		$args = $this->sanitize_args( $args, $validation_rules );

		$response = $this->client->call( self::FILTER_ENDPOINT, MusicIDB_Client::$POST, $args );
		$event_response = new Event_Response( $response, $args );

		return $event_response;
	}

	// TODO: Implement additional API methods
	// POST /v2/event	
	// PATCH /v2/event/{eventId}
}
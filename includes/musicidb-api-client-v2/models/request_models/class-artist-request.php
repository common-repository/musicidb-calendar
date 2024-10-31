<?php

namespace MusicIDB\Client;

class Artist_Request extends MusicIDB_Request {

	const BASE_ENDPOINT = '/v2/artist';
	const EVENTS_ENDPOINT = 'events';

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
	**	endpoint: GET /v2/artist
	** 	Get all artists
	**
	**	@param array $args 	The api endpoint params
	**
	** 	@see http://api.musicidb.com/swagger#/
	** 	@return array An array of artists
	**/
	public function get_artists( $args ) {
		$validation_rules = self::VALIDATION_RULES[self::LABEL_LIST];
		$endpoint = self::BASE_ENDPOINT;

		$args = $this->sanitize_args( $args, $validation_rules );

		$response = $this->client->call( $endpoint, MusicIDB_Client::$GET, $args );
		$artist_response = new Artist_Response( $response, $args );
		
		return $artist_response;
	}

	/** 
	**	endpoint: GET /v2/artist/{artistId}
	** 	Get the specified artist
	**
	**	@param array $args 	The api endpoint params
	**
	** 	@see http://api.musicidb.com/swagger#/
	** 	@return array The specified artist
	**/
	public function get_artist( $args ) {
		$validation_rules = self::VALIDATION_RULES[self::LABEL_SINGLE];
		$endpoint = self::BASE_ENDPOINT;

		$args = $this->sanitize_args( $args, $validation_rules );
		$original_args = $args;

		$request = sprintf( '%s/%d', $endpoint, $args['id'] );
		unset( $args['id'] );

		$response = $this->client->call( $request, MusicIDB_Client::$GET, $args );

		$artist_response = new Artist_Response( $response, $original_args );
		
		return $artist_response;
	}

	/** 
	**	endpoint: GET /v2/artist/{artistId}/events
	** 	Get the specified artist
	**
	**	@param array $args 	The api endpoint params
	**
	** 	@see http://api.musicidb.com/swagger#/
	** 	@return array The specified artist
	**/
	public function get_artist_events( $args ) {
		$validation_rules = self::VALIDATION_RULES[self::LABEL_EVENTS];
		$endpoint = self::BASE_ENDPOINT;

		$args = $this->sanitize_args( $args, $validation_rules );
		$original_args = $args;

		$request = sprintf( '%s/%d/%s', $endpoint, $args['id'], self::EVENTS_ENDPOINT );
		unset( $args['id'] );

		$response = $this->client->call( $request, MusicIDB_Client::$GET, $args );
		$artist_response = new Artist_Response( $response, $original_args );
		
		return $artist_response;
	}

	// TODO: POST /v2/artist

}
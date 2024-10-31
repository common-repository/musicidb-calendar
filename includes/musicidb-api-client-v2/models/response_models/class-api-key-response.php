<?php 

namespace MusicIDB\Client;

class Api_Key_Response extends MusicIDB_Response {

	private $api_key = '';
	private $associated_entities = array();
	private $api_key_permissions = array();

	private $entity_types = array( 
		'venue' => 'MusicIDB\Client\Api_Key_Venue_Details',
		'artist' => 'MusicIDB\Client\Api_Key_Artist_Details',
	);

	public function __construct( $response, $request_args ) {
		parent::__construct( $response, $request_args );

		if( MusicIDB_Response::STATUS_200 === $this->status ) {

			$this->api_key = $request_args['api_key'];
			$this->parse_response_to_props();

		}
	}

	private function parse_response_to_props() {

		if( !empty( $this->response_body ) ) {

			$response_arr = $this->response_body;

			foreach ( $this->entity_types as $type => $class ) {

				if( empty( $class ) ) {
					continue;
				}
				
				if( !empty( $response_arr[$type] ) ) {

					foreach ( $response_arr[$type] as $id => $permissions ) {
						
						$this->associated_entities[$type][] = $id;

						$permissions_model = $this->create_new_response_model( $type, $class, $id, $permissions );
						
						if( false !== $permissions_model )
							$this->api_key_permissions[$type][] = $permissions_model;

					}

				}

			}

		}

	}

	private function create_new_response_model( $type, $class, $id, $permissions ) {

		if( empty( $type ) ) {
			return false;
		}

		if( empty( $class ) ) {
			return false;
		}

		// don't allow this method to be abused
		if( !array_key_exists( $type, $this->entity_types ) ) {
			return false;
		}

		return new $class( array( 
			$type => $id,
			'permissions' => $permissions 
		) );

	}

	private function equals_ignore_case( $subject, $compare ) {
		return strtolower( trim( $subject ) ) == strtolower( trim( $compare ) );
	}

	public function get_api_key() {
		return $this->api_key;
	}

	public function get_associated_entities() {
		return $this->associated_entities;
	}

	public function get_venue_ids() {
		return !empty( $this->associated_entities['venue'] ) 
					? $this->associated_entities['venue'] 
					: array();
	}

	public function get_artist_ids() {
		return !empty( $this->associated_entities['artist'] ) 
					? $this->associated_entities['artist'] 
					: array();
	}

	public function get_api_key_permissions() {
		return $this->api_key_permissions;
	}

	public function get_api_key_venue_permissions() {
		return $this->api_key_permissions['venue'];
	}

	public function get_api_key_artist_permissions() {
		return $this->api_key_permissions['artist'];
	}

}
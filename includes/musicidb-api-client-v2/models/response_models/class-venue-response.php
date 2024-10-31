<?php 

namespace MusicIDB\Client;

class Venue_Response extends MusicIDB_Response {
	
	const RESPONSE_TYPE = MusicIDB_Client::RESPONSE_TYPE_VENUE;

	private $venues = array();
	private $events = array();

	public function __construct( $response, $request_args ) {
		parent::__construct( $response, $request_args );

		$this->response_type = self::RESPONSE_TYPE;

		if( MusicIDB_Response::STATUS_200 === $this->status ) {
			if( !empty( $this->response_meta ) ) {
				$this->total = $this->response_meta['total'];
			}

			$this->set_venues();
			$this->set_events();
		}
	}

	private function set_venues() {

		if( !empty( $this->response_body ) ) {

			$response_arr = $this->response_body;

			foreach ( $response_arr as $data ) {
				// bail if unexpected responseType
				if( self::RESPONSE_TYPE !== $data['responseType'] )
					continue;

				$venue = new Venue( $data );

				$this->venues[] = $venue;

			}

		}

	}

	private function set_events() {

		if( !empty( $this->response_body ) ) {

			$response_arr = $this->response_body;

			foreach ( $response_arr as $data ) {
				
				// bail if unexpected responseType
				if( Event_Response::RESPONSE_TYPE !== $data['responseType'] )
					continue;

				$event = new Event( $data );

				$this->events[] = $event;

			}

		}

	}

	public function get_venues() { return $this->venues; }
	public function get_events() { return $this->events; }

}
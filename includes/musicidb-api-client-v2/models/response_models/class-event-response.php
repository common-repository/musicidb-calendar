<?php 

namespace MusicIDB\Client;

class Event_Response extends MusicIDB_Response {
	
	const RESPONSE_TYPE = MusicIDB_Client::RESPONSE_TYPE_EVENT;

	private $events = array();

	public function __construct( $response, $request_args ) {
		parent::__construct( $response, $request_args );

		$this->response_type = self::RESPONSE_TYPE;

		if( MusicIDB_Response::STATUS_200 === $this->status ) {
			if( !empty( $this->response_meta ) ) {
				$this->total = $this->response_meta['total'];
			}

			$this->set_events();
		}
		
	}

	private function set_events() {

		if( !empty( $this->response_body ) ) {

			$response_arr = $this->response_body;

			foreach ( $response_arr as $data ) {
				
				// bail if unexpected responseType
				if( self::RESPONSE_TYPE !== $data['responseType'] )
					continue;

				$event = new Event( $data );

				$this->events[] = $event;

			}

		}

	}

	public function get_events() {
		return $this->events;
	}

}
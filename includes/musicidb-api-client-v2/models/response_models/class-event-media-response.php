<?php 

namespace MusicIDB\Client;

class Event_Media_Response extends MusicIDB_Response {

	private $event_id;
	private $artist_media = array();

	public function __construct( $response, $request_args ) {
		parent::__construct( $response, $request_args );

		if( MusicIDB_Response::STATUS_200 === $this->status ) {
			if( !empty( $this->response_meta ) ) {
				$this->total = $this->response_meta['total'];
			}

			$this->event_id = $request_args['id'];
			$this->set_artist_media();
		}
		
	}

	public function set_artist_media() {
		
		if( !empty( $this->response_body ) ) {

			$response_arr = $this->response_body;

			foreach ( $response_arr as $data ) {

				// no response type for media
				// TODO: Fix this in the API and 
				// then update here
				if( !empty( $data['responseType']) )
					continue;

				$artist_media = new Artist( $data );
				$this->artist_media[] = $artist_media;

			}

		}

	}

	public function get_artist_media() {
		return $this->artist_media;
	}

	protected function set_event_id( $event_id ) { $this->event_id = $event_id; }
	public function get_event_id() { return $this->event_id; }

}
<?php 

namespace MusicIDB\Client;

class Artist_Response extends MusicIDB_Response {
	
	const RESPONSE_TYPE = MusicIDB_Client::RESPONSE_TYPE_ARTIST;

	private $artists = array();

	public function __construct( $response, $request_args ) {
		parent::__construct( $response, $request_args );

		$this->response_type = self::RESPONSE_TYPE;

		if( MusicIDB_Response::STATUS_200 === $this->status ) {
			if( !empty( $this->response_meta ) ) {
				$this->total = $this->response_meta['total'];
			}

			$this->set_artists();
		}
	}

	private function set_artists() {

		if( !empty( $this->response_body ) ) {

			$response_arr = $this->response_body;

			foreach ( $response_arr as $data ) {
				// bail if unexpected responseType
				if( self::RESPONSE_TYPE !== $data['responseType'] )
					continue;

				$artist = new Artist( $data );

				$this->artists[] = $artist;

			}

		}

	}

	public function get_artists() { return $this->artists; }

}
<?php

namespace MusicIDB\Client;

class Api_Key_Request extends MusicIDB_Request {

	const BASE_ENDPOINT = '/v2/ApiKey';

	const LABEL_DETAILS = 'details';

	public function get_details() {

		if( empty( $this->client->get_api_key() ) ) {
			throw new Api_Exception('No API key found for API client');
		}

		$api_key = $this->client->get_api_key();

		$request = self::BASE_ENDPOINT . '/' . $api_key;
		$response = $this->client->call_without_api_key( $request, MusicIDB_Client::$GET );

		$api_key_response = new Api_Key_Response( $response, array( 
			'api_key' => $api_key 
		) );

		return $api_key_response;

	}

}
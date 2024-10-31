<?php

namespace MusicIDB\Client;

abstract class MusicIDB_Response {

	const STATUS_200 = 200;
	const STATUS_401 = 401;
	const STATUS_404 = 404;
	const STATUS_500 = 500;

	protected $status;
	protected $error;
	protected $message;

	// passed from request
	protected $request_args;

	protected $response_type;
	protected $response_headers;
	protected $response_body;
	protected $response_meta;
	protected $total;

	public function __construct( $response, $request_args ) {

		$this->response_headers = !empty( $response['headers'] ) ? $response['headers'] : array();
		$this->response_body = !empty( $response['body'] ) ? json_decode( $response['body'], true ) : array();

		// Handle response status codes
		if( !empty($this->response_body['statusCode']) ) {
			switch ($this->response_body['statusCode']) {
				case self::STATUS_500:
				case self::STATUS_404:
				case self::STATUS_401:
					$this->status = $this->response_body['statusCode'];
					break;
				
				default:
					$this->status = self::STATUS_500;
					break;
			}

			$this->error = $this->response_body['error'];
			$this->message = $this->response_body['message'];
		} elseif( !empty( $this->response_body['errors'] ) ) {
			// Handle Swagger errors
			$this->status = self::STATUS_500;
			$this->error = $this->response_body['errors'][0]['code'];
			$this->message = $this->response_body['errors'][0]['errors'][0]['message'];
		} else {

			// Media does not return response meta
			if( 
				!empty( $this->response_body[0]['responseType'] ) 
				&& MusicIDB_Client::RESPONSE_TYPE_META == $this->response_body[0]['responseType'] 
			) {

				$this->response_meta = $this->response_body[0];
				unset( $this->response_body[0] );

			}

			$this->status = self::STATUS_200;

		}

		$this->request_args = $request_args;

		if( self::STATUS_200 !== $this->status && self::STATUS_404 !== $this->status ) {
			throw new Api_Exception( $this->error . ': ' . $this->message . ' Code: ' . $this->status, $this->status );
		}

	}


	/** Getters & Setters **/
		public function get_status() { return $this->status; }
		public function get_error() { return $this->error; }
		public function get_message() { return $this->message; }
		public function get_request_arg() { return $this->request_args; }
		public function get_response_meta() { return $this->response_meta; }
		public function get_response_type() { return $this->response_type; }
		public function get_response_headers() { return $this->response_headers; }
		public function get_response_body() { return $this->response_body; }
		public function get_total() { return $this->total; }

}
<?php

namespace MusicIDB\Client;

class Address extends MusicIDB_Model {

	const VALIDATION_RULES = array(
		'address' => array(
			'type' => MusicIDB_Model::TYPE_STRING
		),
		'city' => array(
			'type' => MusicIDB_Model::TYPE_STRING
		),
		'state' => array(
			'type' => MusicIDB_Model::TYPE_STRING
		),
		'country' => array(
			'type' => MusicIDB_Model::TYPE_STRING
		),
		'postal-code' => array(
			'type' => MusicIDB_Model::TYPE_STRING,
			'method_name' => 'postal_code',
		),
	);

	private $address;
	private $city;
	private $state;
	private $country;
	private $postal_code;

	public function __construct( $data ) {

		$this->store_data( $data, self::VALIDATION_RULES );

	}

	/** Getters & Setters */
		protected function set_address( $address ) { $this->address = $address; }
		public function get_address() { return $this->address; }

		protected function set_city( $city ) { $this->city = $city; }
		public function get_city() { return $this->city; }

		protected function set_state( $state ) { $this->state = $state; }
		public function get_state() { return $this->state; }

		protected function set_country( $country ) { $this->country = $country; }
		public function get_country() { return $this->country; }

		protected function set_postal_code( $postal_code ) { $this->postal_code = $postal_code; }
		public function get_postal_code() { return $this->postal_code; }

    	
}
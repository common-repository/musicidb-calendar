<?php

namespace MusicIDB\Client;

class Venue extends MusicIDB_Model {

	const VALIDATION_RULES = array(
		'id' => array(
			'type' => MusicIDB_Model::TYPE_INT
		),
		'name' => array(
			'type' => MusicIDB_Model::TYPE_STRING
		),
		'logo' => array(
			'type' => MusicIDB_Model::TYPE_URL
		),
		'menu' => array(
			'type' => MusicIDB_Model::TYPE_STRING
		),
		'phone' => array(
			'type' => MusicIDB_Model::TYPE_STRING
		),
		'email' => array(
			'type' => MusicIDB_Model::TYPE_STRING
		),
		'about' => array(
			'type' => MusicIDB_Model::TYPE_STRING
		),
		'address' => array(
			'type' => MusicIDB_Model::TYPE_ARRAY
		),
	);

	private $name;
	private $logo;
	private $menu;
	private $phone;
	private $email;
	private $about;
	private $address;

	public function __construct( $data ) {

		$this->store_data( $data, self::VALIDATION_RULES );

	}

	/** Getters & Setters */
		protected function set_name( $name ) { $this->name = $name; }
		public function get_name() { return $this->name; }
		
		protected function set_logo( $logo ) { $this->logo = $logo; }
		public function get_logo() { return $this->logo; }
		
		protected function set_menu( $menu ) { $this->menu = $menu; }
		public function get_menu() { return $this->menu; }
		
		protected function set_phone( $phone ) { $this->phone = $phone; }
		public function get_phone() { return $this->phone; }
		
		protected function set_email( $email ) { $this->email = $email; }
		public function get_email() { return $this->email; }
		
		protected function set_about( $about ) { $this->about = $about; }
		public function get_about() { return $this->about; }
		
		protected function set_address( $address ) { $this->address = new Address( $address ); }
		public function get_address() { return $this->address; }
		
    	
}
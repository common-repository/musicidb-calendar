<?php

namespace MusicIDB\Client;

class Artist_Link extends MusicIDB_Model {

	const VALIDATION_RULES = array(
		'name' => array(
			'type' => MusicIDB_Model::TYPE_STRING
		),
		'link' => array(
			'type' => MusicIDB_Model::TYPE_URL
		),
	);

	private $name;
	private $link;

	public function __construct( $data ) {

		$this->store_data( $data, self::VALIDATION_RULES );

	}

	/** Getters & Setters */
		protected function set_name( $name ) { $this->name = $name; }
		public function get_name() { return $this->name; }

		protected function set_link( $link ) { $this->link = $link; }
		public function get_link() { return $this->link; }
    	
}
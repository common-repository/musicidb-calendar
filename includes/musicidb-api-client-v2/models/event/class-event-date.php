<?php

namespace MusicIDB\Client;

class Event_Date extends MusicIDB_Model {

	const VALIDATION_RULES = array(
		'date' => array(
			'type' => MusicIDB_Model::TYPE_DATE,
			'format' => 'Y-m-d'
		),
		'door' => array(
			'type' => MusicIDB_Model::TYPE_STRING
		),
	);

	private $date;
	private $door;

	public function __construct( $data ) {

		$this->store_data( $data, self::VALIDATION_RULES );

	}

	/** Getters & Setters */
		protected function set_date( $date ) { $this->date = $date; }
		public function get_date() { return $this->date; }

		protected function set_door( $door ) {
			// If you're reading this, here be madness...
			// TODO: Seriously though, this is a data issue which
			// needs to be addressed in our application's db and 
			// then this can be removed
			$this->door = str_ireplace( ':null null', '', $door ); 
		}
		public function get_door() { return $this->door; }
    	
}
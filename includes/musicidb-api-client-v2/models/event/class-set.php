<?php

namespace MusicIDB\Client;

class Set extends MusicIDB_Model {

	const VALIDATION_RULES = array(
		'artist' => array(
			'type' => MusicIDB_Model::TYPE_ARRAY
		),
		'start_time' => array(
			'type' => MusicIDB_Model::TYPE_STRING
		),
		'end_time' => array(
			'type' => MusicIDB_Model::TYPE_STRING
		),
		'change_over' => array(
			'type' => MusicIDB_Model::TYPE_INT
		),
	);

	private $artist;
	private $start_time;
	private $end_time;
	private $change_over;

	public function __construct( $data ) {

		$this->store_data( $data, self::VALIDATION_RULES );

	}

	/** Getters & Setters */
		protected function set_artist( $artist ) { $this->artist = new Artist( $artist ); }
		public function get_artist() { return $this->artist; }

		protected function set_start_time( $start_time ) { $this->start_time = $start_time; }
		public function get_start_time() { return $this->start_time; }

		protected function set_end_time( $end_time ) { $this->end_time = $end_time; }
		public function get_end_time() { return $this->end_time; }

		protected function set_change_over( $change_over ) { $this->change_over = $change_over; }
		public function get_change_over() { return $this->change_over; }
    	
}
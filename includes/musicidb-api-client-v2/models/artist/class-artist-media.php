<?php

namespace MusicIDB\Client;

class Artist_Media extends MusicIDB_Model {

	const VALIDATION_RULES = array(
		'photos' => array(
			'type' => MusicIDB_Model::TYPE_ARRAY
		),
		'videos' => array(
			'type' => MusicIDB_Model::TYPE_ARRAY
		),
		'audio' => array(
			'type' => MusicIDB_Model::TYPE_ARRAY
		),
	);

	private $photos;
	private $videos;
	private $audio;

	public function __construct( $data ) {

		$this->store_data( $data, self::VALIDATION_RULES );

	}

	/** Getters & Setters */
		protected function set_photos( $photos ) { $this->photos = $photos; }
		public function get_photos() { return $this->photos; }
    	
		protected function set_videos( $videos ) { $this->videos = $videos; }
		public function get_videos() { return $this->videos; }

		protected function set_audio( $audio ) { $this->audio = $audio; }
		public function get_audio() { return $this->audio; }
		
}
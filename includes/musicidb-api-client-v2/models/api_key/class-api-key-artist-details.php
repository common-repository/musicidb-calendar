<?php

namespace MusicIDB\Client;

class Api_Key_Artist_Details extends MusicIDB_Model {

	const VALIDATION_RULES = array(
		'artist' => array(
			'type' => MusicIDB_Model::TYPE_INT
		),
		'permissions' => array(
			'type' => MusicIDB_Model::TYPE_ARRAY
		)
	);

	private $artist;

	public function __construct( $data ) {

		$this->store_data( $data, self::VALIDATION_RULES );

	}

	/** Getters & Setters */
		protected function set_artist( $artist ) {
			$this->artist = $artist;
		}

		public function get_artist() {
			return $this->artist;
		}

		protected function set_permissions( $permissions ) {
			$this->permissions = new Api_Key_Permissions( $permissions );
		}

		public function get_permissions() {
			return $this->permissions;
		}
		
}
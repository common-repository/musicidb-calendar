<?php

namespace MusicIDB\Client;

class Api_Key_Venue_Details extends MusicIDB_Model {

	const VALIDATION_RULES = array(
		'venue' => array(
			'type' => MusicIDB_Model::TYPE_INT
		),
		'permissions' => array(
			'type' => MusicIDB_Model::TYPE_ARRAY
		)
	);

	private $venue;
	private $permissions;

	public function __construct( $data ) {

		$this->store_data( $data, self::VALIDATION_RULES );

	}

	/** Getters & Setters */
		protected function set_venue( $venue ) {
			$this->venue = $venue;
		}

		public function get_venue() {
			return $this->venue;
		}

		protected function set_permissions( $permissions ) {
			$this->permissions = new Api_Key_Permissions( $permissions );
		}

		public function get_permissions() {
			return $this->permissions;
		}
		
}
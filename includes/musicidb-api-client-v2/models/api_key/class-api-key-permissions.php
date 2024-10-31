<?php

namespace MusicIDB\Client;

class Api_Key_Permissions extends MusicIDB_Model {

	const VALIDATION_RULES = array(
		'canReadUnpublished' => array(
			'type' => MusicIDB_Model::TYPE_BOOL,
			'method_name' => 'can_read_unpublished',
		),
		'canReadDeleted' => array(
			'type' => MusicIDB_Model::TYPE_BOOL,
			'method_name' => 'can_read_deleted',
		)
	);

	private $can_read_unpublished;
	private $can_read_deleted;

	public function __construct( $data ) {

		$this->store_data( $data, self::VALIDATION_RULES );

	}

	/** Getters & Setters */
		private function set_can_read_unpublished( $can_read_unpublished ) {
			$this->can_read_unpublished = $can_read_unpublished;
		}

		public function get_can_read_unpublished() {
			return $this->can_read_unpublished;
		}

		private function set_can_read_deleted( $can_read_deleted ) {
			$this->can_read_deleted = $can_read_deleted;
		}

		public function get_can_read_deleted() {
			return $this->can_read_deleted;
		}
		
}
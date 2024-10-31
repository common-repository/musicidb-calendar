<?php

namespace MusicIDB\Client;

class Artist extends MusicIDB_Model {

	const VALIDATION_RULES = array(
		'id' => array(
			'type' => MusicIDB_Model::TYPE_INT
		),
		'name' => array(
			'type' => MusicIDB_Model::TYPE_STRING
		),
		'image_url' => array(
			'type' => MusicIDB_Model::TYPE_URL
		),
		'bio' => array(
			'type' => MusicIDB_Model::TYPE_STRING
		),
		'links' => array(
			'type' => MusicIDB_Model::TYPE_ARRAY
		),
		'genres' => array(
			'type' => MusicIDB_Model::TYPE_ARRAY
		),
		'location' => array(
			'type' => MusicIDB_Model::TYPE_ARRAY
		),
		'event_count' => array(
			'type' => MusicIDB_Model::TYPE_INT
		),
		'media' => array(
			'type' => MusicIDB_Model::TYPE_ARRAY
		),
	);

	private $name;
    private $image_url;
    private $bio;
    private $links;
    private $genres;
    private $location;
    private $event_count;
    private $media;
    private $is_media_response;

	public function __construct( $data ) {

		$this->store_data( $data, self::VALIDATION_RULES );

	}

	/** Getters & Setters */
		
		protected function set_name( $name ) { $this->name = $name; }
		public function get_name() { return $this->name; }

		protected function set_image_url( $image_url ) { $this->image_url = $image_url; }
		public function get_image_url() { return $this->image_url; }

		protected function set_bio( $bio ) { $this->bio = $bio; }
		public function get_bio() { return $this->bio; }

		protected function set_links( $links ) { 
			// Convert raw response data 
			// into Artist_Link
			if( !empty( $links ) ) {
				$link_objects = array();

				foreach( $links as $link ) {

					$link_data = array(
						'name' => $link[0],
						'link' => $link[1]
					);

					$link_obj = new Artist_link( $link_data );

					if( !empty($link_obj) ) {
						$link_objects[] = $link_obj;
					}
				}

				$this->links = $link_objects;
			}
		}
		public function get_links() { return $this->links; }

		protected function set_genres( $genres ) { $this->genres = $genres; }
		public function get_genres() { return $this->genres; }

		protected function set_location( $location ) { $this->location = new Address( $location ); }
		public function get_location() { return $this->location; }

		protected function set_is_media_response( $is_media_response ) { $this->is_media_response = $is_media_response; }
		public function is_media_response() { return $this->is_media_response; }

		protected function set_event_count( $event_count ) { 
			// side-effect of api response 
			// inconsistency
			$this->is_media_response = true;
			$this->event_count = $event_count; 
		}
		public function get_event_count() { return $this->event_count; }

		protected function set_media( $media ) { 
			// side-effect of api response 
			// inconsistency
			$this->is_media_response = true;

			$this->media = new Artist_Media( $media );
		}
		public function get_media() { return $this->media; }
}
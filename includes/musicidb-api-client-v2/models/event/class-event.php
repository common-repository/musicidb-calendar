<?php

namespace MusicIDB\Client;

class Event extends MusicIDB_Model {

	const VALIDATION_RULES = array(
		'id' => array(
			'type' => MusicIDB_Model::TYPE_INT
		),
		'subtitle' => array(
			'type' => MusicIDB_Model::TYPE_STRING
		),
		'thumbnail' => array(
			'type' => MusicIDB_Model::TYPE_URL
		),
		'poster' => array(
			'type' => MusicIDB_Model::TYPE_URL
		),
		'ticket_link' => array(
			'type' => MusicIDB_Model::TYPE_URL
		),
		'ticket_price' => array(
			'type' => MusicIDB_Model::TYPE_STRING
		),
		'door_cover' => array(
			'type' => MusicIDB_Model::TYPE_STRING
		),
		'more_info' => array(
			'type' => MusicIDB_Model::TYPE_STRING
		),
		'age_restriction' => array(
			'type' => MusicIDB_Model::TYPE_STRING
		),
		'facebook_link' => array(
			'type' => MusicIDB_Model::TYPE_URL
		),
		'date' => array(
			'type' => MusicIDB_Model::TYPE_ARRAY
		),
		'is_featured' => array(
			'type' => MusicIDB_Model::TYPE_BOOL
		),
		'sets' => array(
			'type' => MusicIDB_Model::TYPE_ARRAY
		),
		'genres' => array(
			'type' => MusicIDB_Model::TYPE_ARRAY
		),
		'event_genres' => array(
			'type' => MusicIDB_Model::TYPE_ARRAY
		),
		'media_count' => array(
			'type' => MusicIDB_Model::TYPE_INT
		),
		'name' => array(
			'type' => MusicIDB_Model::TYPE_STRING
		),
		'compiled_title' => array(
			'type' => MusicIDB_Model::TYPE_STRING
		),
		'venue' => array(
			'type' => MusicIDB_Model::TYPE_ARRAY
		),
	);

	private $subtitle;
	private $thumbnail;
	private $poster;
	private $ticket_link;
	private $ticket_price;
	private $door_cover;
	private $more_info;
	private $age_restriction;
	private $facebook_link;
	private $date;
	private $is_featured;
	private $sets;
	private $genres;
	private $event_genres;
	private $media_count;
	private $name;
	private $compiled_title;
	private $venue;

	public function __construct( $data ) {

		$this->store_data( $data, self::VALIDATION_RULES );

	}

	/** Getters & Setters */
		protected function set_subtitle( $subtitle ) {	$this->subtitle = $subtitle; }
		public function get_subtitle() {	return $this->subtitle; }

		protected function set_thumbnail( $thumbnail ) {	$this->thumbnail = $thumbnail; }
		public function get_thumbnail() {	return $this->thumbnail; }

		protected function set_poster( $poster ) {	$this->poster = $poster; }
		public function get_poster() {	return $this->poster; }

		protected function set_ticket_link( $ticket_link ) {	$this->ticket_link = $ticket_link; }
		public function get_ticket_link() {	return $this->ticket_link; }

		protected function set_ticket_price( $ticket_price ) {	$this->ticket_price = $ticket_price; }
		public function get_ticket_price() {	return $this->ticket_price; }

		protected function set_door_cover( $door_cover ) {	$this->door_cover = $door_cover; }
		public function get_door_cover() {	return $this->door_cover; }

		protected function set_more_info( $more_info ) {	$this->more_info = $more_info; }
		public function get_more_info() {	return $this->more_info; }

		protected function set_age_restriction( $age_restriction ) {	$this->age_restriction = $age_restriction; }
		public function get_age_restriction() {	return $this->age_restriction; }

		protected function set_facebook_link( $facebook_link ) {	$this->facebook_link = $facebook_link; }
		public function get_facebook_link() {	return $this->facebook_link; }

		protected function set_date( $date ) {	$this->date = new Event_Date( $date ); }
		public function get_date() {	return $this->date; }

		protected function set_is_featured( $is_featured ) {	$this->is_featured = $is_featured; }
		public function get_is_featured() {	return $this->is_featured; }

		protected function set_sets( $sets ) {	

			if( !empty( $sets ) && is_array( $sets ) ) {
				$set_objects = array();

				foreach ( $sets as $set ) {
					$set_objects[] = new Set( $set );
				}

				$this->sets = $set_objects; 
			}
			
		}
		public function get_sets() {	return $this->sets;	}

		protected function set_genres( $genres ) {	$this->genres = $genres; }
		public function get_genres() {	return $this->genres; }

		protected function set_event_genres( $event_genres ) {	$this->event_genres = $event_genres; }
		public function get_event_genres() {	return $this->event_genres; }

		protected function set_media_count( $media_count ) {	$this->media_count = $media_count; }
		public function get_media_count() {	return $this->media_count; }

		protected function set_name( $name ) {	$this->name = $name; }
		public function get_name() {	return $this->name; }

		protected function set_compiled_title( $compiled_title ) {	$this->compiled_title = $compiled_title; }
		public function get_compiled_title() {	return $this->compiled_title; }

		protected function set_venue( $venue ) {	$this->venue = new Venue( $venue ); }
		public function get_venue() {	return $this->venue; }

}
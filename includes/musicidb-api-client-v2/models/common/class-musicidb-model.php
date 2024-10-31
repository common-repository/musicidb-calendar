<?php

namespace MusicIDB\Client;

abstract class MusicIDB_Model {

	const TYPE_INT = 'int';
	const TYPE_BOOL = 'boolean';
	const TYPE_STRING = 'string';
	const TYPE_URL = 'url';
	const TYPE_DATE = 'date';
	const TYPE_ARRAY = 'array';

	protected $id;

	/**
	 *  Store the data for this model
	 * 
	 *  @param  array 	$data 				The response data as an array
	 *  @param  array   $validation_rules  	The validation rules for this model
	 * 
	 * 	@return void
	 */
	protected function store_data( $data, $validation_rules = array() ) {

		if( empty( $validation_rules ) ) {
			throw new Api_Exception( 'No validation rules provided for response' );
		}

		foreach ( $data as $key => $value ) {
			
			// Remove the key from data if
			// it is not specified
			if( !array_key_exists( $key, $validation_rules ) ) {
				continue;
			}

			if( empty($value) )
				continue;

			$valid = true;

			switch( $validation_rules[$key]['type'] ) {

				case self::TYPE_INT:
					$valid = is_int( $value );
					break;

				case self::TYPE_BOOL:
					$valid = is_bool( $value );
					break;

				case self::TYPE_STRING:
					$valid = is_string( $value );
					break;

				case self::TYPE_URL:
					$valid = ( false !== stripos( $value, 'http' ) );
					break;

				case self::TYPE_DATE:
					$format = $validation_rules[$key]['format'];
					$valid = $this->validate_date( $format, $value );
					break;

				case self::TYPE_ARRAY:
					$valid = is_array( $value );
					break;

				default:
					$valid = false;
					break;

			}

			if( false === $valid ) {
				continue;
			}

			if( !empty( $validation_rules[$key]['method_name'] ) )
				$this->set( $validation_rules[$key]['method_name'], $value );
			else
				$this->set( $key, $value );

		}

	}

	/**
	 * Check that a string is a valid date
	 * 
	 * @param  string $format  The date format that the string is passed in
	 * @param  string $value   The string to check
	 * 
	 * @return boolean 	True / False - Valid date; Returns false is format or 
	 *                  value is empty, or if value is not a string
	 */
	protected function validate_date( $format, $value ) {

		$valid = false;

		if( empty( $format ) )
			return false;

		if( empty( $value ) )
			return false;

		if( !is_string( $value ) )
			return false;

		$d = \DateTime::createFromFormat( $format, $value );
    	$valid = ( $d && $d->format($format) == $value );

    	return $valid;
	}

	/** Getters & Setters **/

		public function get($attr) {
	        $getter = "get_{$attr}";
	        
	        if(method_exists($this, $getter)) {
	            return $this->$getter();
	        } else {
	            error_log("attribute '{$attr}' was not found.");
	        }
	    }

	    public function set($attr, $value) {
	        $setter = "set_{$attr}";
	        
	        if (method_exists($this, $setter)) {
	            $this->$setter($value);
	        } else {
	            error_log("attribute '{$attr}' was not found.");
	        }
	    }

		protected function set_id( $id ) { $this->id = $id; }
		public function get_id() { return $this->id; }

}
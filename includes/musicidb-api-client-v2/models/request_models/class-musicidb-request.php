<?php 

namespace MusicIDB\Client;

class MusicIDB_Request {
	const TYPE_INT = 'int';
	const TYPE_BOOL = 'boolean';
	const TYPE_STRING = 'string';
	const TYPE_ARRAY = 'array';

	protected $client;

	public function __construct() {
		$this->client = new MusicIDB_Client();
	}

	/**
	 * Sanitizes args based on provided validation rules
	 * 
	 * @param  array 	$args           	The arguments to sanitize
	 * @param  array  	$validation_rules 	The validtation rules for the request args
	 * 
	 * @return array 	The sanitized arguments
	 */
	protected function sanitize_args( $args, $validation_rules = array() ) {
		$sanitized_args = array();

		if(empty($validation_rules)) {
			throw new Api_Exception('Error: No validation rules specified for request!');
		}

		// Check for required params
		foreach( $validation_rules as $key => $rule ) {

			if( true !== $rule['required'] ) {
				continue;
			}

			if( !array_key_exists( $key, $args ) ) {
				throw new Api_Exception( 'Error: Required request parameter ' . $key . ' was not supplied' );
			}

		}

		// Validate and sanitize
		foreach( $args as $key => $arg_val ) {

			if(!array_key_exists($key, $validation_rules)) {
				throw new Api_Exception('Error: Request parameter "' . $key . '" not recognized' );
			}

			$is_multiple = ( isset( $validation_rules[$key]['multiple'] ) && true === $validation_rules[$key]['multiple'] );

			if( false === $is_multiple && is_array( $arg_val ) ) {
				throw new Api_Exception('Error: Unexpected value for parameter "' . $key . '". Expected: ' . $validation_rules[$key]['type'] . ' Got: Array. Did you forget to specify \'multiple\' in the validation rules for the request?' );
			}

			$vals = is_array( $arg_val ) ? $arg_val : array( $arg_val );
			$sanitized_multiple = array();

			foreach ( $vals as $val ) {

				$sanitized_val = false;
				$valid = false;
			
				switch($validation_rules[$key]['type']) {
					case self::TYPE_INT:
						$valid = is_numeric($val);
						$sanitized_val = intval($val);
						break;
					case self::TYPE_BOOL:
						$valid = is_bool($val);
						$sanitized_val = boolval($val);
						break;
					case self::TYPE_STRING:
						$valid = is_string($val);
						$sanitized_val = strval($val);
						break;
					default:
						throw new Api_Exception('Error: Validation type "' . $validation_rules[$key]['type'] . '" not recognized' );
				}

				if(false === $valid) {
					throw new Api_Exception('Error: Unexpected value for parameter "' . $key . '". Expected: ' . $validation_rules[$key]['type'] . ' Got: ' . gettype($val) );
				}

				if( true === $is_multiple ) {
					$sanitized_multiple[] = $sanitized_val;
				}

			}

			$sanitized_args[$key] = ( false === $is_multiple ) ? $sanitized_val : $sanitized_multiple;
		}

		return $sanitized_args;
	}

}
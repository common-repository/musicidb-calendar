<?php
namespace MusicIDB\Client;

use \Exception;

/**
 * Api_Exception Class
 *
 * @category Class
 * @package  MusicIDB\Client
 */
class Api_Exception extends Exception {

    /**
     * Constructor
     *
     * @param string   $message         Error message
     * @param int      $code            HTTP status code
     * 
     */
    public function __construct( $message = "", $code = 0 ) {
        parent::__construct( $message, $code );
    }

}

<?php
declare( strict_types=1 );

defined( 'ABSPATH' ) || die();

/**
 * Interface: Image_Tag_Validation_Interface
 */
interface Image_Tag_Validation_Interface {

	function get_type() : string;
	function is_type( $test_types ) : bool;
	function is_valid( $test_types = null ) : bool;

}
<?php

declare( strict_types=1 );

namespace Image_Tag\Interfaces;

defined( 'WPINC' ) || die();

/**
 * Interface: Image_Tag\Validation
 */
interface Validation {

	function get_type() : string;
	function is_type( $test_types ) : bool;
	function is_valid( $test_types = null ) : bool;

}
<?php

declare( strict_types=1 );

namespace Image_Tag\Interfaces;

defined( 'WPINC' ) || die();

/**
 * Interface: Image_Tag\Interfaces\Validation
 */
interface Validation {

	public function get_type() : string;

	/**
	 * @param string[] $test_types
	 */
	public function is_type( $test_types ) : bool;

	/**
	 * @param null|string[] $test_types
	 */
	public function is_valid( $test_types = null ) : bool;

}
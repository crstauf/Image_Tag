<?php

class Image_Tag extends Image_Tag_Abstract {


	/*
	 ######  ########    ###    ######## ####  ######
	##    ##    ##      ## ##      ##     ##  ##    ##
	##          ##     ##   ##     ##     ##  ##
	 ######     ##    ##     ##    ##     ##  ##
	      ##    ##    #########    ##     ##  ##
	##    ##    ##    ##     ##    ##     ##  ##    ##
	 ######     ##    ##     ##    ##    ####  ######
	*/

	static function create( $source, $attributes = null, $settings = null ) {

	}

	static function trim( &$value ) {

		$mask  = " \t\n\r\0\x0B"; # from PHP
		$mask .= ',;';            # for item separators

		# Trim string.
		if ( is_string( $value ) )
			return trim( $value, $mask );

		# If not an array, no trimming.
		if ( !is_array( $value ) )
			return $value;

		# Deep trim items in array.
		array_walk( $value, function( &$item, $key ) use( $mask ) {

			if ( is_string( $item ) )
				$item = trim( $item, $mask );

			# Recursive is fun.
			else if ( is_array( $item ) )
				$item = self::trim( $item );

		} );

		return $value;
	}

	function get_type() {
		return 'external';
	}

	function is_type( $test_types ) {
		return !empty( array_intersect( ( array ) $test_types, array(
			'base',
			'remote',
			'external',
		) ) );
	}

	function check_valid() {
	}

}
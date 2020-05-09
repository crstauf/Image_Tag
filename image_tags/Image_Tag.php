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
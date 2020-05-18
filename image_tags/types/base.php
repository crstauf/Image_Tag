<?php

/**
 * Class: Image_Tag
 */
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


	/*
	##     ##    ###    ##       #### ########     ###    ######## ####  #######  ##    ##
	##     ##   ## ##   ##        ##  ##     ##   ## ##      ##     ##  ##     ## ###   ##
	##     ##  ##   ##  ##        ##  ##     ##  ##   ##     ##     ##  ##     ## ####  ##
	##     ## ##     ## ##        ##  ##     ## ##     ##    ##     ##  ##     ## ## ## ##
	 ##   ##  ######### ##        ##  ##     ## #########    ##     ##  ##     ## ##  ####
	  ## ##   ##     ## ##        ##  ##     ## ##     ##    ##     ##  ##     ## ##   ###
	   ###    ##     ## ######## #### ########  ##     ##    ##    ####  #######  ##    ##
	*/

	/**
	 * Get tag type.
	 *
	 * @return string
	 */
	function get_type() {
		return 'base';
	}

	/**
	 * Check tag is valid.
	 *
	 * @uses Image_Tag_Attributes::get()
	 * @return WP_Error|true
	 */
	protected function check_valid() {
		$errors = new WP_Error;

		if ( empty( $this->attributes->get( 'src' ) ) )
			$errors->add( 'required_src', 'The <code>src</code> attribute is required.' );

		if ( $errors->has_errors() )
			return $errors;

		return true;
	}

}

?>
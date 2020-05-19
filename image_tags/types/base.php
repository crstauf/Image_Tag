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

	/**
	 * Create Image_Tag object based on source.
	 *
	 * @param int|string $source
	 * @param null|array|Image_Tag_Attributes $attributes
	 * @param null|array|Image_Tag_Settings $settings
	 * @return Image_Tag_Abstract
	 *
	 * @todo add types
	 */
	static function create( $source, $attributes = array(), $settings = array() ) {
		$attributes = ( array ) $attributes;

		# If URL, create using base object.
		if ( ( bool ) wp_http_validate_url( $source ) ) {
			$attributes['src'] = $source;
			return new Image_Tag( $attributes, $settings );
		}

		# Unable to determine type.
		trigger_error( sprintf( 'Unable to determine image type from source <code>%s</code>.', $source ), E_USER_WARNING );
		return new Image_Tag( $attributes, $settings );
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
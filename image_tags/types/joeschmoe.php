<?php

class Image_Tag_JoeSchmoe extends Image_Tag_Abstract {

	/**
	 * @var string[]
	 */
	const TYPES = array(
		'joeschmoe', // primary type
		'avatar',
		'person',
		'external',
		'remote',
	);


	/*
	##     ##    ###     ######   ####  ######
	###   ###   ## ##   ##    ##   ##  ##    ##
	#### ####  ##   ##  ##         ##  ##
	## ### ## ##     ## ##   ####  ##  ##
	##     ## ######### ##    ##   ##  ##
	##     ## ##     ## ##    ##   ##  ##    ##
	##     ## ##     ##  ######   ####  ######
	*/

	/**
	 * Construct.
	 *
	 * @param null|array|Image_Tag_Attributes $attributes
	 * @param null|array|Image_Tag_Settings $settings
	 */
	function __construct( $attributes = null, $settings = null ) {
		$this->attributes = new Image_Tag_JoeSchmoe_Attributes( $attributes, null, $this );
		$this->settings   = new Image_Tag_JoeSchmoe_Settings( $settings, null, $this );
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
	 * Check tag is valid to print.
	 *
	 * @uses Image_Tag_Attributes::get()
	 * @return WP_Error|true
	 */
	protected function check_valid() {
		$errors = new WP_Error;

		if ( !wp_http_validate_url( $this->attributes->get( 'src', 'view' ) ) )
			$errors->add( 'required_src', 'The <code>src</code> attribute is required.' );

		if ( $errors->has_errors() )
			return $errors;

		return true;
	}

}

?>
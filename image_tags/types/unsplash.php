<?php

class Image_Tag_Unsplash extends Image_Tag_Abstract {

	/**
	 * @var string[]
	 */
	const TYPES = array(
		'unsplash', // primary type
		'source.unsplash.com',
		'unsplash-source',
		'placeholder',
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
		$this->attributes = new Image_Tag_Unsplash_Attributes( $attributes, null, $this );
		$this->settings   = new Image_Tag_Unsplash_Settings( $settings, null, $this );
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
	 * @uses Image_Tag_Abstract::check_valid()
	 * @return WP_Error|true
	 */
	protected function check_valid() {
		$errors = parent::check_valid();

		if ( !is_wp_error( $errors ) )
			$errors = new WP_Error;

		if (
			   empty( $this->settings->get(  'width', 'view' ) )
			&& empty( $this->settings->get( 'height', 'view' ) )
		)
			$errors->add( 'required_size', 'Length of at least one edge is required.' );

		if ( $errors->has_errors() )
			return $errors;

		return true;
	}

}

?>
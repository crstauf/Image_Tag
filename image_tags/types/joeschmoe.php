<?php

class Image_Tag_JoeSchmoe extends Image_Tag_Abstract {

	const TYPES = array(
		'joeschmoe', // primary type
		'avatar',
		'person',
		'external',
	);

	/**
	 * @var string Base URL.
	 */
	const PRIMARY_URL = 'https://joeschmoe.io/api/v1/';

	/**
	 * @var string Cloudflare worker URL.
	 */
	const ALT_URL = 'https://joeschmoe.crstauf.workers.dev/';

	protected function get_url() {
		return static::ALT_URL;
	}

	function generate_src() {
		$src = $this->get_url();

		$gender = $this->settings->get( 'gender', 'view' );
		$seed   = $this->settings->get(   'seed', 'view' );

		if ( !empty( $gender ) )
			$src .=  $gender . '/';

		if ( !empty( $seed ) )
			$src .= $seed;

		return $src;
	}


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
		$this->attributes = new Image_Tag_Attributes( $attributes );
		$this->settings   = new Image_Tag_Settings( $settings, array( 'joeschmoe' => array(
			'gender' => null,
			'seed' => null,
		) ) );

		if ( !in_array( $this->settings->joeschmoe['gender'], array( null, 'male', 'female' ) ) )
			$this->settings['joeschmoe']['gender'] = null;
	}

	/**
	 * To string.
	 *
	 * @uses Image_Tag_Attributes::set()
	 * @uses Image_Tag_Abstract::__toString()
	 * @return null|string
	 */
	function __toString() {
		if ( empty( $this->attributes->get( 'src' ) ) )
			$this->attributes->set( 'src', $this->generate_src() );

		return parent::__toString();
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

	function check_valid() {
		$errors = new WP_Error;

 		if (
			   !wp_http_validate_url( $this->attributes->get( 'src' ) )
			&& !wp_http_validate_url( $this->generate_src() )
		)
 			$errors->add( 'required_src', 'The <code>src</code> attribute is required.' );

 		if ( $errors->has_errors() )
 			return $errors;

 		return true;
	}

}

?>
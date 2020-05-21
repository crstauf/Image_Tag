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

	/**
	 * @var string Base URL.
	 */
	const PRIMARY_URL = 'https://joeschmoe.io/api/v1/';

	/**
	 * @var string Cloudflare worker URL.
	 */
	const ALT_URL = 'https://joeschmoe.crstauf.workers.dev/';

	/**
	 * Get base URL.
	 *
	 * @return string
	 */
	protected function get_url() {
		return 'primary' === $this->settings->get( 'source' )
			? static::PRIMARY_URL
			: static::ALT_URL;
	}

	/**
	 * Generate source from settings.
	 *
	 * @uses static::get_url()
	 * @uses Image_Tag_Settings::get()
	 * @return string
	 */
	protected function generate_src() {
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
		$this->settings   = new Image_Tag_Settings( $settings, array(
			'source' => 'alt',
			'gender' => null,
			'seed' => null,
		) );

		if ( !in_array( $this->settings->gender, array( null, 'male', 'female' ) ) )
			$this->settings->gender = null;
	}

	/**
	 * To string.
	 *
	 * @uses Image_Tag_Attributes::set()
	 * @uses Image_Tag_Abstract::__toString()
	 * @return null|string
	 */
	function __toString() {
		if ( !wp_http_validate_url( $this->attributes->get( 'src' ) ) )
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

	/**
	 * Check tag is valid to print.
	 *
	 * @uses Image_Tag_Attributes::get()
	 * @uses static::generate_src()
	 * @return WP_Error|true
	 */
	protected function check_valid() {
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
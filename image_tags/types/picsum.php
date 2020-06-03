<?php

class Image_Tag_Picsum extends Image_Tag_Abstract {

	const TYPES = array(
		'picsum', // primary type
		'picsum.photos',
		'placeholder',
		'external',
		'remote',
	);

	protected $details = null;


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
		$this->attributes = new Image_Tag_Picsum_Attributes( $attributes, null, $this );
		$this->settings   = new Image_Tag_Picsum_Settings( $settings, null, $this );
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

	/*
	######## ########    ###    ######## ##     ## ########  ########  ######
	##       ##         ## ##      ##    ##     ## ##     ## ##       ##    ##
	##       ##        ##   ##     ##    ##     ## ##     ## ##       ##
	######   ######   ##     ##    ##    ##     ## ########  ######    ######
	##       ##       #########    ##    ##     ## ##   ##   ##             ##
	##       ##       ##     ##    ##    ##     ## ##    ##  ##       ##    ##
	##       ######## ##     ##    ##     #######  ##     ## ########  ######
	*/

	/**
	 * Request image via HTTP GET.
	 *
	 * @param bool $fresh Flag to refresh cached value.
	 * @uses wp_safe_remote_get()
	 * @return WP_Error|array
	 */
	function http( bool $fresh = false ) {
		static $responses = array();

		$image_id = $this->settings->get( 'image_id' );

		if ( $fresh )
			unset( $resources[$image_id] );

		if ( isset( $responses[$image_id] ) )
			return $responses[$image_id];

		$src = $this->attributes->get( 'src', 'view' );

		if (
			empty( $src )
			|| !wp_http_validate_url( $src )
		)
			return new WP_Error( 'required_src', 'Image URL is required to perform HTTP GET request.' );

		$response = wp_safe_remote_get( $src );
		$image_id = ( int ) wp_remote_retrieve_header( $response, 'picsum-id' );

		$responses[$image_id] = $response;

		$this->settings->set( 'image_id', $image_id );
		$this->settings->set(   'random', false );
		$this->settings->set(     'seed', null );

		return $response;
	}

	/**
	 * Get image details.
	 *
	 * @param bool $fresh Flag to refresh cached value.
	 * @uses static::http()
	 * @return object
	 */
	function details( bool $fresh = false ) {
		if ( $fresh )
			$this->details = null;

		if ( !is_null( $this->details ) )
			return $this->details;

		$image_id = $this->settings->get( 'image_id' );

		if ( empty( $image_id ) )
			$image_id = ( int ) wp_remote_retrieve_header( $this->http( $fresh ), 'picsum-id' );

		$response = wp_safe_remote_get( sprintf( '%sid/%d/info', $this->attributes::BASE_URL, $image_id ) );

		if ( is_wp_error( $response ) )
			return ( object ) array();

		$this->details = json_decode( wp_remote_retrieve_body( $response ) );
		return $this->details;
	}

}

?>
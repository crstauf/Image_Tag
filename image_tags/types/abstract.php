<?php

abstract class Image_Tag_Abstract {

	/**
	 * @var null|Image_Tag_Attributes $attributes
	 * @var null|Image_Tag_Settings $settings
	 */
	protected $attributes = null;
	protected $settings   = null;


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
		$this->settings   = new Image_Tag_Settings( $settings );
	}

	/**
	 * Getter.
	 *
	 * @param string $key
	 * @return mixed
	 */
	function __get( string $key ) {
		return $this->$key;
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

	abstract function get_type();
	abstract function is_type( $test_types );
	abstract protected function check_valid();

	/**
	 * Check if image tag is valid.
	 *
	 * @param string|array $types
	 * @uses self::check_valid()
	 * @uses self::is_type()
	 * @return bool
	 */
	function is_valid( $types ) {
		return (
			!is_wp_error( $this->check_valid() )
			&& $this->is_type( $types )
		);
	}

}
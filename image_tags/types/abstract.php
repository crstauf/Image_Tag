<?php

/**
 * Class: Image_Tag_Abstract
 */
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
	 * Setter.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	function __set( string $key, $value ) {
		if ( in_array( $key, array_keys( get_object_vars( $this ) ) ) ) {
			if ( is_subclass_of( $value, Image_Tag_Properties_Abstract::class ) )
				$this->$key = $value;

			return;
		}

		$this->attributes->$key = $value;
	}

	/**
	 * Getter.
	 *
	 * @param string $key
	 * @return mixed
	 */
	function __get( string $key ) {
		if ( in_array( $key, array_keys( get_object_vars( $this ) ) ) )
			return $this->$key;

		return $this->attributes->$key;
	}

	/**
	 * Check if isset.
	 *
	 * @param string $key
	 * @return bool
	 */
	function __isset( string $key ) {
		if ( in_array( $key, array_keys( get_object_vars( $this ) ) ) )
			return isset( $this->$key );

		return isset( $this->attributes->$key );
	}

	/**
	 * Unsetter.
	 *
	 * @param string $key
	 */
	function __unset( string $key ) {
		if ( in_array( $key, array_keys( get_object_vars( $this ) ) ) )
			$this->$key = null;

		unset( $this->attributes->$key );
	}

	/**
	 * To string.
	 *
	 * @uses static::check_valid()
	 * @uses Image_Tag_Attributes::__toString()
	 * @return string
	 */
	function __toString() {
		$errors = $this->check_valid();

		if ( is_wp_error( $errors ) ) {
			foreach ( $errors->get_error_messages() as $message )
				trigger_error( $message, E_USER_WARNING );

			return null;
		}

		$array = array(
			'<img',
			$this->attributes->__toString(),
			'/>',
		);

		return implode( ' ', $array );
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
	abstract function get_type();

	/**
	 * Check if tag is one of specified types.
	 *
	 * @param string|array $test_types
	 * @uses static::get_type()
	 * @return bool
	 */
	function is_type( $test_types ) {
		return in_array( $this->get_type(), ( array ) $test_types );
	}

	/**
	 * Perform validation checks.
	 *
	 * @return WP_Error|true
	 */
	abstract protected function check_valid();

	/**
	 * Check if image tag is valid.
	 *
	 * @param null|string|array $types
	 * @uses static::is_type()
	 * @uses static::check_valid()
	 * @return bool
	 */
	function is_valid( $types = null ) {
		if (
			!is_null( $types )
			&& !$this->is_type( $types )
		)
			return false;

		return true === $this->check_valid();
	}

}
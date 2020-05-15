<?php

/**
 * @todo add ArrayAccess for attributes
 * @todo add Iterator for attributes
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


	/*
	   ###    ######## ######## ########  #### ########  ##     ## ######## ########  ######
	  ## ##      ##       ##    ##     ##  ##  ##     ## ##     ##    ##    ##       ##    ##
	 ##   ##     ##       ##    ##     ##  ##  ##     ## ##     ##    ##    ##       ##
	##     ##    ##       ##    ########   ##  ########  ##     ##    ##    ######    ######
	#########    ##       ##    ##   ##    ##  ##     ## ##     ##    ##    ##             ##
	##     ##    ##       ##    ##    ##   ##  ##     ## ##     ##    ##    ##       ##    ##
	##     ##    ##       ##    ##     ## #### ########   #######     ##    ########  ######
	*/

	function add_attributes( array $attributes ) {
		$this->attributes->add( $attributes );
		return $this;
	}

	function add_attribute( string $attribute, $value ) {
		$this->attributes->add( $attribute, $value );
		return $this;
	}

	function set_attributes( array $attributes ) {
		$this->attributes->set( $attributes );
		return $this;
	}

	function set_attribute( string $attribute, $value ) {
		$this->attributes->set( $attribute, $value );
		return $this;
	}

	function attributes_are_set( array $attributes ) {
		return $this->attributes->isset( $attributes );
	}

	function attribute_isset( string $attribute ) {
		return $this->attribute->isset( $attribute );
	}

	function attributes_exist( array $attributes ) {
		return $this->attributes->exist( $attributes );
	}

	function attribute_exists( string $attribute ) {
		return $this->attributes->exist( $attribute );
	}

	function add_to_attributes( array $attributes ) {
		$this->attributes->add_to( $attributes );
		return $this;
	}

	function add_to_attribute( string $attribute, $value ) {
		$this->attributes->add_to( $attribute, $value );
		return $this;
	}

	function get_attributes( $filter = null, string $context = 'view' ) {
		return $this->attributes->get( $filter, $context );
	}

	function get_attribute( string $attribute, string $context = 'view' ) {
		return $this->attributes->get( $attribute, $context );
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
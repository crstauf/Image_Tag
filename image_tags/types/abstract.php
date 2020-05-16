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
	########  ########   #######  ########  ######## ########  ######## #### ########  ######
	##     ## ##     ## ##     ## ##     ## ##       ##     ##    ##     ##  ##       ##    ##
	##     ## ##     ## ##     ## ##     ## ##       ##     ##    ##     ##  ##       ##
	########  ########  ##     ## ########  ######   ########     ##     ##  ######    ######
	##        ##   ##   ##     ## ##        ##       ##   ##      ##     ##  ##             ##
	##        ##    ##  ##     ## ##        ##       ##    ##     ##     ##  ##       ##    ##
	##        ##     ##  #######  ##        ######## ##     ##    ##    #### ########  ######
	*/

	/**
	 * Passthru parameters to specified property object and method.
	 *
	 * @param string $property_type
	 * @param string $method_name
	 * @param array $params
	 * @return mixed
	 */
	protected function access_property( string $property_type, string $method_name, ...$params ) {
		switch ( $property_type ) {

			case 'attribute':
				$property_type = 'attributes';
				break;

			case 'setting':
				$property_type = 'settings';
				break;

		}

		if ( !in_array( $property_type, array( 'attributes', 'settings' ) ) ) {
			trigger_error( sprintf( 'Properties of type <code>%s</code> do not exist.', $property_type ), E_USER_WARNING );
			return;
		}

		return $this->$property_type->$method_name( ...$params );
	}

	/**
	 * Add properties.
	 *
	 * @param string $property_type
	 * @param string|array $properties
	 * @param mixed $value
	 * @uses static::access_property()
	 * @return $this
	 *
	 * @todo add more tests
	 */
	function add( string $property_type, $properties, $value = null ) {
		$this->access_property( $property_type, 'add', $properties, $value );
		return $this;
	}

	/**
	 * Set properties.
	 *
	 * @param string $property_type
	 * @param string|array $properties
	 * @param mixed $value
	 * @uses static::access_property()
	 * @return $this
	 *
	 * @todo add more tests
	 */
	function set( string $property_type, $properties, $value = null ) {
		$this->access_property( $property_type, 'set', $properties, $value );
		return $this;
	}

	/**
	 * Check specified properties are set.
	 *
	 * @param string $property_type
	 * @param string|array $properties
	 * @uses static::access_property()
	 * @return bool
	 *
	 * @todo add more tests
	 */
	function isset( string $property_type, $properties ) {
		return $this->access_property( $property_type, 'isset', $properties );
	}

	/**
	 * Check specified properties exist.
	 *
	 * @param string $property_type
	 * @param string|string[] $properties Array of attribute names.
	 * @uses static::access_property()
	 * @return bool
	 *
	 * @todo add more tests
	 */
	function exists( string $property_type, $properties ) {
		return $this->access_property( $property_type, 'exists', $properties );
	}

	/**
	 * Add values to specified properties.
	 *
	 * @param string $property_type
	 * @param array $properties
	 * @param mixed $value
	 * @uses static::access_property()
	 * @return $this
	 *
	 * @todo add more tests
	 */
	function add_to( string $property_type, $properties, $value = null ) {
		$this->access_property( $property_type, 'add_to', $properties, $value );
		return $this;
	}

	/**
	 * Get properties, by filter.
	 *
	 * @param string $property_type
	 * @param null|string|string[] $properties
	 * @param string $context view|edit
	 * @uses static::access_property()
	 * @return mixed
	 *
	 * @todo add more tests
	 */
	function get( string $property_type, $properties = null, string $context = 'view' ) {
		return $this->access_property( $property_type, 'get', $properties, $context );
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
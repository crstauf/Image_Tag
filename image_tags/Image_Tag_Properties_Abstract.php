<?php

abstract class Image_Tag_Properties_Abstract implements ArrayAccess {

	const DEFAULTS = array();

	/**
	 * @var null|array $properties
	 * @var null|array $defaults
	 */
	protected $properties = array();
	protected $defaults = array();


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
	 * @param array|self $properties
	 * @param array $defaults
	 * @uses self::get()
	 * @uses self::set()
	 */
	function __construct( $properties, array $defaults = array() ) {
		if ( is_a( $properties, static::class ) )
			$properties = $properties->get( null, 'edit' );

		$this->defaults = wp_parse_args( $defaults, static::DEFAULTS );
		$this->set( wp_parse_args( $properties, $this->defaults ) );
	}

	/**
	 * Setter.
	 *
	 * @param string $property
	 * @param mixed $value
	 * @uses self::set()
	 */
	function __set( string $property, $value ) {
		$this->set( $property, $value );
	}

	/**
	 * Getter.
	 *
	 * @param string $property
	 * @uses self::get()
	 * @return mixed
	 */
	function __get( string $property ) {
		return $this->get( $property, 'edit' );
	}

	/**
	 * Check if isset.
	 *
	 * @param string $property
	 * @uses self::isset()
	 * @return bool
	 */
	function __isset( string $property ) {
		return $this->isset( $property );
	}

	/**
	 * Unsetter.
	 *
	 * @param string $property
	 * @uses self::unset()
	 */
	function __unset( string $property ) {
		$this->unset( $property );
	}


	/*
	   ###    ########  ########
	  ## ##   ##     ## ##     ##
	 ##   ##  ##     ## ##     ##
	##     ## ##     ## ##     ##
	######### ##     ## ##     ##
	##     ## ##     ## ##     ##
	##     ## ########  ########
	*/

	/**
	 * Add properties.
	 *
	 * Set property if not already set.
	 *
	 * @param string|array $properties
	 * @param mixed $value
	 * @uses self::add_property()
	 * @uses self::add_properties()
	 */
	function add( $properties, $value = null ) {
		is_string( $properties )
			? $this->add_property( $properties, $value )
			: $this->add_properties( $properties );
	}

	/**
	 * Add single property.
	 *
	 * @param string $property
	 * @param mixed $value
	 * @uses self::isset()
	 * @uses self::set()
	 */
	protected function add_property( string $property, $value ) {
		if ( !$this->isset( $property ) )
			$this->set( $property, $value );
	}

	/**
	 * Add multiple properties.
	 *
	 * @param array $properties
	 * @uses self::add_property()
	 */
	protected function add_properties( array $properties ) {
		foreach ( $properties as $property => $value )
			$this->add_property( $property, $value );
	}


	/*
	 ######  ######## ########
	##    ## ##          ##
	##       ##          ##
	 ######  ######      ##
	      ## ##          ##
	##    ## ##          ##
	 ######  ########    ##
	*/

	/**
	 * Set properties.
	 *
	 * @param string|array $properties
	 * @param mixed $value
	 * @uses self::_set()
	 */
	abstract function set( $properties, $value = null );

	/**
	 * Set raw property.
	 *
	 * @param string $property
	 * @param mixed $value
	 */
	function _set( string $property, $value ) {
		$this->properties[$property] = $value;
	}


	/*
	 ######   ######## ########
	##    ##  ##          ##
	##        ##          ##
	##   #### ######      ##
	##    ##  ##          ##
	##    ##  ##          ##
	 ######   ########    ##
	*/

	/**
	 * Check if properties are set.
	 *
	 * @param string|array $properties
	 * @return bool
	 */
	function isset( $properties ) {

		# Check single property.
		if ( is_string( $properties ) )
			return isset( $this->properties[$properties] );

		# Check multiple properties.
		foreach ( $properties as $property )
			if ( !isset( $this->properties[$property] ) )
				return false;

		return true;
	}

	/**
	 * Get properties.
	 *
	 * @param string|array $filter
	 * @param string $context
	 * @return array
	 */
	function get( $filter = array(), string $context = 'view' ) {
		$filter = ( array ) $filter;

		$properties = $this->properties;

		# Filter to requested properties.
		if ( !empty( $filter ) )
			$properties = array_intersect_key( $properties, array_flip( $filter ) );

		# Return requested properties.
		return 'view' === $context
			? array_filter( $properties )
			: $properties;
	}

	/**
	 * Unset properties.
	 *
	 * @param string|array $properties
	 * @todo define
	 */
	function unset( $properties ) {}


	/*
	   ###    ########  ########     ###    ##    ##    ###     ######   ######  ########  ######   ######
	  ## ##   ##     ## ##     ##   ## ##    ##  ##    ## ##   ##    ## ##    ## ##       ##    ## ##    ##
	 ##   ##  ##     ## ##     ##  ##   ##    ####    ##   ##  ##       ##       ##       ##       ##
	##     ## ########  ########  ##     ##    ##    ##     ## ##       ##       ######    ######   ######
	######### ##   ##   ##   ##   #########    ##    ######### ##       ##       ##             ##       ##
	##     ## ##    ##  ##    ##  ##     ##    ##    ##     ## ##    ## ##    ## ##       ##    ## ##    ##
	##     ## ##     ## ##     ## ##     ##    ##    ##     ##  ######   ######  ########  ######   ######
	*/

	function offsetExists( $offset ) {
		return isset( $this->properties[$offset] );
	}

	function offsetGet( $offset ) {
		return $this->properties[$offset];
	}

	function offsetSet( $offset, $value ) {
		$this->properties[$offset] = $value;
	}

	function offsetUnset( $offset ) {
		unset( $this->properties[$offset] );

		if ( isset( static::DEFAULTS[$offset] ) )
			$this->properties[$offset] = static::DEFAULTS[$offset];
	}

}

?>
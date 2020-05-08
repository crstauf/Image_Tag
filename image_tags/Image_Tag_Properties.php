<?php

class Image_Tag_Properties implements ArrayAccess {

	const NAME = 'property';
	const DEFAULTS = array();

	/**
	 * @var null|array $properties
	 * @var null|array $defaults
	 */
	protected $properties = array();
	protected $defaults   = array();

	protected static function function_name( string $property ) {
		return preg_replace( '/[^A-z0-9_]/', '_', $property );
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
	 * @param array|self $properties
	 * @param array $defaults
	 * @uses self::get()
	 * @uses self::set()
	 */
	function __construct( $properties = array(), array $defaults = array() ) {
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
	 *
	 * @todo redefine based on self::get()
	 */
	function set( $properties, $value = null ) {
		if ( is_string( $properties ) ) {
			$this->_set( $properties, $value );
			return;
		}

		foreach ( $properties as $property => $value )
			$this->_set( $property, $value );
	}

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
	######## ##     ## ####  ######  ########  ######
	##        ##   ##   ##  ##    ##    ##    ##    ##
	##         ## ##    ##  ##          ##    ##
	######      ###     ##   ######     ##     ######
	##         ## ##    ##        ##    ##          ##
	##        ##   ##   ##  ##    ##    ##    ##    ##
	######## ##     ## ####  ######     ##     ######
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
	 * Check if properties exist.
	 *
	 * @param string|array $properties
	 * @return bool
	 */
	function exists( $properties ) {

		# Check single proeprty.
		if ( is_string( $properties ) )
			return array_key_exists( $properties, $this->properties );

		# Check multiple properties.
		foreach ( $properties as $property )
			if ( !array_key_exists( $property, $this->properties ) )
				return false;

		return true;
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
	 * Get properties.
	 *
	 * @param null|string|array $properties
	 * @param string $context
	 * @uses self::get_property()
	 * @uses self::get_properties()
	 * @return string|array
	 */
	function get( $properties = null, string $context = 'view' ) {
		return is_string( $properties )
			? $this->get_property( $properties, $context )
			: $this->get_properties( $context, $properties );
	}

	/**
	 * Get properties.
	 *
	 * @param string $context
	 * @param string|array $keys
	 * @return array
	 */
	protected function get_properties( string $context = 'view', array $keys = null ) {
		if ( is_null( $keys ) )
			$keys = array_keys( $this->properties );

		$properties = array();

		foreach ( $keys as $key )
			$properties[$key] = $this->get_property( $key, $context );

		return $properties;
	}

	/**
	 * Get property.
	 *
	 * @param string $property
	 * @param string $context
	 * @return string
	 */
	protected function get_property( string $property, string $context = 'view' ) {
		if ( !$this->isset( $property ) )
			return null;

		$format = sprintf( 'get_%%s_%s', static::NAME );

		# Override by property name.
		$method_name = sprintf( $format, static::function_name( $property ) );
		if ( method_exists( $this, $method_name ) )
			return call_user_func( array( $this, $method_name ), $context );

		# Override by type of property's value.
		$method_name = sprintf( $format, gettype( $this->properties[$property] ) );
		if ( method_exists( $this, $method_name ) )
			return call_user_func( array( $this, $method_name ), $property, $context );

		return $this->properties[$property];
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
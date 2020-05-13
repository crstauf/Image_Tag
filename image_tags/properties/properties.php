<?php
/**
 * @todo implement Iterator
 */

class Image_Tag_Properties implements ArrayAccess {

	/**
	 * @var string NAME
	 * @var array DEFAULTS
	 */
	const NAME = 'property';
	const DEFAULTS = array();

	/**
	 * @var null|array $properties
	 * @var null|array $defaults
	 */
	protected $properties = array();
	protected $defaults   = array();


	/*
	 ######  ########    ###    ######## ####  ######
	##    ##    ##      ## ##      ##     ##  ##    ##
	##          ##     ##   ##     ##     ##  ##
	 ######     ##    ##     ##    ##     ##  ##
	      ##    ##    #########    ##     ##  ##
	##    ##    ##    ##     ##    ##     ##  ##    ##
	 ######     ##    ##     ##    ##    ####  ######
	*/

	/**
	 * Make provided property safe for use in function name.
	 *
	 * Replaces any non-alphanumeric characters with underscore.
	 *
	 * @param string $property
	 * @return string
	 */
	static function function_name( string $property ) {
		return preg_replace( '/[^A-z0-9_]/', '_', trim( $property ) );
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
	 * @uses static::get()
	 * @uses static::set()
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
	 * @uses static::set()
	 */
	function __set( string $property, $value ) {
		$this->set( $property, $value );
	}

	/**
	 * Getter.
	 *
	 * @param string $property
	 * @uses static::get()
	 * @return mixed
	 *
	 * @todo add test for accessing 'properties' and 'defaults'
	 */
	function __get( string $property ) {
		if ( in_array( $property, array( 'properties', 'defaults' ) ) )
			return $this->$property;

		return $this->get( $property, 'edit' );
	}

	/**
	 * Check if isset.
	 *
	 * @param string $property
	 * @uses static::isset()
	 * @return bool
	 */
	function __isset( string $property ) {
		return $this->isset( $property );
	}

	/**
	 * Unsetter.
	 *
	 * @param string $property
	 * @uses static::unset()
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
	 * @uses static::add_property()
	 * @uses static::add_properties()
	 * @return $this
	 */
	function add( $properties, $value = null ) {
		is_string( $properties )
			? $this->add_property(   $properties, $value )
			: $this->add_properties( $properties );

		return $this;
	}

	/**
	 * Add single property.
	 *
	 * @param string $property
	 * @param mixed $value
	 * @uses static::isset()
	 * @uses static::set_property()
	 */
	protected function add_property( string $property, $value ) {
		if ( !$this->isset( $property ) )
			$this->set_property( $property, $value );
	}

	/**
	 * Add multiple properties.
	 *
	 * @param array $properties
	 * @uses static::add_property()
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
	 * @uses static::set_property()
	 * @uses static::set_properties()
	 * @return $this
	 */
	function set( $properties, $value = null ) {
		is_string( $properties )
			? $this->set_property( $properties, $value )
			: $this->set_properties( ( array ) $properties );

		return $this;
	}

	/**
	 * Set properties.
	 *
	 * @param array $properties
	 * @uses static::set_property()
	 */
	protected function set_properties( array $properties ) {
		foreach ( $properties as $property => $value )
			$this->set_property( $property, $value );
	}

	/**
	 * Set property.
	 *
	 * @param string $property
	 * @param mixed $value
	 * @uses static::function_name()
	 * @uses self::_set()
	 */
	protected function set_property( string $property, $value ) {
		$format = sprintf( 'set_%%s_%s', static::NAME );

		# Override by property name.
		$method_name = sprintf( $format, static::function_name( $property ) );
		if ( method_exists( $this, $method_name ) ) {
			call_user_func( array( $this, $method_name ), $value );
			return;
		}

		# Override by property type.
		$types = array();

		# Get type from defaults.
		if ( array_key_exists( $property, static::DEFAULTS ) )
			$types[] = gettype( static::DEFAULTS[$property] );

		# Add type of setting value.
		$types[] = gettype( $value );

		# Check each type for a defined method, and call the first existing.
		foreach ( $types as $type ) {
			$method_name = sprintf( $format, $type );

			if ( method_exists( $this, $method_name ) ) {
				call_user_func( array( $this, $method_name ), $property, $value );
				return;
			}
		}

		# Set directly.
		$this->_set( $property, $value );
	}

	/**
	 * Set property directly.
	 *
	 * @param string $property
	 * @param mixed $value
	 */
	final function _set( string $property, $value ) {
		$this->properties[$property] = $value;
	}

	/**
	 * Unset properties.
	 *
	 * @param string|array $properties
	 * @todo define
	 */
	function unset( $properties ) {
		foreach ( ( array ) $properties as $property )
			unset( $this->properties[$property] );

		return $this;
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
	 * @uses static::get_property()
	 * @uses static::get_properties()
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

		# If edit context, return raw.
		if ( 'edit' === $context )
			return $this->_get( $property );

		$format = sprintf( 'get_%%s_%s_for_view', static::NAME );

		# Override by property name.
		$method_name = sprintf( $format, static::function_name( $property ) );
		if ( method_exists( $this, $method_name ) )
			return call_user_func( array( $this, $method_name ) );

		# Override by type of property's value.
		$method_name = sprintf( $format, gettype( $this->properties[$property] ) );
		if ( method_exists( $this, $method_name ) )
			return call_user_func( array( $this, $method_name ), $property );

		# No overrides; get directly.
		return $this->_get( $property );
	}

	/**
	 * Get property directly.
	 *
	 * @param string $property
	 * @return mixed
	 */
	protected function _get( string $property ) {
		return $this->properties[$property];
	}


	/*
	   ###    ########  ########     ###    ##    ##    ###     ######   ######  ########  ######   ######
	  ## ##   ##     ## ##     ##   ## ##    ##  ##    ## ##   ##    ## ##    ## ##       ##    ## ##    ##
	 ##   ##  ##     ## ##     ##  ##   ##    ####    ##   ##  ##       ##       ##       ##       ##
	##     ## ########  ########  ##     ##    ##    ##     ## ##       ##       ######    ######   ######
	######### ##   ##   ##   ##   #########    ##    ######### ##       ##       ##             ##       ##
	##     ## ##    ##  ##    ##  ##     ##    ##    ##     ## ##    ## ##    ## ##       ##    ## ##    ##
	##     ## ##     ## ##     ## ##     ##    ##    ##     ##  ######   ######  ########  ######   ######
	*/

	/**
	 * ArrayAccess: exists
	 *
	 * @param string|int $offset
	 * @return bool
	 */
	function offsetExists( $offset ) {
		return isset( $this->properties[$offset] );
	}

	/**
	 * ArrayAccess: get
	 *
	 * @param string|int $offset
	 * @return mixed
	 */
	function offsetGet( $offset ) {
		return $this->properties[$offset];
	}

	/**
	 * ArrayAccess: set
	 *
	 * @param string|int $offset
	 * @param mixed $value
	 */
	function offsetSet( $offset, $value ) {
		$this->properties[$offset] = $value;
	}

	/**
	 * ArrayAccess: unset
	 *
	 * @param string|int $offset
	 */
	function offsetUnset( $offset ) {
		unset( $this->properties[$offset] );
	}

}

?>
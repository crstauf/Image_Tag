<?php

/**
 * Class: Image_Tag_Attributes
 */
class Image_Tag_Attributes extends Image_Tag_Properties_Abstract {

	/**
	 * @var array
	 */
	const DEFAULTS = array(

		# Strings or integers.
		'id' => null,
		'alt' => null,
		'src' => null,
		'title' => null,
		'width' => null,
		'height' => null,
		'data-src' => null,

		# Arrays.
		'data-srcset' => array(),
		'data-sizes' => array(),
		'srcset' => array(),
		'style' => array(),
		'sizes' => array(),
		'class' => array(),

	);


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
	 * Set attributes.
	 *
	 * @param string|array $attributes
	 * @param mixed $value
	 * @uses self::set_one()
	 * @return self
	 */
	function set( $attributes, $value = null ) {

		# Set single attribute.
		if ( is_string( $attributes ) ) {
			$this->set_one( $attributes, $value );
			return $this;
		}

		# Set multiple attributes.
		foreach ( $attributes as $attribute => $value )
			$this->set_one( $attribute, $value );

		# Return self for chaining.
		return $this;
	}

	/**
	 * Set one attribute.
	 *
	 * @param string $attribute
	 * @param mixed $value
	 * @uses parent::_set()
	 */
	protected function set_one( string $attribute, $value ) {
		$format = 'set_%s_attribute';

		# Override by attribute name.
		$method_name = sprintf( $format, $attribute );
		if ( method_exists( $this, $method_name ) )
			return call_user_func( array( $this, $method_name ), $value );

		# Override by value type.
		$method_name = sprintf( $format, gettype( $value ) );
		if ( method_exists( $this, $method_name ) )
			return call_user_func( array( $this, $method_name ), $value );

		# Set raw value.
		$this->_set( $attribute, $value );
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
	 * Get attributes.
	 *
	 * @param string|array $filter
	 * @param string $context view|edit
	 * @return
	 */
	function get( $filter = array(), string $context = 'view' ) {

		# Get filtered attributes.
		$attributes = parent::get( $filter, $context );

		# If edit context, return raw attributes.
		if ( 'edit' === $context )
			return $attributes;

		$return = array();

		# Iterate across filtered attributes.
		foreach ( $attributes as $attribute => $value ) {

			# Check for specific method for attribute.
			$method_name = sprintf( 'get_%s_attribute', $attribute );

			if ( method_exists( $this, $method_name ) ) {
				$return[$attribute] = call_user_func( array( $this, $method_name ), $context );
				continue;
			}

			# Check for specific method for attribute value's type.
			$method_name = sprintf( 'get_%s_attribute', gettype( $value ) );

			if ( method_exists( $this, $method_name ) ) {
				$return[$attribute] = call_user_func( array( $this, $method_name ), $context );
				continue;
			}

			# Store raw value.
			$return[$attribute] = $value;
		}

		# If only one filter, return as string.
		if ( 1 !== count( $filter ) )
			return array_pop( $return );

		return $return;
	}

}

?>
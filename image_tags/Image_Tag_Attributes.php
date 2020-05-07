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

	/**
	 * Set "class" attribute.
	 *
	 * @param string|array $value
	 * @uses Image_Tag::trim()
	 * @uses self::_set()
	 */
	protected function set_class_attribute( $classes ) {
		if ( is_string( $classes ) )
			$classes = explode( ' ', trim( $classes ) );

		if ( empty( $classes ) )
			$classes = array();

		# If no array, bail.
		if ( !is_array( $classes ) ) {
			trigger_error( sprintf( 'Value of type <code>%s</code> is not valid for <code>%s</code> attribute.', gettype( $classes ), 'class' ) );
			return;
		}

		# Cleanup.
		$classes = Image_Tag::trim( $classes ); // remove excess characters from items
		$classes = array_filter( $classes );    // remove empty items
		$classes = array_values( $classes );    // reset array keys

		# Set.
		$this->_set( 'class', $classes );
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

		$return = array();

		# If "edit" context, return raw attributes.
		if ( 'edit' === $context ) {
			$return = $attributes;

		# Iterate across filtered attributes.
		} else
			foreach ( $attributes as $attribute => $value ) {

				# Check for specific method for attribute.
				$method_name = sprintf( 'get_%s_attribute', $attribute );
				if ( method_exists( $this, $method_name ) ) {
					$return[$attribute] = call_user_func( array( $this, $method_name ) );
					continue;
				}

				# Check for specific method for attribute value's type.
				$method_name = sprintf( 'get_%s_attribute', gettype( $value ) );
				if ( method_exists( $this, $method_name ) ) {
					$return[$attribute] = call_user_func( array( $this, $method_name ), $attribute );
					continue;
				}

				# Store raw value.
				$return[$attribute] = $value;
			}

		# If only one filter, return as string.
		if (
			!is_array( $filter )
			|| 1 === count( $filter )
		) {
			return array_pop( $return );
		}

		return $return;
	}

	/**
	 * Get "class" attribute, in "view" context.
	 *
	 * @uses self::get()
	 * @return string
	 */
	protected function get_class_attribute() {
		$classes = $this->get( 'class', 'edit' );
		$classes = array_unique( $classes );
		return implode( ' ', $classes );
	}

	protected function get_style_attribute() {
		return $this->get_array_attribute( 'style', '; ' );
	}

	protected function get_array_attribute( string $attribute, string $glue = ', ' ) {
		$value = $this->get( $attribute, 'edit' );
		return implode( $glue, $value );
	}

}

?>
<?php

/**
 * Class: Image_Tag_Attributes
 */
class Image_Tag_Attributes extends Image_Tag_Properties {

	/**
	 * @var string
	 * @var array
	 */
	const NAME = 'attribute';
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

	/**
	 * Remove typical leading/trailing characters from property.
	 *
	 * @see trim()
	 * @param mixed Attribute value (passed by reference).
	 * @return mixed
	 */
	static function trim( &$value ) {

		$mask  = " \t\n\r\0\x0B"; // from PHP's trim()
		$mask .= ','; // for sizes and srcset attributes
		$mask .= ';'; // for style attribute

		# Trim string.
		if ( is_string( $value ) )
			return trim( $value, $mask );

		# If not an array, no trimming.
		if ( !is_array( $value ) )
			return $value;

		# Deep trim items in array.
		array_walk( $value, function( &$item, $key ) use( $mask ) {

			if ( is_string( $item ) )
				$item = trim( $item, $mask );

			# Recursive is fun.
			else if ( is_array( $item ) )
				$item = self::trim( $item );

		} );

		return $value;
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
	 * Set "class" attribute.
	 *
	 * @param string|array $value
	 * @uses Image_Tag::trim()
	 * @uses static::trim()
	 * @uses static::_set()
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
		$classes = static::trim( $classes ); // remove excess characters from items
		$classes = array_filter( $classes ); // remove empty items
		$classes = array_values( $classes ); // reset array keys

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
	 * @param string|array $keys
	 * @param string $context view|edit
	 * @return string|array
	 */
	function get( $attributes = null, string $context = 'view' ) {
		$values = parent::get( $attributes, $context );

		if (
			'edit' === $context
			|| !is_array( $values )
		)
			return $values;

		return array_filter( $values );
	}

	/**
	 * Get "class" attribute in view context.
	 *
	 * @uses static::get()
	 * @return string
	 */
	protected function get_class_attribute() {
		$classes = $this->_get( 'class' );
		$classes = array_unique( $classes );
		return implode( ' ', $classes );
	}

	/**
	 * Get "style" attribute in view context.
	 *
	 * @return string
	 */
	protected function get_style_attribute() {
		return $this->get_array_attribute( 'style', '; ' );
	}

	/**
	 * Get attribute that's an array in view context.
	 *
	 * @param string $attribute
	 * @param string $glue
	 * @return string
	 */
	protected function get_array_attribute( string $attribute, string $glue = ', ' ) {
		$value = $this->_get( $attribute );
		return implode( $glue, $value );
	}

}

?>
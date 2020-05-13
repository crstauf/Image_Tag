<?php

/**
 * Class: Image_Tag_Attributes
 */
class Image_Tag_Attributes extends Image_Tag_Properties {

	/**
	 * @var string NAME
	 */
	const NAME = 'attribute';

	/**
	 * @var array DEFAULTS Default attributes and values.
	 */
	const DEFAULTS = array(

		# Strings or integers.
		'id' => null,
		'alt' => '',
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
		'sizes' => array( '100vw' ),
		'class' => array(),

	);

	/**
	 * @var string[] ORDER Order of printing attributes.
	 */
	const ORDER = array(
		'id',
		'class',
		'src',
		'data-src',
		'srcset',
		'data-srcset',
		'sizes',
		'data-sizes',
		'width',
		'height',
		'title',
		'alt',
		'style',
	);


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
	 * Remove typical leading/trailing characters from property.
	 *
	 * @see trim()
	 * @param mixed Attribute value (passed by reference).
	 * @return mixed
	 */
	protected static function trim( &$value ) {

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

	/**
	 * Recursively explode strings in a multi-dimensional array.
	 *
	 * @param array[] $array
	 * @param string $delimeter
	 * @uses static::explode_deep()
	 * @return array
	 */
	protected static function explode_deep( array $array, string $delimeter = ',' ) {
		$flattened_array = array();

		foreach ( $array as $item )

			# If an array, do recursive!
			if ( is_array( $item ) )
				$flattened_array = array_merge(
					$flattened_array,
					static::explode_deep( $item, $delimeter )
				);

			# If string, explode and merge.
			else if ( is_string( $item ) )
				$flattened_array = array_merge(
					$flattened_array,
					explode( $delimeter, $item )
				);

			# If something else, add to array.
			else
				$flattened_array[] = $item;

		return $flattened_array;
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
	 * To string.
	 *
	 * @uses static::get()
	 * @uses static::force()
	 * @return string
	 */
	function __toString() {

		# Get attributes.
		$attributes = $this->get( null, 'edit' );

		# Remove empty values, except empty string.
		$attributes = array_filter( $attributes, function( $value ) {
			return (
				!empty( $value )
				|| '' === $value
			);
		} );

		$array = array();

		# Add attributes to string in specified order.
		foreach ( static::ORDER as $attribute )
			if ( isset( $attributes[$attribute] ) )
				$array[$attribute] = sprintf( '%s="%s"', $attribute, esc_attr( $this->get( $attribute ) ) );

		# Add remaining attributes.
		$diff = array_diff_key( $attributes, array_flip( static::ORDER ) );
		foreach ( $diff as $attribute => $value )
			$array[$attribute] = sprintf( '%s="%s"', $attribute, esc_attr( $value ) );

		# Apply filters.
		$array  = apply_filters( 'image_tag/attributes/output/array', $array, $this );
		$string = apply_filters( 'image_tag/attributes/output', implode( ' ', $array ), $array, $this );

		return $string;
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
	 * @param string|array $classes
	 * @uses static::set_array_attribute()
	 */
	protected function set_class_attribute( $classes ) {
		$this->set_array_attribute( 'class', $classes, ' ' );
	}

	/**
	 * Set array attribute.
	 *
	 * @param string $attribute
	 * @param mixed $value
	 * @param string $delimeter
	 * @uses static::trim()
	 * @uses static::_set()
	 */
	protected function set_array_attribute( string $attribute, $value, string $delimeter = ',' ) {

		# If value is empty, set empty array.
		if ( empty( $value ) ) {
			$this->_set( $attribute, array() );
			return;
		}

		# If value is a string, explode!
		if ( is_string( $value ) )
			$value = explode( $delimeter, $value );

		# If value is an array, explode deeply!
		else if ( is_array( $value ) )
			$value = static::explode_deep( $value, $delimeter );

		# If not an array, alert and bail.
		if ( !is_array( $value ) ) {
			trigger_error( sprintf( 'Value of type <code>%s</code> is not valid for <code>%s</code> attribute.', gettype( $value ), $attribute ) );
			return;
		}

		# Cleanup.
		$value = static::trim( $value ); // remove excess characters from items
		$value = array_filter( $value ); // remove empty items
		$value = array_values( $value ); // reset array keys

		# Set value.
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
	 * @param string|array $keys
	 * @param string $context view|edit
	 * @uses Image_Tag_Properties::get()
	 * @return string|array
	 */
	function get( $attributes = null, string $context = 'view' ) {
		return parent::get( $attributes, $context );
	}

	/**
	 * Get "class" attribute in view context.
	 *
	 * @uses static::_get()
	 * @return string
	 */
	protected function get_class_attribute_for_view() {
		$classes = $this->_get( 'class' );                            // get array of classes
		$classes = array_unique( $classes );                          // remove duplicates
		$classes = array_map( array( __CLASS__, 'trim' ), $classes ); // trim items in array
		$classes = array_filter( $classes );
		return implode( ' ', $classes );
	}

	/**
	 * Get "style" attribute in view context.
	 *
	 * @uses static::get_array_attribute_for_view()
	 * @return string
	 */
	protected function get_style_attribute_for_view() {
		return $this->get_array_attribute_for_view( 'style', '; ' );
	}

	/**
	 * Get attribute that's an array in view context.
	 *
	 * @param string $attribute
	 * @param string $glue
	 * @return string
	 */
	protected function get_array_attribute_for_view( string $attribute, string $glue = ', ' ) {
		$value = $this->_get( $attribute );
		$value = array_map( array( __CLASS__, 'trim' ), $value );
		return implode( $glue, $value );
	}

}

?>
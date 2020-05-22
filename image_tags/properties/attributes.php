<?php

/**
 * Class: Image_Tag_Attributes
 */
class Image_Tag_Attributes extends Image_Tag_Properties_Abstract {

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
		'src',
		'data-src',
		'srcset',
		'data-srcset',
		'sizes',
		'data-sizes',
		'class',
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
				$item = static::trim( $item );

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
	 * Getter.
	 *
	 * @param string $property
	 * @uses static::get_property()
	 * @return mixed
	 */
	function __get( string $property ) {
		if ( in_array( $property, array( 'properties', 'defaults' ) ) )
			return $this->$property;

		return $this->get_property( $property, 'edit' );
	}

	/**
	 * To string.
	 *
	 * @uses static::get()
	 * @return string
	 */
	function __toString() {
		$attributes = $this->get( null, 'view' );
		$array = array();

		# Add attributes to string in specified order.
		foreach ( static::ORDER as $attribute )
			if ( isset( $attributes[$attribute] ) )
				$array[$attribute] = sprintf( '%s="%s"', $attribute, esc_attr( $attributes[$attribute] ) );

		# Add remaining attributes.
		$diff = array_diff_key( $attributes, array_flip( static::ORDER ) );
		foreach ( $diff as $attribute => $value )
			$array[$attribute] = sprintf( '%s="%s"', $attribute, esc_attr( $attributes[$attribute] ) );

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
	 * @param string|array $attributes
	 * @param string $context view|edit
	 * @uses Image_Tag_Properties::get()
	 * @return string|array
	 */
	function get( $attributes = null, string $context = 'edit' ) {
		$value = is_string( $attributes )
			? $this->get_property( $attributes, $context )
			: $this->get_properties( $attributes, $context );

		# Return attributes immediately.
		if ( 'edit' === $context )
			return $value;

		# If not a list of attributes, no processing to do.
		if ( is_string( $attributes ) )
			return $value;

		# Remove null and empty arrays from attributes.
		return array_filter( $value, function( $item ) {
			return (
				!is_null( $item )
				&& array() !== $item
			);
		} );
	}

	/**
	 * Get attributes.
	 *
	 * @param string|array $keys
	 * @return array
	 */
	protected function get_properties( array $keys = null, string $context = 'edit' ) {
		if ( is_null( $keys ) )
			$keys = array_keys( $this->properties );

		$attributes = array();

		foreach ( $keys as $key )
			$attributes[$key] = $this->get_property( $key, $context );

		return $attributes;
	}

	/**
	 * Get attribute.
	 */
	protected function get_property( string $attribute, string $context = 'edit' ) {
		$value = parent::get_property( $attribute );

		# If edit context or null, return.
		if ( 'edit' === $context )
			return $value;

		$format = sprintf( 'get_%%s_%s_for_view', static::NAME );

		# Override by property name.
		$method_name = sprintf( $format, static::function_name( $attribute ) );
		if ( method_exists( $this, $method_name ) )
			return call_user_func( array( $this, $method_name ) );

		# Override by type of property's value.
		$method_name = sprintf( $format, gettype( $this->properties[$attribute] ) );
		if ( method_exists( $this, $method_name ) )
			return call_user_func( array( $this, $method_name ), $attribute );

		# No overrides; return direct value.
		return $value;
	}

	/**
	 * Get "class" attribute in view context.
	 *
	 * @uses static::_get()
	 * @return string
	 *
	 * @todo add test for returning null
	 */
	protected function get_class_attribute_for_view() {
		$classes = $this->_get( 'class' );

		if ( empty( $classes ) )
			return null;

		$classes = array_unique( $classes );                          // remove duplicates
		$classes = array_map( array( __CLASS__, 'trim' ), $classes ); // trim items
		$classes = array_filter( $classes );                          // renive enpty items
		$classes = implode( ' ', $classes );                          // implode

		return $classes;
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
	 *
	 * @todo add test for returning null
	 */
	protected function get_array_attribute_for_view( string $attribute, string $glue = ', ' ) {
		$value = $this->_get( $attribute );

		if ( empty( $value ) )
			return null;

		$value = array_map( array( __CLASS__, 'trim' ), $value );
		return implode( $glue, $value );
	}

}

?>
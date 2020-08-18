<?php
/**
 * Abstract class for the helper methods.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || die();

/**
 * Abstract class: Image_Tag_Helpers.
 */
abstract class Image_Tag_Helpers implements Image_Tag_Attributes_Interface, Image_Tag_Settings_Interface, Image_Tag_Validation_Interface {


	/*
	   ###    ######## ######## ########  #### ########  ##     ## ######## ########  ######
	  ## ##      ##       ##    ##     ##  ##  ##     ## ##     ##    ##    ##       ##    ##
	 ##   ##     ##       ##    ##     ##  ##  ##     ## ##     ##    ##    ##       ##
	##     ##    ##       ##    ########   ##  ########  ##     ##    ##    ######    ######
	#########    ##       ##    ##   ##    ##  ##     ## ##     ##    ##    ##             ##
	##     ##    ##       ##    ##    ##   ##  ##     ## ##     ##    ##    ##       ##    ##
	##     ##    ##       ##    ##     ## #### ########   #######     ##    ########  ######
	*/

	/**
	 * Store for attributes.
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * Store for attribute defaults.
	 * @var array
	 */
	protected $attribute_defaults = array(
		'alt' => '',
	);

	/**
	 * List of attribute names expected to be stored as arrays.
	 * @var string[]
	 */
	protected $attribute_arrays = array(
		'class',
		'sizes',
		'style',
		'srcset',
		'data-sizes',
		'data-srcset',
	);

	/**
	 * Remove typical leading/trailing characters from attribute.
	 *
	 * @see trim()
	 * @param mixed Attribute value (passed by reference).
	 * @return mixed
	 *
	 * @codeCoverageIgnore
	 */
	protected static function trim_attribute( &$value ) {
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
				$item = static::trim_attribute( $item );

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
	 *
	 * @codeCoverageIgnore
	 */
	protected static function explode_deep( array $array, string $delimeter = ',' ) : array {
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

	/**
	 * Check if attribute is set.
	 *
	 * @see Image_Tag_Test_Attributes::test__has_attribute()
	 * @param string $name Attribute name.
	 * @return bool
	 */
	function has_attribute( string $name ) : bool {
		return isset( $this->attributes[ $name ] );
	}

	/**
	 * Get specified attribute values.
	 *
	 * @see Image_Tag_Test_Attributes::test__get_attributes()
	 * @param array $names Names of attributes to retrieve.
	 * @param string $context Context of request.
	 * @uses $this->get_attribute()
	 * @return array
	 */
	function get_attributes( $names = array(), string $context = 'view' ) : array {
		if ( empty( $names ) )
			$names = array_keys( wp_parse_args(
				$this->attributes,
				$this->attribute_defaults
			) );

		$attributes = array();

		foreach ( ( array ) $names as $name )
			$attributes[ $name ] = $this->get_attribute( $name, $context );

		return $attributes;
	}

	/**
	 * Get specific attribute value.
	 *
	 * @see Image_Tag_Test_Attributes::test__get_attribute()
	 * @param string $name Attribute name.
	 * @param string $context Context of request.
	 * @uses $this->has_attribute()
	 * @return mixed
	 */
	function get_attribute( string $name, string $context = 'view' ) {
		$functions = array();
		$attribute_type = null;

		# Check for function for specific attribute, ex: get_class_attribute().
		$function_name = sprintf( 'get_%s_attribute', $name );
		$functions[ $function_name ] = array( $context );

		# Get variable type from current value.
		if ( isset( $this->attributes[ $name ] ) )
			$attribute_type = gettype( $this->attributes[ $name ] );

		# Check if variable is expected to be array.
		if ( in_array( $name, $this->attribute_arrays ) )
			$attribute_type = 'array';

		# Check for function for specific variable type, ex: get_attribute_from_array().
		if ( is_string( $attribute_type ) ) {
			$function_name = sprintf( 'get_attribute_from_%s', $attribute_type );
			$functions[ $function_name ] = array(
				$name,
				$context,
			);
		}

		# Check for attribute specific functions.
		foreach ( $functions as $function_name => $function_params ) {
			if ( !is_callable( array( $this, $function_name ) ) )
				continue;

			return call_user_func_array( array( $this, $function_name ), $function_params );
		}

		# If no set value, return default.
		if (
			!$this->has_attribute( $name )
			&& isset( $this->attribute_defaults[ $name ] )
		)
			return $this->attribute_defaults[ $name ];

		return $this->attributes[ $name ];
	}

		/**
		 * Get 'class' attribute.
		 *
		 * @param string $context
		 * @return array|string
		 *
		 * @codeCoverageIgnore
		 */
		protected function get_class_attribute( string $context ) {
			if ( 'view' !== $context )
				return $this->attributes['class'];

			return implode( ' ', $this->attributes['class'] );
		}

		/**
		 * Get 'style' attribute.
		 *
		 * @param string $context
		 * @return array|string
		 *
		 * @codeCoverageIgnore
		 */
		protected function get_style_attribute( string $context ) {
			if ( 'view' !== $context )
				return $this->attributes['style'];

			return implode( '; ', $this->attributes['style'] );
		}

		/**
		 * Get attribute from array.
		 *
		 * @param string $name Attribute name.
		 * @param string $context
		 * @return array|string
		 *
		 * @codeCoverageIgnore
		 */
		protected function get_attribute_from_array( string $name, string $context ) {
			if ( 'view' !== $context )
				return $this->attributes[ $name ];

			return implode( ', ', $this->attributes[ $name ] );
		}

	/**
	 * Set multiple attributes.
	 *
	 * @see Image_Tag_Test_Attributes::test__set_attributes()
	 * @param array $attributes Array of attribute names and values to set.
	 * @uses $this->set_attribute()
	 * @return Image_Tag
	 */
	function set_attributes( array $attributes ) : Image_Tag {
		foreach ( $attributes as $name => $value )
			$this->set_attribute( $name, $value );

		return $this;
	}

	/**
	 * Set attribute.
	 *
	 * @see Image_Tag_Test_Attributes::test__set_attribute()
	 * @param string $name Attribute name.
	 * @param mixed $value Attribute value.
	 * @return Image_Tag
	 */
	function set_attribute( string $name, $value ) : Image_Tag {
		$functions = array();
		$attribute_type = null;

		# Check for function for specific attribute, ex: get_class_attribute().
		$function_name = sprintf( 'set_%s_attribute', $name );
		$functions[ $function_name ] = array( $value );

		# Get variable type from current value.
		if ( isset( $this->attributes[ $name ] ) )
			$attribute_type = gettype( $this->attributes[ $name ] );

		# Check if variable is expected to be array.
		if ( in_array( $name, $this->attribute_arrays ) )
			$attribute_type = 'array';

		# Check for function for specific variable type, ex: get_attribute_from_array().
		if ( is_string( $attribute_type ) ) {
			$function_name = sprintf( 'set_attribute_to_%s', $attribute_type );
			$functions[ $function_name ] = array(
				$name,
				$value,
			);
		}

		# Check for attribute specific functions.
		foreach ( $functions as $function_name => $function_params ) {
			if ( !is_callable( array( $this, $function_name ) ) )
				continue;

			call_user_func_array( array( $this, $function_name ), $function_params );
			return $this;
		}

		$this->attributes[ $name ] = $value;

		return $this;
	}

		/**
		 * Set attribute to array.
		 *
		 * @param string $name
		 * @param mixed $value
		 * @param string $delimeter
		 *
		 * @codeCoverageIgnore
		 */
		protected function set_attribute_to_array( string $name, $value, string $delimeter = ',' ) : void {
			if ( empty( $value ) ) {
				$this->attributes[ $name ] = array();
				return;
			}

			if ( is_string( $value ) )
				$value = explode( $delimeter, $value );

			else if ( is_array( $value ) )
				$value = static::explode_deep( $value, $delimeter );

			if ( !is_array( $value ) ) {
				trigger_error( sprintf( 'Value of type <code>%s</code> is not valid for <code>%s</code> attribute.', gettype( $value ), $attribute ) );
				return;
			}

			$value = static::trim_attribute( $value ); // remove excess characters from items
			$value = array_filter( $value ); // remove empty items
			$value = array_values( $value ); // reset array keys

			$this->attributes[ $name ] = $value;
		}


	/*
	 ######  ######## ######## ######## #### ##    ##  ######    ######
	##    ## ##          ##       ##     ##  ###   ## ##    ##  ##    ##
	##       ##          ##       ##     ##  ####  ## ##        ##
	 ######  ######      ##       ##     ##  ## ## ## ##   ####  ######
	      ## ##          ##       ##     ##  ##  #### ##    ##        ##
	##    ## ##          ##       ##     ##  ##   ### ##    ##  ##    ##
	 ######  ########    ##       ##    #### ##    ##  ######    ######
	*/

	/**
	 * Store for settings.
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Store for setting defaults.
	 * @var array
	 */
	protected $setting_defaults = array();

	/**
	 * Check setting is set.
	 *
	 * @see Image_Tag_Test_Settings::test__has_setting()
	 * @param string $key Setting key.
	 * @return bool
	 */
	function has_setting( string $key ) : bool {
		return isset( $this->settings[ $key ] );
	}

	/**
	 * Get specified setting values.
	 *
	 * @see Image_Tag_Test_Settings::test__get_settings()
	 * @param array $keys Setting keys.
	 * @uses $this->get_setting()
	 * @return array
	 */
	function get_settings( $keys = array() ) : array {
		if ( empty( $keys ) )
			$keys = array_keys( wp_parse_args(
				$this->settings,
				$this->setting_defaults
			) );

		$settings = array();

		foreach ( $keys as $key )
			$settings[ $key ] = $this->get_setting( $key );

		return $settings;
	}

	/**
	 * Get specified setting value.
	 *
	 * @see Image_Tag_Test_Settings::test__get_setting()
	 * @param string $key
	 * @uses $this->has_setting()
	 * @return mixed
	 */
	function get_setting( string $key ) {
		if (
			!$this->has_setting( $key )
			&& isset( $this->setting_defaults[ $key ] )
		)
			return $this->setting_defaults[ $key ]; // @codeCoverageIgnore

		return $this->settings[ $key ];
	}

	/**
	 * Set multiple settings.
	 *
	 * @see Image_Tag_Test_Settings::test__set_settings()
	 * @param array $settings
	 * @uses $this->set_setting()
	 * @return Image_Tag
	 */
	function set_settings( array $settings ) : Image_Tag {
		foreach ( $settings as $key => $value )
			$this->set_setting( $key, $value );

		return $this;
	}

	/**
	 * Set specified setting.
	 *
	 * @see Image_Tag_Test_Settings::test__set_setting()
	 * @param string $key
	 * @param mixed $value
	 * @return Image_Tag
	 */
	function set_setting( string $key, $value ) : Image_Tag {
		$function_name = sprintf( 'set_%s_setting', $key );

		if ( is_callable( array( $this, $function_name ) ) )
			call_user_func( array( $this, $function_name ), $value ); // @codeCoverageIgnore
		else
			$this->settings[ $key ] = $value;

		return $this;
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

	/**
	 * Keywords to identify image type.
	 * @var string[]
	 */
	const TYPES = array();

	/**
	 * Get primary image type.
	 *
	 * @see Image_Tag_Test_Validation::test__get_type()
	 * @return string
	 */
	function get_type() : string {
		return static::TYPES[0];
	}

	/**
	 * Check if image is one of specified types.
	 *
	 * @see Image_Tag_Test_Validation::test__is_type()
	 * @param string[]|string $test_types
	 * @return bool
	 */
	function is_type( $test_types ) : bool {
		return !empty( array_intersect( static::TYPES, ( array ) $test_types ) );
	}

	/**
	 * Perform validation checks.
	 *
	 * @uses $this->perform_validation_checks()
	 * @return WP_Error|true
	 *
	 * @codeCoverageIgnore
	 */
	 protected function check_valid() {
		$errors = $this->perform_validation_checks();

		if ( $errors->has_errors() )
			return $errors;

		return true;
	}

	/**
	 * Perform validation checks.
	 *
	 * @return WP_Error
	 *
	 * @codeCoverageIgnore
	 */
	protected function perform_validation_checks() : WP_Error {
		$errors = new WP_Error;

		if (
			!$this->has_attribute( 'src' )
			|| empty( $this->get_attribute( 'src' ) )
		)
			$errors->add( 'required_src', 'The <code>src</code> attribute is required.' );

		return $errors;
	}

	/**
	 * Check if image tag is valid.
	 *
	 * @see Image_Tag_Test_Validation::test__is_valid()
	 * @param null|string[]|string $types
	 * @uses $this->is_type()
	 * @uses $this->check_valid()
	 * @return bool
	 */
	function is_valid( $test_types = null ) : bool {
		if (
			   !is_null( $test_types )
			&& !$this->is_type( $test_types )
		)
			return false;

		return true === $this->check_valid();
	}


}
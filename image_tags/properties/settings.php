<?php

class Image_Tag_Settings extends Image_Tag_Properties_Abstract {

	const NAME = 'setting';
	const DEFAULTS = array(
		'before_output' => array(),
		'after_output' => array(),
	);


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
	 * @uses Image_Tag_Properties::__get()
	 * @return mixed
	 */
	function __get( string $setting ) {
		if ( 'settings' === $setting )
			return $this->properties;

		return parent::__get( $setting );
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
	 * Get settings.
	 *
	 * @param string|array $settings
	 * @param string $context view|edit
	 * @uses Image_Tag_Properties::get()
	 * @return string|array
	 *
	 * @todo add test for 'edit' context and filter
	 */
	function get( $settings = null, string $context = 'edit' ) {
		$value = is_string( $settings )
			? $this->get_property( $settings, $context )
			: $this->get_properties( $settings, $context );

		# Return attributes immediately.
		if ( 'edit' === $context )
			return $value;

		# If not a list of settings, no processing to do.
		if ( is_string( $settings ) )
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
	 * Get settings.
	 *
	 * @param string|array $keys
	 * @return array
	 */
	protected function get_properties( array $keys = null, string $context = 'edit' ) {
		if ( is_null( $keys ) )
			$keys = array_keys( $this->properties );

		$settings = array();

		foreach ( $keys as $key )
			$settings[$key] = $this->get_property( $key, $context );

		return $settings;
	}

	/**
	 * Get setting.
	 */
	function get_property( string $setting, string $context = 'edit' ) {
		$value = parent::get_property( $setting );

		# If edit context or null, return.
		if (
			is_null( $value )
			|| 'edit' === $context
		)
			return $value;

		$format = sprintf( 'get_%%s_%s_for_view', static::NAME );

		# Override by property name.
		$method_name = sprintf( $format, static::function_name( $setting ) );
		if ( method_exists( $this, $method_name ) )
			return call_user_func( array( $this, $method_name ) );

		# No overrides; return direct value.
		return $value;
	}


	/*
	 #######  ##     ## ######## ########  ##     ## ########
	##     ## ##     ##    ##    ##     ## ##     ##    ##
	##     ## ##     ##    ##    ##     ## ##     ##    ##
	##     ## ##     ##    ##    ########  ##     ##    ##
	##     ## ##     ##    ##    ##        ##     ##    ##
	##     ## ##     ##    ##    ##        ##     ##    ##
	 #######   #######     ##    ##         #######     ##
	*/

	/**
	 * Set "before_output" setting.
	 *
	 * @param string|array $value
	 * @uses static::_set()
	 * @uses static::add_output()
	 */
	protected function set_before_output_setting( $value ) {
		if ( empty( $value ) ) {
			$this->_set( 'before_output', array() );
			return;
		}

		$this->add_output( 'before', $value );
	}

	/**
	 * Set "after_output" setting.
	 *
	 * @param string|array $value
	 * @uses static::_set()
	 * @uses static::add_output()
	 */
	protected function set_after_output_setting( $value ) {
		if ( empty( $value ) ) {
			$this->_set( 'after_output', array() );
			return;
		}

		$this->add_output( 'after', $value );
	}

	/**
	 * Add to "before_output" setting.
	 *
	 * @param mixed $add_values
	 * @uses static::add_output()
	 */
	protected function add_to_before_output_setting( $add_values ) {
		$this->add_output( 'before', $add_values );
	}

	/**
	 * Add to "after_output" setting.
	 *
	 * @param mixed $add_values
	 * @uses static::add_output()
	 */
	protected function add_to_after_output_setting( $add_values ) {
		$this->add_output( 'after', $add_values );
	}

	/**
	 * Add value(s) to "{before|after}_output" setting.
	 *
	 * @param string $prosition before|after
	 * @param string|array $add_values
	 * @param int $priority
	 * @uses static::get_property()
	 * @uses static::_set()
	 * @return $this
	 *
	 * @todo add test for chaining
	 */
	function add_output( string $position, $add_values, int $priority = 10 ) {

		# Get current value.
		$value = $this->get_property( $position . '_output', 'edit' );

		# If adding value is a string, convert to array.
		if ( !is_array( $add_values ) )
			$add_values = array( $priority => $add_values );

		# Function to remove empty and invalid items.
		$map = function( $item ) use( &$map ) {
			if ( is_array( $item ) )
				return array_filter( array_map( $map, $item ) );

			if (
				!is_string( $item )
				|| empty( trim( $item ) )
			)
				return null;

			return $item;
		};

		# Remove empty and invalid items.
		$add_values = array_map( $map, $add_values );

		# Add each value to array.
		foreach ( $add_values as $priority => $add_value ) {
			if ( !is_integer( $priority ) )
				$priority = 10;

			if ( !isset( $value[$priority] ) )
				$value[$priority] = array();

			# Add string to array.
			if ( is_string( $add_value ) ) {
				$value[$priority][] = $add_value;
				continue;
			}

			# Copy all items in order into return array.
			array_walk_recursive( $add_value, function( $item ) use( &$value, $priority ) {
				$value[$priority][] = $item;
			} );
		}

		# Sort array by numeric order of keys.
		ksort( $value, SORT_NUMERIC );

		$this->_set( $position . '_output', $value );

		return $this;
	}

	/**
	 * Override "before_output" value in "view" context.
	 *
	 * @uses static::get_output()
	 * @return null|string
	 */
	protected function get_before_output_setting_for_view() {
		return $this->get_output( 'before' );
	}

	/**
	 * Override "after_output" value in "view" context.
	 *
	 * @uses static::get_output()
	 * @return null|string
	 */
	protected function get_after_output_setting_for_view() {
		return $this->get_output( 'after' );
	}

	/**
	 * Get output.
	 *
	 * @param string $position before|after
	 * @uses static::_get()
	 * @return null|string
	 */
	protected function get_output( string $position ) {
		$value = $this->_get( $position . '_output' );

		# If empty, return null.
		if ( empty( array_filter( $value ) ) )
			return null;

		$array = array();

		# Copy all items in order into return array.
		array_walk_recursive( $value, function( $item ) use( &$array ) {
			$array[] = $item;
		} );

		return "\n" . trim( implode( "\n", array_filter( $array ) ) ) . "\n";
	}

}

?>
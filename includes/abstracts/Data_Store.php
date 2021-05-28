<?php

declare( strict_types=1 );

namespace Image_Tag\Abstracts;

defined( 'WPINC' ) || die();

/**
 * Abstract class: Image_Tag\Abstracts\Data_Store
 */
class Data_Store implements \Image_Tag\Interfaces\Data_Store {

	/**
	 * @var array Data storage.
	 */
	protected $store = array();

	/**
	 * Construct.
	 *
	 * @param array|object $data
	 */
	function __construct( $data ) {
		if (
			is_object( $data )
			&& is_a( $data, self::class )
		) {
			$this->store = $data->store;
			return;
		}

		if ( !is_array( $data ) )
			return;

		$this->store = $data;
	}

	/**
	 * Getter.
	 *
	 * @param string $key
	 * @return mixed
	 */
	function __get( string $key ) {
		return $this->$key;
	}

	/**
	 * Setter.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	function __set( string $key, $value ) : void {
		$this->store[ $key ] = $value;
	}

	/**
	 * To string.
	 *
	 * @uses $this->output()
	 * @return string
	 */
	function __toString() : string {
		return $this->output();
	}

	/**
	 * Set key/value pair.
	 *
	 * @param string|array $set
	 * @param null|mixed $value
	 * @return self
	 */
	function set( $set, $value = null ) : self {
		if ( is_array( $set ) ) {
			foreach ( $set as $key => $value )
				if ( !array_key_exists( $key, $this->store ) )
					$this->store[ $key ] = $value;

			return $this;
		}

		if (
			!is_string( $set )
			|| array_key_exists( $set, $this->store )
		)
			return $this;

		if ( is_null( $value ) ) {
			trigger_error( sprintf( 'Cannot set value of <code>null</code> for key <code>%s</code>.', $set ), E_USER_NOTICE );
			return $this;
		}

		$this->store[ $set ] = $value;

		return $this;
	}

	/**
	 * Update key/value pair.
	 *
	 * @param string|array $update
	 * @param null|mixed $value
	 * @return self
	 */
	function update( $update, $value = null ) : self {
		if ( is_array( $update ) ) {
			foreach ( $update as $key => $value )
				$this->store[ $key ] = $value;

			return $this;
		}

		if ( is_null( $value ) ) {
			trigger_error( sprintf( 'Cannot update value to <code>null</code> for key <code>%s</code>.', $update ), E_USER_NOTICE );
			return $this;
		}

		if ( !is_string( $update ) )
			return $this;

		$this->store[ $update ] = $value;

		return $this;
	}

	/**
	 * Check if key or value exists in store.
	 *
	 * @param string|array $has
	 * @param bool $check_value
	 * @return bool|array
	 */
	function has( $has = null, bool $check_value = true ) {
		if ( in_array( $has, array( null, '', array() ) ) )
			return !empty( $this->store );

		if ( is_string( $has ) )
			return (
				array_key_exists( $has, $this->store )
				|| (
					$check_value
					&& in_array( $has, $this->store )
				)
			);

		$output = array();

		foreach ( $has as $key )
			$output[ $key ] = (
				array_key_exists( $key, $this->store )
				|| (
					$check_value
					&& in_array( $key, $this->store )
				)
			);

		return $output;
	}

	/**
	 * Get data by key.
	 *
	 * @return mixed
	 */
	function get( $get = array() ) {
		if ( array() === $get )
			return $this->store;

		if ( is_string( $get ) )
			return $this->store[ $get ];

		$output = array();

		foreach ( $get as $key )
			$output[ $key ] = $this->store[ $key ];

		return $output;
	}

	/**
	 * Append value onto key.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param string $glue
	 * @uses $this->has()
	 * @uses $this->set()
	 * @uses $this->get()
	 * @uses $this->update()
	 * @return self
	 */
	function append( string $key, $value, string $glue = ' ' ) : self {
		if ( !$this->has( $key ) ) {
			$this->set( $key, $value );
			return $this;
		}

		$update_value_with  = $this->get( $key );
		$update_value_with .= $glue;
		$update_value_with .= $value;

		$this->update( $key, $update_value_with );

		return $this;
	}

	function remove( string $key ) : self {
		unset( $this->store[ $key ] );
		return $this;
	}

}
<?php

declare( strict_types=1 );

namespace Image_Tag\Abstracts;

class Data_Store implements \Image_Tag\Interfaces\Data_Store {

	protected $store = array();

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

	function __get( string $key ) {
		return $this->store;
	}

	function __set( string $key, $value ) {
		$this->store[ $key ] = $value;
	}

	function __toString() : string {
		return $this->output();
	}

	function set( $set, $value = null ) : self {
		if ( is_array( $set ) ) {
			foreach ( $set as $key => $value )
				if ( !array_key_exists( $key, $this->store ) )
					$this->store[ $key ] = $value;

			return $this;
		}

		if ( !is_string( $set ) )
			return $this;

		if ( is_null( $value ) ) {
			trigger_error( sprintf( 'Cannot set value of <code>null</code> for key <code>%s</code>.', $set ), E_USER_NOTICE );
			return $this;
		}

		$this->store[ $set ] = $value;

		return $this;
	}

	function update( $update, $value = null ) : self {
		if ( is_array( $update ) ) {
			foreach ( $update as $key => $value )
				$this->store[ $key ] = $value;

			return $this;
		}

		if ( is_null( $value ) ) {
			trigger_error( sprintf( 'Cannot set value of <code>null</code> for key <code>%s</code>.', $set ), E_USER_NOTICE );
			return $this;
		}

		if ( !is_string( $update ) )
			return $this;

		$this->store[ $update ] = $value;
	}

	function has( $has = array() ) {
		if ( array() === $has )
			return !empty( $this->store );

		if ( is_string( $has ) )
			return array_key_exists( $has, $this->store );

		$output = array();

		foreach ( $has as $key )
			$output[ $key ] = array_key_exists( $key, $this->store );

		return $output;
	}

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

	function output() : string {
		$output = array();

		foreach ( $this->get() as $key => $value )
			$output[] = sprintf( '%s="%s"', $key, $value );

		return implode( ' ', $output );
	}

}
<?php declare(strict_types=1);

namespace Image_Tag\Stores;

#[\AllowDynamicProperties]
abstract class Store_Abstract {

	/**
	 * Construct.
	 */
	public function __construct( $data = array() ) {
		if ( is_array( $data ) ) {
			$data = (object) $data;
		}

		foreach ( get_object_vars( $data ) as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * Check for data.
	 */
	public function has( string $key ) : bool {
		return isset( $this->$key );
	}

	public function get( string $key, $default = null ) : mixed {
		if ( ! $this->has( $key ) ) {
			return $default;
		}

		return $this->$key;
	}

	/**
	 * Add data (no overwrite).
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function add( string $key, $value ) : self {
		if ( $this->has( $key ) ) {
			return $this;
		}

		$this->set( $key, $value );

		return $this;
	}

	/**
	 * Set data (overwrite).
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function set( string $key, $value ) : self {
		$this->$key = $value;

		return $this;
	}

	/**
	 * Alias for set().
	 */
	public function update( string $key, $value ) : self {
		return $this->set( $key, $value );
	}

	/**
	 * Delete data.
	 *
	 * @param string $key
	 */
	public function unset( ...$keys ) : self {
		foreach ( $keys as $key ) {
			unset( $this->$key );
		}

		return $this;
	}

	/**
	 * Alias for unset().
	 *
	 * @param string|string[]
	 */
	public function delete( ...$keys ) : self {
		return $this->unset( $keys );
	}

}

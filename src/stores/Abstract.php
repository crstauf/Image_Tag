<?php declare(strict_types=1);

namespace Image_Tag\Stores;

#[\AllowDynamicProperties]
abstract class Store_Abstract {

	/**
	 * Add data (no overwrite).
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function add( string $key, $value ) : self {
		if ( isset( $this->$key ) ) {
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
		$this->set( $key, $value );

		return $this;
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
		$this->unset( $keys );

		return $this;
	}

}

<?php declare(strict_types=1);

namespace Image_Tag\Stores;

#[\AllowDynamicProperties]
abstract class Store_Abstract {

	/**
	 * Set data (override).
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function set( string $key, $value ) : void {
		$this->$key = $value;
	}

	/**
	 * Alias for set().
	 */
	public function update( string $key, $value ) : void {
		$this->set( $key, $value );
	}

	/**
	 * Delete data.
	 *
	 * @param string $key
	 */
	public function unset( string $key ) : void {
		unset( $this->$key );
	}

	/**
	 * Alias for unset().
	 */
	public function delete( string $key ) : void {
		$this->unset( $key );
	}

}

<?php

declare( strict_types=1 );

namespace Image_Tag\Interfaces;

defined( 'WPINC' ) || die();

/**
 * Interface: Image_Tag\Interfaces\Data_Store
 */
interface Data_Store {

	/**
	 * @param string $set
	 * @param mixed $value
	 */
	public function set( $set, $value = null ) : self;

	/**
	 * @param string $update
	 * @param mixed $value
	 */
	public function update( $update, $value = null ) : self;

	/**
	 * @param string $has
	 *
	 * @return mixed
	 */
	public function has( $has );

	/**
	 * @param string $get
	 *
	 * @return mixed
	 */
	public function get( $get );

	/**
	 * @param string $key
	 * @param string $value
	 * @param string $glue
	 */
	public function append( string $key, string $value, string $glue = ' ' ) : self;

	/**
	 * @param string $key
	 */
	public function remove( string $key ) : self;

}
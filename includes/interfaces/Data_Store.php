<?php

declare( strict_types=1 );

namespace Image_Tag\Interfaces;

defined( 'WPINC' ) || die();

/**
 * Interface: Image_Tag\Interfaces\Data_Store
 */
interface Data_Store {

	public function set( $set, $value = null ) : self;
	public function update( $update, $value = null ) : self;
	public function has( $has );
	public function get( $get );
	public function append( string $key, string $value, string $glue = ' ' ) : self;
	public function remove( string $key ) : self;

}
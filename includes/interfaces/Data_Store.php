<?php

declare( strict_types=1 );

namespace Image_Tag\Interfaces;

defined( 'WPINC' ) || die();

/**
 * Interface: Image_Tag\Interfaces\Data_Store
 */
interface Data_Store {

	function set( $set, $value = null ) : self;
	function update( $update, $value = null ) : self;
	function has( $has );
	function get( $get );

}
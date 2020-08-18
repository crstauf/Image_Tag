<?php
declare( strict_types=1 );

defined( 'ABSPATH' ) || die();

/**
 * Interface: Image_Tag_Attributes_Interface
 */
interface Image_Tag_Attributes_Interface {

	function has_attribute( string $name ) : bool;

	function get_attributes( $names = array(), string $context = 'view' ) : array;
	function get_attribute( string $name, string $context = 'view' );

	function set_attributes( array $attributes ) : Image_Tag;
	function set_attribute( string $name, $value ) : Image_Tag;

}
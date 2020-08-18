<?php
declare( strict_types=1 );

defined( 'ABSPATH' ) || die();

/**
 * Interface: Image_Tag_Settings_Interface
 */
interface Image_Tag_Settings_Interface {

	function has_setting( string $key ) : bool;

	function get_settings( array $keys = array() ) : array;
	function get_setting( string $key );

	function set_settings( array $settings ) : Image_Tag;
	function set_setting( string $key, $value ) : Image_Tag;

}
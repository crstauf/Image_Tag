<?php
declare( strict_types=1 );

defined( 'ABSPATH' ) || die();

/**
 * Interface: Image_Tag_Sources_Interface
 */
interface Image_Tag_Sources_Interface {

	function has_source( string $url ) : bool;
	function add_source( string $url, string $descriptor = '' ) : Image_Tag;
	function set_source( string $url, string $descriptor = '' ) : Image_Tag;
	function delete_source( string $url ) : Image_Tag;

	function get_sources() : array;

}
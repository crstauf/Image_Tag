<?php
/**
 * Image tag generator for WordPress images.
 */

defined( 'ABSPATH' ) || die();

/**
 * Abstract class: Image_Tag_WP
 */
abstract class _Image_Tag_WP extends _Image_Tag {

	/**
	 * @todo add test
	 */
	function is_type( $compare_types ) {
		if ( parent::is_type( $compare_types ) )
			return true;

		$actual_types = array(
			'wp',
			'local',
			'internal',
			'wordpress',
		);

		foreach ( ( array ) $compare_types as $type )
			if ( in_array( $type, $actual_types ) )
				return true;

		return false;
	}

}

?>
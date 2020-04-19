<?php
/**
 * Image tag generator for placeholder images.
 */

defined( 'ABSPATH' ) || die();

/**
 * Abstract class: Image_Tag_Placeholder
 */
abstract class _Image_Tag_Placeholder extends _Image_Tag {

	/**
	 * @todo add test
	 */
	function is_type( $compare_types ) {
		$actual_types = array(
			'remote',
			'external',
			'__placeholder',
		);

		foreach ( ( array ) $compare_types as $type )
			if ( in_array( $type, $actual_types ) )
				return true;

		return false;
	}

}

?>
<?php
/**
 * Image tag generator for Placeholder.com.
 *
 * @link https://placeholder.com
 */

defined( 'ABSPATH' ) || die();

require_once '_Image_Tag_Placeholder.php';

/**
 * Class: Image_Tag_Placeholder
 */
class Image_Tag_Placeholder extends _Image_Tag_Placeholder {

	/**
	 * @var string Base URL.
	 */
	const BASE_URL = 'https://via.placeholder.com/';

	/**
	 * @todo add test
	 */
	function get_type() {
		return 'placeholder';
	}

	/**
	 * @todo add test
	 */
	function is_type( $compare_types ) {
		if ( parent::is_type( $compare_types ) )
			return true;

		$actual_types = array(
			'placeholdit',
			'placehold.it',
			$this->get_type(),
			'placeholder.com',
		);

		foreach ( ( array ) $compare_types as $type )
			if ( in_array( $type, $actual_types ) )
				return true;

		return false;
	}

}
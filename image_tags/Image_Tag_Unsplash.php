<?php
/**
 * Image tag generator for Unsplash Source.
 *
 * @link https://source.unsplash.com/
 */

defined( 'ABSPATH' ) || die();

require_once '_Image_Tag_Placeholder.php';

/**
 * Class: Image_Tag_Unsplash
 */
class Image_Tag_Unsplash extends _Image_Tag_Placeholder {

	/**
	 * @var string Base URL.
	 */
	const BASE_URL = 'https://source.unsplash.com/';

	/**
	 * @todo add test
	 */
	function get_type() {
		return 'unsplash';
	}

	/**
	 * @todo add test
	 */
	function is_type( $compare_types ) {
		if ( parent::is_type( $compare_types ) )
			return true;

		$actual_types = array(
			$this->get_type(),
			'source-unsplash',
			'source.unsplash.com',
		);

		foreach ( ( array ) $compare_types as $type )
			if ( in_array( $type, $actual_types ) )
				return true;

		return false;
	}

}

?>
<?php
/**
 * Image tag generator for Picsum.photos.
 *
 * @link https://picsum.photos
 */

defined( 'ABSPATH' ) || die();

require_once '_Image_Tag_Placeholder.php';

/**
 * Class: Image_Tag_Picsum
 */
class Image_Tag_Picsum extends _Image_Tag_Placeholder {

	/**
	 * @var string Base URL.
	 */
	const BASE_URL = 'https://picsum.photos/';

	/**
	 * @todo add test
	 */
	function get_type() {
		return 'picsum';
	}

	/**
	 * @todo add test
	 */
	function is_type( $compare_types ) {
		if ( parent::is_type( $compare_types ) )
			return true;

		$actual_types = array(
			'lorem-picsum',
			'Lorem Picsum',
			'photos.picsum',
			$this->get_type(),
		);

		foreach ( ( array ) $compare_types as $type )
			if ( in_array( strtolower( $type ), $actual_types ) )
				return true;

		return false;
	}

}

?>
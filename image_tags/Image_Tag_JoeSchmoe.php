<?php
/**
 * Image tag generator for JoeSchmoe.
 *
 * @link https://joeschmoe.io/
 */

defined( 'ABSPATH' ) || die();

require_once '_Image_Tag_Placeholder.php';

/**
 * Class: Image_Tag_JoeSchmoe
 */
class Image_Tag_JoeSchmoe extends _Image_Tag_Placeholder {

	/**
	 * @var string Base URL.
	 */
	const BASE_URL = 'https://joeschmoe.io/api/v1/';

	/**
	 * @todo add test
	 */
	function get_type() {
		return 'joeschmoe';
	}

	/**
	 * @todo add test
	 */
	function is_type( $compare_types ) {
		if ( parent::is_type( $compare_types ) )
			return true;

		$actual_types = array(
			'avatar',
			'person',
			'profile',
			$this->get_type(),
		);

		foreach ( ( array ) $compare_types as $type )
			if ( in_array( $type, $actual_types ) )
				return true;

		return false;
	}

}

?>
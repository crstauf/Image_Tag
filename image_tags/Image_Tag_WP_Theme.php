<?php
/**
 * Image tag generator for WordPress theme images.
 */

defined( 'ABSPATH' ) || die();

require_once '_Image_Tag_WP.php';

/**
 * Class: Image_Tag_WP_Theme
 */
class Image_Tag_WP_Theme extends _Image_Tag_WP {

	/**
	 * @var string $path
	 */
	protected $path;

	/**
	 * @todo add test
	 */
	function get_type() {
		return 'theme';
	}

	/**
	 * @todo add test
	 */
	function is_type( $compare_types ) {
		if ( parent::is_type( $compare_types ) )
			return true;

		$actual_types = array(
			'wp-theme',
			'wordpress-theme',
			$this->get_type(),
		);

		foreach ( ( array ) $compare_types as $type )
			if ( in_array( $type, $actual_types ) )
				return true;

		return false;
	}

}

?>
<?php
/**
 * Image tag generator for WordPress images.
 */

defined( 'ABSPATH' ) || die();

/**
 * Abstract class: Image_Tag_WP
 */
abstract class Image_Tag_WP extends Image_Tag {

	/**
	 * @var null|string $orientation
	 */
	protected $orientation = null;

	/**
	 * Determine and set image orientation.
	 *
	 * @uses $this->get_ratio()
	 * @uses $this->add_class()
	 */
	function set_orientation() {
		$ratio = $this->get_ratio();

		if ( $ratio > 1 )
			$this->orientation = 'portrait';

		else if ( $ratio < 1 )
			$this->orientation = 'landscape';

		else if ( 1 === $ratio )
			$this->orientation = 'square';

		else
			$this->orientation = 'unknown';

		$this->add_class( 'orientation-' . $this->orientation );
	}

	/**
	 * Get common colors.
	 *
	 * @param string $path Path to image.
	 * @param int $count Number of colors to determine.
	 * @uses GetImageMostCommonColors->Get_Colors()
	 * @return array
	 */
	protected function _get_colors( string $path, int $count = 3 ) {
		static $util = null;
		require_once trailingslashit( dirname( __DIR__ ) ) . 'class-get-image-most-common-colors.php';

		if ( is_null( $util ) )
			$util = new GetImageMostCommonColors;

		$_colors = $util->Get_Colors( $path, $count );
		$colors = array();

		foreach ( $_colors as $color => $percentage )
			$colors['#' . $color] = $percentage;

		return $colors;
	}

	/**
	 * Public access to get common colors.
	 *
	 * Abstract to pass proper image path, and
	 * opportunity to implement caching.
	 *
	 * @param int $count
	 * @uses $this->_get_colors()
	 * @return array
	 */
	abstract function get_colors( int $count = 3 );

	/**
	 * Get most common color.
	 *
	 * @uses $this->get_colors()
	 * @return string
	 */
	function get_mode_color() {
		return array_keys( $this->get_colors() )[0];
	}

}

?>
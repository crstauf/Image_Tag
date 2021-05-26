<?php

declare( strict_types=1 );

namespace Image_Tag\Abstracts;

defined( 'WPINC' ) || die();

/**
 * Abstract class: Image_Tag\Abstracts\WordPress
 */
abstract class WordPress extends Base {

	protected $orientation = null;

	/**
	 * Identify common colors in image.
	 *
	 * @param string $path
	 * @param int $count
	 * @return array
	 */
	protected static function identify_colors( string $path, int $count = 5 ) : array {
		require_once \Image_Tag\Plugin::inc() . 'class-get-image-most-common-colors.php';

		$util = new GetImageMostCommonColors;
		$_colors = $util->Get_Colors( $path, $count );
		$colors = array();

		foreach ( $_colors as $color => $percentage )
			$colors['#' . $color] = $percentage;

		return $colors;
	}

	/**
	 * Set image orientation.
	 *
	 * @uses $this->get_ratio()
	 * @return void
	 */
	protected function set_orientation() : void {
		$ratio = $this->get_ratio();

		if ( $ratio > 1 )
			$this->orientation = 'portrait';

		else if ( $ratio < 1 )
			$this->orientation = 'landscape';

		else if ( 1 === $ratio )
			$this->orientation = 'square';

		else
			$this->orientation = 'unknown';
	}

	/**
	 * Get ratio of image dimensions: width divided by height.
	 *
	 * @return float
	 */
	abstract function get_ratio() : float;

	/**
	 * Get most common colors.
	 *
	 * @param int $count
	 * @uses static::identify_colors()
	 * @return array
	 */
	abstract function get_colors( int $count = 5 ) : array;

	/**
	 * Get most common color.
	 *
	 * @uses $this->get_colors()
	 * @return string
	 */
	function get_mode_color() : string {
		return array_keys( $this->get_colors() )[0];
	}

}
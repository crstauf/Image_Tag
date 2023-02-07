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
	protected static function identify_colors( string $path, int $count = 3 ) : array {
		require_once \Image_Tag\Plugin::inc() . 'class-get-image-most-common-colors.php';

		$util    = new \GetImageMostCommonColors;
		$_colors = $util->Get_Colors( $path, $count );

		if ( empty( $_colors ) ) {
			return array();
		}

		$colors = array();

		foreach ( $_colors as $color => $percentage ) {
			$colors[] = '#' . $color;
		}

		return $colors;
	}

	/**
	 * Generate LQIP and return base64 encoded string.
	 *
	 * @uses wp_get_image_editor()
	 * @return string
	 */
	protected static function generate_lqip( string $path ) : string {
		$editor = wp_get_image_editor( $path );
		$size   = $editor->get_size();
		$ratio  = $size['height'] / $size['width'];

		$resize_width  = 20;
		$resize_height = 20;

		if ( $size['width'] > $size['height'] ) {
			$resize_height = $resize_width * $ratio;
		} else if ( $size['width'] < $size['height'] ) {
			$resize_width = $resize_height * $ratio;
		}

		$editor->resize( $resize_width, $resize_height );
		$editor->set_quality( 50 );

		$path = $editor->generate_filename( 'lqip', get_temp_dir() );
		$editor->save( $path );

		$mime          = wp_get_image_mime( $path );
		$plain_encoded = base64_encode( file_get_contents( $path ) );
		$data64        = sprintf( 'data:%s;base64,%s', $mime, $plain_encoded );

		unlink( $path );

		return $data64;
	}

	/**
	 * Set image orientation.
	 *
	 * @uses $this->ratio()
	 * @return void
	 */
	protected function set_orientation() : void {
		$ratio = $this->ratio();

		if ( $ratio > 1 ) {
			$this->orientation = 'portrait';
		} else if ( $ratio < 1 ) {
			$this->orientation = 'landscape';
		} else if ( 1 === $ratio ) {
			$this->orientation = 'square';
		} else {
			$this->orientation = 'unknown';
		}
	}

	/**
	 * Get ratio of image dimensions: width divided by height.
	 *
	 * @return float
	 */
	abstract public function ratio() : float;

	/**
	 * Get most common colors.
	 *
	 * @param int $count
	 * @uses static::identify_colors()
	 * @return array
	 */
	abstract public function colors( int $count = 5 ) : array;

	/**
	 * Get most common color.
	 *
	 * @uses $this->get_colors()
	 * @return string
	 */
	public function mode_color() : string {
		$colors = $this->colors();
		return $colors[0];
	}

	/**
	 * Get encoded LQIP.
	 *
	 * @return string
	 */
	abstract public function lqip() : string;

}
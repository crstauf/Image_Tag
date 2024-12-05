<?php declare(strict_types=1);

namespace Image_Tag\Traits;

trait LQIP {

	use Local;

	/** @var null|string */
	protected ?string $lqip = null;

	public function lqip() : string {
		if ( is_string( $this->lqip ) ) {
			return $this->lqip;
		}

		$cache_key = sprintf( 'lqip_%s', hash( 'md5', $this->path ) );
		$lqip      = get_transient( $cache_key );

		if ( ! empty( $lqip ) && is_string( $lqip ) ) {
			return $this->lqip = $lqip;
		}

		$this->generate_lqip();

		if ( ! is_string( $this->lqip ) ) {
			return '';
		}

		set_transient( $cache_key, $this->lqip );

		return $this->lqip;
	}

	protected function generate_lqip( int $resize_width = 20, int $resize_height = 20 ) : void {
		if ( is_string( $this->lqip ) ) {
			return;
		}

		$path = $this->path();

		if ( $this->has_alpha_channel( $path ) ) {
			return;
		}

		$editor = wp_get_image_editor( $path );
		$path   = $editor->generate_filename( 'lqip', get_temp_dir() );

		if ( is_wp_error( $editor ) || ! is_string( $path ) || empty( $path ) ) {
			return;
		}

		$size  = $editor->get_size();
		$ratio = absint( $size['width'] ) / absint( $size['height'] );

		if ( $ratio > 1 ) {
			$resize_height = $size['width'] * $ratio;
		} else if ( $ratio < 1 ) {
			$resize_width = $size['height'] * $ratio;
		}

		$editor->resize( $resize_width, $resize_height );
		$editor->set_quality( 50 );
		$editor->save( $path );

		$contents = ( string ) file_get_contents( $path );

		if ( empty( $contents ) ) {
			return;
		}

		$mime   = wp_get_image_mime( $path );
		$base64 = base64_encode( $contents );
		$data64 = sprintf( 'data:%s;base64,%s', $mime, $base64 );

		unlink( $path );

		$this->lqip = $data64;
	}

	/**
	 * Check if image has alpha transparency.
	 *
	 * Used to prevent LQIP.
	 *
	 * @param string $path
	 *
	 * @return bool
	 */
	public function has_alpha_channel( string $path = '' ) : bool {
		if ( empty( $path ) ) {
			$path = $this->path;
		}

		require_once ABSPATH . WPINC . '/class-wp-image-editor.php';
		require_once ABSPATH . WPINC . '/class-wp-image-editor-imagick.php';

		$imagick = new \WP_Image_Editor_Imagick( $path );

		if ( ! $imagick->test() ) {
			return false;
		}

		$object = new \Imagick();
		$object->readImage( $path );

		return ( bool ) $object->getImageAlphaChannel();
	}

}

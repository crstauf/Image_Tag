<?php declare(strict_types=1);

namespace Image_Tag\Traits;

require_once 'Local.php';

use Image_Tag\Manager;

trait Common_Colors {

	use Local;

	/** @var string[] */
	protected array $colors = array();

	protected function identify_colors( int $count = 3 ) : void {
		require_once dirname( __DIR__ ) . '/class-get-image-most-common-colors.php';

		$util    = new \GetImageMostCommonColors;
		$_colors = $util->Get_Colors( $this->path(), $count );

		if ( empty( $_colors ) ) {
			$this->colors = array();
			return;
		}

		$colors = array();

		foreach ( $_colors as $color => $percentage ) {
			$colors[] = '#' . $color;
		}

		$this->colors = $colors;
	}

	/**
	 * Get most common colors.
	 *
	 * @param int $count
	 * @return string|string[]
	 */
	public function colors( int $count = 3 ) : string|array {
		if ( ! empty( $this->colors ) && count( $this->colors ) >= $count ) {
			return array_slice( $this->colors, 0, $count );
		}

		$this->cached_colors();

		if ( ! empty( $this->colors ) && count( $this->colors ) >= $count ) {
			return array_slice( $this->colors, 0, $count );
		}

		$this->identify_colors( $count );

		if ( empty( $this->colors ) ) {
			return array();
		}

		$this->cache_colors();

		if ( 1 === $count ) {
			return $this->colors[0];
		}

		return $this->colors;
	}

	protected function cached_colors() : void {
		$cache_key = sprintf( 'common_colors_%s', hash( 'md5', $this->path() ) );
		$colors    = get_transient( $cache_key );

		if ( empty( $colors ) || ! is_array( $colors ) ) {
			return;
		}

		$this->colors = $colors;
	}

	protected function cache_colors() : void {
		$cache_key = sprintf( 'common_colors_%s', hash( 'md5', $this->path() ) );

		set_transient( $cache_key, $this->colors );
	}

}

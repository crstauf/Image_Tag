<?php

declare( strict_types=1 );

namespace Image_Tag\Data_Stores;

use Image_Tag\Abstracts\Data_Store;

/**
 * Class: Image_Tag\Data_Stores\Attributes
 */
class Attributes extends Data_Store {

	/**
	 * Output attributes.
	 *
	 * @return string
	 */
	public function output() : string {
		$prefix = '';
		$output = array();

		foreach ( $this->get() as $key => $value ) {
			$output[] = sprintf( '%s="%s"', $key, $value );
		}

		$glue = ' ';

		if ( defined( 'WP_DEBUG' ) && constant( 'WP_DEBUG' ) ) {
			$prefix = PHP_EOL;
			$glue  .= $prefix;
		}

		return $prefix . implode( $glue, $output );
	}

}

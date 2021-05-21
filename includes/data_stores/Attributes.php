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
	function output() : string {
		$output = array();

		foreach ( $this->get() as $key => $value )
			$output[] = sprintf( '%s="%s"', $key, $value );

		return implode( ' ', $output );
	}

}
<?php declare(strict_types=1);

namespace Image_Tag\Stores;

require_once 'Abstract.php';

class Attributes extends Store_Abstract {

	public function __toString() : string {
		$array = array();

		foreach ( get_object_vars( $this ) as $attr => $value ) {
			$array[] = sprintf( '%s="%s"', $attr, esc_attr( $value ) );
		}

		return implode( ' ', $array );
	}

}

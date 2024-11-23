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

	public function move( string $source_key, string $dest_key, ?string $default = '' ) : self {
		if ( ! isset( $this->$source_key ) ) {
			$this->set( $dest_key, $default );
			return $this;
		}

		$this->add( $dest_key, $this->$source_key );
		$this->unset( $source_key );

		return $this;
	}

	public function append( string $key, int|string $value, string $glue = ' ' ) : self {
		if ( ! $this->has( $key ) ) {
			return $this->set( $key, $value );
		}

		$value_with_appended = $this->get( $key );
		$value_with_appended .= $glue;
		$value_with_appended .= $value;

		return $this->update( $key, $value_with_appended );
	}

}

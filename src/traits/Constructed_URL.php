<?php declare(strict_types=1);

namespace Image_Tag\Traits;

trait Constructed_URL {

	/** @var string[] */
	protected array $constructed_url;

	protected function url_segment( null|int|string $var ) : self {
		if ( is_null( $var ) ) {
			return $this;
		}

		$this->constructed_url[] = $var;

		return $this;
	}

	protected function set_constructed_url( array $args = array() ) : self {
		$url = implode( '/', $this->constructed_url );
		$url = add_query_arg( $args, $url );

		$this->attribute( 'src', $url );

		return $this;
	}

}

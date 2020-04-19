<?php
/**
 * Image tag generator for Unsplash Source.
 *
 * @link https://source.unsplash.com/
 */

defined( 'ABSPATH' ) || die();

require_once '_Image_Tag_Placeholder.php';

/**
 * Class: Image_Tag_Unsplash
 */
class Image_Tag_Unsplash extends _Image_Tag_Placeholder {

	/**
	 * @var string Base URL.
	 */
	const BASE_URL = 'https://source.unsplash.com/';

	/**
	 * @var array
	 */
	protected $settings = array(
		'image_id' => null,
		'user' => null,
		'user_likes' => null,
		'collection' => null,
		'update' => null,
		'featured' => false,
		'width' => null,
		'height' => null,
		'search' => array(),
	);

	/**
	 * @todo add test
	 */
	function get_type() {
		return 'unsplash';
	}

	/**
	 * @todo add test
	 */
	function is_type( $compare_types ) {
		if ( parent::is_type( $compare_types ) )
			return true;

		$actual_types = array(
			$this->get_type(),
			'source-unsplash',
			'unsplash source',
			'source.unsplash.com',
		);

		foreach ( ( array ) $compare_types as $type )
			if ( in_array( strtolower( $type ), $actual_types ) )
				return true;

		return false;
	}

	/**
	 * @todo add test
	 */
	function is_valid() {
		return (
			   !empty( $this->get_width() )
			&& !empty( $this->get_height() )
		);
	}

	/**
	 * @todo add test
	 */
	function get_src_attribute() {
		if ( empty( $this->_get_attribute( 'src' ) ) )
			return $this->generate_url();

		return $this->_get_attribute( 'src' );
	}

	/**
	 * @todo add test
	 */
	protected function generate_url() {
		$src = self::BASE_URL;

		# Add ID.
		if ( !empty( $this->get_setting( 'image_id' ) ) )
			$src .= urlencode( $this->get_setting( 'image_id' ) ) . '/';

		else if ( !empty( $this->get_setting( 'user' ) ) )
			$src .= sprintf( 'user/%s/', urlencode( $this->get_setting( 'user' ) ) );

		else if ( !empty( $this->get_setting( 'user_likes' ) ) )
			$src .= sprintf( 'user/%s/likes/', urlencode( $this->get_setting( 'user_likes' ) ) );

		else if ( !empty( $this->get_setting( 'collection' ) ) )
			$src .= sprintf( 'collection/%d/', urlencode( $this->get_setting( 'collection' ) ) );

		if ( ( bool ) $this->get_setting( 'featured' ) )
			$src .= 'featured/';

		if (
			   !empty( $this->get_setting( 'width' ) )
			&& !empty( $this->get_setting( 'height' ) )
		)
			$src .= sprintf( '%dx%d/', $this->get_setting( 'width' ), $this->get_setting( 'height' ) );

		if ( !empty( $this->get_setting( 'update' ) ) )
			$src .= $this->get_setting( 'update' ) . '/';

		if ( !empty( $this->get_setting( 'search' ) ) ) {
			$search = array_map( 'urlencode', $this->get_setting( 'search' ) );
			$src .= sprintf( '?%s', implode( ',', $search ) );
		}

		return $src;
	}

}

?>
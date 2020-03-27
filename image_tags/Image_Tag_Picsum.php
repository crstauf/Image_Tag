<?php
/**
 * Image tag generator for Picsum.photos.
 *
 * @link https://picsum.photos
 */

defined( 'ABSPATH' ) || die();

/**
 * Class: Image_Tag_Picsum
 *
 * @todo add lqip
 */
class Image_Tag_Picsum extends Image_Tag {

	/**
	 * @var string Base URL.
	 */
	const BASE_URL = 'https://picsum.photos/';

	/**
	 * @var array $settings
	 */
	protected $settings = array(
		'blur' => false,
		'seed' => null,
		'width' => null,
		'height' => null,
		'random' => false,
		'image_id' => null,
		'grayscale' => false,
	);

	/**
	 * @var array $details
	 */
	protected $details = null;

	/**
	 * To string.
	 *
	 * @uses $this->get_setting()
	 * @uses Image_Tag::__toString()
	 * @return string
	 */
	function __toString() {
		if ( empty( $this->get_setting( 'width' ) ) ) {
			trigger_error( 'Picsum image requires width.', E_USER_WARNING );
			return '';
		}

		return parent::__toString();
	}

	/**
	 * Generate source URL.
	 *
	 * @uses $this->get_setting()
	 * @return string
	 */
	function get_src_attribute() {
		$src = self::BASE_URL;

		# Add ID.
		if ( !empty( $this->get_setting( 'image_id' ) ) )
			$src .= 'id/' . $this->get_setting( 'image_id' ) . '/';

		# Add seed.
		else if ( !empty( $this->get_setting( 'seed' ) ) )
			$src .= 'seed/' . $this->get_setting( 'seed' ) . '/';

		# Add width.
		if ( !empty( $this->get_setting( 'width' ) ) )
			$src .= ( int ) $this->get_setting( 'width' ) . '/';

		# Add height.
		if ( !empty( $this->get_setting( 'height' ) ) )
			$src .= ( int ) $this->get_setting( 'height' ) . '/';

		# Add query params.
		# Add blur.
		if ( false !== $this->get_setting( 'blur' ) )
			$src = add_query_arg( 'blur', $this->get_setting( 'blur' ), $src );

		# Add random.
		if (
			  !empty( $this->get_setting( 'random'   ) )
			&& empty( $this->get_setting( 'image_id' ) )
			&& empty( $this->get_setting( 'seed'     ) )
		)
			$src = add_query_arg( 'random', $this->get_setting( 'random' ), $src );

		# Add grayscale.
		if ( !empty( $this->get_setting( 'grayscale' ) ) )
			$src = add_query_arg( 'grayscale', 1, $src );

		return $src;
	}

	/**
	 * Magical getter for "width" attribute.
	 *
	 * @uses $this->_get_attribute()
	 * @uses $this->_get_setting()
	 * @return int
	 */
	function get_width_attribute() {
		if ( !empty( $this->_get_attribute( 'width' ) ) )
			return ( int ) $this->_get_attribute( 'width' );

		if ( !empty( $this->_get_setting( 'width' ) ) )
			return ( int ) $this->_get_setting( 'width' );

		return 1024;
	}

	/**
	 * Magical getter for "height" attribute.
	 *
	 * If specified, returns the height value.
	 * Otherwise, returns the width value.
	 *
	 * @uses $this->_get_attribute()
	 * @uses $this->_get_setting()
	 * @uses $this->get_width_attribute()
	 * @return int
	 */
	function get_height_attribute() {
		if ( !empty( $this->_get_attribute( 'height' ) ) )
			return ( int ) $this->_get_attribute( 'height' );

		if ( !empty( $this->_get_setting( 'height' ) ) )
			return ( int ) $this->_get_setting( 'height' );

		return ( int ) $this->get_width_attribute();
	}

	/**
	 * Get "blur" setting.
	 *
	 * @uses $this->_get_setting()
	 * @return mixed
	 */
	function get_blur_setting() {
		$blur = $this->_get_setting( 'blur' );

		if ( true === $blur )
			return 10;

		return $blur;
	}

	/**
	 * Get "seed" setting.
	 *
	 * @uses $this->_get_setting()
	 * @return string
	 */
	function get_seed_setting() {
		$seed = $this->_get_setting( 'seed' );

		if ( is_null( $seed ) )
			return null;

		return urlencode( sanitize_title_with_dashes( $seed ) );
	}

	/**
	 * Get "width" setting.
	 *
	 * @uses $this->_get_setting()
	 * @uses $this->_get_attribute()
	 * @return null|int
	 */
	function get_width_setting() {
		if ( !empty( $this->_get_setting( 'width' ) ) )
			return ( int ) $this->_get_setting( 'width' );

		if ( !empty( $this->_get_attribute( 'width' ) ) )
			return ( int ) $this->_get_attribute( 'width' );

		return null;
	}

	/**
	 * Get "height" setting.
	 *
	 * @uses $this->_get_setting()
	 * @uses $this->_get_attribute()
	 * @return null|int
	 */
	function get_height_setting() {
		if ( !empty( $this->_get_setting( 'height' ) ) )
			return ( int ) $this->_get_setting( 'height' );

		if ( !empty( $this->_get_attribute( 'height' ) ) )
			return ( int ) $this->_get_attribute( 'height' );

		return null;
	}

	/**
	 * Get "random" setting.
	 *
	 * @uses $this->_get_setting()
	 * @return mixed
	 */
	function get_random_setting() {
		static $_random = 0;
		$random = $this->_get_setting( 'random' );

		if ( false === $random )
			return false;

		if ( true === $random )
			return ++$_random;

		return $random;
	}

	/**
	 * Get image details from API (cached locally).
	 *
	 * @uses $this->get_setting()
	 * @uses $this->http()
	 * @uses wp_remote_get()
	 * @return object
	 */
	function details() {
		if ( !is_null( $this->details ) )
			return $this->details;

		$image_id = $this->get_setting( 'image_id' );

		if ( empty( $image_id ) )
			$image_id = ( int ) wp_remote_retrieve_header( $this->http(), 'picsum-id' );

		$response = wp_remote_get( sprintf( '%sid/%d/info', self::BASE_URL, $image_id ) );

		if ( is_wp_error( $response ) )
			return ( object ) array();

		return ( $this->details = json_decode( wp_remote_retrieve_body( $response ) ) );
	}

	/**
	 * Prevent transposing into Picsum image.
	 *
	 * @param array $attributes
	 * @param array $settings
	 * @return $this
	 */
	function picsum( array $attributes = array(), array $settings = array() ) {
		return $this;
	}

}

?>
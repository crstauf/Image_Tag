<?php
/**
 * Image tag generator for Unsplash Source.
 *
 * @link https://source.unsplash.com/
 */

defined( 'ABSPATH' ) || die();

/**
 * Class: Image_Tag_UnsplashSource
 */
class Image_Tag_Unsplash extends Image_Tag {

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

	function is_valid() {
		return (
			   !empty( $this->get_setting(  'width' ) )
			&& !empty( $this->get_setting( 'height' ) )
		);
	}

	function get_src_attribute() {
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

	function set_update_setting( $value ) {
		if ( !in_array( $value, array( null, 'daily', 'weekly' ) ) ) {
			trigger_error( 'Unsplash Source frequency can only be daily or weekly (default: <code>null</code>).' );
			$value = null;
		}

		$this->_set_setting( 'update', $value );
	}

	function set_search_setting( $value ) {
		$value = ( array ) $value;
		$this->_set_setting( 'search', $value );
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

}

?>
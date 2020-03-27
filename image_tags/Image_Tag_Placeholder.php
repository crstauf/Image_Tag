<?php
/**
 * Image tag generator for Placeholder.com.
 *
 * @link https://placeholder.com
 */

defined( 'ABSPATH' ) || die();

/**
 * Class: Image_Tag_Placeholder
 */
class Image_Tag_Placeholder extends Image_Tag {

	/**
	 * @var string Base URL.
	 */
	const BASE_URL = 'https://via.placeholder.com/';

	/**
	 * @var array $settings
	 */
	protected $settings = array(
		'text' => null,
		'width' => null,
		'height' => null,
		'bg-color' => null,
		'text-color' => null,
	);

	/**
	 * To string.
	 *
	 * @uses $this->get_setting()
	 * @uses Image_Tag::__toString()
	 * @return string
	 */
	function __toString() {
		if ( empty( $this->get_setting( 'width' ) ) ) {
			trigger_error( 'Placeholder image requires width.', E_USER_WARNING );
			return '';
		}

		return parent::__toString();
	}

	/**
	 * Get "bg-color" setting.
	 *
	 * @return string
	 */
	function get_bg_color_setting() {
		return urlencode( str_replace( '#', '', $this->settings['bg-color'] ) );
	}

	/**
	 * Get "text-color" setting.
	 *
	 * @return string
	 */
	function get_text_color_setting() {
		return urlencode( str_replace( '#', '', $this->settings['text-color'] ) );
	}

	/**
	 * Generate source URL.
	 *
	 * @uses $this->get_setting()
	 * @return string
	 */
	function get_src_attribute() {
		$src = self::BASE_URL;

		$dimensions = '';

		# Add width.
		if ( !empty( $this->get_setting( 'width' ) ) )
			$dimensions .= ( int ) $this->get_setting( 'width' );

		# Add height.
		if ( !empty( $this->get_setting( 'height' ) ) )
			$dimensions .= 'x' . ( int ) $this->get_setting( 'height' );

		# Add dimensions.
		$src .= !empty( $dimensions )
			? trailingslashit( $dimensions )
			: '';

		# Add background color.
		if ( !empty( $this->get_setting( 'bg-color' ) ) ) {
			$src .= $this->get_setting( 'bg-color' ) . '/';

			# Add text color.
			if ( !empty( $this->get_setting( 'text-color' ) ) )
				$src .= $this->get_setting( 'text-color' ) . '/';
		}

		# Add text.
		if ( !empty( $this->get_setting( 'text' ) ) )
			$src = add_query_arg( 'text', urlencode( $this->get_setting( 'text' ) ) );

		return $src;
	}

	/**
	 * Magical getter for "width" attribute.
	 *
	 * @uses $this->_get_attribute()
	 * @uses $this->_get_setting()
	 * @return null|int
	 */
	function get_width_attribute() {
		if ( !empty( $this->_get_attribute( 'width' ) ) )
			return ( int ) $this->_get_attribute( 'width' );

		if ( !empty( $this->_get_setting( 'width' ) ) )
			return ( int ) $this->_get_setting( 'width' );

		return null;
	}

	/**
	 * Magical getter for "height" attribute.
	 *
	 * @uses $this->_get_attribute()
	 * @uses $this->_get_setting()
	 * @return null|int
	 */
	function get_height_attribute() {
		if ( !empty( $this->_get_attribute( 'height' ) ) )
			return ( int ) $this->_get_attribute( 'height' );

		if ( !empty( $this->_get_setting( 'height' ) ) )
			return ( int ) $this->_get_setting( 'height' );

		if ( !empty( $this->_get_attribute( 'width' ) ) )
			return ( int ) $this->_get_attribute( 'width' );

		if ( !empty( $this->_get_setting( 'width' ) ) )
			return ( int ) $this->_get_setting( 'width' );

		return null;
	}

	/**
	 * Magical getter for "width" setting.
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
	 * Magical getter for "height" setting.
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
	 * Prevent transposing into a Placeholder image.
	 *
	 * @param array $attributes
	 * @param array $settings
	 * @return $this
	 */
	function placeholder( array $attributes = array(), array $settings = array() ) {
		return $this;
	}

}
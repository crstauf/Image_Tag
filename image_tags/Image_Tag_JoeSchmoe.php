<?php
/**
 * Image tag generator for JoeSchmoe.
 *
 * @link https://joeschmoe.io/
 */

defined( 'ABSPATH' ) || die();

require_once '_Image_Tag_Placeholder.php';

/**
 * Class: Image_Tag_JoeSchmoe
 */
class Image_Tag_JoeSchmoe extends _Image_Tag_Placeholder {

	/**
	 * @var string Base URL.
	 */
	const BASE_URL = 'https://joeschmoe.io/api/v1/';

	/**
	 * @var array
	 */
	protected $settings = array(
		'gender' => null,
		'seed' => null,
	);

	/**
	 * Get type.
	 *
	 * @return string
	 *
	 * @todo add test
	 */
	function get_type() {
		return 'joeschmoe';
	}

	/**
	 * Check type.
	 *
	 * @param string|array $compare_types
	 * @uses Image_Tag::is_type()
	 * @uses $this->get_type()
	 * @return true
	 *
	 * @todo add test
	 */
	function is_type( $compare_types ) {
		if ( parent::is_type( $compare_types ) )
			return true;

		$actual_types = array(
			'avatar',
			'person',
			'profile',
			'joe schmoe',
			$this->get_type(),
		);

		foreach ( ( array ) $compare_types as $type )
			if ( in_array( strtolower( $type ), $actual_types ) )
				return true;

		return false;
	}

	/**
	 * Check properties are sufficient to create tag.
	 *
	 * @return true
	 */
	protected function check_valid() {
		return true;
	}

	/**
	 * Get "gender" setting.
	 *
	 * @uses $this->_get_setting()
	 * @return string
	 *
	 * @todo add test
	 */
	function get_gender_setting() {
		$setting = $this->_get_setting( 'gender' );

		if ( !in_array( $setting, array(
			null,
			'male',
			'female',
		) ) ) {
			trigger_error( sprintf( 'Image Tag: JoeSchmoe <code>gender</code> setting cannot be <code>%s</code>; must be <code>male</code> or <code>female</code>.', $setting ) );
			return null;
		}

		return $setting;
	}

	/**
	 * Get "src" attribute.
	 *
	 * @uses $this->_get_attribute()
	 * @uses $this->generate_url()
	 * @return string
	 *
	 * @todo add test
	 */
	function get_src_attribute() {
		if ( empty( $this->_get_attribute( 'src' ) ) )
			return $this->generate_url();

		return $this->_get_attribute( 'src' );
	}

	/**
	 * Generate URL.
	 *
	 * @uses $this->get_setting()
	 * @return string
	 *
	 * @todo add test
	 */
	protected function generate_url() {
		$src = static::BASE_URL;

		# Add gender.
		if ( !empty( $this->get_setting( 'gender' ) ) )
			$src .= $this->get_setting( 'gender' ) . '/';

		# Add seed, or random.
		if ( !empty( $this->get_setting( 'seed' ) ) )
			$src .= urlencode( $this->get_setting( 'seed' ) ) . '/';
		else
			$src .= 'random/';

		return $src;
	}

	/**
	 * Get primary width.
	 *
	 * If "width" attribute not set, returns SVG's "viewbox" width (last check: 125).
	 *
	 * @uses $this->get_attribute()
	 * @return int
	 */
	function get_width() {
		if ( !empty( ( int ) $this->get_attribute( 'width' ) ) )
			return ( int ) $this->get_attribute( 'width' );

		return 125;
	}

	/**
	 * Get primary height.
	 *
	 * If "height" attribute not set, returns SVG's "viewbox" height (last check: 125).
	 *
	 * @uses $this->get_attribute()
	 * @return int
	 */
	function get_height() {
		if ( !empty( ( int ) $this->get_attribute( 'height' ) ) )
			return ( int ) $this->get_attribute( 'height' );

		return 125;
	}

	/**
	 * Get ratio.
	 *
	 * @uses $this->get_width()
	 * @uses $this->get_height()
	 * @uses _Image_Tag::get_ratio()
	 * @return int
	 */
	function get_ratio() {
		if (
			   empty( $this->get_width() )
			|| empty( $this->get_height() )
		)
			return 1;

		return parent::get_ratio();
	}

	/**
	 * Prevent transformation into JoeSchmoe.
	 *
	 * @param array $attributes
	 * @param array $settings
	 * @return $this
	 */
	function into_joeschmoe( $attributes = array(), array $settings = array() ) {
		return $this;
	}

}

?>
<?php
/**
 * Image tag generator for JoeSchmoe.
 *
 * @link https://joeschmoe.io/
 */

defined( 'ABSPATH' ) || die();

/**
 * Class: Image_Tag_JoeSchmoe
 */
class Image_Tag_JoeSchmoe extends Image_Tag {

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
	 * Construct.
	 */
	function __construct( array $attributes = array(), array $settings = array() ) {
		parent::__construct( $attributes, $settings );
	}

	/**
	 * Get "gender" setting.
	 *
	 * @uses $this->_get_setting()
	 * @return null|string
	 */
	function get_gender_setting() {
		$setting = $this->_get_setting( 'gender' );

		if ( !in_array( $setting, array(
			'male',
			'female',
		) ) )
			return null;

		return $setting;
	}

	/**
	 * Generate "src" attribute.
	 *
	 * @uses $this->get_setting()
	 * @return string
	 */
	protected function get_src_attribute() {
		$src = static::BASE_URL;

		# Add gender.
		if ( !empty( $this->get_setting( 'gender' ) ) )
			$src .= $this->get_setting( 'gender' ) . '/';

		# Add seed, or random.
		if ( !empty( $this->get_setting( 'seed' ) ) )
			$src .= $this->get_setting( 'seed' ) . '/';
		else
			$src .= 'random/';

		return $src;
	}

	/**
	 * Get ratio.
	 *
	 * @return int
	 */
	function get_ratio() {
		return 1;
	}

	/**
	 * Prevent transposing into a JoeSchmoe image.
	 *
	 * @param array $attributes
	 * @param array $settings
	 * @return $this
	 */
	function joeschmoe( array $attributes = array(), array $settings = array() ) {
		return $this;
	}

}

?>
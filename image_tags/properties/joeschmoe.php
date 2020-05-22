<?php
/**
 * Property classes for JoeSchmoe image tag.
 *
 * @todo add tests
 */

defined( 'ABSPATH' ) || die();

/**
 * Class: Image_Tag_JoeSchmoe_Attributes
 */
class Image_Tag_JoeSchmoe_Attributes extends Image_Tag_Attributes {

	/**
	 * @var string Base URL.
	 */
	const PRIMARY_URL = 'https://joeschmoe.io/api/v1/';

	/**
	 * @var string Cloudflare worker URL.
	 */
	const ALT_URL = 'https://joeschmoe.crstauf.workers.dev/';

	/**
	 * Get base URL.
	 *
	 * @return string
	 */
	protected function get_url() {
		return 'primary' === $this->image_tag->settings->source
			? static::PRIMARY_URL
			: static::ALT_URL;
	}

	/**
	 * Get "src" attribute.
	 *
	 * @uses static::get_url()
	 * @uses Image_Tag_JoeSchmoe_Settings::get()
	 * @return string
	 */
	function get_src_attribute_for_view() {
		if ( wp_http_validate_url( $this->get( 'src' ) ) )
			return $this->get( 'src' );

		$src = $this->get_url();

		$gender = $this->image_tag->settings->get( 'gender', 'view' );
		$seed   = $this->image_tag->settings->get(   'seed', 'view' );

		if ( !empty( $gender ) )
			$src .=  $gender . '/';

		if ( !empty( $seed ) )
			$src .= $seed;

		return $src;
	}

}

/**
 * Class: Image_Tag_JoeSchmoe_Settings
 */
class Image_Tag_JoeSchmoe_Settings extends Image_Tag_Settings {

	/**
	 * @var array DEFAULTS
	 */
	const DEFAULTS = array(
		'before_output' => array(),
		'after_output' => array(),
		'source' => 'alt',
		'gender' => null,
		'seed' => null,
	);

	/**
	 * Limit values for "gender" setting.
	 *
	 * @param null|string $value
	 * @uses static::_set()
	 */
	protected function set_gender_setting( $value ) {
		if ( !in_array( $value, array( null, 'male', 'female' ) ) )
			$value = null;

		$this->_set( 'gender', $value );
	}

}

?>
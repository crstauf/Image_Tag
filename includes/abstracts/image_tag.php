<?php
/**
 * Main abstract class for Image Tag types.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || die();

/**
 * Abstract class: Image_Tag.
 */
abstract class Image_Tag extends Image_Tag_Helpers implements Image_Tag_Attributes_Interface, Image_Tag_Settings_Interface, Image_Tag_Sources_Interface, Image_Tag_Validation_Interface {

	/**
	 * Smallest transparent data URI image.
	 * @var string
	 */
	const BLANK = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';

	/**
	 * Create Image_Tag object from source.
	 *
	 * @param int|string $source Image source.
	 * @param null|array $attributes
	 * @param null|array $settings
	 * @uses Image_Tag_Plugin::inc()
	 * @uses esc_url_raw()
	 * @return Image_Tag
	 *
	 * @todo add test
	 */
	static function create( $source, $attributes = array(), $settings = array() ) : self {

		/**
		 * If URL, create using base object.
		 *
		 * Previously used wp_http_validate_url(), but the use of
		 * gethostbyname() can cause slow downs, so use esc_url_raw() and
		 * do string comparison instead.
		 */
		if ( esc_url_raw( $source ) === $source ) {
			require_once Image_Tag_Plugin::inc() . 'types/base.php';
			$object = new Image_Tag_Base( $attributes, $settings );
			$object->add_source( $source );
			return $object;
		}

		# Unable to determine type.
		trigger_error( sprintf( 'Unable to determine image type from source <code>%s</code>.', $source ), E_USER_WARNING );
		return new Image_Tag_Base( $attributes, $settings );
	}

	/**
	 * Construct.
	 *
	 * @param null|array $attributes Image tag attributes.
	 * @param null|array $settings Image settings.
	 * @uses $this->set_attributes()
	 * @uses $this->set_settings()
	 */
	function __construct( $attributes = array(), $settings = array() ) {
		$this->set_attributes( ( array ) $attributes );
		$this->set_settings( ( array ) $settings );
	}

	/**
	 * To string.
	 *
	 * @uses $this->check_valid()
	 * @uses $this->get_attributes()
	 * @return string
	 */
	function __toString() : string {
		$this->apply_sources();
		$errors = $this->check_valid();

		if ( is_wp_error( $errors ) ) {
			foreach ( $errors->get_error_messages() as $message )
				trigger_error( $message, E_USER_WARNING );

			return '';
		}

		$array = array(
			'<img',
		);

		$attributes = wp_parse_args( $this->get_attributes(), $this->attribute_defaults );

		foreach ( $attributes as $attribute_name => $attribute_value )
			$array['attribute_' . $attribute_name] = $attribute_name . '="' . esc_attr( $attribute_value ) . '"';

		$array[] = '/>';

		$array  = apply_filters( 'image_tag/output/array', $array, $this );
		$array  = array_filter( $array );
		$string = implode( ' ', $array );
		$string = trim( $string );

		return apply_filters( 'image_tag/output/string', $string, $this );
	}

}
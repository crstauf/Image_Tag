<?php
/**
 * Base image tag generator.
 */

defined( 'ABSPATH' ) || die();

/**
 * Class: Image_Tag
 */
class Image_Tag extends _Image_Tag {

	/**
	 * Create Image Tag object.
	 *
	 * @uses Image_Tag_WP_Attachment::__construct()
	 * @uses Image_Tag_Picsum::__construct()
	 * @uses Image_Tag_Placeholder::__construct()
	 * @uses Image_Tag_Unsplash::__construct()
	 * @uses Image_Tag::__construct()
	 * @uses Image_Tag_WP_Theme::__construct()
	 *
	 * @param $source
	 * @param null|array $attributes
 	 * @param array $settings
 	 * @return Image_Tag
	 */
	static function create( $source, $attributes = array(), $settings = array() ) {
		$attributes = ( array ) $attributes;
		$settings = ( array ) $settings;

		# If integer, create WordPress attachment image.
		if ( is_int( $source ) )
			return new Image_Tag_WP_Attachment( $source, $attributes, $settings );

		# If source is "picsum", create picsum.photos image.
		if ( 'picsum' === $source )
			return new Image_Tag_Picsum( $attributes, $settings );

		# If source is "placeholder", create Placeholder image.
		if ( 'placeholder' === $source )
			return new Image_Tag_Placeholder( $attributes, $settings );

		# If source is "joeschmoe", create JoeSchmoe image.
		if ( 'joeschmoe' === $source )
			return new Image_Tag_JoeSchmoe( $attributes, $settings );

		# If source is "unsplash", create Unsplash image.
		if ( 'unsplash' === $source )
			return new Image_Tag_Unsplash( $attributes, $settings );

		# If URL, create external image.
		if ( ( bool ) wp_http_validate_url( $source ) ) {
			$attributes['src'] = $source;
			return new Image_Tag( $attributes, $settings );
		}

		# If string, create WordPress theme image.
		if ( is_string( $source ) )
			return new Image_Tag_WP_Theme( $source, $attributes, $settings );

		trigger_error( sprintf( 'Unable to determine image type from source: <code>%s</code>.', $source ), E_USER_WARNING );
		return new static( $attributes, $settings );
	}

	/**
	 * Trim expected separators.
	 *
	 * @param array|string &$value
	 * @return array|string
	 */
	protected static function trim( &$value ) {
		if ( is_string( $value ) )
			return trim( $value, ",; \t\n\r\0\x0B" );

		if ( !is_array( $value ) )
			return $value;

		array_walk( $value, function( &$item, $key ) {
			if ( is_string( $item ) )
				$item = trim( $item, ",; \t\n\r\0\x0B" );
			else if ( is_array( $item ) )
				$item = Image_Tag::trim( $item );
		} );

		return $value;
	}

	/**
	 * @todo add test
	 */
	function get_type() {
		return 'external';
	}

	/**
	 * @todo add test
	 */
	function is_type( $compare_types ) {
		if ( parent::is_type( $compare_types ) )
			return true;

		$actual_types = array(
			'remote',
			$this->get_type(),
		);

		foreach ( ( array ) $compare_types as $type )
			if ( in_array( $type, $actual_types ) )
				return true;

		return false;
	}

}

?>
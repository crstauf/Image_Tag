<?php

declare( strict_types=1 );

use Image_Tag\Plugin;
use Image_Tag\Abstracts\Base;
use Image_Tag\Data_Stores\Attributes;
use Image_Tag\Data_Stores\Settings;

defined( 'WPINC' ) || die();

/**
 * Class: Image_Tag
 *
 * @property int $attachment_id
 */
class Image_Tag extends Base {

	/**
	 * @var array Image types.
	 */
	const TYPES = array(
		'base',
		'default',
	);

	/**
	 * Create image tag.
	 *
	 * @param int|string $source
	 * @param null|array|Attributes $attributes
	 * @param null|array|Settings $settings
	 * @return Image_Tag
	 */
	public static function create( $source, $attributes = null, $settings = null ) : Base {
		if ( is_a( $source, self::class ) ) {
			return $source;
		}

		if ( is_int( $source ) ) {
			require_once Plugin::inc() . 'types/WP_Attachment.php';
			return new Image_Tag\Types\WP_Attachment( $source, $attributes, $settings );
		}

		if ( ! is_string( $source ) ) {
			return new Image_Tag( $attributes, $settings );
		}

		$_source = strtolower( $source );

		if ( 'joeschmoe' === $_source ) {
			require_once Plugin::inc() . 'types/JoeSchmoe.php';
			return new Image_Tag\Types\JoeSchmoe( $attributes, $settings );
		}

		if ( 'picsum' === $_source ) {
			require_once Plugin::inc() . 'types/Picsum.php';
			return new Image_Tag\Types\Picsum( $attributes, $settings );
		}

		if ( 'placeholder' === $_source ) {
			require_once Plugin::inc() . 'types/Placeholder.php';
			return new Image_Tag\Types\Placeholder( $attributes, $settings );
		}

		if ( 'unsplash' === $_source ) {
			require_once Plugin::inc() . 'types/Unsplash.php';
			return new Image_Tag\Types\Unsplash( $attributes, $settings );
		}

		if ( ( bool ) wp_http_validate_url( $source ) ) {
			$attributes = new Attributes( $attributes );
			$attributes->set( 'src', $source );
			return new Image_Tag( $attributes, $settings );
		}

		# Only option left is a theme image.
		require_once Plugin::inc() . 'types/WP_Theme.php';
		return new Image_Tag\Types\WP_Theme( $source, $attributes, $settings );
	}

	/**
	 * Construct.
	 *
	 * @param null|array|Attributes $attributes
	 * @param null|array|Settings $settings
	 * @uses $this->construct()
	 * @uses Attributes->set()
	 */
	public function __construct( $attributes = null, $settings = null ) {
		$this->construct( $attributes, $settings );
	}

	/**
	 * Perform validation checks.
	 *
	 * @uses Attributes::has()
	 * @return \WP_Error
	 */
	protected function perform_validation_checks() : \WP_Error {
		$errors = new \WP_Error;

		if ( $this->attributes->empty( 'src' ) ) {
			$errors->add( 'empty_source', 'Image tag requires a source.' );
		}

		return $errors;
	}

}

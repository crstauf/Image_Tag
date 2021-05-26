<?php
/**
 * WordPress attachment image.
 */

declare( strict_types=1 );

namespace Image_Tag\Types;
use Image_Tag\Data_Stores\Attributes;
use Image_Tag\Data_Stores\Settings;

defined( 'WPINC' ) || die();

/**
 * Class: Image_Tag\Types\WP_Attachment
 */
class WP_Attachment extends \Image_Tag\Abstracts\WordPress {

	protected $attachment_id;

	/**
	 * @var array Image types.
	 */
	const TYPES = array(
		'attachment',
		'local',
		'internal',
		'wordpress',
	);

	/**
	 * Construct.
	 *
	 * @param int $attachment_id
	 * @param null|array|Attributes $attributes
	 * @param null|array|Settings $settings
	 * @uses $this->construct()
	 * @uses $this->is_valid()
	 */
	function __construct( int $attachment_id, $attributes = null, $settings = null ) {
		$this->attachment_id = $attachment_id;

		$this->construct( $attributes, $settings );

		if ( !$this->is_valid() )
			return;
	}

	/**
	 * Property getter.
	 *
	 * @param string $property
	 * @uses parent::__get()
	 * @return mixed
	 */
	function __get( string $property ) {
		if ( 'attachment_id' === $property )
			return $this->attachment_id;

		return parent::__get( $property );
	}

	/**
	 * Perform validation checks.
	 *
	 * @return \WP_Error
	 */
	protected function perform_validation_checks() : \WP_Error {
		$errors = new \WP_Error;

		if ( empty( $this->attachment_id ) )
			$errors->add( 'attachment_id', 'Attachment ID is missing.' );

		else if ( 'attachment' !== get_post_type( $this->attachment_id ) )
			$errors->add( 'not_attachment', 'Provided ID is not for an attachment.' );

		else if ( !wp_attachment_is_image( $this->attachment_id ) )
			$errors->add( 'not_image', 'Attachment is not an image.' );

		return $errors;
	}

	/**
	 * Get ratio of image dimensions: width divided by height.
	 *
	 * @return float
	 */
	function get_ratio() : float {
		return 1.0;
	}

	/**
	 * Get most common colors.
	 *
	 * @param int $count
	 * @uses static::identify_colors()
	 * @return array
	 */
	function get_colors( int $colors = 5 ) : array {
		return array();
	}

	/**
	 * Create Attributes object to use for output.
	 *
	 * @uses parent::output_attributes()
	 * @return Attributes
	 */
	protected function output_attributes() : Attributes {
		$attributes = parent::output_attributes();

		$src = wp_get_attachment_image_src( $this->attachment_id, 'full' );

		if ( empty( $attributes->alt ) )
			$attributes->update( 'alt', get_the_title( $this->attachment_id ) );

		$attributes->set( 'src',    $src[0] );
		$attributes->set( 'class', 'attachment attachment-' . $this->attachment_id );
		$attributes->set( 'width',  $src[1] );
		$attributes->set( 'height', $src[2] );

		return $attributes;
	}

}
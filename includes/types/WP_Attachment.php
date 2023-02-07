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
	protected $wp_largest_size  = 'thumbnail';
	protected $wp_smallest_size = 'full';

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
	 * Get uploads directory path.
	 *
	 * @uses wp_get_upload_dir()
	 * @return string
	 */
	public static function uploads_dir() : string {
		$uploads = wp_get_upload_dir();
		return $uploads['basedir'];
	}

	/**
	 * Construct.
	 *
	 * @param int $attachment_id
	 * @param null|array|Attributes $attributes
	 * @param null|array|Settings $settings
	 * @uses $this->construct()
	 * @uses $this->is_valid()
	 * @uses $this->identify_sizes();
	 */
	public function __construct( int $attachment_id, $attributes = null, $settings = null ) {
		$attachment_id       = ( int ) apply_filters( 'image_tag/wp_attachment/attachment_id', $attachment_id, $attributes, $settings );
		$this->attachment_id = $attachment_id;

		$this->construct( $attributes, $settings );

		if ( ! $this->is_valid() ) {
			return;
		}

		$this->identify_sizes();
		$this->set_orientation();
	}

	/**
	 * Property getter.
	 *
	 * @param string $property
	 * @uses parent::__get()
	 * @return mixed
	 */
	public function __get( string $property ) {
		if ( 'attachment_id' === $property ) {
			return $this->attachment_id;
		}

		return parent::__get( $property );
	}

	/**
	 * Get attachment meta data.
	 *
	 * @return array
	 */
	protected function metadata() : array {
		return ( array ) get_post_meta( $this->attachment_id, '_wp_attachment_metadata', true );
	}

	/**
	 * Get path to specified size of image.
	 *
	 * @param string $size
	 * @uses $this->metadata()
	 * @return string
	 */
	protected function path( string $size = 'full' ) : string {
		if ( 'full' === $size ) {
			return get_attached_file( $this->attachment_id );
		}

		$metadata = $this->metadata();

		$path  = trailingslashit( static::uploads_dir() );
		$path .= trailingslashit( dirname( $metadata['file'] ) );
		$path .= $metadata['sizes'][ $size ]['file'];

		return $path;
	}

	/**
	 * Identify sizes of image.
	 *
	 * @return void
	 */
	protected function identify_sizes() : void {
		if ( ! $this->settings->has( 'image-sizes' ) ) {
			$this->wp_smallest_size = 'full';
			$this->wp_largest_size  = 'full';

			return;
		}

		$meta = $this->metadata();

		if ( empty( $meta ) ) {
			$this->wp_smallest_size = 'full';
			$this->wp_largest_size  = 'full';

			trigger_error( sprintf( 'Unable to get <code>_wp_attachment_metadata</code> meta for attachment <code>%d</code>', $this->attachment_id ), E_USER_WARNING );
			return;
		}

		/**
		 * Images that are smaller than 'thumbnail'
		 * will not have sizes, so set to 'full'.
		 */
		if ( empty( $meta['sizes'] ) ) {
			$this->wp_smallest_size = 'full';
			$this->wp_largest_size  = 'full';

			return;
		}

		$all_sizes         = $meta['sizes'];
		$all_sizes['full'] = array(
			'file'   => basename( $meta['file'] ),
			'width'  => $meta['width'],
			'height' => $meta['height'],
		);

		$requested_sizes = array_flip( $this->settings->get( 'image-sizes' ) );
		$sizes           = array_intersect_key( $all_sizes, $requested_sizes );

		$largest_sq_px  = absint( $all_sizes[ $this->wp_largest_size ]['width'] ) * absint( $all_sizes[ $this->wp_largest_size ]['height'] );
		$smallest_sq_px = absint( $all_sizes[ $this->wp_smallest_size ]['width'] ) * absint( $all_sizes[ $this->wp_smallest_size ]['height'] );

		foreach ( $sizes as $size => $data ) {

			# Calculate this size's square pixels
			$this_sq_px = absint( $data['width'] ) * absint( $data['height'] );

			# Compare to largest square pixels
			if ( $this_sq_px > $largest_sq_px ) {
				$this->wp_largest_size = $size;
				$largest_sq_px         = $this_sq_px;
			}

			# Compare to smallest square pixels
			if ( $this_sq_px < $smallest_sq_px ) {
				$this->wp_smallest_size = $size;
				$smallest_sq_px         = $this_sq_px;
			}
		}
	}

	/**
	 * Get ratio of image dimensions: width divided by height.
	 *
	 * @return float
	 */
	public function ratio() : float {
		$size = wp_get_attachment_image_src( $this->attachment_id, $this->wp_largest_size );

		if ( empty( $size ) || empty( $size[1] ) || empty( $size[2] ) ) {
			return 0;
		}

		return absint( $size[1] ) / absint( $size[2] );
	}

	/**
	 * Get most common colors.
	 *
	 * @param int $count
	 * @uses static::identify_colors()
	 * @return array
	 */
	public function colors( int $count = 3 ) : array {
		$meta = get_post_meta( $this->attachment_id, '_common_colors', true );

		if (
			 ! empty( $meta )
			&& count( $meta ) >= $count
		) {
			return array_slice( $meta, 0, $count );
		}

		$path   = $this->path( 'thumbnail' );
		$colors = static::identify_colors( $path, $count );

		if ( empty( $colors ) ) {
			return array();
		}

		update_post_meta( $this->attachment_id, '_common_colors', $colors );

		return $colors;
	}

	/**
	 * Perform validation checks.
	 *
	 * @return \WP_Error
	 */
	protected function perform_validation_checks() : \WP_Error {
		$errors = new \WP_Error;

		if ( empty( $this->attachment_id ) ) {
			$errors->add( 'attachment_id', 'Attachment ID is missing.' );

		} else if ( 'attachment' !== get_post_type( $this->attachment_id ) ) {
			$errors->add( 'not_attachment', 'Provided ID is not for an attachment.' );

		} else if ( ! wp_attachment_is_image( $this->attachment_id ) ) {
			$errors->add( 'not_image', 'Attachment is not an image.' );
		}

		return $errors;
	}

	/**
	 * Create Attributes object to use for output.
	 *
	 * @uses parent::output_attributes()
	 * @return Attributes
	 */
	public function output_attributes() : Attributes {
		$attributes = parent::output_attributes();

		$attributes->set( 'class', 'attachment attachment-' . $this->attachment_id );

		if ( empty( $attributes->alt ) ) {
			$attributes->update( 'alt', get_the_title( $this->attachment_id ) );
		}

		$this->output_source_attributes( $attributes );

		return $attributes;
	}

	/**
	 * Add source attributes.
	 *
	 * @param $attributes Reference to Attributes object.
	 * @return void
	 */
	protected function output_source_attributes( &$attributes ) : void {
		$src = wp_get_attachment_image_src( $this->attachment_id, $this->wp_smallest_size );

		$attributes->set( 'src', $src[0] );
		$attributes->set( 'width', $src[1] );
		$attributes->set( 'height', $src[2] );

		if (
			! $this->settings->has( 'image-sizes' )
			|| 1 === count( $this->settings->get( 'image-sizes' ) )
		) {
			return;
		}

		$requested_sizes = $this->settings->get( 'image-sizes' );
		$wp_sizes        = get_intermediate_image_sizes();
		$wp_sizes[]      = 'full';
		$sizes           = array_intersect( $requested_sizes, $wp_sizes );
		$sizes           = array_unique( $sizes );

		$glue = ', ';

		if ( defined( 'WP_DEBUG' ) && constant( 'WP_DEBUG' ) ) {
			$glue .= PHP_EOL . "\t";
		}

		$widths = array();

		foreach ( $sizes as $size ) {
			$src = wp_get_attachment_image_src( $this->attachment_id, $size );

			if ( empty( $src ) ) {
				continue;
			}

			if ( in_array( $src[1], $widths ) ) {
				continue;
			}

			$widths[] = $src[1];
			$value    = $src[0] . ' ' . $src[1] . 'w';

			$attributes->append( 'srcset', $value, $glue );
		}

		if (
			$attributes->has( 'srcset' )
			&& ! $attributes->has( 'sizes' )
		) {
			$attributes->set( 'sizes', '100vw' );
		}
	}

	/**
	 * Get or generate low-quality image placeholder (LQIP).
	 *
	 * Gets LQIP from meta data, or generates and stores LQIP to
	 * attachment meta data.
	 *
	 * @uses \Image_Tag\Abstracts\WordPress::generate_lqip()
	 * @uses $this->path()
	 * @return string
	 */
	public function lqip() : string {
		$meta = get_post_meta( $this->attachment_id, '_lqip', true );

		if ( ! empty( $meta ) ) {
			return $meta;
		}

		$lqip = static::generate_lqip( $this->path( 'medium' ) );

		add_post_meta( $this->attachment_id, '_lqip', $lqip, true );

		return $lqip;
	}

}

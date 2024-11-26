<?php declare(strict_types=1);

namespace Image_Tag;

class Attachment implements Interfaces\Core {

	use Traits\Attributes,
		Traits\Dimensions,
		Traits\Fallbacks,
		Traits\Lazysizes,
		Traits\Output,
		Traits\Settings,
		Traits\Validation;

	/** @var string */
	protected readonly int $attachment_id;

	/** @var string Largest named image size. */
	protected string $largest = 'thumbnail';

	/** @var string Smallest named image size. */
	protected string $smallest = 'full';

	/** @var array */
	public readonly array $sizes;


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
	 */
	public function __construct(
		int $attachment_id,
		Stores\Attributes $attributes = new Stores\Attributes,
		Stores\Settings $settings = new Stores\Settings
	) {
		$attachment_id = absint( apply_filters( 'image_tag/attachment/id', $attachment_id, $attributes, $settings ) );

		$this->attachment_id = $attachment_id;
		$this->attributes    = $attributes;
		$this->settings      = $settings;

		$this->attribute( 'class', sprintf( 'attachment attachment-%d', $attachment_id ) );
		$this->attribute( 'alt', get_the_title( $attachment_id ) );
	}

	public function output() : string {
		$this->identify_sizes();
		return $this->markup();
	}

	/**
	 * Validation checks.
	 */
	protected function perform_validation_checks() : \WP_Error {
		$errors = new \WP_Error;
		$checks = true;

		if ( empty( $this->attachment_id ) ) {
			$errors->add( 'attachment_id', 'Attachment ID is required.' );
			$checks = false;
		}

		if ( $checks && 'attachment' !== get_post_type( $this->attachment_id ) ) {
			$errors->add( 'not_attachment', 'Provided ID is not an attachment.' );
			$checks = false;
		}

		if ( $checks && ! wp_attachment_is_image( $this->attachment_id ) ) {
			$errors->add( 'not_image', 'Attachment is not an image.' );
		}

		return $errors;
	}

	/**
	 * Get attachment meta data.
	 *
	 * @return mixed[]
	 */
	protected function metadata() : array {
		if ( ! $this->is_valid() ) {
			return array();
		}

		$meta = get_post_meta( $this->attachment_id, '_wp_attachment_metadata', true );

		if ( ! is_array( $meta ) ) {
			$meta = array();
		}

		if ( empty( $meta ) ) {
			trigger_error( sprintf( 'Unable to get <code>_wp_attachment_metadata</code> meta for attachment <code>%d</code>', $this->attachment_id ), E_USER_WARNING );
		}

		return $meta;
	}

	/**
	 * Get path to specified size of image.
	 *
	 * @param string $size
	 * @uses $this->metadata()
	 * @return string
	 */
	protected function path( string $size = 'full' ) : string {
		$path = '';

		if ( 'full' === $size ) {
			$path = get_attached_file( $this->attachment_id );
		}

		if ( is_string( $path ) ) {
			return '';
		}

		$metadata = $this->metadata();

		$path  = trailingslashit( static::uploads_dir() );
		$path .= trailingslashit( dirname( $metadata['file'] ) );
		$path .= $metadata['sizes'][ $size ]['file'];

		return $path;
	}

	protected function setting__image_sizes( $sizes ) : void {
		if ( is_string( $sizes ) ) {
			$sizes = explode( ' ', $sizes );
		}

		$this->settings->update( 'image-sizes', $sizes );
		$this->identify_sizes();
	}

	protected function identify_sizes() : void {
		if ( isset( $this->sizes ) ) {
			return;
		}

		if ( ! $this->settings->has( 'image-sizes' ) ) {
			$this->settings->update( 'image-sizes', 'full' );
		}

		$meta = $this->metadata();

		if ( empty( $meta ) || empty( $meta['sizes'] ) || ! is_array( $meta['sizes'] ) ) {
			$this->smallest = 'full';
			return;
		}

		$all         = $meta['sizes'];
		$all['full'] = array(
			'file'   => basename( $meta['file'] ),
			'width'  => absint( $meta['width'] ),
			'height' => absint( $meta['height'] ),
		);

		$requested         = $this->settings->get( 'image-sizes' );
		$requested_flipped = array_flip( $requested );
		$unsorted_sizes    = array_intersect_key( $all, $requested_flipped );

		$largest_sq_px  = absint( $all[ $this->largest ]['width'] ) * absint( $all[ $this->largest ]['height'] );
		$smallest_sq_px = absint( $all[ $this->smallest ]['width'] ) * absint( $all[ $this->smallest ]['height'] );
		$sq_pxs         = array();

		foreach ( $unsorted_sizes as $name => $size ) {
			$size_sq_px      = absint( $size['width'] ) * absint( $size['height'] );
			$sq_pxs[ $name ] = $size_sq_px;
		}

		asort( $sq_pxs, SORT_NUMERIC );

		$sorted_size_names = array_keys( $sq_pxs );
		$sorted_sizes      = array();

		foreach ( $sorted_size_names as $name ) {
			$sorted_sizes[ $name ] = $unsorted_sizes[ $name ];
		}

		$this->sizes    = $sorted_sizes;
		$this->smallest = reset( $sorted_size_names );
		$this->largest  = end( $sorted_size_names );

		$src = wp_get_attachment_image_src( $this->attachment_id, $this->smallest );

		if ( ! is_array( $src ) || empty( $src ) ) {
			return;
		}

		$this->attributes( array(
			'src'    => $src[0],
			'width'  => $src[1],
			'height' => $src[2],
		) );

		if ( count( $this->sizes ) === 1 ) {
			return;
		}

		$widths = array();
		$glue   = ', ';

		if ( defined( 'WP_DEBUG' ) && constant( 'WP_DEBUG' ) ) {
			$glue .= PHP_EOL . "\t";
		}

		foreach ( array_keys( $this->sizes ) as $name ) {
			$src = wp_get_attachment_image_src( $this->attachment_id, $name );

			if ( ! is_array( $src ) || empty( $src ) ) {
				continue;
			}

			if ( in_array( $src[1], $widths ) ) {
				continue;
			}

			$widths[] = $src[1];
			$source   = sprintf( '%s %dw', $src[0], $src[1] );

			$this->attributes->append( 'srcset', $source, $glue );
		}

		if ( ! $this->attributes->has( 'srcset' ) ) {
			return;
		}

		$this->attributes->add( 'sizes', '100vw' );
	}

}

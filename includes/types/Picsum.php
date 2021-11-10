<?php
/**
 * Placeholder image service: Lorem Picsum
 *
 * @link https://picsum.photos/
 * @todo add static functions for API calls
 */

declare( strict_types=1 );

namespace Image_Tag\Types;
use Image_Tag\Data_Stores\Attributes;
use Image_Tag\Data_Stores\Settings;

defined( 'WPINC' ) || die();

/**
 * Class: Image_Tag\Types\Picsum
 */
class Picsum extends \Image_Tag\Abstracts\Base implements \Image_Tag\Interfaces\Dynamic_Source {

	const BASE_URL = 'https://picsum.photos';

	/**
	 * @var array Image types.
	 */
	const TYPES = array(
		'picsum',
		'__placeholder',
	);

	/**
	 * Get list of available images from Picsum API.
	 *
	 * @link https://picsum.photos/#list-images Documentation
	 * @param int $page
	 * @return array
	 */
	static function list( int $page = 1 ) : array {
		$url = add_query_arg( array(
			'page' => $page,
			'limit' => 50,
		), static::BASE_URL . '/v2/list' );

		$response = wp_remote_get( $url );

		if ( 200 !== wp_remote_retrieve_response_code( $response ) )
			return array();

		$body = wp_remote_retrieve_body( $response );
		return json_decode( $body );
	}

	/**
	 * Get image details from Picsum API.
	 *
	 * @link https://picsum.photos/#image-details Documentation.
	 * @param int $image_id
	 * @return object
	 */
	static function details( int $image_id ) : object {
		$default = ( object ) array(
			'id' => '',
			'author' => '',
			'width' => 0,
			'height' => 0,
			'url' => '',
			'download_url' => '',
		);

		if ( empty( $image_id ) )
			return $default;

		$url = sprintf( '%s/id/%d/info', static::BASE_URL, $image_id );

		$response = wp_remote_get( $url );

		if ( 200 !== wp_remote_retrieve_response_code( $response ) )
			return $default;

		$body = wp_remote_retrieve_body( $response );
		return json_decode( $body );
	}

	/**
	 * Construct.
	 *
	 * @param null|array|Attributes $attributes
	 * @param null|array|Settings $settings
	 * @uses $this->construct()
	 */
	function __construct( $attributes = null, $settings = null ) {
		$this->construct( $attributes, $settings );
	}

	/**
	 * Create Attributes object to use for output.
	 *
	 * @uses Base::output_attributes()
	 * @uses $this->generate_source()
	 * @return Attributes
	 */
	protected function output_attributes() : Attributes {
		$attributes = parent::output_attributes();
		$attributes->update( 'src', $this->generate_source() );

		return $attributes;
	}

	/**
	 * Generate image source.
	 *
	 * @uses Settings::has()
	 * @uses Settings::get()
	 * @uses Attributes::has()
	 * @uses Attributes::get()
	 * @uses Attributes::update()
	 * @return string
	 */
	 function generate_source() : string {
		static $random = 1;

		if ( array_key_exists( __FUNCTION__, $this->cache ) )
			return $this->cache[ __FUNCTION__ ];

		$src = array( static::BASE_URL );

		# Image ID
		if ( $this->settings->has( 'image-id', false ) )
			$src[] = sprintf( 'id/%d', absint( $this->settings->get( 'image-id' ) ) );

		# Seed
		if ( $this->settings->has( 'seed', false ) )
			$src[] = sprintf( 'seed/%s', sanitize_title_with_dashes( $this->settings->get( 'seed' ) ) );

		# Width
		     if ( $this->settings->has(   'width', false ) ) $src[] = $this->settings->get(   'width' );
		else if ( $this->attributes->has( 'width', false ) ) $src[] = $this->attributes->get( 'width' );

		# Height
		     if ( $this->settings->has(   'height', false ) ) $src[] = $this->settings->get(   'height' );
		else if ( $this->attributes->has( 'height', false ) ) $src[] = $this->attributes->get( 'height' );

		# Convert to string
		$src = implode( '/', $src );

		# Grayscale
		if ( $this->settings->has( 'grayscale', false ) )
			$src = add_query_arg( 'grayscale', 1, $src );

		# Blur
		if ( $this->settings->has( 'blur', false ) )
			$src = add_query_arg( 'blur', absint( $this->settings->get( 'blur' ) ), $src );

		if ( $this->settings->has( 'random', false ) )
			$src = add_query_arg( 'random', $random++, $src );

		$this->cache[ __FUNCTION__ ] = $src;

		return $src;
	}

	/**
	 * Get ratio of image dimensions: width divided by height.
	 *
	 * @return float
	 */
	public function ratio() : float {
		$width  = 0;
		$height = 0;

		if ( $this->settings->has( 'width', false ) )
			$width = $this->settings->get( 'width' );
		else if ( $this->attributes->has( 'width', false ) )
			$width = $this->attributes->get( 'width' );

		if ( $this->settings->has( 'height', false ) )
			$height = $this->settings->get( 'height' );
		else if ( $this->attributes->has( 'height', false ) )
			$height = $this->attributes->get( 'height' );

		if ( empty( $height ) )
			$height = $width;

		if ( empty( $width ) )
			$width = $height;

		if ( empty( $height ) )
			return 0;

		return absint( $width ) / absint( $height );
	}

	/**
	 * Perform validation checks.
	 *
	 * @uses $this->validate_dimensions()
	 * @return WP_Error
	 */
	protected function perform_validation_checks() : \WP_Error {
		$errors = new \WP_Error;

		try {
			$this->validate_dimensions();
		} catch ( \Exception $e ) {
			$errors->add( 'picsum', $e->getMessage() );
		}

		return $errors;
	}

	/**
	 * Check that at least one dimension is set.
	 *
	 * @uses Settings::has()
	 * @uses Attributes::has()
	 * @return void
	 */
	protected function validate_dimensions() : void {
		if (
			   $this->settings->has(   'width',  false )
			|| $this->attributes->has( 'width',  false )
			|| $this->settings->has(   'height', false )
			|| $this->attributes->has( 'height', false )
		)
			return;

		throw new \Exception( 'Picsum requires at least one dimension.' );
	}

	/**
	 * Prevent conversion to same type.
	 *
	 * @param null|array|Attributes $attributes
	 * @param null|array|Settings $settings
	 * @return self
	 */
	function picsum( $attributes = null, $settings = null ) : self {
		trigger_error( sprintf( 'Image is already type <code>%s</code>', $this->get_type() ) );
		return $this;
	}

	/**
	 * Get image info from Picsum API.
	 *
	 * @uses Settings::has()
	 * @uses Settings::get()
	 * @uses static::details()
	 * @return object
	 */
	function info() : object {
		if ( array_key_exists( __FUNCTION__, $this->cache ) )
			return $this->cache[ __FUNCTION__ ];

		$defaults = ( object ) array(
			'id' => '',
			'author' => '',
			'width' => 0,
			'height' => 0,
			'url' => '',
			'download_url' => '',
		);

		if ( !$this->settings->has( 'image-id' ) )
			return $defaults;

		$id = absint( $this->settings->get( 'image-id' ) );

		if ( empty( $id ) )
			return $defaults;

		$details = static::details( $id );
		$this->cache[ __FUNCTION__ ] = $details;

		return $details;
	}

}
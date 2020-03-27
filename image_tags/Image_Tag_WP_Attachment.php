<?php
/**
 * Image tag generator for WordPress attachment images.
 */

defined( 'ABSPATH' ) || die();

require_once 'Image_Tag_WP.php';

/**
 * Class: Image_Tag_WP_Attachment
 */
class Image_Tag_WP_Attachment extends Image_Tag_WP {

	/**
	 * @var int $attachment_id
	 */
	protected $attachment_id;

	/**
	 * @var array $settings
	 */
	protected $settings = array(
		'image-sizes' => array( 'full' ),
	);

	/**
	 * @var array $versions
	 */
	protected $versions = array(
		'__largest' => null,
		'__smallest' => null,
	);

	/**
	 * Construct.
	 *
	 * @param int $attachment_id
	 * @param array $attributes
	 * @param array $settings
	 * @uses Image_Tag::__construct()
	 * @uses $this->set_source()
	 */
	protected function __construct( int $attachment_id, array $attributes = array(), array $settings = array() ) {
		$this->attachment_id = $attachment_id;

		parent::__construct( $attributes, $settings );

		if ( !$this->is_valid() )
			return;

		$this->set_source();
		$this->set_srcset();
		$this->set_orientation();
	}

	/**
	 * Getter.
	 *
	 * @param string $key
	 * @uses Image_Tag_WP::__get()
	 * @return mixed
	 */
	function __get( $key ) {
		if ( 'attachment_id' === $key )
			return $this->attachment_id;

		return parent::__get( $key );
	}

	/**
	 * Check if the image is valid.
	 *
	 * @uses get_post_type()
	 * @uses wp_attachment_is_image()
	 * @return bool
	 */
	function is_valid() {
		return (
			'attachment' === get_post_type( $this->attachment_id )
			&& wp_attachment_is_image( $this->attachment_id )
		);
	}

	/**
	 * Set attachment image source.
	 *
	 * @uses $this->get_setting()
	 * @uses $this->_set_attribute()
	 */
	protected function set_source() {
		$image_sizes = $this->get_setting( 'image-sizes' );

		for ( $i = 0; $i < count( $image_sizes ); $i++ ) {
			$attachment = wp_get_attachment_image_src( $this->attachment_id, $image_sizes[$i] );

			if ( !empty( $attachment ) )
				break;
		}

		if ( empty( $attachment ) ) {
			trigger_error( sprintf( 'Attachment <code>%d</code> does not exist.', $this->attachment_id ), E_USER_WARNING );
			$this->_set_attribute( 'src', self::BLANK );
			return;
		}

		$this->_set_attribute( 'src', $attachment[0] );
	}

	/**
	 * Set "srcset" attribute from image versions.
	 *
	 * @uses $this->get_attribute()
	 * @uses $this->get_versions()
	 * @uses $this->add_srcset()
	 */
	protected function set_srcset() {
		if ( !empty( $this->get_attribute( 'srcset' ) ) )
			return;

		$versions = $this->get_versions();
		unset(
			$versions['__largest'],
			$versions['__smallest']
		);

		if ( 1 === count( $versions ) )
			return;

		foreach ( $versions as $version )
			if (
				   !empty( $version->url )
				&& !empty( $version->width )
			)
				$this->add_srcset( $version->url . ' ' . $version->width . 'w' );

	}

	/**
	 * Set "image_sizes" setting.
	 *
	 * @param array|string
	 * @uses $this->_set_setting()
	 */
	protected function set_image_sizes_setting( $image_sizes ) {
		if ( is_string( $image_sizes ) )
			$image_sizes = explode( ' ', $image_sizes );

		if ( !is_array( $image_sizes ) ) {
			trigger_error( 'Image sizes must be a string or array.' );
			return array( 'full' );
		}

		$wp_image_sizes = get_intermediate_image_sizes();
		$wp_image_sizes[] = 'full';

		$image_sizes = array_values( array_intersect( $image_sizes, $wp_image_sizes ) );

		foreach ( $image_sizes as $i => $image_size )
			if ( empty( wp_get_attachment_image_src( $this->attachment_id, $image_size ) ) )
				unset( $image_sizes[$i] );

		$this->_set_setting( 'image-sizes', $image_sizes );
	}

	/**
	 * Get width of largest image version.
	 *
	 * @uses $this->get_versions()
	 * @return int
	 */
	function get_width() {
		return ( int ) $this->get_versions()['__largest']->width;
	}

	/**
	 * Get height of largest image version.
	 *
	 * @uses $this->get_versions()
	 * @return int
	 */
	function get_height() {
		return ( int ) $this->get_versions()['__largest']->height;
	}

	/**
	 * Magical getter for "class" attribute.
	 *
	 * @uses $this->_get_attribute()
	 * @uses $this->get_setting()
	 */
	protected function get_class_attribute() {
		$classes = $this->_get_attribute( 'class' );

		$image_sizes = $this->get_setting( 'image-sizes' );
		$classes[] = 'size-' . $image_sizes[0];

		$classes = get_post_class( $classes, $this->attachment_id );

		return implode( ' ', array_unique( $classes ) );
	}

	/**
	 * Get data for versions of image from specified image sizes.
	 *
	 * @uses $this->get_setting()
	 * @uses $this->get_version()
	 * @return array
	 */
	function get_versions() {
		if ( !empty( array_filter( $this->versions ) ) )
			return $this->versions;

		$image_sizes = $this->get_setting( 'image-sizes' );
		$largest  = null;
		$smallest = null;

		foreach ( $image_sizes as $image_size ) {
			$version = $this->get_version( $image_size );

			# Determine if largest.
			if (
				is_null( $largest )
				|| ( $version->width * $version->height ) > ( $largest->width * $largest->height )
			)
				$largest = $this->versions['__largest'] = &$this->versions[$image_size];

			# Determine if smallest.
			if (
				is_null( $smallest )
				|| ( $version->width * $version->height ) < ( $smallest->width * $smallest->height )
			)
				$smallest = $this->versions['__smallest'] = &$this->versions[$image_size];
		}

		# If no versions, use "full" image size.
		if ( empty( array_filter( $this->versions ) ) ) {
			trigger_error( sprintf( 'Attachment <code>%d</code> does not have the following sizes: <code>%s</code>; using <code>full</code> image size.', $this->attachment_id, implode( '</code>, <code>', $image_sizes ) ), E_USER_WARNING );

			$this->set_setting( 'image-sizes', array( 'full' ) );

			return $this->get_versions();
		}

		return $this->versions;
	}

	/**
	 * Get data for specified version of image from image sizes.
	 *
	 * @param string $image_size
	 * @uses $this->get_versions()
	 * @return object
	 */
	function get_version( string $image_size ) {
		if ( !empty( $this->versions[$image_size] ) )
			return $this->versions[$image_size];

		if ( in_array( $image_size, array( '__largest', '__smallest' ) ) )
			return $this->get_versions()[$image_size];

		$upload_dir = trailingslashit( wp_get_upload_dir()['basedir'] );

		# If full size.
		if ( 'full' === $image_size ) {
			$version = wp_get_attachment_metadata( $this->attachment_id );
			$version['path'] = $upload_dir . $version['file'];
			$version['file'] = basename( $version['file'] );
			$version['url'] = wp_get_attachment_image_src( $this->attachment_id, 'full' )[0];

		# If intermediate image size.
		} else {
			$version = image_get_intermediate_size( $this->attachment_id, $image_size );

			if ( empty( $version ) )
				return ( object ) array();

			$version['path'] = $upload_dir . $version['path'];
		}

		unset(
			$version['sizes'],
			$version['mime-type'],
			$version['image_meta']
		);

		$version = ( object ) $version;
		$this->versions[$image_size] = $version;

		return $version;
	}

	/**
	 * Get transient key for attachment common colors.
	 *
	 * @param int $attachment_id
	 * @return string
	 */
	static function colors_transient_key( int $attachment_id ) {
		return sprintf( 'attachment_%d_common_colors', $attachment_id );
	}

	/**
	 * Get common colors (cached to attachment's meta data).
	 *
	 * @param int $count
	 * @param bool $force
	 * @uses static::colors_transient_key()
	 * @uses $this->_get_colors()
	 * @uses $this->get_version()
	 * @return array
	 */
	function get_colors( int $count = 3, bool $force = false ) {
		$transient_key = static::colors_transient_key( $this->attachment_id );

		if ( !$force )
			$transient = get_transient( $transient_key );

		if (
			  !empty( $transient )
			&& count( $transient ) >= $count
		)
			return $transient;

		$colors = $this->_get_colors( $this->get_version( '__largest' )->path, $count );
		set_transient( $transient_key, $colors, DAY_IN_SECONDS );

		return $colors;
	}

	/**
	 * Transpose WP attachment image to Picsum image.
	 *
	 * @param array $attributes
	 * @param array $settings
	 * @uses Image_Tag->picsum()
	 * @uses Image_Tag_Picsum->get_attribute()
	 * @uses Image_Tag_Picsum->set_attribute()
	 * @uses $this->get_versions()
	 * @uses Image_Tag::create()
	 * @uses Image_Tag_Picsum->add_srcset()
	 * @return Image_Tag_Picsum
	 */
	function picsum( array $attributes = array(), array $settings = array() ) {
		$picsum = parent::picsum( $attributes, $settings );

		if (
			empty( $attributes['srcset'] )
			&& !empty( $picsum->get_attribute( 'srcset' ) )
		) {
			$picsum->set_attribute( 'srcset', array() );

			foreach ( $this->get_versions() as $image_size => $version ) {
				if ( in_array( $image_size, array( '__largest', '__smallest' ) ) )
					continue;

				$tmp = Image_Tag::create( 'picsum', array(), array(
					 'width' => $version->width,
					'height' => $version->height,
					'random' => true,
				) );

				$picsum->add_srcset( $tmp->get_attribute( 'src' ) . ' ' . $version->width . 'w' );
			}
		}

		return $picsum;
	}

	/**
	 * Transpose WP attachment image to Placeholder image.
	 *
	 * @param array $attributes
	 * @param array $settings
	 * @uses Image_Tag->placeholder()
	 * @uses Image_Tag_Placeholder->get_attribute()
	 * @uses Image_Tag_Placeholder->set_attribute()
	 * @uses $this->get_versions()
	 * @uses Image_Tag::create()
	 * @uses Image_Tag_Placeholder->add_srcset()
	 * @return Image_Tag_Placeholder
	 */
	function placeholder( array $attributes = array(), array $settings = array() ) {
		$placeholder = parent::placeholder( $attributes, $settings );

		if (
			empty( $attributes['srcset'] )
			&& !empty( $placeholder->get_attribute( 'srcset' ) )
		) {
			$placeholder->set_attribute( 'srcset', array() );

			foreach ( $this->get_versions() as $image_size => $version ) {
				if ( in_array( $image_size, array( '__largest', '__smallest' ) ) )
					continue;

				$tmp = Image_Tag::create( 'placeholder', array(), array(
					 'width' => $version->width,
					'height' => $version->height,
				) );

				$placeholder->add_srcset( $tmp->get_attribute( 'src' ) . ' ' . $version->width . 'w' );
			}
		}

		return $placeholder;
	}

	/**
	 * Get transient key for base64 encoded LQIP.
	 *
	 * @param int $attachment_id
	 * @return string
	 */
	static function lqip_transient_key( int $attachment_id ) {
		return sprintf( 'attachment_%d_lqip_base64', $attachment_id );
	}

	/**
	 * Get low-quality image placeholder.
	 *
	 * @param array $attributes
	 * @param array $settings
	 * @uses $this->generate_lqip()
	 * @uses static::lqip_transient_key()
	 * @return self
	 */
	function lqip( array $attributes = array(), array $settings = array() ) {
		$_attributes = $this->attributes;

		unset(
			$_attributes['srcset'],
			$_attributes['sizes']
		);

		$defaults = apply_filters( 'image_tag/lqip_defaults', array(
			'lqip-width'  => 100,   // width of generated LQIP
			'lqip-height' => null,  // height of generated LQIP
			'lqip-crop'   => false, // crop or maintain aspect ratio of LQIP
			'lqip-force'  => false, // force regenerate LQIP
		) );

		$attributes = wp_parse_args( $attributes, $_attributes );
		$settings   = wp_parse_args( $settings, $this->settings );
		$settings   = wp_parse_args( $settings, $defaults );

		# Force generate the LQIP.
		if ( $settings['lqip-force'] )
			return $this->generate_lqip( $attributes, $settings );

		# Get transient value.
		$transient = get_transient( static::lqip_transient_key( $this->attachment_id ) );

		# Check transient exists.
		if ( !empty( $transient ) ) {
			$this->versions['__lqip'] = ( object ) array(
				'url' => $transient,
			);
			$lqip = clone $this;

			$this->set_attributes( $attributes );
			$this->set_settings( $settings );

			$lqip->set_attribute( 'src', $transient );
			$lqip->add_class( 'lqip' );

			return $lqip;
		}

		# Generate the LQIP.
		return $this->generate_lqip( $attributes, $settings );
	}

	/**
	 * Generate LQIP.
	 *
	 * @link https://stackoverflow.com/questions/3967515/how-to-convert-an-image-to-base64-encoding
	 * @param array $attributes
	 * @param array $settings
	 * @uses $this->get_version()
	 * @uses static::lqip_transient_key()
	 * @return self
	 */
	protected function generate_lqip( array $attributes, array $settings ) {
		$editor = wp_get_image_editor( $this->get_version( 'full' )->path );

		if (
			   is_wp_error( $editor )
			|| is_wp_error( $editor->resize( $settings['lqip-width'], $settings['lqip-height'], $settings['lqip-crop'] ) )
		) {
			$lqip = clone $this;
			$lqip->set_setting( 'image-sizes', 'medium' );
			return $lqip;
		}

		$lqip_meta = $editor->save();

		$type = pathinfo( $lqip_meta['path'], PATHINFO_EXTENSION );
		$data = file_get_contents( $lqip_meta['path'] );
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode( $data );

		set_transient( static::lqip_transient_key( $this->attachment_id ), $base64, DAY_IN_SECONDS );

		$this->versions['__lqip'] = ( object ) array(
			'url' => $base64,
		);

		$lqip = clone $this;

		$lqip->set_attributes( $attributes );
		$lqip->set_settings( $settings );

		$lqip->add_class( 'lqip' );
		$lqip->set_attribute( 'src', $base64 );

		return $lqip;
	}

}

?>
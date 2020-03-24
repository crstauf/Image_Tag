<?php
/**
 * Image tag generator.
 *
 * Plugin name: Image Tag Generator
 * Plugin URI: https://github.com/crstauf/image_tag
 * Description: WordPress drop-in to generate <code>img</code> tags.
 * Author: Caleb Stauffer
 * Author URI: https://develop.calebstauffer.com
 * Version: 1.0
 */

/**
 * Class: Image_Tag
 */
class Image_Tag implements ArrayAccess {

	/**
	 * @var string Encoded transparent gif.
	 */
	const BLANK = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

	/**
	 * @var array $attributes Internal attributes of img tag.
	 */
	protected $attributes = array(

		# Strings.
		'id' => null,
		'alt' => null,
		'src' => null,
		'width' => null,
		'height' => null,

		# Arrays.
		'class' => array(),
		'style' => array(),
		'sizes' => array(),
		'srcset' => array(),

	);

	/**
	 * @var array $settings Settings of img tag.
	 */
	protected $settings = array();

	/**
	 * @param $source
	 * @param array $attributes
 	 * @param array $settings
 	 * @return Image_Tag
	 */
	static function create( $source, array $attributes = array(), array $settings = array() ) {

		# If integer, create WordPress attachment image.
		if ( is_int( $source ) )
			return new Image_Tag_WP_Attachment( $source, $attributes, $settings );

		# If source is "picsum", create picsum.photos image.
		if ( 'picsum' === $source )
			return new Image_Tag_Picsum( $attributes, $settings );

		# If source is "placeholder", create Placeholder image.
		if ( 'placeholder' === $source )
			return new Image_Tag_Placeholder( $attributes, $settings );

		# If URL, create external image.
		if ( ( bool ) wp_http_validate_url( $source ) ) {
			$attributes['src'] = $source;
			return new Image_Tag( $attributes, $settings );
		}

		# If string, create WordPress theme image.
		if ( is_string( $source ) )
			return new Image_Tag_WP_Theme( $source, $attributes, $settings );

		trigger_error( sprintf( 'Unable to determine image type from source: <code>%s</code>.', $source ), E_USER_WARNING );
	}

	/**
	 * Construct.
	 *
	 * @param $source
	 * @param array $attributes
	 * @param array $settings
	 * @uses $this->set_attributes()
	 * @uses $this->set_settings()
	 */
	function __construct( array $attributes = array(), array $settings = array() ) {
		$this->set_attributes( $attributes );
		$this->set_settings( $settings );
	}

	/**
	 * Getter.
	 *
	 * @param string $key
	 * @return mixed
	 */
	function __get( $key ) {
		return $this->attributes[$key];
	}

	/**
	 * To string.
	 *
	 * @uses $this->get_attributes()
	 * @return string
	 */
	function __toString() {
		$array = array( '<img' );

		foreach ( $this->get_attributes() as $key => $value )
			$array[$key] = $key . '="' . esc_attr( $value ) . '"';

		$array[] = '/>';

		$array = apply_filters( 'image_tag/_output/array', $array );

		$string  = $this->get_setting( 'before_output' );
		$string .= apply_filters( 'image_tag/_output/string', implode( ' ', $array ) );
		$string .= $this->get_setting( 'after_output' );

		return apply_filters( 'image_tag/output', $string );
	}

	/**
	 * Check if the image is valid.
	 *
	 * @uses $this->get_attribute()
	 * @return bool
	 */
	function is_valid() {
		return !empty( $this->get_attribute( 'src' ) );
	}

	/**
	 * Check type of image.
	 *
	 * @param string $type
	 * @return bool
	 */
	function is_type( string $type ) {
		switch ( strtolower( $type ) ) {

			case 'remote':
			case 'external':
				return (
					Image_Tag::class === get_class( $this )
					|| $this->is_type( '__placeholder' )
				);

			case '__placeholder':
				return (
					   $this->is_type( 'picsum' )
					|| $this->is_type( 'placeholder' )
				);

			case 'attachment':
			case 'wp-attachment':
			case 'wordpress-attachment':
				return is_a( $this, 'Image_Tag_WP_Attachment' );

			case 'theme':
			case 'wp-theme':
			case 'wordpress-theme':
				return is_a( $this, 'Image_Tag_WP_Theme' );

			case 'wp':
			case 'local':
			case 'internal':
			case 'wordpress':
				return is_a( $this, 'Image_Tag_WP' );

			case 'picsum':
				return is_a( $this, 'Image_Tag_Picsum' );

			case 'placeholder':
				return is_a( $this, 'Image_Tag_Placeholder' );

		}

		return false;
	}

	/**
	 * Set an array of settings.
	 *
	 * @param array
	 * @uses $this->set_setting()
	 */
	function set_settings( array $settings ) {
		foreach ( $settings as $key => $value )
			$this->set_setting( $key, $value );
	}

	/**
	 * Public acccess to set setting.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @uses $this->_set_setting()
	 */
	function set_setting( string $key, $value ) {
		$method_name = 'set_' . $key . '_setting';
		$method_name = str_replace( '-', '_', $method_name );

		method_exists( $this, $method_name )
			? $this->$method_name( $value )
			: $this->_set_setting( $key, $value );
	}

	/**
	 * Internal access to set setting.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	protected function _set_setting( string $key, $value ) {
		$this->settings[$key] = $value;
	}

	/**
	 * Public access to get setting.
	 *
	 * @param string $key
	 * @uses $this->_get_setting()
	 * @return mixed
	 */
	function get_setting( string $key ) {
		$method_name = 'get_' . $key . '_setting';
		$method_name = str_replace( '-', '_', $method_name );

		return method_exists( $this, $method_name )
			? $this->$method_name()
			: $this->_get_setting( $key );
	}

	protected function get_before_output_setting() {
		return array_key_exists(   'before_output', $this->settings )
			? $this->_get_setting( 'before_output' )
			: '';
	}

	protected function get_after_output_setting() {
		return array_key_exists(   'after_output', $this->settings )
			? $this->_get_setting( 'after_output' )
			: '';
	}

	/**
	 * Internal access to get setting.
	 *
	 * @param string $key
	 * @return mixed
	 */
	protected function _get_setting( string $key ) {
		return $this->settings[$key];
	}

	/**
	 * Set an array of attributes.
	 *
	 * @param array $attributes
	 * @uses $this->set_attribute()
	 */
	function set_attributes( array $attributes ) {
		foreach ( $attributes as $key => $value )
			$this->set_attribute( $key, $value );
	}

	/**
	 * Public access to set attribute.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @uses $this->_set_attribute()
	 */
	function set_attribute( string $key, $value ) {
		$method_name = 'set_' . $key . '_attribute';
		$method_name = str_replace( '-', '_', $method_name );

		method_exists( $this, $method_name )
			? $this->$method_name( $value )
			: $this->_set_attribute( $key, $value );
	}

	/**
	 * Internal setter for "class" attribute.
	 *
	 * @param array|string $classes
	 * @uses $this->_set_attribute()
	 */
	protected function set_class_attribute( $classes ) {
		if ( is_string( $classes ) )
			$classes = explode( ' ', $classes );

		if ( !is_array( $classes ) ) {
			trigger_error( sprintf( 'Value of type <code>%s</code> is not valid for <code>class</code> attribute.', gettype( $classes ) ), E_USER_NOTICE );
			return;
		}

		$classes = array_map( 'trim', $classes );
		$this->_set_attribute( 'class', $classes );
	}

	/**
	 * Internal setter for "sizes" attribute.
	 *
	 * @param array|string $sizes
	 * @uses $this->_set_attribute()
	 */
	protected function set_sizes_attribute( $sizes ) {
		if ( is_string( $sizes ) )
			$sizes = explode( ', ', $sizes );

		if ( !is_array( $sizes ) ) {
			trigger_error( sprintf( 'Value of type <code>%s</code> is not valid for <code>sizes</code> attribute.', gettype( $sizes ) ), E_USER_NOTICE );
			return;
		}

		$sizes = array_map( 'trim', $sizes );
		$this->_set_attribute( 'sizes', $sizes );
	}

	/**
	 * Internal setter for "srcset" attribute.
	 *
	 * @param array|string $srcset
	 * @uses $this->_set_attribute()
	 */
	protected function set_srcset_attribute( $srcset ) {
		if ( is_string( $srcset ) )
			$srcset = explode( ', ', $srcset );

		if ( !is_array( $srcset ) ) {
			trigger_error( sprintf( 'Value of type <code>%s</code> is not valid for <code>srcset</code> attribute.', gettype( $srcset ) ), E_USER_NOTICE );
			return;
		}

		$srcset = array_map( 'trim', $srcset );
		$this->_set_attribute( 'srcset', $srcset );
	}

	/**
	 * Internal setter for "style" attribute.
	 *
	 * @param array|string $styles
	 * @uses $this->_set_attribute()
	 */
	protected function set_style_attribute( $styles ) {
		if ( is_string( $styles ) )
			$styles = explode( ';', $styles );

		if ( !is_array( $styles ) ) {
			trigger_error( sprintf( 'Value of type <code>%s</code> is not valid for <code>style</code> attribute.', gettype( $styles ) ), E_USER_NOTICE );
			return;
		}

		$styles = array_map( 'trim', $styles );
		$this->_set_attribute( 'style', $styles );
	}

	/**
	 * Internal setter for attribute.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	protected function _set_attribute( string $key, $value ) {
		$this->attributes[$key] = $value;
	}

	/**
	 * Public access to get attributes.
	 *
	 * @uses $this->get_attribute()
	 * @return array
	 */
	function get_attributes() {
		$attributes = array();

		foreach ( array_keys( $this->attributes ) as $key )
			$attributes[$key] = $this->get_attribute( $key );

		return array_filter( $attributes );
	}

	/**
	 * Public access to get attribute.
	 *
	 * @param string $key
	 * @uses $this->_get_attribute()
	 * @return mixed
	 */
	function get_attribute( string $key ) {
		$method_name = 'get_' . $key . '_attribute';
		$method_name = str_replace( '-', '_', $method_name );

		return method_exists( $this, $method_name )
			? $this->$method_name()
			: $this->_get_attribute( $key );
	}

	/**
	 * Internal getter for "class" attribute.
	 *
	 * @return string
	 */
	protected function get_class_attribute() {
		return implode( ' ', array_unique( $this->attributes['class'] ) );
	}

	/**
	 * Internal getter for "sizes" attribute.
	 *
	 * @return string
	 */
	protected function get_sizes_attribute() {
		return implode( ', ', $this->attributes['sizes'] );
	}

	/**
	 * Internal getter for "srcset" attribute.
	 *
	 * @return string
	 */
	protected function get_srcset_attribute() {
		return implode( ', ', $this->attributes['srcset'] );
	}

	/**
	 * Internal getter for "style" attribute.
	 *
	 * @return string
	 */
	protected function get_style_attribute() {
		return trim( implode( '; ', $this->attributes['style'] ) );
	}

	/**
	 * Internal access to get attribute.
	 *
	 * @param string $key
	 * @return mixed
	 */
	protected function _get_attribute( string $key ) {
		return $this->attributes[$key];
	}

	/**
	 * Helper to add class name.
	 *
	 * @param string $class
	 */
	function add_class( string $class ) {
		$this->attributes['class'][] = $class;
	}

	/**
	 * Helper to remove class name.
	 *
	 * @param string $class
	 */
	function remove_class( string $class ) {
		foreach ( $this->attributes['class'] as $i => $_class )
			if ( $class === $_class )
				unset( $this->attributes['class'][$i] );
	}

	/**
	 * Helper to add size to "sizes" attribute.
	 *
	 * @param string $size
	 */
	function add_size( string $size ) {
		$this->attributes['sizes'][] = $size;
	}

	/**
	 * Helper to add source to "srcset" attribute.
	 *
	 * @param string $srcset
	 */
	function add_srcset( string $srcset ) {
		$this->attributes['srcset'][] = $srcset;
	}

	/**
	 * Helper to add style to "style" attribute.
	 *
	 * @param string $style
	 */
	function add_style( string $style ) {
		$this->attributes['style'][] = $style;
	}

	/**
	 * Get primary width of image.
	 *
	 * @uses $this->get_attribute()
	 * @return null|int
	 */
	function get_width() {
		return $this->get_attribute( 'width' );
	}

	/**
	 * Get primary height of image.
	 *
	 * @uses $this->get_attribute()
	 * @return null|int
	 */
	function get_height() {
		return $this->get_attribute( 'height' );
	}

	/**
	 * Get image ratio.
	 *
	 * @uses $this->get_height()
	 * @uses $this->get_width()
	 * @return float
	 */
	function get_ratio() {
		$height = $this->get_height();
		$width  = $this->get_width();

		if (
			   !is_numeric( $width  )
			|| !is_numeric( $height )
		)
			return null;

		return $height / $width;
	}

	/**
	 * Request image with GET method.
	 *
	 * @param bool $force Flag to use cached value or make new request.
	 * @uses $this->get_attribute()
	 * @uses wp_remote_get()
	 * @return WP_Error|array
	 */
	function http( bool $force = false ) {
		static $_cache = array();

		$src = $this->get_attribute( 'src' );

		if (
			!$force
			&& isset( $_cache[$src] )
		)
			return $_cache[$src];

		return ( $_cache[$src] = wp_remote_get( $src ) );
	}

	/**
	 * Transpose attributes and settings into Picsum image.
	 *
	 * @param array $attributes
	 * @param array $settings
	 * @uses $this->get_width()
	 * @uses $this->get_height()
	 * @return Image_Tag_Picsum
	 */
	function picsum( array $attributes = array(), array $settings = array() ) {
		$attributes = wp_parse_args( $attributes, $this->attributes );
		$settings = wp_parse_args( $settings, array(
			 'width' => $this->get_width(),
			'height' => $this->get_height(),
		) );

		return Image_Tag::create( 'picsum', $attributes, $settings );
	}

	/**
	 * Transpose attributes and settings into Placeholder image.
	 *
	 * @param array $attributes
	 * @param array $settings
	 * @uses $this->get_width()
	 * @uses $this->get_height()
	 * @return Image_Tag_Placeholder
	 */
	function placeholder( array $attributes = array(), array $settings = array() ) {
		$attributes = wp_parse_args( $attributes, $this->attributes );
		$settings = wp_parse_args( $settings, array(
			 'width' => $this->get_width(),
			'height' => $this->get_height(),
		) );

		return Image_Tag::create( 'placeholder', $attributes, $settings );
	}

	/**
	 * Create noscript version of image tag.
	 *
	 * @param array $attributes
	 * @param array $settings
	 *
	 * @uses $this->set_attributes()
	 * @uses $this->set_settings()
	 * @uses $this->set_setting()
	 * @uses $this->get_setting()
	 * @uses $this->add_class()
	 *
	 * @return $this
	 */
	function noscript( array $attributes = array(), array $settings = array() ) {
		$nojs = clone $this;

		$nojs->set_attributes( $attributes );
		$nojs->set_settings( $settings );

		$nojs->set_setting( 'before_output',  '<noscript>' . $this->get_setting( 'before_output' ) );
		$nojs->set_setting(  'after_output', '</noscript>' . $this->get_setting(  'after_output' ) );

		$nojs->add_class( 'no-js' );

		return $nojs;
	}

	/**
	 * Create lazyloaded version of image tag.
	 *
	 * @param array $attributes
	 * @param array $settings
	 *
	 * @uses $this->set_attributes()
	 * @uses $this->set_settings()
	 * @uses $this->add_class()
	 * @uses $this->get_attribute()
	 * @uses $this->set_attribute()
	 * @uses $this->noscript()
	 *
	 * @return $this
	 */
	function lazyload( array $attributes = array(), array $settings = array() ) {
		$attributes = wp_parse_args( $attributes, array(
			'data-src' => null,
			'data-sizes' => null,
			'data-srcset' => null,
		) );

		$lazyload = clone $this;

		$lazyload->set_attributes( $attributes );
		$lazyload->set_settings( $settings );

		$lazyload->add_class( 'lazyload hide-if-no-js' );

		if ( !empty( $lazyload->get_attribute( 'src' ) ) ) {
			$lazyload->set_attribute( 'data-src', $lazyload->get_attribute( 'src' ) );
			$lazyload->set_attribute( 'src', static::BLANK );
		}

		if ( !empty( $lazyload->get_attribute( 'srcset' ) ) ) {
			$lazyload->set_attribute( 'data-srcset', $lazyload->get_attribute( 'srcset' ) );
			$lazyload->set_attribute( 'srcset', array() );
		}

		if ( !empty( $lazyload->get_attribute( 'sizes' ) ) ) {
			$lazyload->set_attribute( 'data-sizes', $lazyload->get_attribute( 'sizes' ) );
			$lazyload->set_attribute( 'sizes', array() );
		} else if ( !empty( $lazyload->get_attribute( 'data-srcset' ) ) )
			$lazyload->set_attribute( 'data-sizes', 'auto' );

		$lazyload->set_setting( 'after_output', $this->noscript() );

		return $lazyload;
	}

	/**
	 * ArrayAccess: exists
	 *
	 * @param $offset
	 * @return bool
	 */
	function offsetExists( $offset ) {
		return (
			   isset( $this->attributes[$offset] )
			|| isset( $this->settings[$offset] )
		);
	}

	/**
	 * ArrayAccess: get
	 *
	 * @param $offset
	 * @return mixed
	 */
	function offsetGet( $offset ) {
		if ( isset( $this->attributes[$offset] ) )
			return $this->attributes[$offset];

		if ( isset( $this->settings[$offset] ) )
			return $this->settings[$offset];

		return null;
	}

	/**
	 * ArrayAccess: set
	 *
	 * @param $offset
	 * @param $value
	 */
	function offsetSet( $offset, $value ) {}

	/**
	 * ArrayAccess: unset
	 *
	 * @param $offset
	 */
	function offsetUnset( $offset ) {}

}


/*
##      ## ########
##  ##  ## ##     ##
##  ##  ## ##     ##
##  ##  ## ########
##  ##  ## ##
##  ##  ## ##
 ###  ###  ##
*/

/**
 * Abstract class: Image_Tag_WP
 */
abstract class Image_Tag_WP extends Image_Tag {

	/**
	 * @var null|string $orientation
	 */
	protected $orientation = null;

	/**
	 * Determine and set image orientation.
	 *
	 * @uses $this->get_ratio()
	 * @uses $this->add_class()
	 */
	function set_orientation() {
		$ratio = $this->get_ratio();

		if ( $ratio > 1 )
			$this->orientation = 'portrait';

		else if ( $ratio < 1 )
			$this->orientation = 'landscape';

		else if ( 1 === $ratio )
			$this->orientation = 'square';

		else
			$this->orientation = 'unknown';

		$this->add_class( 'orientation-' . $this->orientation );
	}

	/**
	 * Get common colors.
	 *
	 * @param string $path Path to image.
	 * @param int $count Number of colors to determine.
	 * @uses GetImageMostCommonColors->Get_Colors()
	 * @return array
	 */
	protected function _get_colors( string $path, int $count = 3 ) {
		static $util = null;
		require_once trailingslashit( __DIR__ ) . 'class-get-image-most-common-colors.php';

		if ( is_null( $util ) )
			$util = new GetImageMostCommonColors;

		$_colors = $util->Get_Colors( $path, $count );
		$colors = array();

		foreach ( $_colors as $color => $percentage )
			$colors['#' . $color] = $percentage;

		return $colors;
	}

	/**
	 * Public access to get common colors.
	 *
	 * Abstract to pass proper image path, and
	 * opportunity to implement caching.
	 *
	 * @param int $count
	 * @uses $this->_get_colors()
	 * @return array
	 */
	abstract function get_colors( int $count = 3 );

	/**
	 * Get most common color.
	 *
	 * @uses $this->get_colors()
	 * @return string
	 */
	function get_mode_color() {
		return array_keys( $this->get_colors() )[0];
	}

}


/*
##      ## ########   ##        ###    ######## ########    ###     ######  ##     ## ##     ## ######## ##    ## ########
##  ##  ## ##     ## ####      ## ##      ##       ##      ## ##   ##    ## ##     ## ###   ### ##       ###   ##    ##
##  ##  ## ##     ##  ##      ##   ##     ##       ##     ##   ##  ##       ##     ## #### #### ##       ####  ##    ##
##  ##  ## ########          ##     ##    ##       ##    ##     ## ##       ######### ## ### ## ######   ## ## ##    ##
##  ##  ## ##         ##     #########    ##       ##    ######### ##       ##     ## ##     ## ##       ##  ####    ##
##  ##  ## ##        ####    ##     ##    ##       ##    ##     ## ##    ## ##     ## ##     ## ##       ##   ###    ##
 ###  ###  ##         ##     ##     ##    ##       ##    ##     ##  ######  ##     ## ##     ## ######## ##    ##    ##
*/

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
	 * @uses Image_Tag::__get()
	 * @return string
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
	 * @return array
	 */
	function get_versions() {
		if ( !empty( array_filter( $this->versions ) ) )
			return $this->versions;

		$image_sizes = $this->get_setting( 'image-sizes' );
		$upload_dir = trailingslashit( wp_get_upload_dir()['basedir'] );

		$largest  = null;
		$smallest = null;

		foreach ( $image_sizes as $image_size ) {

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
					continue;

				$version['path'] = $upload_dir . $version['path'];
			}

			unset(
				$version['sizes'],
				$version['mime-type'],
				$version['image_meta']
			);

			$version = ( object ) $version;
			$this->versions[$image_size] = $version;

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

		return $this->versions;
	}

	/**
	 * Get common colors (cached to attachment's meta data).
	 *
	 * @param int $count
	 * @uses $this->_get_colors()
	 * @uses $this->get_versions()
	 * @return array
	 */
	function get_colors( int $count = 3 ) {
		$meta_key = '_common_colors';
		$meta = get_post_meta( $this->attachment_id, $meta_key, true );

		if (
			  !empty( $meta )
			&& count( $meta ) >= $count
		)
			return $meta;

		$colors = $this->_get_colors( $this->get_versions()['__smallest']->path, $count );
		add_post_meta( $this->attachment_id, $meta_key, $colors, true );

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
	 * Get low-quality image placeholder.
	 *
	 * @param array $attributes
	 * @param array $settings
	 * @uses Images_Tag::create()
	 * @uses Image_Tag->add_class()
	 * @return self
	 */
	function lqip( array $attributes = array(), array $settings = array( 'image-sizes' => 'medium' ) ) {
		$_attributes = $this->attributes;
		unset(
			$_attributes['srcset'],
			$_attributes['sizes']
		);

		$attributes = wp_parse_args( $attributes, $_attributes );
		$settings   = wp_parse_args( $settings, $this->settings );

		$lqip = Image_Tag::create( $this->attachment_id, $attributes, $settings );
		$lqip->add_class( 'lqip' );

		return $lqip;
	}

}


/*
##      ## ########   ##     ######## ##     ## ######## ##     ## ########
##  ##  ## ##     ## ####       ##    ##     ## ##       ###   ### ##
##  ##  ## ##     ##  ##        ##    ##     ## ##       #### #### ##
##  ##  ## ########             ##    ######### ######   ## ### ## ######
##  ##  ## ##         ##        ##    ##     ## ##       ##     ## ##
##  ##  ## ##        ####       ##    ##     ## ##       ##     ## ##
 ###  ###  ##         ##        ##    ##     ## ######## ##     ## ########
*/

/**
 * Class: Image_Tag_WP_Theme
 */
class Image_Tag_WP_Theme extends Image_Tag_WP {

	/**
	 * @var string $path
	 */
	protected $path;

	/**
	 * Construct.
	 *
	 * @param string $source
	 * @param array $attributes
	 * @param array $settings
	 * @uses Image_Tag::__construct()
	 */
	protected function __construct( string $source, array $attributes = array(), array $settings = array() ) {
		$this->path = locate_template( $source );

		if ( !$this->is_valid() ) {
			trigger_error( sprintf( 'Unable to find <code>%s</code> in theme.', $this->path ), E_USER_WARNING );
			return;
		}

		parent::__construct( $attributes, $settings );

		$this->set_source( $source );
		$this->set_orientation();
	}

	/**
	 * Check if the image is valid.
	 *
	 * @return bool
	 */
	function is_valid() {
		return (
			!empty( $this->path )
			&& file_exists( $this->path )
		);
	}

	/**
	 * Set theme image source.
	 *
	 * @param string $source
	 * @uses $this->_set_attribute()
	 */
	protected function set_source( string $source ) {
		foreach ( array(
			STYLESHEETPATH => get_stylesheet_directory_uri(),
			  TEMPLATEPATH => get_template_directory_uri(),
		) as $themepath => $themeurl )
			if ( false !== strpos( $this->path, $themepath ) )
				$this->_set_attribute( 'src', trailingslashit( $themeurl ) . $source );
	}

	/**
	 * Get image width.
	 *
	 * @return int
	 */
	function get_width() {
		return ( int ) getimagesize( $this->path )[0];
	}

	/**
	 * Get image height.
	 *
	 * @return int
	 */
	function get_height() {
		return ( int ) getimagesize( $this->path )[1];
	}

	/**
	 * Get common colors (cached to transient).
	 *
	 * @param int $count
	  *@uses $this->_get_colors()
	 * @return array
	 */
	function get_colors( int $count = 3 ) {
		$transient_key = 'theme_img_colors_' . md5( $this->path );
		$transient = get_transient( $transient_key );

		if (
			  !empty( $transient )
			&& count( $transient ) >= $count
		)
			return $transient;

		$colors = $this->_get_colors( $this->path, $count );
		set_transient( $transient_key, $colors );

		return $colors;
	}

}


/*
########  ####  ######   ######  ##     ## ##     ##
##     ##  ##  ##    ## ##    ## ##     ## ###   ###
##     ##  ##  ##       ##       ##     ## #### ####
########   ##  ##        ######  ##     ## ## ### ##
##         ##  ##             ## ##     ## ##     ##
##         ##  ##    ## ##    ## ##     ## ##     ##
##        ####  ######   ######   #######  ##     ##
*/

/**
 * Class: Image_Tag_Picsum
 *
 * @link https://picsum.photos
 */
class Image_Tag_Picsum extends Image_Tag {

	/**
	 * @var string Base URL.
	 */
	const BASE_URL = 'https://picsum.photos/';

	/**
	 * @var array $settings
	 */
	protected $settings = array(
		'blur' => false,
		'seed' => null,
		'width' => null,
		'height' => null,
		'random' => false,
		'image_id' => null,
		'grayscale' => false,
	);

	/**
	 * @var array $details
	 */
	protected $details = null;

	/**
	 * To string.
	 *
	 * @uses $this->get_setting()
	 * @uses Image_Tag::__toString()
	 * @return string
	 */
	function __toString() {
		if ( empty( $this->get_setting( 'width' ) ) ) {
			trigger_error( 'Picsum image requires width.', E_USER_WARNING );
			return '';
		}

		return parent::__toString();
	}

	/**
	 * Generate source URL.
	 *
	 * @uses $this->get_setting()
	 * @return string
	 */
	function get_src_attribute() {
		$src = self::BASE_URL;

		# Add ID.
		if ( !empty( $this->get_setting( 'image_id' ) ) )
			$src .= 'id/' . $this->get_setting( 'image_id' ) . '/';

		# Add seed.
		else if ( !empty( $this->get_setting( 'seed' ) ) )
			$src .= 'seed/' . $this->get_setting( 'seed' ) . '/';

		# Add width.
		if ( !empty( $this->get_setting( 'width' ) ) )
			$src .= ( int ) $this->get_setting( 'width' ) . '/';

		# Add height.
		if ( !empty( $this->get_setting( 'height' ) ) )
			$src .= ( int ) $this->get_setting( 'height' ) . '/';

		# Add query params.
		# Add blur.
		if ( false !== $this->get_setting( 'blur' ) )
			$src = add_query_arg( 'blur', $this->get_setting( 'blur' ), $src );

		# Add random.
		if ( !empty( $this->get_setting( 'random' ) ) )
			$src = add_query_arg( 'random', $this->get_setting( 'random' ), $src );

		# Add grayscale.
		if ( !empty( $this->get_setting( 'grayscale' ) ) )
			$src = add_query_arg( 'grayscale', 1, $src );

		return $src;
	}

	/**
	 * Magical getter for "width" attribute.
	 *
	 * @uses $this->_get_attribute()
	 * @uses $this->_get_setting()
	 * @return int
	 */
	function get_width_attribute() {
		if ( !empty( $this->_get_attribute( 'width' ) ) )
			return ( int ) $this->_get_attribute( 'width' );

		if ( !empty( $this->_get_setting( 'width' ) ) )
			return ( int ) $this->_get_setting( 'width' );

		return 1024;
	}

	/**
	 * Magical getter for "height" attribute.
	 *
	 * If specified, returns the height value.
	 * Otherwise, returns the width value.
	 *
	 * @uses $this->_get_attribute()
	 * @uses $this->_get_setting()
	 * @uses $this->get_width_attribute()
	 * @return int
	 */
	function get_height_attribute() {
		if ( !empty( $this->_get_attribute( 'height' ) ) )
			return ( int ) $this->_get_attribute( 'height' );

		if ( !empty( $this->_get_setting( 'height' ) ) )
			return ( int ) $this->_get_setting( 'height' );

		return ( int ) $this->get_width_attribute();
	}

	/**
	 * Get "blur" setting.
	 *
	 * @uses $this->_get_setting()
	 * @return mixed
	 */
	function get_blur_setting() {
		$blur = $this->_get_setting( 'blur' );

		if ( true === $blur )
			return 10;

		return $blur;
	}

	/**
	 * Get "seed" setting.
	 *
	 * @uses $this->_get_setting()
	 * @return string
	 */
	function get_seed_setting() {
		$seed = $this->_get_setting( 'seed' );

		if ( is_null( $seed ) )
			return null;

		return urlencode( sanitize_title_with_dashes( $seed ) );
	}

	/**
	 * Get "width" setting.
	 *
	 * @uses $this->_get_setting()
	 * @uses $this->_get_attribute()
	 * @return null|int
	 */
	function get_width_setting() {
		if ( !empty( $this->_get_setting( 'width' ) ) )
			return ( int ) $this->_get_setting( 'width' );

		if ( !empty( $this->_get_attribute( 'width' ) ) )
			return ( int ) $this->_get_attribute( 'width' );

		return null;
	}

	/**
	 * Get "height" setting.
	 *
	 * @uses $this->_get_setting()
	 * @uses $this->_get_attribute()
	 * @return null|int
	 */
	function get_height_setting() {
		if ( !empty( $this->_get_setting( 'height' ) ) )
			return ( int ) $this->_get_setting( 'height' );

		if ( !empty( $this->_get_attribute( 'height' ) ) )
			return ( int ) $this->_get_attribute( 'height' );

		return null;
	}

	/**
	 * Get "random" setting.
	 *
	 * @uses $this->_get_setting()
	 * @return mixed
	 */
	function get_random_setting() {
		static $_random = 0;
		$random = $this->_get_setting( 'random' );

		if ( false === $random )
			return false;

		if ( true === $random )
			return ++$_random;

		return $random;
	}

	/**
	 * Get image details from API (cached locally).
	 *
	 * @uses $this->get_setting()
	 * @uses $this->http()
	 * @uses wp_remote_get()
	 * @return object
	 */
	function details() {
		if ( !is_null( $this->details ) )
			return $this->details;

		$image_id = $this->get_setting( 'image_id' );

		if ( empty( $image_id ) )
			$image_id = ( int ) wp_remote_retrieve_header( $this->http(), 'picsum-id' );

		$response = wp_remote_get( sprintf( '%sid/%d/info', self::BASE_URL, $image_id ) );

		if ( is_wp_error( $response ) )
			return ( object ) array();

		return ( $this->details = json_decode( wp_remote_retrieve_body( $response ) ) );
	}

	/**
	 * Prevent transposing into Picsum image.
	 *
	 * @param array $attributes
	 * @param array $settings
	 * @return $this
	 */
	function picsum( array $attributes = array(), array $settings = array() ) {
		return $this;
	}

}


/*
########  ##          ###     ######  ######## ##     ##  #######  ##       ########  ######## ########
##     ## ##         ## ##   ##    ## ##       ##     ## ##     ## ##       ##     ## ##       ##     ##
##     ## ##        ##   ##  ##       ##       ##     ## ##     ## ##       ##     ## ##       ##     ##
########  ##       ##     ## ##       ######   ######### ##     ## ##       ##     ## ######   ########
##        ##       ######### ##       ##       ##     ## ##     ## ##       ##     ## ##       ##   ##
##        ##       ##     ## ##    ## ##       ##     ## ##     ## ##       ##     ## ##       ##    ##
##        ######## ##     ##  ######  ######## ##     ##  #######  ######## ########  ######## ##     ##
*/

/**
 * Class: Image_Tag_Placeholder
 *
 * @link https://placeholder.com
 */
class Image_Tag_Placeholder extends Image_Tag {

	/**
	 * @var string Base URL.
	 */
	const BASE_URL = 'https://via.placeholder.com/';

	/**
	 * @var array $settings
	 */
	protected $settings = array(
		'text' => null,
		'width' => null,
		'height' => null,
		'bg-color' => null,
		'text-color' => null,
	);

	/**
	 * To string.
	 *
	 * @uses $this->get_setting()
	 * @uses Image_Tag::__toString()
	 * @return string
	 */
	function __toString() {
		if ( empty( $this->get_setting( 'width' ) ) ) {
			trigger_error( 'Placeholder image requires width.', E_USER_WARNING );
			return '';
		}

		return parent::__toString();
	}

	/**
	 * Get "bg-color" setting.
	 *
	 * @return string
	 */
	function get_bg_color_setting() {
		return urlencode( str_replace( '#', '', $this->settings['bg-color'] ) );
	}

	/**
	 * Get "text-color" setting.
	 *
	 * @return string
	 */
	function get_text_color_setting() {
		return urlencode( str_replace( '#', '', $this->settings['text-color'] ) );
	}

	/**
	 * Generate source URL.
	 *
	 * @uses $this->get_setting()
	 * @return string
	 */
	function get_src_attribute() {
		$src = self::BASE_URL;

		$dimensions = '';

		# Add width.
		if ( !empty( $this->get_setting( 'width' ) ) )
			$dimensions .= ( int ) $this->get_setting( 'width' );

		# Add height.
		if ( !empty( $this->get_setting( 'height' ) ) )
			$dimensions .= 'x' . ( int ) $this->get_setting( 'height' );

		# Add dimensions.
		$src .= !empty( $dimensions )
			? trailingslashit( $dimensions )
			: '';

		# Add background color.
		if ( !empty( $this->get_setting( 'bg-color' ) ) ) {
			$src .= $this->get_setting( 'bg-color' ) . '/';

			# Add text color.
			if ( !empty( $this->get_setting( 'text-color' ) ) )
				$src .= $this->get_setting( 'text-color' ) . '/';
		}

		# Add text.
		if ( !empty( $this->get_setting( 'text' ) ) )
			$src = add_query_arg( 'text', urlencode( $this->get_setting( 'text' ) ) );

		return $src;
	}

	/**
	 * Magical getter for "width" attribute.
	 *
	 * @uses $this->_get_attribute()
	 * @uses $this->_get_setting()
	 * @return null|int
	 */
	function get_width_attribute() {
		if ( !empty( $this->_get_attribute( 'width' ) ) )
			return ( int ) $this->_get_attribute( 'width' );

		if ( !empty( $this->_get_setting( 'width' ) ) )
			return ( int ) $this->_get_setting( 'width' );

		return null;
	}

	/**
	 * Magical getter for "height" attribute.
	 *
	 * @uses $this->_get_attribute()
	 * @uses $this->_get_setting()
	 * @return null|int
	 */
	function get_height_attribute() {
		if ( !empty( $this->_get_attribute( 'height' ) ) )
			return ( int ) $this->_get_attribute( 'height' );

		if ( !empty( $this->_get_setting( 'height' ) ) )
			return ( int ) $this->_get_setting( 'height' );

		if ( !empty( $this->_get_attribute( 'width' ) ) )
			return ( int ) $this->_get_attribute( 'width' );

		if ( !empty( $this->_get_setting( 'width' ) ) )
			return ( int ) $this->_get_setting( 'width' );

		return null;
	}

	/**
	 * Magical getter for "width" setting.
	 *
	 * @uses $this->_get_setting()
	 * @uses $this->_get_attribute()
	 * @return null|int
	 */
	function get_width_setting() {
		if ( !empty( $this->_get_setting( 'width' ) ) )
			return ( int ) $this->_get_setting( 'width' );

		if ( !empty( $this->_get_attribute( 'width' ) ) )
			return ( int ) $this->_get_attribute( 'width' );

		return null;
	}

	/**
	 * Magical getter for "height" setting.
	 *
	 * @uses $this->_get_setting()
	 * @uses $this->_get_attribute()
	 * @return null|int
	 */
	function get_height_setting() {
		if ( !empty( $this->_get_setting( 'height' ) ) )
			return ( int ) $this->_get_setting( 'height' );

		if ( !empty( $this->_get_attribute( 'height' ) ) )
			return ( int ) $this->_get_attribute( 'height' );

		return null;
	}

	/**
	 * Prevent transposing into a Placeholder image.
	 *
	 * @param array $attributes
	 * @param array $settings
	 * @return $this
	 */
	function placeholder( array $attributes = array(), array $settings = array() ) {
		return $this;
	}

}

?>

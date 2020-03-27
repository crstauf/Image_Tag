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

defined( 'ABSPATH' ) || die();

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

		# If source is "joeschmoe", create JoeSchmoe image.
		if ( 'joeschmoe' === $source )
			return new Image_Tag_JoeSchmoe( $attributes, $settings );

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
					|| $this->is_type( 'joeschmoe' )
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

			case 'joeschmoe':
				return is_a( $this, 'Image_Tag_JoeSchmoe' );

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
			|| empty( $width  )
			|| empty( $height )
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
	 * @uses Image_Tag::create()
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
	 * @uses Image_Tag::create()
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
	 * Transpose attributes and settings into Joe Schmoe image.
	 *
	 * @param array $attributes
	 * @param array $settings
	 * @uses Image_Tag::create()
	 * @return Image_Tag_JoeSchmoe
	 */
	function joeschmoe( array $attributes = array(), array $settings = array() ) {
		$_attributes = $this->attributes;

		unset(
			$_attributes['srcset']
		);

		$attributes = wp_parse_args( $attributes, $_attributes );

		return Image_Tag::create( 'joeschmoe', $attributes, $settings );
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

		do_action( 'created_lazyload_image', $lazyload );

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

require_once 'image_tags/Image_Tag_WP.php';
require_once 'image_tags/Image_Tag_WP_Attachment.php';
require_once 'image_tags/Image_Tag_WP_Theme.php';
require_once 'image_tags/Image_Tag_Picsum.php';
require_once 'image_tags/Image_Tag_Placeholder.php';
require_once 'image_tags/Image_Tag_JoeSchmoe.php';



?>

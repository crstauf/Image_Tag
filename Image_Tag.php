<?php
/**
 * Image tag mamangement.
 *
 * Plugin name: Image Tag Generator
 * Author: Caleb Stauffer
 * Version: 1.0
 */

/**
 * Class: Image_Tag
 *
 * @todo add helpers for srcset and sizes attributes
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
			return new Image_Tag_WP_Theme( $source, $attribtues, $settings );

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

		return implode( ' ', $array );
	}

	function set_settings( array $settings ) {
		foreach ( $settings as $key => $value )
			$this->set_setting( $key, $value );
	}

	function set_setting( $key, $value ) {
		$method_name = 'set_' . $key . '_setting';
		$method_name = str_replace( '-', '_', $method_name );

		method_exists( $this, $method_name )
			? $this->$method_name( $value )
			: $this->_set_setting( $key, $value );
	}

	protected function _set_setting( string $key, $value ) {
		$this->settings[$key] = $value;
	}

	function get_setting( string $key ) {
		$method_name = 'get_' . $key . '_setting';
		$method_name = str_replace( '-', '_', $method_name );

		return method_exists( $this, $method_name )
			? $this->$method_name()
			: $this->_get_setting( $key );
	}

	protected function _get_setting( string $key ) {
		return $this->settings[$key];
	}

	function set_attributes( array $attributes ) {
		foreach ( $attributes as $key => $value )
			$this->set_attribute( $key, $value );
	}

	function set_attribute( string $key, $value ) {
		$method_name = 'set_' . $key . '_attribute';
		$method_name = str_replace( '-', '_', $method_name );

		method_exists( $this, $method_name )
			? $this->$method_name( $value )
			: $this->_set_attribute( $key, $value );
	}

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

	protected function _set_attribute( string $key, $value ) {
		$this->attributes[$key] = $value;
	}

	function get_attributes() {
		$attributes = array();

		foreach ( array_keys( $this->attributes ) as $key )
			$attributes[$key] = $this->get_attribute( $key );

		return array_filter( $attributes );
	}

	function get_attribute( string $key ) {
		$method_name = 'get_' . $key . '_attribute';
		$method_name = str_replace( '-', '_', $method_name );

		return method_exists( $this, $method_name )
			? $this->$method_name()
			: $this->_get_attribute( $key );
	}

	protected function get_class_attribute() {
		return implode( ' ', array_unique( $this->attributes['class'] ) );
	}

	protected function get_style_attribute() {
		return trim( implode( '; ', $this->attributes['style'] ) );
	}

	protected function _get_attribute( string $key ) {
		return $this->attributes[$key];
	}

	function add_class( $class ) {
		$this->attributes['class'][] = $class;
	}

	function add_style( string $style ) {
		$this->attributes['style'][] = $style;
	}

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

	function offsetSet( $offset, $value ) {}
	function offsetUnset( $offset ) {}

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
class Image_Tag_WP_Attachment extends Image_Tag {

	/**
	 * @var int $attachment_id
	 */
	protected $attachment_id;

	/**
	 * @var array $settings
	 */
	protected $settings = array(
		'image_sizes' => array( 'full' ),
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
		$this->set_source( $attachment_id );
	}

	protected function set_image_sizes_setting( $image_sizes ) {
		if ( is_string( $image_sizes ) )
			$image_sizes = explode( ' ', $image_sizes );

		if ( !is_array( $image_sizes ) ) {
			trigger_error( 'Image sizes must be a string or array.' );
			return array( 'full' );
		}

		$wp_image_sizes = get_intermediate_image_sizes();
		$wp_image_sizes[] = 'full';

		$this->_set_setting( 'image_sizes', array_values( array_intersect( $image_sizes, $wp_image_sizes ) ) );
	}

	protected function set_source( int $attachment_id ) {
		$image_sizes = $this->get_setting( 'image_sizes' );

		for ( $i = 0; empty( $attachment ), $i < count( $image_sizes ); $i++ )
			$attachment = wp_get_attachment_image_src( $attachment_id, $image_sizes[$i] );

		if ( empty( $attachment ) ) {
			trigger_error( sprintf( 'Attachment <code>%d</code> does not exist.', $attachment_id ), E_USER_WARNING );
			$this->_set_attribute( 'src', self::BLANK );
			return;
		}

		$this->_set_attribute( 'src', $attachment[0] );
	}

	protected function get_id_attribute() {
		if ( empty( $this->_get_attribute( 'id' ) ) )
			return 'attachment-' . $this->attachment_id;

		return $this->_get_attribute( 'id' );
	}

	protected function get_class_attribute() {
		$classes = $this->_get_attribute( 'class' );

		$classes[] = 'attachment-' . $this->attachment_id;

		$image_sizes = $this->get_setting( 'image_sizes' );
		$classes[] = 'size-' . $image_sizes[0];

		return implode( ' ', array_unique( $classes ) );
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
class Image_Tag_WP_Theme extends Image_Tag {

	/**
	 * Construct.
	 *
	 * @param string $source
	 * @param array $attributes
	 * @param array $settings
	 * @uses Image_Tag::__construct()
	 */
	protected function __construct( string $source, array $attributes = array(), array $settings = array() ) {
		parent::__construct( $source, $attributes, $settings );
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

	function get_width_attribute() {
		if ( !empty( $this->_get_attribute( 'width' ) ) )
			return $this->_get_attribute( 'width' );

		if ( !empty( $this->_get_setting( 'width' ) ) )
			return $this->_get_setting( 'width' );

		return 1024;
	}

	function get_height_attribute() {
		if ( !empty( $this->_get_attribute( 'height' ) ) )
			return $this->_get_attribute( 'height' );

		if ( !empty( $this->_get_setting( 'height' ) ) )
			return $this->_get_setting( 'height' );

		if ( !empty( $this->_get_attribute( 'width' ) ) )
			return $this->_get_attribute( 'width' );

		if ( !empty( $this->_get_setting( 'width' ) ) )
			return $this->_get_setting( 'width' );

		return 1024;
	}

	function get_blur_setting() {
		$blur = $this->_get_setting( 'blur' );

		if ( true === $blur )
			return 10;

		return $blur;
	}

	function get_seed_setting() {
		$seed = $this->_get_setting( 'seed' );

		if ( is_null( $seed ) )
			return null;

		return urlencode( sanitize_title_with_dashes( $seed ) );
	}

	function get_width_setting() {
		if ( !empty( $this->_get_setting( 'width' ) ) )
			return $this->_get_setting( 'width' );

		if ( !empty( $this->_get_attribute( 'width' ) ) )
			return $this->_get_attribute( 'width' );

		return null;
	}

	function get_height_setting() {
		if ( !empty( $this->_get_setting( 'height' ) ) )
			return $this->_get_setting( 'height' );

		if ( !empty( $this->_get_attribute( 'height' ) ) )
			return $this->_get_attribute( 'height' );

		return null;
	}

	function get_random_setting() {
		static $_random = 0;
		$random = $this->_get_setting( 'random' );

		if ( false === $random )
			return false;

		if ( true === $random )
			return ++$_random;

		return $random;
	}

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

	function get_bg_color_setting() {
		return urlencode( str_replace( '#', '', $this->settings['bg-color'] ) );
	}

	function get_text_color_setting() {
		return urlencode( str_replace( '#', '', $this->settings['text-color'] ) );
	}

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

	function get_width_attribute() {
		if ( !empty( $this->_get_attribute( 'width' ) ) )
			return $this->_get_attribute( 'width' );

		if ( !empty( $this->_get_setting( 'width' ) ) )
			return $this->_get_setting( 'width' );

		return null;
	}

	function get_height_attribute() {
		if ( !empty( $this->_get_attribute( 'height' ) ) )
			return $this->_get_attribute( 'height' );

		if ( !empty( $this->_get_setting( 'height' ) ) )
			return $this->_get_setting( 'height' );

		if ( !empty( $this->_get_attribute( 'width' ) ) )
			return $this->_get_attribute( 'width' );

		if ( !empty( $this->_get_setting( 'width' ) ) )
			return $this->_get_setting( 'width' );

		return null;
	}

	function get_width_setting() {
		if ( !empty( $this->_get_setting( 'width' ) ) )
			return $this->_get_setting( 'width' );

		if ( !empty( $this->_get_attribute( 'width' ) ) )
			return $this->_get_attribute( 'width' );

		return null;
	}

	function get_height_setting() {
		if ( !empty( $this->_get_setting( 'height' ) ) )
			return $this->_get_setting( 'height' );

		if ( !empty( $this->_get_attribute( 'height' ) ) )
			return $this->_get_attribute( 'height' );

		return null;
	}

}

/**
 * Script implementation: lazysizes
 *
 * @link https://github.com/aFarkas/lazysizes
 */
trait img_lazysizes {

	/**
	 * @var array $settings__lazysizes
	 */
	protected $settings__lazysizes = array(
		'enabled' => false,
	);

}

require_once 'samples.php';

?>

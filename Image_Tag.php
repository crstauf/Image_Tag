<?php
/**
 * Image tag generator.
 *
 * Plugin name: Image Tag Generator
 * Plugin URI: https://github.com/crstauf/image_tag
 * Description: WordPress drop-in to generate <code>img</code> tags.
 * Author: Caleb Stauffer
 * Author URI: https://develop.calebstauffer.com
 * Version: 2.0
 */

defined( 'ABSPATH' ) || die();

/**
 * Class: Image_Tag
 */
class Image_Tag implements ArrayAccess {

	/**
	 * @var string Base64 encoded transparent gif.
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
	 * @var array $settings Settings for object.
	 */
	protected $settings = array(
		'before_output' => null,
		'after_output' => null,
		'sizes' => array(),
	);

	/**
	 * @param $source
	 * @param null|array $attributes
 	 * @param array $settings
 	 * @return Image_Tag
	 */
	static function create( $source, $attributes = array(), array $settings = array() ) {
		$attributes = ( array ) $attributes;

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
	}

	/**
	 * Construct.
	 *
	 * @param array $attributes
	 * @param array $settings
	 * @uses $this->set_attributes()
	 * @uses $this->set_settings()
	 */
	function __construct( array $attributes, array $settings = array() ) {
		$this->set_attributes( $attributes );
		$this->set_settings( $settings );
	}

	/**
	 * Getter.
	 *
	 * @param string $key
	 * @uses $this->_get_attribute()
	 * @return mixed
	 */
	function __get( $key ) {
		return $this->_get_attribute( $key );
	}

	/**
	 * To string.
	 *
	 * @uses $this->is_valid()
	 * @uses $this->check_valid()
	 * @uses $this->get_attributes()
	 * @uses $this->get_setting()
	 * @return null|string
	 */
	function __toString() {
		if ( !$this->is_valid() ) {
			foreach ( $this->check_valid()->get_error_messages() as $error )
				trigger_error( $error, E_USER_WARNING );

			return null;
		}

		$array = array( '<img' );

		foreach ( $this->get_attributes() as $key => $value )
			$array[$key] = $key . '="' . esc_attr( $value ) . '"';

		$array[] = '/>';

		$array = apply_filters( 'image_tag/output/array', $array, $this );

		$string  = $this->get_setting( 'before_output' );
		$string .= apply_filters( 'image_tag/output/string', implode( ' ', $array ), $this );
		$string .= $this->get_setting( 'after_output' );

		return apply_filters( 'image_tag/output', $string );
	}

	/**
	 * Check properties are sufficient to create tag.
	 *
	 * @uses $this->check_valid()
	 * @return bool
	 */
	function is_valid() {
		return true === $this->check_valid();
	}

	/**
	 * Check properties are sufficient to create tag.
	 *
	 * @uses $this->get_attribute()
	 * @return true|WP_Error
	 */
	protected function check_valid() {
		$errors = new WP_Error;

		if ( empty( $this->get_attribute( 'src' ) ) )
			$errors->add( 'required_attribute', 'Image requires <code>src</code> attribute.' );

		if ( $errors->has_errors() )
			return $errors;

		return true;
	}


	/*
	 ######  ######## ########       ###    ######## ######## ########  #### ########  ##     ## ######## ########  ######
	##    ## ##          ##         ## ##      ##       ##    ##     ##  ##  ##     ## ##     ##    ##    ##       ##    ##
	##       ##          ##        ##   ##     ##       ##    ##     ##  ##  ##     ## ##     ##    ##    ##       ##
	 ######  ######      ##       ##     ##    ##       ##    ########   ##  ########  ##     ##    ##    ######    ######
	      ## ##          ##       #########    ##       ##    ##   ##    ##  ##     ## ##     ##    ##    ##             ##
	##    ## ##          ##       ##     ##    ##       ##    ##    ##   ##  ##     ## ##     ##    ##    ##       ##    ##
	 ######  ########    ##       ##     ##    ##       ##    ##     ## #### ########   #######     ##    ########  ######
	*/

	/**
	 * Set attributes.
	 *
	 * @param array $attributes
	 * @uses $this->set_attribute()
	 */
	function set_attributes( array $attributes ) {
		foreach ( $attributes as $attribute => $value )
			$this->set_attribute( $attribute, $value );
	}

	/**
	 * Set attribute.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @uses $this->_set_attribute()
	 */
	function set_attribute( string $key, $value ) {
		$method_name = preg_replace( '/[^A-z0-9_]/', '_', 'set_' . $key . '_attribute' );

		if ( is_callable( array( $this, $method_name ) ) )
			return $this->$method_name();

		return $this->_set_attribute( $key, $value );
	}

	/**
	 * Set raw attribute.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	protected function _set_attribute( string $key, $value ) {
		$this->attributes[$key] = $value;
	}


	/*
	 ######   ######## ########       ###    ######## ######## ########  #### ########  ##     ## ######## ########  ######
	##    ##  ##          ##         ## ##      ##       ##    ##     ##  ##  ##     ## ##     ##    ##    ##       ##    ##
	##        ##          ##        ##   ##     ##       ##    ##     ##  ##  ##     ## ##     ##    ##    ##       ##
	##   #### ######      ##       ##     ##    ##       ##    ########   ##  ########  ##     ##    ##    ######    ######
	##    ##  ##          ##       #########    ##       ##    ##   ##    ##  ##     ## ##     ##    ##    ##             ##
	##    ##  ##          ##       ##     ##    ##       ##    ##    ##   ##  ##     ## ##     ##    ##    ##       ##    ##
	 ######   ########    ##       ##     ##    ##       ##    ##     ## #### ########   #######     ##    ########  ######
	*/

	/**
	 * Get attributes.
	 *
	 * @uses $this->get_attribute()
	 * @return array
	 */
	function get_attributes() {
		$attributes = array();

		foreach ( array_keys( $this->attributes ) as $attribute )
			$attributes[$attribute] = $this->get_attribute( $attribute );

		return array_filter( $attributes );
	}

	/**
	 * Get raw attributes.
	 *
	 * @uses $this->_get_attribute()
	 * @return array
	 */
	function _get_attributes() {
		$attributes = array();

		foreach ( array_keys( $this->attributes ) as $attribute )
			$attributes[$attribute] = $this->_get_attribute( $attribute );

		return $attributes;
	}

	/**
	 * Get attribute.
	 *
	 * @param string $key
	 * @uses $this->_get_attribute()
	 * @return mixed
	 */
	function get_attribute( string $key ) {
		$method_name = preg_replace( '/[^A-z0-9_]/', '_', 'get_' . $key . '_attribute' );

		# Allow overriding attribute retrieval by name.
		if ( is_callable( array( $this, $method_name ) ) )
			return $this->$method_name();

		$type = gettype( $this->_get_attribute( $key ) );
		$method_name = 'get_' . $type . '_attribute';

		# Allow overriding attribute retrieval by variable type.
		if ( is_callable( array( $this, $method_name ) ) )
			return $this->$method_name( $key );

		return $this->_get_attribute( $key );
	}

	/**
	 * Get array attribute.
	 *
	 * @param string $key
	 * @uses $this->_get_attribute()
	 * @return string
	 */
	function get_array_attribute( string $key ) {
		return implode( ', ', $this->_get_attribute( $key ) );
	}

	/**
	 * Get raw attribute.
	 *
	 * @param string $key
	 * @return mixed
	 */
	function _get_attribute( string $key ) {
		return $this->attributes[$key];
	}


	/*
	 ######  ######## ########     ######  ######## ######## ######## #### ##    ##  ######    ######
	##    ## ##          ##       ##    ## ##          ##       ##     ##  ###   ## ##    ##  ##    ##
	##       ##          ##       ##       ##          ##       ##     ##  ####  ## ##        ##
	 ######  ######      ##        ######  ######      ##       ##     ##  ## ## ## ##   ####  ######
	      ## ##          ##             ## ##          ##       ##     ##  ##  #### ##    ##        ##
	##    ## ##          ##       ##    ## ##          ##       ##     ##  ##   ### ##    ##  ##    ##
	 ######  ########    ##        ######  ########    ##       ##    #### ##    ##  ######    ######
	*/

	/**
	 * Set settings.
	 *
	 * @param array $settings
	 * @uses $this->set_setting()
	 */
	function set_settings( array $settings ) {
		foreach ( $settings as $setting => $value )
			$this->set_setting( $setting, $value );
	}

	/**
	 * Set setting.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @uses $this->_set_setting()
	 */
	function set_setting( string $key, $value ) {
		$method_name = preg_replace( '/[^A-z0-9_]/', '_', 'set_' . $key . '_setting' );

		if ( is_callable( array( $this, $method_name ) ) )
			return $this->$method_name();

		return $this->_set_setting( $key, $value );
	}

	/**
	 * Set raw setting.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	protected function _set_setting( string $key, $value ) {
		$this->settings[$key] = $value;
	}


	/*
	 ######   ######## ########     ######  ######## ######## ######## #### ##    ##  ######    ######
	##    ##  ##          ##       ##    ## ##          ##       ##     ##  ###   ## ##    ##  ##    ##
	##        ##          ##       ##       ##          ##       ##     ##  ####  ## ##        ##
	##   #### ######      ##        ######  ######      ##       ##     ##  ## ## ## ##   ####  ######
	##    ##  ##          ##             ## ##          ##       ##     ##  ##  #### ##    ##        ##
	##    ##  ##          ##       ##    ## ##          ##       ##     ##  ##   ### ##    ##  ##    ##
	 ######   ########    ##        ######  ########    ##       ##    #### ##    ##  ######    ######
	*/

	/**
	 * Get settings.
	 *
	 * @uses $this->get_setting()
	 * @return array
	 */
	function get_settings() {
		$settings = array();

		foreach ( array_keys( $this->settings ) as $setting )
			$settings[$setting] = $this->get_setting( $setting );

		return $settings;
	}

	/**
	 * Get setting.
	 *
	 * @param string $key
	 * @uses $this->_get_setting()
	 * @return mixed
	 */
	function get_setting( string $key ) {
		$method_name = preg_replace( '/[^A-z0-9_]/', '_', 'get_' . $key . '_setting' );

		if ( is_callable( array( $this, $method_name ) ) )
			return $this->$method_name();

		return $this->_get_setting( $key );
	}

	/**
	 * Get raw setting.
	 *
	 * @param string $key
	 * @return mixed
	 */
	function _get_setting( string $key ) {
		return $this->settings[$key];
	}


	/*
	   ###    ########  ########     ##     ## ######## ##       ########  ######## ########   ######
	  ## ##   ##     ## ##     ##    ##     ## ##       ##       ##     ## ##       ##     ## ##    ##
	 ##   ##  ##     ## ##     ##    ##     ## ##       ##       ##     ## ##       ##     ## ##
	##     ## ##     ## ##     ##    ######### ######   ##       ########  ######   ########   ######
	######### ##     ## ##     ##    ##     ## ##       ##       ##        ##       ##   ##         ##
	##     ## ##     ## ##     ##    ##     ## ##       ##       ##        ##       ##    ##  ##    ##
	##     ## ########  ########     ##     ## ######## ######## ##        ######## ##     ##  ######
	*/

	function add_class( $classes ) {}
	function add_style( string $style ) {}
	function add_sizes_item( $media_condition, string $width ) {}
	function add_srcset_item( string $width, string $url ) {}


	/*
	 ######  ######## ########    ##     ## ######## ##       ########  ######## ########   ######
	##    ## ##          ##       ##     ## ##       ##       ##     ## ##       ##     ## ##    ##
	##       ##          ##       ##     ## ##       ##       ##     ## ##       ##     ## ##
	 ######  ######      ##       ######### ######   ##       ########  ######   ########   ######
	      ## ##          ##       ##     ## ##       ##       ##        ##       ##   ##         ##
	##    ## ##          ##       ##     ## ##       ##       ##        ##       ##    ##  ##    ##
	 ######  ########    ##       ##     ## ######## ######## ##        ######## ##     ##  ######
	*/

	function set_sizes_item( $media_condition, string $width ) {}
	function set_srcset_item( string $width, string $url ) {}


	/*
	########  ######## ##     ##  #######  ##     ## ########    ##     ## ######## ##       ########  ######## ########   ######
	##     ## ##       ###   ### ##     ## ##     ## ##          ##     ## ##       ##       ##     ## ##       ##     ## ##    ##
	##     ## ##       #### #### ##     ## ##     ## ##          ##     ## ##       ##       ##     ## ##       ##     ## ##
	########  ######   ## ### ## ##     ## ##     ## ######      ######### ######   ##       ########  ######   ########   ######
	##   ##   ##       ##     ## ##     ##  ##   ##  ##          ##     ## ##       ##       ##        ##       ##   ##         ##
	##    ##  ##       ##     ## ##     ##   ## ##   ##          ##     ## ##       ##       ##        ##       ##    ##  ##    ##
	##     ## ######## ##     ##  #######     ###    ########    ##     ## ######## ######## ##        ######## ##     ##  ######
	*/

	function remove_classes( $classes ) {}
	function remove_sizes_item( $media_conditions ) {}
	function remove_srcset_item( $widths ) {}


	/*
	   ###    ########  ########     ###    ##    ##    ###     ######   ######  ########  ######   ######
	  ## ##   ##     ## ##     ##   ## ##    ##  ##    ## ##   ##    ## ##    ## ##       ##    ## ##    ##
	 ##   ##  ##     ## ##     ##  ##   ##    ####    ##   ##  ##       ##       ##       ##       ##
	##     ## ########  ########  ##     ##    ##    ##     ## ##       ##       ######    ######   ######
	######### ##   ##   ##   ##   #########    ##    ######### ##       ##       ##             ##       ##
	##     ## ##    ##  ##    ##  ##     ##    ##    ##     ## ##    ## ##    ## ##       ##    ## ##    ##
	##     ## ##     ## ##     ## ##     ##    ##    ##     ##  ######   ######  ########  ######   ######
	*/

	/**
	 * ArrayAccess: exists
	 *
	 * @param $offset
	 * @return bool
	 */
	function offsetExists( $offset ) {
		return isset( $this->attributes[$offset] );
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

		return null;
	}

	/**
	 * ArrayAccess: set
	 *
	 * @param $offset
	 * @param $value
	 * @uses $this->set_attribute()
	 */
	function offsetSet( $offset, $value ) {
		$this->set_attribute( $offset, $value );
	}

	/**
	 * ArrayAccess: unset
	 *
	 * @param $offset
	 * @uses $this->set_attribute()
	 */
	function offsetUnset( $offset ) {
		$this->set_attribute( $offset, null );
	}

}

require_once 'image_tags/Image_Tag_JoeSchmoe.php';
require_once 'image_tags/Image_Tag_Picsum.php';
require_once 'image_tags/Image_Tag_Placeholder.php';
require_once 'image_tags/Image_Tag_WP_Attachment.php';
require_once 'image_tags/Image_Tag_WP_Theme.php';
require_once 'image_tags/Image_Tag_Unsplash.php';

?>

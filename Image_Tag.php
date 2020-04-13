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

require_once 'image_tags/Image_Tag_WP_Attachment.php';
require_once 'image_tags/Image_Tag_WP_Theme.php';
require_once 'image_tags/Image_Tag_Picsum.php';
require_once 'image_tags/Image_Tag_Placeholder.php';
require_once 'image_tags/Image_Tag_JoeSchmoe.php';
require_once 'image_tags/Image_Tag_Unsplash.php';

?>

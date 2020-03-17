<?php

/**
 * @group wp
 * @group wp_attachment
 *
** @todo add tests for returning Picsum or Placeholder
 */
class Image_Tag_WP_Attachment_Test extends WP_UnitTestCase {

	static $attachment_id = null;

	/**
	 * @see https://wordpress.stackexchange.com/questions/256830/programmatically-adding-images-to-media-library
	 */
	static function setUpBeforeClass() {
		$filename    = 'img.jpg';
		$image_path  = trailingslashit( __DIR__ ) . $filename;
		$upload_dir  = wp_upload_dir();
		$image_data  = file_get_contents( $image_path );
		$file        = $upload_dir['basedir'] . '/' . $filename;
		$wp_filetype = wp_check_filetype( $filename, null );

		if ( wp_mkdir_p( $upload_dir['path'] ) )
			$file = $upload_dir['path'] . '/' . $filename;

		file_put_contents( $file, $image_data );

		$attachment = array(
		  'post_mime_type' => $wp_filetype['type'],
		  'post_title' => sanitize_file_name( $filename ),
		  'post_content' => '',
		  'post_status' => 'inherit'
		);

		static::$attachment_id = wp_insert_attachment( $attachment, $file );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		$attach_data = wp_generate_attachment_metadata( static::$attachment_id, $file );
		wp_update_attachment_metadata( static::$attachment_id, $attach_data );
	}

	function test_image_sizes() {
		$image_sizes = array( 'small', 'medium' );

		$img = Image_Tag::create( static::$attachment_id, array(), array(
			'image_sizes' => $image_sizes,
		) );

		$wp_image_sizes = get_intermediate_image_sizes();
		$wp_images_sizes[] = 'full';

		$image_sizes = array_intersect( $image_sizes, $wp_image_sizes );

		foreach ( $image_sizes as $image_size ) {
			$attachment = wp_get_attachment_image_src( static::$attachment_id, $image_size );

			if ( !empty( $attachment ) )
				break;
		}

		$this->assertEquals( esc_attr( $attachment[0] ), esc_attr( $img->get_attribute( 'src' ) ) );
	}

	function test_attribute_defaults() {
		$image_size = 'medium';

		$img = Image_Tag::create( static::$attachment_id, array(), array(
			'image_sizes' => $image_size,
		) );

		# Attribute: id.
		$this->assertEquals( 'attachment-' . static::$attachment_id, $img->get_attribute( 'id' ) );

		# Attribute: class.
		$this->assertContains( 'attachment-' . static::$attachment_id, $img->get_attribute( 'class' ) );
		$this->assertContains( 'size-' . $image_size, $img->get_attribute( 'class' ) );
	}

	function test_id_attribute() {
		$image_size = 'medium';

		$img = Image_Tag::create( static::$attachment_id, array(), array(
			'image_sizes' => $image_size,
		) );

		$this->assertEquals( 'attachment-' . static::$attachment_id, $img->get_attribute( 'id' ) );

		$img = Image_Tag::create( static::$attachment_id, array(
			'id' => __FUNCTION__,
		), array(
			'image_sizes' => $image_size,
		) );

		$this->assertEquals( __FUNCTION__, $img->get_attribute( 'id' ) );
	}

	function test_invalid_attachment() {
		$img = @Image_Tag::create( PHP_INT_MAX, array(), array(
			'image_sizes' => 'medium',
		) );
		$this->assertEquals( Image_Tag::BLANK, $img->get_attribute( 'src' ) );
	}

	function test_image_versions() {
		$img = Image_Tag::create( static::$attachment_id, array(), array(
			'image_sizes' => array( 'thumbnail', 'medium', 'medium_large', 'large', 'full' ),
		) );

		$upload_dir = trailingslashit( wp_get_upload_dir()['path'] );
		$upload_url = trailingslashit( wp_get_upload_dir()['url'] );

		$versions = array(
			'__largest' => null,
			'__smallest' => null,

			'thumbnail' => ( object ) array(
				'file' => 'img-150x150.jpg',
				'width'  => 150,
				'height' => 150,
				'path' => $upload_dir . 'img-150x150.jpg',
				'url'  => $upload_url . 'img-150x150.jpg',
			),

			'medium' => ( object ) array(
				'file' => 'img-300x200.jpg',
				'width'  => 300,
				'height' => 200,
				'path' => $upload_dir . 'img-300x200.jpg',
				'url'  => $upload_url . 'img-300x200.jpg',
			),

			'medium_large' => ( object ) array(
				'file' => 'img-768x512.jpg',
				'width'  => 768,
				'height' => 512,
				'path' => $upload_dir . 'img-768x512.jpg',
				'url'  => $upload_url . 'img-768x512.jpg',
			),

			'large' => ( object ) array(
				'file' => 'img-1024x682.jpg',
				'width'  => 1024,
				'height' => 682,
				'path' => $upload_dir . 'img-1024x682.jpg',
				'url'  => $upload_url . 'img-1024x682.jpg',
			),

			'full' => ( object ) array(
				'file' => 'img.jpg',
				'width'  => 2000,
				'height' => 1333,
				'path' => $upload_dir . 'img.jpg',
				'url'  => $upload_url . 'img.jpg',
			),

		);

		$versions['__largest']  = &$versions['full'];
		$versions['__smallest'] = &$versions['thumbnail'];

		$this->assertEquals( $versions, $img->get_versions() );
	}

}

<?php

/**
 * @group wp
 * @group wp_attachment
 *
 * @todo add tests for returning Picsum or Placeholder
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

	function test_getter() {
		$img = Image_Tag::create( static::$attachment_id );

		$this->assertEquals( static::$attachment_id, $img->attachment_id );
		$this->assertContains( 'img.jpg', $img->src );
	}

	function test_attribute_defaults() {
		$image_size = 'medium';

		$img = Image_Tag::create( static::$attachment_id, array(), array(
			'image_sizes' => $image_size,
		) );

		# Attribute: class.
		$this->assertContains( 'post-' . static::$attachment_id, $img->get_attribute( 'class' ) );
		$this->assertContains( 'size-' . $image_size, $img->get_attribute( 'class' ) );
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

		$_versions = $img->get_versions();
		$this->assertEquals( $versions, $_versions );
		$this->assertEquals( $_versions, $img->get_versions() );
	}

	function test_width() {
		$img = Image_Tag::create( static::$attachment_id );
		$this->assertEquals( 2000, $img->get_width() );
	}

	function test_height() {
		$img = Image_Tag::create( static::$attachment_id );
		$this->assertEquals( 1333, $img->get_height() );
	}

	function test_ratio() {
		$img = Image_Tag::create( static::$attachment_id );
		$this->assertEquals( 0.6665, $img->get_ratio() );
	}

	function test_picsum() {
		$img = Image_Tag::create( static::$attachment_id, array(), array( 'image-sizes' => array( 'medium', 'large' ) ) );
		$picsum = $img->picsum();

		$this->assertInstanceOf( 'Image_Tag_Picsum', $picsum );
		$this->assertNotEmpty( $picsum->get_attribute( 'srcset' ) );
	}

	function test_placeholder() {
		$img = Image_Tag::create( static::$attachment_id, array(), array( 'image-sizes' => array( 'medium', 'large' ) ) );
		$placeholder = $img->placeholder();

		$this->assertInstanceOf( 'Image_Tag_Placeholder', $placeholder );
		$this->assertNotEmpty( $placeholder->get_attribute( 'srcset' ) );
	}

	function test_joeschmoe() {
		$img = Image_Tag::create( static::$attachment_id, array(), array( 'image-sizes' => array( 'medium', 'large' ) ) );
		$joeschmoe = $img->joeschmoe();

		$this->assertInstanceOf( 'Image_Tag_JoeSchmoe', $joeschmoe );
		$this->assertEmpty( $joeschmoe->get_attribute( 'srcset' ) );
	}

	function test_colors() {
		$img = Image_Tag::create( static::$attachment_id );
		$colors = array(
			'#202020' => 0.51737373737374,
			'#304060' => 0.19494949494949,
			'#303040' => 0.12673400673401,
		);

		$this->assertEquals( $colors, $img->get_colors() );
		$this->assertEquals( array_keys( $colors )[0], $img->get_mode_color() );

		$transient_key = Image_Tag_WP_Attachment::colors_transient_key( static::$attachment_id );
		$this->assertEquals( $colors, get_transient( $transient_key ) );

		$transient_timeout = get_option( '_transient_timeout_' . $transient_key );
		$life = $transient_timeout - time();
		$this->assertGreaterThanOrEqual( DAY_IN_SECONDS,     $life );
		$this->assertLessThanOrEqual(    DAY_IN_SECONDS + 1, $life );

		# Test retrieval from transient.
		$this->assertEquals( $colors, $img->get_colors() );
	}

	function test_lqip() {
		$img = Image_Tag::create( static::$attachment_id, array(), array( 'image-sizes' => 'medium' ) );
		$lqip = $img->lqip();

		$this->assertContains( 'base64', $lqip->get_attribute( 'src' ) );
		$this->assertContains( 'lqip', $lqip->get_attribute( 'class' ) );

		$transient_key = Image_Tag_WP_Attachment::lqip_transient_key( static::$attachment_id );
		$this->assertNotEmpty( get_transient( $transient_key ) );

		$transient_timeout = get_option( '_transient_timeout_' . $transient_key );
		$life = $transient_timeout - time();
		$this->assertGreaterThanOrEqual( DAY_IN_SECONDS,     $life );
		$this->assertLessThanOrEqual(    DAY_IN_SECONDS + 1, $life );

		# Test retrieval from transient.
		$this->assertEquals( $lqip->get_attribute( 'src' ), $img->lqip()->get_attribute( 'src' ) );

		$decoded = base64_decode( explode( ',', $lqip->get_attribute( 'src' ) )[1] );
		$size = getimagesizefromstring( $decoded );
		$this->assertEquals( 100, $lqip->get_setting( 'lqip-width' ) );
		$this->assertEquals( $lqip->get_setting( 'lqip-width' ), $size[0] );

		$this->assertEquals( $lqip->get_attribute( 'src' ),  $img->get_version( '__lqip' )->url );
		$this->assertEquals( $lqip->get_attribute( 'src' ), $lqip->get_version( '__lqip' )->url );
	}

	function test_lazyload() {
		$img = Image_Tag::create( static::$attachment_id, array(), array(
			'image-sizes' => array( 'medium', 'large' ),
		) );

		$lazyload = $img->lazyload();

		$this->assertEquals( $img->get_attribute( 'srcset' ), $lazyload->get_attribute( 'data-srcset' ) );
		$this->assertEquals( 'auto', $lazyload->get_attribute( 'data-sizes' ) );

		$img->set_attribute( 'sizes', '100vw' );

		$this->assertEquals( '100vw', $img->lazyload()->get_attribute( 'data-sizes' ) );
	}

	function test_type() {
		$img = Image_Tag::create( static::$attachment_id );

		$this->assertTrue( $img->is_type(                   'wp' ) );
		$this->assertTrue( $img->is_type(                'local' ) );
		$this->assertTrue( $img->is_type(             'internal' ) );
		$this->assertTrue( $img->is_type(            'wordpress' ) );
		$this->assertTrue( $img->is_type(           'attachment' ) );
		$this->assertTrue( $img->is_type(        'wp-attachment' ) );
		$this->assertTrue( $img->is_type( 'wordpress-attachment' ) );

		$this->assertFalse( $img->is_type(           'theme' ) );
		$this->assertFalse( $img->is_type(        'wp-theme' ) );
		$this->assertFalse( $img->is_type( 'wordpress-theme' ) );

		$this->assertFalse( $img->is_type( 'remote' ) );
	}

	function test_valid() {
		$img = Image_Tag::create( static::$attachment_id );

		$this->assertTrue( $img->is_valid() );

		$img = Image_Tag::create( 1 );

		$this->assertFalse( $img->is_valid() );
	}

}

<?php

/**
 * @link https://picsum.photos/
 * @group picsum
 *
 * @todo add tests for returning Placeholder
 */
class Image_Tag_Picsum_Test extends WP_UnitTestCase {

	function test_base_source() {
		$img = Image_Tag::create( 'picsum' );

		$this->assertEquals( 'https://picsum.photos/', Image_Tag_Picsum::BASE_URL );
		$this->assertEquals( Image_Tag_Picsum::BASE_URL, $img->get_attribute( 'src' ) );
	}

	function test_blur_setting() {
		$img = Image_Tag::create( 'picsum', array(), array(
			'blur' => true,
		) );

		$this->assertEquals( 10, $img->get_setting( 'blur' ) );
		$this->assertContains( 'blur=10', $img->get_attribute( 'src' ) );
		$this->assertNotContains( 'blur=10', remove_query_arg( 'blur', $img->get_attribute( 'src' ) ) );

		$img = Image_Tag::create( 'picsum', array(), array(
			'blur' => 5,
		) );

		$this->assertEquals( 5, $img->get_setting( 'blur' ) );
		$this->assertContains( 'blur=5', $img->get_attribute( 'src' ) );
		$this->assertNotContains( 'blur=5', remove_query_arg( 'blur', $img->get_attribute( 'src' ) ) );
	}

	/**
	 * @group http
	 */
	function test_blur_http() {
		$img = Image_Tag::create( 'picsum', array(), array(
			'width' => 200,
			'blur' => true,
		) );

		$this->assertEquals( 'image/jpeg', wp_remote_retrieve_header( $img->http(), 'content-type' ) );
	}

	function test_seed_setting() {
		$img = Image_Tag::create( 'picsum' );
		$this->assertNull( $img->get_setting( 'seed' ) );

		$seed = 'a?b/c&d';
		$img = Image_Tag::create( 'picsum', array(), array(
			'seed' => $seed,
		) );

		$seed = urlencode( sanitize_title_with_dashes( $seed ) );

		$this->assertEquals( $seed, $img->get_setting( 'seed' ) );
		$this->assertContains( 'seed/' . $seed . '/', $img->get_attribute( 'src' ) );
	}

	/**
	 * @group http
	 */
	function test_seed_http() {
		$seed = 'c&d/a?b';

		$img = Image_Tag::create( 'picsum', array(), array(
			'width' => 200,
			'seed' => $seed,
		) );

		$this->assertEquals( 'image/jpeg', wp_remote_retrieve_header( $img->http(), 'content-type' ) );
	}

	function test_width() {
		$width = 200;

		$img = Image_Tag::create( 'picsum', array(), array(
			'width' => $width,
		) );

		$this->assertEquals( $width, $img->get_setting(   'width' ) );
		$this->assertEquals( $width, $img->get_attribute( 'width' ) );
		$this->assertContains( '/' . $width . '/', $img->get_attribute( 'src' ) );

		$img = Image_Tag::create( 'picsum', array(
			'width' => $width,
		) );

		$this->assertEquals( $width, $img->get_setting(   'width' ) );
		$this->assertEquals( $width, $img->get_attribute( 'width' ) );
		$this->assertContains( '/' . $width . '/', $img->get_attribute( 'src' ) );

		$img = Image_Tag::create( 'picsum', array(
			'width' => $width,
		), array(
			'width' => $width * 2,
		) );

		$this->assertEquals( $width,     $img->get_attribute( 'width' ) );
		$this->assertEquals( $width * 2, $img->get_setting(   'width' ) );
		$this->assertContains( '/' . ( $width * 2 ) . '/', $img->get_attribute( 'src' ) );
		$this->assertContains( 'width="' . $width . '"', $img->__toString() );

		$img = Image_Tag::create( 'picsum' );
		$this->assertEquals( 1024, $img->get_attribute( 'width' ) );
		$this->assertEmpty( @$img->__toString() );
	}

	/**
	 * @group http
	 */
	function test_width_http() {
		$width = 200;

		$img = Image_Tag::create( 'picsum', array(), array(
			'width' => $width,
		) );

		$this->assertEquals( 'image/jpeg', wp_remote_retrieve_header( $img->http(), 'content-type' ) );
	}

	function test_height() {
		$width  = 200;
		$height = 300;

		$img = Image_Tag::create( 'picsum', array(), array(
			 'width' => $width,
			'height' => $height,
		) );

		$this->assertEquals(  $width, $img->get_setting(    'width' ) );
		$this->assertEquals(  $width, $img->get_attribute(  'width' ) );
		$this->assertEquals( $height, $img->get_setting(   'height' ) );
		$this->assertEquals( $height, $img->get_attribute( 'height' ) );
		$this->assertContains( '/' . $width . '/' . $height . '/', $img->get_attribute( 'src' ) );

		$img = Image_Tag::create( 'picsum', array(
			 'width' => $width,
			'height' => $height,
		) );

		$this->assertEquals(  $width, $img->get_setting(    'width' ) );
		$this->assertEquals(  $width, $img->get_attribute(  'width' ) );
		$this->assertEquals( $height, $img->get_setting(   'height' ) );
		$this->assertEquals( $height, $img->get_attribute( 'height' ) );
		$this->assertContains( '/' . $width . '/' . $height . '/', $img->get_attribute( 'src' ) );

		$img = Image_Tag::create( 'picsum', array(
			 'width' => $width,
			'height' => $height,
		), array(
			 'width' => 2 * $width,
			'height' => 2 * $height,
		) );

		$this->assertEquals( 2 * $width,  $img->get_setting(    'width' ) );
		$this->assertEquals( 2 * $height, $img->get_setting(   'height' ) );
		$this->assertEquals(  $width, $img->get_attribute(  'width' ) );
		$this->assertEquals( $height, $img->get_attribute( 'height' ) );
		$this->assertContains( '/' . ( 2 * $width ) . '/' . ( 2 * $height ) . '/', $img->get_attribute( 'src' ) );
		$this->assertContains( 'width="' . $width . '" height="' . $height . '"', $img->__toString() );
	}

	/**
	 * @group http
	 */
	function test_height_http() {
		$width  = 200;
		$height = 300;

		$img = Image_Tag::create( 'picsum', array(), array(
			 'width' => $width,
			'height' => $height,
		) );

		$this->assertEquals( 'image/jpeg', wp_remote_retrieve_header( $img->http(), 'content-type' ) );
	}

	function test_random_setting() {
		$img = Image_Tag::create( 'picsum' );
		$this->assertFalse( $img->get_setting( 'random' ) );

		$img = Image_Tag::create( 'picsum', array(), array(
			'random' => true,
			'width' => 200,
		) );

		$img2 = Image_Tag::create( 'picsum', array(), array(
			'random' => true,
			'width' => 200,
		) );

		$this->assertNotFalse( $img->get_setting( 'random' ) );
		$this->assertContains( 'random=', $img->get_attribute( 'src' ) );
		$this->assertNotEquals( $img->get_setting( 'random' ), $img2->get_setting( 'random' ) );

		$img = Image_Tag::create( 'picsum', array(), array(
			'random' => 'random',
		) );
		$this->assertEquals( 'random', $img->get_setting( 'random' ) );
	}

	/**
	 * @group http
	 */
	function test_random_http() {
		$img = Image_Tag::create( 'picsum', array(), array(
			'random' => true,
			'width' => 200,
		) );

		$img_id = wp_remote_retrieve_header( $img->http(), 'picsum-id' );

		# Try a few times, because randomly generated numbers may be equal.
		for ( $i = 0; $i < 3; $i++ ) {
			$img2 = Image_Tag::create( 'picsum', array(), array(
				'random' => true,
				'width' => 200,
			) );

			$img2_id = wp_remote_retrieve_header( $img2->http(), 'picsum-id' );

			if ( $img_id !== $img2_id )
				break;
		}

		$this->assertEquals( 'image/jpeg', wp_remote_retrieve_header(  $img->http(), 'content-type' ) );
		$this->assertEquals( 'image/jpeg', wp_remote_retrieve_header( $img2->http(), 'content-type' ) );
		$this->assertNotEquals( $img_id, $img2_id );
	}

	function test_image_id_setting() {
		$img = Image_Tag::create( 'picsum' );
		$this->assertNull( $img->get_setting( 'image_id' ) );

		$image_id = mt_rand( 1, 999 );
		$img = Image_Tag::create( 'picsum', array(), array(
			'image_id' => $image_id,
		) );

		$this->assertEquals( $image_id, $img->get_setting( 'image_id' ) );
		$this->assertContains( '/id/' . $image_id . '/', $img->get_attribute( 'src' ) );
	}

	/**
	 * @group http
	 */
	function test_image_id_http() {
		$image_id = mt_rand( 1, 999 );

		$img = Image_Tag::create( 'picsum', array(), array(
			'width' => 200,
			'image_id' => $image_id,
		) );

		$type = wp_remote_retrieve_header( $img->http(), 'content-type' );
		$message = sprintf( '%s is a %s.', $img->get_attribute( 'src' ), $type );
		$this->assertEquals( 'image/jpeg', $type, $message );
	}

	function test_grayscale_setting() {
		$img = Image_Tag::create( 'picsum' );
		$this->assertFalse( $img->get_setting( 'grayscale' ) );

		$img = Image_Tag::create( 'picsum', array(), array(
			'grayscale' => true,
		) );

		$this->assertTrue( $img->get_setting( 'grayscale' ) );
		$this->assertContains( 'grayscale=1', $img->get_attribute( 'src' ) );
	}

	/**
	 * @group http
	 */
	function test_grayscale_http() {
		$img = Image_Tag::create( 'picsum', array(), array(
			'width' => 200,
			'grayscale' => true,
		) );

		$this->assertEquals( 'image/jpeg', wp_remote_retrieve_header( $img->http(), 'content-type' ) );
	}

	function test_multiple() {
		$img = Image_Tag::create( 'picsum', array(), array(
			'blur' => 5,
			'seed' => __FUNCTION__,
			'width' => 400,
			'grayscale' => true,
		) );
		$this->assertEquals( '<img src="https://picsum.photos/seed/test_multiple/400/?blur=5&amp;grayscale=1" width="400" height="400" />', $img->__toString() );

		$img = Image_Tag::create( 'picsum', array(), array(
			'blur' => 2,
			'width' => 1920,
			'height' => 1080,
			'image_id' => 333,
		) );
		$this->assertEquals( '<img src="https://picsum.photos/id/333/1920/1080/?blur=2" width="1920" height="1080" />', $img->__toString() );
	}

	function test_details() {
		$seed = 'seed'; # Seed returns image 1081.
		$image_id = 1081;

		$img = Image_Tag::create( 'picsum', array(), array(
			'image_id' => $image_id,
		) );

		$details = ( object ) array(
			'id' => $image_id,
			'author' => 'Julien Moreau',
			'width' => 5512,
			'height' => 3708,
			'url' => 'https://unsplash.com/photos/688Fna1pwOQ',
			'download_url' => 'https://picsum.photos/id/1081/5512/3708',
		);

		$this->assertEquals( $details, $img->details() );
		$this->assertEquals( $details, $img->details() ); # Check cached value.

		# Test getting details without `image_id` setting.
		$img = Image_Tag::create( 'picsum', array(), array(
			'width' => 200,
			'seed' => $seed,
		) );

		$this->assertEquals( $details, $img->details() );
	}

	function test_picsum() {
		$img = Image_Tag::create( 'picsum', array(), array(
			'width' => 200,
		) );

		$this->assertEquals( $img, $img->picsum() );
	}

	function test_placeholder() {
		$img = Image_Tag::create( 'picsum', array(), array(
			'width' => 200,
		) );

		$this->assertInstanceOf( 'Image_Tag_Placeholder', $img->placeholder() );
	}

}

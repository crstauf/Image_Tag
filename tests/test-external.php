<?php

/**
 * @todo add tests for returning Picsum or Placeholder
 */
class Image_Tag_Base_Test extends WP_UnitTestCase {

	function test_external() {
		$src = 'https://picsum.photos/400/300';
		$img = Image_Tag::create( $src );
		$this->assertEquals( $src, $img->get_attribute( 'src' ) );
	}

	function test_http_cache() {
		$src = 'https://picsum.photos/400/300';
		$img = Image_Tag::create( $src );

		$this->assertEquals( $img->http(), $img->http() );
	}

	function test_picsum() {
		$src = 'https://picsum.photos/400/300';
		$img = Image_Tag::create( $src );

		$this->assertInstanceOf( 'Image_Tag_Picsum', $img->picsum() );
	}

	function test_placeholder() {
		$src = 'https://picsum.photos/400/300';
		$img = Image_Tag::create( $src );

		$this->assertInstanceOf( 'Image_Tag_Placeholder', $img->placeholder() );
	}

}

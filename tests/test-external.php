<?php

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

	function test_lazyload() {
		$src = 'https://picsum.photos/400/300';

		$img = Image_Tag::create( $src, array(
			'id' => 'tester',
			'width' => 400,
			'height' => 300,
		) );

		$lazyload = $img->lazyload( array(
			'id' => 'tester-lazyload',
		) );

		$this->assertEquals( $img->get_attribute( 'width' ), $lazyload->get_attribute( 'width' ) );
		$this->assertNotEquals( $img->get_attribute( 'id' ), $lazyload->get_attribute( 'id' ) );
		$this->assertEquals( $img->get_attribute( 'src' ), $lazyload->get_attribute( 'data-src' ) );
	}

	function test_ratio() {
		$src = 'https://picsum.photos/400/300';

		$img = Image_Tag::create( $src );

		$this->assertNull( $img->get_ratio() );

		$img->set_attribute( 'width',  '100' );
		$img->set_attribute( 'height', '200' );

		$this->assertEquals( 2, $img->get_ratio() );
	}

	function test_type() {
		$src = 'https://picsum.photos/400/300';
		$img = Image_Tag::create( $src );

		$this->assertTrue( $img->is_type(   'remote' ) );
		$this->assertTrue( $img->is_type( 'external' ) );

		$this->assertFalse( $img->is_type( '__placeholder' ) );
		$this->assertFalse( $img->is_type( 'unrecognized type' ) );
	}

	function test_valid() {
		$src = 'https://picsum.photos/400/300';
		$img = Image_Tag::create( $src );

		$this->assertTrue( $img->is_valid() );
	}

}

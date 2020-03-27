<?php

/**
 * @group wp
 * @group wp_theme
 */
class Image_Tag_WP_Theme_Test extends WP_UnitTestCase {

	const SRC = 'assets/images/2020-landscape-1.png';

	function test_source() {
		$img = Image_Tag::create( static::SRC );
		$this->assertEquals( trailingslashit( get_stylesheet_directory_uri() ) . static::SRC, $img->get_attribute( 'src' ) );
	}

	function test_width() {
		$img = Image_Tag::create( static::SRC );
		$this->assertEquals( 1200, $img->get_width() );
	}

	function test_height() {
		$img = Image_Tag::create( static::SRC );
		$this->assertEquals( 769, $img->get_height() );
	}

	function test_ratio() {
		$img = Image_Tag::create( static::SRC );
		$this->assertEquals( 0.640833333333333, $img->get_ratio() );
	}

	function test_picsum() {
		$img = Image_Tag::create( static::SRC );
		$this->assertInstanceOf( 'Image_Tag_Picsum', $img->picsum() );
	}

	function test_placeholder() {
		$img = Image_Tag::create( static::SRC );
		$this->assertInstanceOf( 'Image_Tag_Placeholder', $img->placeholder() );
	}

	function test_joeschmoe() {
		$img = Image_Tag::create( static::SRC );
		$this->assertInstanceOf( 'Image_Tag_JoeSchmoe', $img->joeschmoe() );
	}

	function test_colors() {
		$img = Image_Tag::create( static::SRC );
		$colors = array(
			'#c03050' => 0.48354166666666665,
			'#802040' => 0.31951388888888888,
			'#602030' => 0.19229166666666667,
		);

		$this->assertEquals( $colors, $img->get_colors() );
		$this->assertEquals( array_keys( $colors )[0], $img->get_mode_color() );
	}

	function test_type() {
		$img = Image_Tag::create( static::SRC );

		$this->assertTrue( $img->is_type(              'wp' ) );
		$this->assertTrue( $img->is_type(           'local' ) );
		$this->assertTrue( $img->is_type(           'theme' ) );
		$this->assertTrue( $img->is_type(        'internal' ) );
		$this->assertTrue( $img->is_type(        'wp-theme' ) );
		$this->assertTrue( $img->is_type(       'wordpress' ) );
		$this->assertTrue( $img->is_type( 'wordpress-theme' ) );

		$this->assertFalse( $img->is_type(           'attachment' ) );
		$this->assertFalse( $img->is_type(        'wp-attachment' ) );
		$this->assertFalse( $img->is_type( 'wordpress-attachment' ) );

		$this->assertFalse( $img->is_type( 'external' ) );
	}

	function test_valid() {
		$img = Image_Tag::create( static::SRC );

		$this->assertTrue( $img->is_valid() );

		$img = @Image_Tag::create( 'does-not-exist.jpg' );

		$this->assertFalse( $img->is_valid() );
	}

}

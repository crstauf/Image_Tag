<?php

/**
 * @link https://placeholder.com/
 * @group placeholder
 *
 * @todo add tests for returning Picsum
 */
class Image_Tag_Placeholder_Test extends WP_UnitTestCase {

	function test_base_source() {
		$img = Image_Tag::create( 'placeholder' );

		$this->assertEquals( 'https://via.placeholder.com/', Image_Tag_Placeholder::BASE_URL );
		$this->assertEquals( Image_Tag_Placeholder::BASE_URL, $img->get_attribute( 'src' ) );
	}

	function test_text_setting() {
		$text = 'Test text setting';

		$img = Image_Tag::create( 'placeholder', array(), array(
			'text' => $text,
		) );

		$this->assertEquals( $text, $img->get_setting( 'text' ) );
		$this->assertContains( urlencode( $text ), $img->get_attribute( 'src' ) );
	}

	function test_width() {
		$width = 200;

		$img = Image_Tag::create( 'placeholder', array(), array(
			'width' => $width,
		) );

		$this->assertEquals( $width, $img->get_setting(   'width' ) );
		$this->assertEquals( $width, $img->get_attribute( 'width' ) );
		$this->assertContains( '/' . $width . '/', $img->get_attribute( 'src' ) );

		$img = Image_Tag::create( 'placeholder', array(
			'width' => $width,
		) );

		$this->assertEquals( $width, $img->get_setting(   'width' ) );
		$this->assertEquals( $width, $img->get_attribute( 'width' ) );
		$this->assertContains( '/' . $width . '/', $img->get_attribute( 'src' ) );

		$img = Image_Tag::create( 'placeholder', array(
			'width' => $width,
		), array(
			'width' => $width * 2,
		) );

		$this->assertEquals( $width,     $img->get_attribute( 'width' ) );
		$this->assertEquals( $width * 2, $img->get_setting(   'width' ) );
		$this->assertContains( '/' . ( $width * 2 ) . '/', $img->get_attribute( 'src' ) );
		$this->assertContains( 'width="' . $width . '"', $img->__toString() );

		$img = Image_Tag::create( 'placeholder' );
		$this->assertNull( $img->get_attribute( 'width' ) );
		$this->assertEmpty( @$img->__toString() );
	}

	function test_height() {
		$width  = 200;
		$height = 300;

		$img = Image_Tag::create( 'placeholder', array(), array(
			'width' => $width,
		) );

		$this->assertEquals( $width, $img->get_attribute( 'height' ) );

		$img = Image_Tag::create( 'placeholder', array(), array(
			 'width' => $width,
			'height' => $height,
		) );

		$this->assertEquals(  $width, $img->get_setting(    'width' ) );
		$this->assertEquals(  $width, $img->get_attribute(  'width' ) );
		$this->assertEquals( $height, $img->get_setting(   'height' ) );
		$this->assertEquals( $height, $img->get_attribute( 'height' ) );
		$this->assertContains( '/' . $width . 'x' . $height . '/', $img->get_attribute( 'src' ) );

		$img = Image_Tag::create( 'placeholder', array(
			 'width' => $width,
			'height' => $height,
		) );

		$this->assertEquals(  $width, $img->get_setting(    'width' ) );
		$this->assertEquals(  $width, $img->get_attribute(  'width' ) );
		$this->assertEquals( $height, $img->get_setting(   'height' ) );
		$this->assertEquals( $height, $img->get_attribute( 'height' ) );
		$this->assertContains( '/' . $width . 'x' . $height . '/', $img->get_attribute( 'src' ) );

		$img = Image_Tag::create( 'placeholder', array(
			 'width' => $width,
			'height' => $height,
		), array(
			 'width' => 2 * $width,
			'height' => 2 * $height,
		) );

		$this->assertEquals( $width,      $img->get_attribute(  'width' ) );
		$this->assertEquals( $width * 2,  $img->get_setting(    'width' ) );
		$this->assertEquals( $height,     $img->get_attribute( 'height' ) );
		$this->assertEquals( $height * 2, $img->get_setting(   'height' ) );
		$this->assertContains( '/' . ( 2 * $width ) . 'x' . ( 2 * $height ) . '/', $img->get_attribute( 'src' ) );
		$this->assertContains( 'width="' . $width . '" height="' . $height . '"', $img->__toString() );

		$img = Image_Tag::create( 'placeholder' );
		$this->assertNull( $img->get_attribute( 'height' ) );
	}

	function test_bg_color() {
		$color = '#FF0000';

		$img = Image_Tag::create( 'placeholder', array(), array(
			'bg-color' => $color,
		) );

		$color = str_replace( '#', '', $color );

		$this->assertEquals( $color, $img->get_setting( 'bg-color' ) );
		$this->assertContains( '/' . urlencode( $color ) . '/', $img->get_attribute( 'src' ) );
	}

	function test_text_color() {
		$bg_color   = '#FF0000';
		$text_color = '#FFF';

		$img = Image_Tag::create( 'placeholder', array(), array(
			  'bg-color' => $bg_color,
			'text-color' => $text_color,
		) );

		$text_color = str_replace( '#', '', $text_color );
		  $bg_color = str_replace( '#', '',   $bg_color );

		$this->assertEquals(   $bg_color, $img->get_setting(   'bg-color' ) );
		$this->assertEquals( $text_color, $img->get_setting( 'text-color' ) );
		$this->assertContains( '/' . urlencode( $bg_color ) . '/' . urlencode( $text_color ) . '/', $img->get_attribute( 'src' ) );
	}

	function test_placeholder() {
		$img = Image_Tag::create( 'placeholder', array(), array(
			'width' => 200,
		) );

		$this->assertEquals( $img, $img->placeholder() );
	}

	function test_picsum() {
		$img = Image_Tag::create( 'picsum', array(), array(
			'width' => 200,
		) );

		$this->assertInstanceOf( 'Image_Tag_Picsum', $img->picsum() );
	}

	function test_ratio() {
		$img = Image_Tag::create( 'picsum' );

		$this->assertEquals( 1, $img->get_ratio() );

		$img->set_setting( 'width',  100 );
		$img->set_setting( 'height', 200 );

		$this->assertEquals( 2, $img->get_ratio() );
	}

	function test_type() {
		$img = Image_Tag::create( 'placeholder' );

		$this->assertTrue( $img->is_type(        'remote' ) );
		$this->assertTrue( $img->is_type(      'external' ) );
		$this->assertTrue( $img->is_type(   'placeholder' ) );
		$this->assertTrue( $img->is_type( '__placeholder' ) );

		$this->assertFalse( $img->is_type( 'local' ) );
	}

}

?>

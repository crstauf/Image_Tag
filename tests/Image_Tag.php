<?php

declare( strict_types=1 );

namespace Image_Tag\Tests;
use \Image_Tag;

class _Image_Tag extends \WP_UnitTestCase {

	function test_types() : void {
		$expected = array(
			'base',
			'default',
		);

		$this->assertEquals( $expected, Image_Tag::TYPES );
	}

	function test_create() : void {
		$actual_src = 'https://doesnotexist.com/doesnotexist.jpg';
		$actual_attributes = array(
			'width' => 1600,
			'height' => 900,
		);
		$actual_settings = array(
			'foo' => 'bar',
			'bar' => 'foo',
		);

		$actual_object = Image_Tag::create(
			$actual_src,
			$actual_attributes,
			$actual_settings
		);

		$this->assertInstanceOf( Image_Tag::class, $actual_object );
		$this->assertEquals( $actual_src, $actual_object->attributes->get( 'src' ) );

		foreach ( $actual_attributes as $attribute => $value )
			$this->assertEquals( $value, $actual_object->attributes->get( $attribute ) );

		foreach ( $actual_settings as $setting => $value )
			$this->assertEquals( $value, $actual_object->settings->get( $setting ) );
	}

	function test_output() : void {
		$expected = sprintf( '%s<img %swidth="%d" %sheight="%d" %ssrc="%s" %salt="%s" />%s',
			PHP_EOL, PHP_EOL,
			1600, PHP_EOL,
			900, PHP_EOL,
			'https://doesnotexist.com/doesnotexist.jpg', PHP_EOL,
			'',
			PHP_EOL
		);

		$object = Image_Tag::create(
			'https://doesnotexist.com/doesnotexist.jpg',
			array(
				'width' => 1600,
				'height' => 900,
			)
		);

		$this->assertEquals( $expected, $object->output() );

		foreach ( array( '', null, false ) as $source ) {
			$object = new Image_Tag( $source );

			$this->assertEquals( '', $object->output() );
		}

		$this->_test_output_fallback();
	}

	protected function _test_output_fallback() : void {
		$object = new Image_Tag(
			array(
				'width' => 1600,
				'height' => 900,
			),
			array(
				'fallback' => array(
					'https://doesnotexist.com/doesnotexist.jpg' => false,
					'https://doesnotexist.com/doesnotexist.png' => true,
				),
			),
		);

		$expected = sprintf( '%s<img %swidth="%d" %sheight="%d" %ssrc="%s" %salt="%s" />%s',
			PHP_EOL, PHP_EOL,
			1600, PHP_EOL,
			900, PHP_EOL,
			'https://doesnotexist.com/doesnotexist.png', PHP_EOL,
			'',
			PHP_EOL
		);

		$this->assertEquals( $expected, $object->output() );
	}

	function test_lazyload() : void {
		$object = Image_Tag::create(
			'https://doesnotexist.com/doesnotexist.jpg',
			array(
				'width' => 1600,
				'height' => 900,
			)
		);

		$expected = sprintf( '%s<img %swidth="%d" %sheight="%d" %ssrc="%s" %salt="%s" %sclass="%s" %sdata-src="%s" />%s',
			PHP_EOL, PHP_EOL,
			1600, PHP_EOL,
			900, PHP_EOL,
			Image_Tag::BLANK, PHP_EOL,
			'', PHP_EOL,
			'lazyload hide-if-no-js', PHP_EOL,
			'https://doesnotexist.com/doesnotexist.jpg',
			PHP_EOL
		);

		$expected .= sprintf( '%s<noscript>%s<img %swidth="%d" %sheight="%d" %ssrc="%s" %salt="%s" %sclass="%s" %sloading="%s" />%s</noscript>',
			PHP_EOL, PHP_EOL, PHP_EOL,
			1600, PHP_EOL,
			900, PHP_EOL,
			'https://doesnotexist.com/doesnotexist.jpg', PHP_EOL,
			'', PHP_EOL,
			'no-js', PHP_EOL,
			'lazy',
			PHP_EOL
		);

		$this->assertEquals( $expected, $object->lazyload() );
	}

	function test_noscript() : void {
		$object = Image_Tag::create(
			'https://doesnotexist.com/doesnotexist.jpg',
			array(
				'width' => 1600,
				'height' => 900,
			)
		);

		$expected = sprintf( '<noscript>%s<img %swidth="%d" %sheight="%d" %ssrc="%s" %salt="%s" />%s</noscript>',
			PHP_EOL, PHP_EOL,
			1600, PHP_EOL,
			900, PHP_EOL,
			'https://doesnotexist.com/doesnotexist.jpg', PHP_EOL,
			'',
			PHP_EOL
		);

		$this->assertEquals( $expected, $object->noscript() );
	}

	function test_get_type() : void {
		$object = new Image_Tag;
		$this->assertEquals( 'base', $object->get_type() );
	}

	function test_is_type() : void {
		$object = new Image_Tag;

		$this->assertTrue( $object->is_type( 'base' ) );
		$this->assertTrue( $object->is_type( 'default' ) );
		$this->assertTrue( $object->is_type( array(
			'base',
			'fejwklfew',
		) ) );

		$this->assertFalse( $object->is_type( 'placeholder' ) );
		$this->assertFalse( $object->is_type( 'external' ) );
		$this->assertFalse( $object->is_type( array(
			'placeholder',
			'external',
		) ) );
	}

	function test_is_valid() : void {
		$this->assertTrue( Image_Tag::create( 'https://doesnotexist.com/doesnotexist.jpg' )->is_valid() );
		$this->assertTrue( Image_Tag::create( 'https://doesnotexist.com/doesnotexist.jpg' )->is_valid( 'base' ) );
		$this->assertTrue( Image_Tag::create( 'https://doesnotexist.com/doesnotexist.jpg' )->is_valid( array( 'base', 'fdsjfew' ) ) );

		$this->assertFalse( Image_Tag::create( 'https://doesnotexist.com/doesnotexist.jpg' )->is_valid( 'fdsfew' ) );
		$this->assertFalse( Image_Tag::create( 'https://doesnotexist.com/doesnotexist.jpg' )->is_valid( array( 'fdsfew', 'fewls' ) ) );

		$object = new Image_Tag;
		$this->assertFalse( $object->is_valid() );
		$this->assertFalse( $object->is_valid( 'base' ) );
	}

	protected function _test_is_valid_fallback() : void {
		$object = new Image_Tag( null, array(
			'fallback' => array(
				'https://doesnotexist.com/doesnotexist.jpg' => true,
			),
		) );

		$this->assertTrue( $object->is_valid( null, true ) );
		$this->assertTrue( $object->is_valid( 'base', true ) );
		$this->assertTrue( $object->is_valid( array( 'base', 'fewaf' ), true ) );

		$this->assertFalse( $object->is_valid() );
		$this->assertFalse( $object->is_valid( 'base' ) );
	}

}

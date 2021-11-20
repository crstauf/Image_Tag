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

	function test_toString() : void {
		$expected = sprintf( '%s<img width="%d" %sheight="%d" %ssrc="%s" %salt="" />%s',
			PHP_EOL,
			1600, PHP_EOL,
			900, PHP_EOL,
			'https://doesnotexist.com/doesnotexist.jpg', PHP_EOL,
			PHP_EOL
		);

		$object = Image_Tag::create(
			'https://doesnotexist.com/doesnotexist.jpg',
			array(
				'width' => 1600,
				'height' => 900,
			)
		);

		$this->assertEquals( $expected, $object->__toString() );
		$this->assertEquals( $expected, sprintf( '%s', $object ) );

		foreach ( array( '', null, false ) as $source ) {
			$object = new Image_Tag( $source );

			$this->assertEquals( '', $object->__toString() );
			$this->assertEquals( '', sprintf( '%s', $object ) );
		}
	}

	function test_output() : void {
		$expected = sprintf( '%s<img width="%d" %sheight="%d" %ssrc="%s" %salt="" />%s',
			PHP_EOL,
			1600, PHP_EOL,
			900, PHP_EOL,
			'https://doesnotexist.com/doesnotexist.jpg', PHP_EOL,
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
	}

}

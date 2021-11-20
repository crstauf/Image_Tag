<?php

declare( strict_types=1 );

namespace Image_Tag\Tests;
use \Image_Tag;

class _Image_Tag extends \WP_UnitTestCase {

	/**
	 * @group constant
	 */
	function test_types() : void {
		$expected = array(
			'base',
			'default',
		);

		$this->assertEquals( $expected, Image_Tag::TYPES );
	}

	/**
	 * @dataProvider creationProvider
	 * @covers \Image_Tag::create()
	 * @covers \Image_Tag::__construct()
	 */
	function test_create( $expected_class, $actual_src, $actual_attributes, $actual_settings ) : void {
		$actual_object = Image_Tag::create(
			$actual_src,
			$actual_attributes,
			$actual_settings
		);

		$this->assertInstanceOf( $expected_class, $actual_object );

		foreach ( $actual_attributes as $attribute => $value )
			$this->assertEquals( $value, $actual_object->attributes->get( $attribute ) );

		foreach ( $actual_settings as $setting => $value )
			$this->assertEquals( $value, $actual_object->settings->get( $setting ) );
	}

	function creationProvider() : array {
		$attachment_id = wp_insert_attachment(
			array(
				'post_content' => 'https://unsplash.com/photos/zs98a0DtKL4',
			),
			trailingslashit( __DIR__ ) . 'stephen-phillips-hostreviews-co-uk-zs98a0DtKL4-unsplash.jpg'
		);

		$data = array();

		$data['Image_Tag'] = array(
			Image_Tag::class,
			'https://doesnotexist.com/doesnotexist.jpg',
			array(
				'width' => 1600,
				'height' => 900,
			),
			array(
				'foo' => 'bar',
				'bar' => 'foo',
			),
		);

		$data['joeschmoe'] = array(
			\Image_Tag\Types\JoeSchmoe::class,
			'joeschmoe',
			array(
				'width' => 500,
				'height' => 500,
			),
			array(
				'gender' => 'male',
			),
		);

		$data['picsum'] = array(
			\Image_Tag\Types\Picsum::class,
			'picsum',
			array(
				'width' => 800,
				'height' => 600,
			),
			array(
				'width' => 1600,
				'height' => 1200,
			),
		);

		$data['placeholder'] = array(
			\Image_Tag\Types\Placeholder::class,
			'placeholder',
			array(
				'width' => 400,
				'height' => 300,
			),
			array(
				'bg_color' => 'FF0000',
			),
		);

		$data['unsplash'] = array(
			\Image_Tag\Types\Unsplash::class,
			'unsplash',
			array(
				'width' => 1600,
				'height' => 1000,
			),
			array(
				'random' => true,
			),
		);

		$data['wp_theme'] = array(
			\Image_Tag\Types\WP_Theme::class,
			'assets/images/Daffodils.jpg',
			array(
				'width' => 894,
				'height' => 1108,
			),
			array()
		);

		$data['wp_attachment'] = array(
			\Image_Tag\Types\WP_Attachment::class,
			$attachment_id,
			array(
				'width' => 1000,
				'height' => 667,
			),
			array(
				'image-sizes' => array( 'medium', 'full' ),
			)
		);

		$data['default'] = array(
			Image_Tag::class,
			1.5,
			array(
				'width' => 15,
				'height' => 15,
			),
			array(),
		);

		return $data;
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

	/**
	 * @covers \Image_Tag::perform_validation_checks()
	 */
	function test_is_valid() : void {
		$this->assertTrue( Image_Tag::create( 'https://doesnotexist.com/doesnotexist.jpg' )->is_valid() );
		$this->assertTrue( Image_Tag::create( 'https://doesnotexist.com/doesnotexist.jpg' )->is_valid( 'base' ) );
		$this->assertTrue( Image_Tag::create( 'https://doesnotexist.com/doesnotexist.jpg' )->is_valid( array( 'base', 'fdsjfew' ) ) );

		$this->assertFalse( Image_Tag::create( 'https://doesnotexist.com/doesnotexist.jpg' )->is_valid( 'fdsfew' ) );
		$this->assertFalse( Image_Tag::create( 'https://doesnotexist.com/doesnotexist.jpg' )->is_valid( array( 'fdsfew', 'fewls' ) ) );

		$object = new Image_Tag;
		$this->assertFalse( $object->is_valid() );
		$this->assertFalse( $object->is_valid( 'base' ) );

		$this->_test_is_valid_fallback();
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

	function test_joeschmoe() : void {
		$object = Image_Tag::create( 'https://doesnotexist.com/doesnotexist.jpg', array(
			'width' => 1600,
			'height' => 900,
		) );

		$joeschmoe = $object->joeschmoe();

		$this->assertInstanceOf( \Image_Tag\Types\JoeSchmoe::class, $joeschmoe );
		$this->assertEquals( 1600, $joeschmoe->attributes->get( 'width' ) );
		$this->assertEquals(  900, $joeschmoe->attributes->get( 'height' ) );

		$expected = sprintf( '%s<img %swidth="%d" %sheight="%d" %ssrc="%s" %salt="%s" />%s',
			PHP_EOL, PHP_EOL,
			1600, PHP_EOL,
			900, PHP_EOL,
			'https://doesnotexist.com/doesnotexist.jpg', PHP_EOL,
			'',
			PHP_EOL
		);

		$this->assertNotEquals( $expected, $joeschmoe->output() );
	}

	function test_picsum() : void {
		$object = Image_Tag::create( 'https://doesnotexist.com/doesnotexist.jpg', array(
			'width' => 1600,
			'height' => 900,
		) );

		$picsum = $object->picsum();

		$this->assertInstanceOf( \Image_Tag\Types\Picsum::class, $picsum );
		$this->assertEquals( 1600, $picsum->attributes->get( 'width' ) );
		$this->assertEquals(  900, $picsum->attributes->get( 'height' ) );

		$expected = sprintf( '%s<img %swidth="%d" %sheight="%d" %ssrc="%s" %salt="%s" />%s',
			PHP_EOL, PHP_EOL,
			1600, PHP_EOL,
			900, PHP_EOL,
			'https://doesnotexist.com/doesnotexist.jpg', PHP_EOL,
			'',
			PHP_EOL
		);

		$this->assertNotEquals( $expected, $picsum->output() );
	}

	function test_placeholder() : void {
		$object = Image_Tag::create( 'https://doesnotexist.com/doesnotexist.jpg', array(
			'width' => 1600,
			'height' => 900,
		) );

		$placeholder = $object->placeholder();

		$this->assertInstanceOf( \Image_Tag\Types\Placeholder::class, $placeholder );
		$this->assertEquals( 1600, $placeholder->attributes->get( 'width' ) );
		$this->assertEquals(  900, $placeholder->attributes->get( 'height' ) );

		$expected = sprintf( '%s<img %swidth="%d" %sheight="%d" %ssrc="%s" %salt="%s" />%s',
			PHP_EOL, PHP_EOL,
			1600, PHP_EOL,
			900, PHP_EOL,
			'https://doesnotexist.com/doesnotexist.jpg', PHP_EOL,
			'',
			PHP_EOL
		);

		$this->assertNotEquals( $expected, $placeholder->output() );
	}

	function test_unsplash() : void {
		$object = Image_Tag::create( 'https://doesnotexist.com/doesnotexist.jpg', array(
			'width' => 1600,
			'height' => 900,
		) );

		$unsplash = $object->unsplash();

		$this->assertInstanceOf( \Image_Tag\Types\Unsplash::class, $unsplash );
		$this->assertEquals( 1600, $unsplash->attributes->get( 'width' ) );
		$this->assertEquals(  900, $unsplash->attributes->get( 'height' ) );

		$expected = sprintf( '%s<img %swidth="%d" %sheight="%d" %ssrc="%s" %salt="%s" />%s',
			PHP_EOL, PHP_EOL,
			1600, PHP_EOL,
			900, PHP_EOL,
			'https://doesnotexist.com/doesnotexist.jpg', PHP_EOL,
			'',
			PHP_EOL
		);

		$this->assertNotEquals( $expected, $unsplash->output() );
	}

}

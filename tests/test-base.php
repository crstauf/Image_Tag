<?php

require_once '_unit-test-case.php';

class Image_Tag_Base_Test extends Image_Tag_UnitTestCase {

	const SOURCE = 'https://source.unsplash.com/random';

	protected function class_name() {
		return Image_Tag::class;
	}

	protected function create( $attributes = array(), $settings = array(), $source = 'https://source.unsplash.com/random' ) {
		return Image_Tag::create( $source, $attributes, $settings );
	}

	function test_toString() {
		$img = $this->construct( array( 'src' => static::SOURCE ) );
		$this->assertEquals( '<img src="' . esc_attr( esc_url( static::SOURCE ) ) . '" />', $img->__toString() );

		$img = $this->create();
		$this->assertEquals( '<img src="' . esc_attr( esc_url( static::SOURCE ) ) . '" />', $img->__toString() );
	}

	function test_default_attributes() {
		parent::test_default_attributes();

		$img = $this->construct();
		$this->assertEmpty( $img->get_attributes() );
	}

	/**
	 * Test get settings.
	 *
	 * @covers Image_Tag::get_settings()
	 */
	function test_get_settings() {
		$settings = array(
			'before_output' => null,
			'after_output' => null,
			'sizes' => array(),
			'foo' => 'foobar',
			'array' => array( 'foobar' ),
			'int' => mt_rand( 1, 999 ),
		);

		$img = $this->create( null, $settings );

		$this->assertSame( $settings, $img->get_settings() );

		$settings['int'] = ( string ) $settings['int'];
		$this->assertNotSame( $settings, $img->get_settings() );
	}

}
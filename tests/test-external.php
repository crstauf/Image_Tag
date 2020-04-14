<?php

require_once '_unit-test-case.php';

class Image_Tag_External_Test extends Image_Tag_UnitTestCase {

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

}
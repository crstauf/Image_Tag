<?php

require_once 'abstract.php';

/**
 * @coversDefaultClass Image_Tag
 */
class Image_Tag_Test extends Image_Tag_Test_Base {

	protected function class_name() {
		return Image_Tag::class;
	}

	/**
	 * @covers ::get_type()
	 */
	function test_get_type() {
		$this->assertSame( 'base', $this->new_instance()->get_type() );
	}

	/**
	 * @covers ::is_type()
	 */
	function test_is_type() {
		$instance = $this->new_instance();

		$this->assertTrue( $instance->is_type( 'base' ) );
		$this->assertTrue( $instance->is_type( array( 'external', 'base' ) ) );

		$this->assertFalse( $instance->is_type( 'external' ) );
		$this->assertFalse( $instance->is_type( 'local' ) );
	}

	/**
	 * @covers ::is_valid()
	 * @covers ::check_valid()
	 */
	function test_is_valid() {
		$instance = $this->new_instance();

		$this->assertFalse( $instance->is_valid( 'base' ) );
		$this->assertFalse( $instance->is_valid() );

		$instance->attributes->set( 'src', 'https://source.unsplash.com/1000x1000' );

		$this->assertTrue( $instance->is_valid( array( 'foobar', 'base' ) ) );
		$this->assertTrue( $instance->is_valid( 'base' ) );
		$this->assertTrue( $instance->is_valid() );

		$this->assertFalse( $instance->is_valid( array( 'foo', 'bar' ) ) );
		$this->assertFalse( $instance->is_valid( 'foobar' ) );
	}

}
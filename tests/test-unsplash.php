<?php

require_once '_unit-test-case.php';

class Image_Tag_Unsplash_Test extends Image_Tag_UnitTestCase {

	protected function class_name() {
		return 'Image_Tag_Unsplash';
	}

	protected function create( $attributes = array(), $settings = array(), $source = 'unsplash' ) {
		return Image_Tag::create( $source, $attributes, $settings );
	}

	function test_base_url() {
		$this->assertSame( 'https://source.unsplash.com/', constant( $this->class_name() . '::BASE_URL' ) );
	}

	function test_default_settings() {}

	function test_toString() {}

	function test_type() {
		$type = 'unsplash';
		$img = $this->create( $type, array() );

		$this->assertSame( $type, $img->get_type() );

		$types = array(
			'remote',
			'external',
			'__placeholder',
			'source-unsplash',
			'Unsplash Source',
			'source.unsplash.com',
			$type,
		);

		foreach ( $types as $type )
			$this->assertTrue( $img->is_type( $type ), sprintf( 'Failed asserting that "%s" is a valid type.', $type ) );
	}

	function test_valid() {}

	/**
	 * @covers Image_Tag_Unsplash::get_src_attribute()
	 * @covers Image_Tag_Unsplash::generate_url()
	 */
	function test_src_attribute() {}

	/**
	 * Test get settings.
	 *
	 * @covers Image_Tag_Unsplash::get_settings()
	 */
	function test_get_settings() {}

}

?>
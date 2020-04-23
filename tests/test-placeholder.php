<?php

require_once '_unit-test-case.php';

class Image_Tag_Placeholder_Test extends Image_Tag_UnitTestCase {

	protected function class_name() {
		return 'Image_Tag_Placeholder';
	}

	protected function create( $attributes = array(), $settings = array(), $source = null ) {

	}

	function test_toString() {

	}

	function test_base_url() {
		$this->assertSame( 'https://via.placeholder.com/', constant( $this->class_name() . '::BASE_URL' ) );
	}

	function test_type() {
		$type = 'placeholder';
		$img = $this->create( $type, array() );

		$this->assertSame( $type, $img->get_type() );

		$types = array(
			'remote',
			'external',
			'__placeholder',
			'placeholdit',
			'placehold.it',
			'placeholder.com',
			$type,
		);

		foreach ( $types as $type )
			$this->assertTrue( $img->is_type( $type ) );
	}

	/**
	 * Test get settings.
	 *
	 * @covers Image_Tag_Placeholder::get_settings()
	 */
	function test_get_settings() {}

	/**
	 * Test low-quality image placeholder.
	 *
	 * @uses Image_Tag::lqip()
	 *
	 * @group features
	 * @group feature-lqip
	 * @covers Image_Tag::lqip()
	 */
	function test_lqip() {}

	/**
	 * Test common colors.
	 *
	 * @uses Image_Tag::common_colors()
	 *
	 * @group features
	 * @group feature-colors
	 * @covers Image_Tag::common_colors()
	 */
	function test_common_colors() {}

	/**
	 * Test mode color.
	 *
	 * @uses Image_Tag::mode_color()
	 *
	 * @group features
	 * @group feature-colors
	 * @covers Image_Tag::mode_color()
	 */
	function test_mode_color() {}

}

?>
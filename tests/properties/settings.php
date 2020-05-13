<?php

require_once 'abstract-properties-tests.php';

/**
 * @coversDefaultClass Image_Tag_Settings
 * @group settings
 */
class Image_Tag_Settings_Test extends Image_Tag_Properties_Tests {

	const DEFAULTS = array(
		'before_output' => array(),
		'after_output' => array(),
	);

	protected function class_name() {
		return Image_Tag_Settings::class;
	}

	/**
	 * Test Image_Tag_Settings::NAME constant value.
	 *
	 * @group constant
	 */
	function test_name_constant() {
		$this->assertSame( 'setting', constant( $this->class_name() . '::NAME' ) );
	}

	/**
	 * Data provider for Image_Tag_Settings_Test::test__get().
	 *
	 * @see Image_Tag_Settings_Test::test__get()
	 * @uses Image_Tag_Properties_Tests::data__get()
	 * @return array[]
	 */
	function data__get() {
		$data = parent::data__get();

		# Add test for property access via alias.
		$data['settings'] = array(
			'settings',
			null,
			static::DEFAULTS,
		);

		return $data;
	}

	/**
	 * @param string $property
	 * @param mixed $value
	 * @param mixed $expected
	 *
	 * @covers ::__get()
	 * @group instance
	 * @group magic
	 * @group get
	 *
	 * @dataProvider data__get
	 */
	function test__get( string $property, $value, $expected = null ) {
		if ( 'settings' === $property ) {
			$this->assertIsArray( $this->get_instance()->$property );
			return;
		}

		parent::test__get( $property, $value, $expected = null );
	}

}

?>
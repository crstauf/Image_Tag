<?php

require_once 'abstract-properties-tests.php';

/**
 * @coversDefaultClass Image_Tag_Attributes
 * @group attributes
 */
class Image_Tag_Attributes_Test extends Image_Tag_Properties_Tests {

	protected function class_name() {
		return Image_Tag_Attributes::class;
	}

	/**
	 * Test Image_Tag_Attributes::NAME constant value.
	 *
	 * @group constant
	 */
	function test_name_constant() {
		$this->assertSame( 'attribute', constant( $this->class_name() . '::NAME' ) );
	}

	/**
	 * Test Image_Tag_Attribute::DEFAULTS constant.
	 *
	 * @group constant
	 */
	function test_defaults_constant() {
		$this->assertSame( array(
			'id' => null,
			'alt' => null,
			'src' => null,
			'title' => null,
			'width' => null,
			'height' => null,
			'data-src' => null,
			'data-srcset' => array(),
			'data-sizes' => array(),
			'srcset' => array(),
			'style' => array(),
			'sizes' => array(),
			'class' => array(),
		), constant( $this->class_name() . '::DEFAULTS' ) );
	}

	function data__construct() {
		$this->markTestIncomplete();
	}

	/**
	 * Test Image_Tag_Attribute::set() function.
	 *
	 * @covers ::set_class_attribute()
	 *
	 * @see self::test_set()
	 * @uses Image_Tag_Properties_Test::data_set()
	 */
	function data_set() {
		$data = parent::data_set();

		$data['class strings'] = array(
			$this->new_instance( array() ),
			'class',
			'foo bar',
			array( 'foo', 'bar' ),
		);

		$data['class string in array'] = array(
			$this->new_instance( array() ),
			array(
				'class' => 'foo bar',
			),
			null,
			array(
				'class' => array( 'foo', 'bar' ),
			),
		);

		$data['class array'] = array(
			$this->new_instance( array() ),
			array(
				'class' => array( 'foo', 'bar' ),
			),
		);

		$data['class crazy array'] = array(
			$this->new_instance( array() ),
			array(
				'class' => array(
					'foo1 bar1',
					array(
						'foo2 bar2',
						'foo3',
						'bar3,'
					),
					array(
						'foo4',
						'bar4',
					),
				),
			),
			null,
			array(
				'class' => array(
					'foo1',
					'bar1',
					'foo2',
					'bar2',
					'foo3',
					'bar3',
					'foo4',
					'bar4',
				),
			),
		);

		return $data;
	}

	/**
	 * @see Image_Tag_Properties_Test::test_set()
	 *
	 * @covers Image_Tag_Properties::set()
	 * @covers Image_Tag_Properties::set_properties()
	 * @covers Image_Tag_Properties::set_property()
	 * @covers Image_Tag_Attributes::set_class_attribute()
	 *
	 * @dataProvider data_set
	 */
	function test_set( Image_Tag_Properties $instance, $set_properties, $value = null, $expected = null ) {
		parent::test_set( $instance, $set_properties, $value, $expected );
	}

}

?>
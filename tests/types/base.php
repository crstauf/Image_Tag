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
	 * Data provider for Image_Tag_Test_Base::test_add().
	 *
	 * @see Image_Tag_Test_Base::test_add()
	 * @return array[]
	 */
	function data_add() {
		return array(

			'string' => array(
				$this->new_instance( array() ),
				new Image_Tag_Attributes( array() ),
				'id',
				__FUNCTION__,
			),

			'array' => array(
				$this->new_instance(),
				new Image_Tag_Attributes( array() ),
				'class',
				array( 'foo', 'bar' ),
			),

			'multiple' => array(
				$this->new_instance(),
				new Image_Tag_Attributes( array() ),
				array(
					'id' => __FUNCTION__,
					'class' => array( 'foo', 'bar' ),
					'width' => 1600,
					'height' => 900,
				),
			),

			'multiple with class string' => array(
				$this->new_instance(),
				new Image_Tag_Attributes( array() ),
				array(
					'id' => __FUNCTION__,
					'class' => 'foo bar',
					'width' => 1600,
					'height' => 900,
				),
				array(
					'id' => __FUNCTION__,
					'class' => array( 'foo', 'bar' ),
					'width' => 1600,
					'height' => 900,
				),
			),

		);
	}

	/**
	 * @covers ::get_type()
	 *
	 * @todo define
	 */
	function test_get_type() {

	}

	/**
	 * @covers ::is_type()
	 *
	 * @todo define
	 */
	function test_is_type() {

	}

	/**
	 * @covers ::check_valid()
	 *
	 * @todo define
	 */
	function test_check_valid() {

	}

}
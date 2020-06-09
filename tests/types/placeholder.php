<?php

require_once 'abstract.php';

/**
 * @coversDefaultClass Image_Tag_Placeholder
 */
class Image_Tag_Placeholder_Test extends Image_Tag_Test_Base {

	protected function class_name() {
		return Image_Tag_Placeholder::class;
	}

	/**
	 * Data provider for Image_Tag_Test_Base::test_constant_types().
	 *
	 * @see Image_Tag_Test_Base::test_constant_types()
	 * @return array[]
	 */
	function data_constant_types() {
		return array(
			array(
				array(
					'placeholder.com',
					'dimensions',
					'size',
					'text',
					'placeholder',
					'external',
					'remote',
				),
			),
		);
	}


	/*
	 ######  ########    ###    ######## ####  ######
	##    ##    ##      ## ##      ##     ##  ##    ##
	##          ##     ##   ##     ##     ##  ##
	 ######     ##    ##     ##    ##     ##  ##
	      ##    ##    #########    ##     ##  ##
	##    ##    ##    ##     ##    ##     ##  ##    ##
	 ######     ##    ##     ##    ##    ####  ######
	*/

	/**
	 * @covers Image_Tag::create()
	 */
	function data_create() {
		return array(
			'placeholder.com' => array(
				$this->new_instance(),
				'placeholder.com',
			),
			'dimensions' => array(
				$this->new_instance(),
				'dimensions',
			),
			'size' => array(
				$this->new_instance(),
				'size',
			),
			'text' => array(
				$this->new_instance(),
				'text',
			),
		);
	}

	function data__toString() {
		$this->markTestIncomplete();
	}

	function test_get_type() {
		$this->markTestIncomplete();
	}

	function test_is_type() {
		$this->markTestIncomplete();
	}

	function test_is_valid() {
		$this->markTestIncomplete();
	}

	function data_http() {
		$this->markTestIncomplete();
	}

	function data_lazyload() {
		$this->markTestIncomplete();
	}

	function data_into() {
		$this->markTestIncomplete();
	}

}

?>
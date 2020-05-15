<?php

require_once 'abstract-properties-tests.php';

/**
 * @coversDefaultClass Image_Tag_Properties
 */
class Image_Tag_Properties_Test extends Image_Tag_Properties_Tests {

	protected function class_name() {
		return Image_Tag_Properties::class;
	}

	/**
	 * Test Image_Tag_Properties::NAME constant value.
	 *
	 * @group constant
	 */
	function test_name_constant() {
		$this->assertSame( 'property', constant( $this->class_name() . '::NAME' ) );
	}

	/**
	 * Data provider for Image_Tag_Properties_Tests::test_set().
	 *
	 * @see Image_Tag_Properties_Base::test_set()
	 * @uses Image_Tag_Properties_Tests::data_set()
	 * @return array[]
	 */
	function data_set() {
		$data = parent::data_set();

		$data['override by name'] = array(
			$this->new_instance(),
			'fejwio',
			__FUNCTION__,
		);

		$data['override by type'] = array(
			$this->new_instance( null, array( 'fejwio' => array() ) ),
			'fejwio',
			range( 1, 5 ),
		);

		return $data;
	}

	/**
	 * Data provider for Image_Tag_Properties_Tests::test_get().
	 *
	 * @see Image_Tag_Properties_Base::test_get()
	 * @uses Image_Tag_Properties_Tests::data_get()
	 * @return array[]
	 */
	function data_get() {
		$data = parent::data_get();

		$data['override by name'] = array(
			$this->get_instance( array( 'fejwio' => __FUNCTION__ ) ),
			'fejwio',
			'Image_Tag_Properties::get_fejwio_property_for_view',
			'view',
		);

		$data['override by type'] = array(
			$this->get_instance( null, array( 'fejwkls' => array() ) ),
			'fejwkls',
			range( 1, 5 ),
			'view',
		);

		return $data;
	}

}

/**
 * Class to test abstract Image_Tag_Properties_Abstract.
 */
class Image_Tag_Properties extends Image_Tag_Properties_Abstract {

	function set_fejwio_property( $value ) {
		$this->_set( 'fejwio', $value );
	}

	function set_array_property( string $attribute, array $value ) {
		$this->_set( $attribute, $value );
	}

	function get_fejwio_property_for_view() {
		return __METHOD__;
	}

	function get_array_property_for_view() {
		return range( 1, 5 );
	}

}

?>
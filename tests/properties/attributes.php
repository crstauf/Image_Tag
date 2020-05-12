<?php

require_once 'abstract-properties-tests.php';

/**
 * @coversDefaultClass Image_Tag_Attributes
 * @group attributes
 */
class Image_Tag_Attributes_Test extends Image_Tag_Properties_Tests {

	const DEFAULTS = array(
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
	);

	protected function class_name() {
		return Image_Tag_Attributes::class;
	}

	/**
	 * Flatten multi-dimensional array.
	 *
	 * Produce expected value of Image_Tag_Attributes::explode_deep().
	 *
	 * @param array[] $array
	 * @return array
	 */
	protected static function flatten( array $array ) {
		$return = array();

		array_walk_recursive( $array, function( $item ) use( &$return ) {
			$return[] = $item;
		} );

		return $return;
	}

	/**
	 * Test Image_Tag_Attributes::NAME constant value.
	 *
	 * @group constant
	 */
	function test_name_constant() {
		$this->assertSame( 'attribute', constant( $this->class_name() . '::NAME' ) );
	}

	/*
	##     ##    ###     ######   ####  ######
	###   ###   ## ##   ##    ##   ##  ##    ##
	#### ####  ##   ##  ##         ##  ##
	## ### ## ##     ## ##   ####  ##  ##
	##     ## ######### ##    ##   ##  ##
	##     ## ##     ## ##    ##   ##  ##    ##
	##     ## ##     ##  ######   ####  ######
	*/

	/**
	 * Test Image_Tag_Attributes::__toString().
	 *
	 * @covers Image_Tag_Attributes::__toString()
	 * @group instance
	 * @group magic
	 * @group output
	 */
	function test__toString() {
		$attributes = array(
			'class' => array( 'foo', 'bar' ),
			'id' => __FUNCTION__,
			'sizes' => '( max-width: 800px ) 50vw, 100vw',
			'data-preloaded' => 1, // test attribute not in DEFAULTS
		);

		$defaults = array(
			'width' => 1600,
			'height' => 900,
		);

		$instance = $this->new_instance( $attributes, $defaults );

		$expected = 'id="' . esc_attr( __FUNCTION__ ) . '" ' .
			'class="foo bar" ' .
			'sizes="( max-width: 800px ) 50vw, 100vw" ' .
			'width="1600" ' .
			'height="900" ' .
			'data-preloaded="1"';

		$this->assertsame( $expected, $instance->__toString() );
	}

	/**
	 * Data provider for Image_Tag_Properties_Test::test__set().
	 *
	 * Set expected value to flattened original value.
	 *
	 * @uses Image_Tag_Properties_Test::data__set()
	 * @return array
	 */
	function data__set() {
		$data = parent::data__set();
		$data['multi-dimensional array'][3] = static::flatten( $data['multi-dimensional array'][2] );

		return $data;
	}

	/**
	 * Data provider for Image_TagProperties_Test::__get().
	 *
	 * Set expected value to flattened original value.
	 *
	 * @uses Image_Tag_Properties_Test::data__get()
	 * @return array
	 */
	function data__get() {
		$data = parent::data__get();
		$data['multi-dimensional array'][2] = static::flatten( $data['multi-dimensional array'][1] );

		return $data;
	}


	/*
	 ######  ######## ########
	##    ## ##          ##
	##       ##          ##
	 ######  ######      ##
	      ## ##          ##
	##    ## ##          ##
	 ######  ########    ##
	*/

	/**
	 * Data provider for Image_Tag_Attribute_Test::test_set().
	 *
	 * Add attribute specific tests.
	 *
	 * @see self::test_set()
	 * @uses Image_Tag_Properties_Test::data_set()
	 * @return array
	 */
	function data_set() {
		$data = parent::data_set();

		$data['srcset'] = array(
			$this->new_instance(),
			'sizes',
			array( '( max-width: 800px ) 100vw', '50vw' ),
		);

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
					5, // to test direct add to $flattened_array in explode_deep()
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
					5,
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
	 * @covers Image_Tag_Attributes::trim()
	 * @covers Image_Tag_Attributes::explode_deep()
	 * @covers Image_Tag_Attributes::set_class_attribute()
	 * @covers Image_Tag_Attributes::set_array_attribute()
	 *
	 * @dataProvider data_set
	 */
	function test_set( Image_Tag_Properties $instance, $set_properties, $value = null, $expected = null ) {
		parent::test_set( $instance, $set_properties, $value, $expected );
	}


	/*
	 ######   ######## ########
	##    ##  ##          ##
	##        ##          ##
	##   #### ######      ##
	##    ##  ##          ##
	##    ##  ##          ##
	 ######   ########    ##
	*/

	/**
	 * Data provider for Image_Tag_Attributes_Test::test_get().
	 *
	 * Add attribute specific tests.
	 *
	 * @see Image_Tag_Properties_Test::test_get()
	 * @uses Image_Tag_Properties_Test::data_get()
	 * @return array[]
	 */
	function data_get() {
		$data  = parent::data_get();

		$data = array(
			'class edit' => array(
				$this->new_instance( array( 'class' => ' foo   bar alpha beta ' ) ),
				'class',
				array( 'foo', 'bar', 'alpha', 'beta' ),
			),
			'class view' => array(
				$this->new_instance( array( 'class' => ' foo   bar alpha beta ' ) ),
				'class',
				'foo bar alpha beta',
				'view',
			),
			'sizes edit' => array(
				$this->new_instance( array( 'sizes' => array( '( min-width: 800px ) 50vw,', ' 100vw' ) ) ),
				'sizes',
				array( '( min-width: 800px ) 50vw', '100vw' ),
			),
			'sizes view' => array(
				$this->new_instance( array( 'sizes' => array( '( min-width: 800px ) 50vw,', ' 100vw' ) ) ),
				'sizes',
				'( min-width: 800px ) 50vw, 100vw',
				'view',
			),
			'style edit' => array(
				$this->new_instance( array( 'style' => 'width: 200px;' ) ),
				'style',
				array( 'width: 200px' ),
			),
			'style view' => array(
				$this->new_instance( array( 'style' => 'width: 200px;' ) ),
				'style',
				'width: 200px',
				'view',
			),
		);

		return $data;
	}

	/**
	 * @see Image_Tag_Properties_Test::test_get()
	 *
	 * @covers Image_Tag_Properties::get()
	 * @covers Image_Tag_Properties::get_properties()
	 * @covers Image_Tag_Properties::get_property()
	 * @covers Image_Tag_Attributes::get()
	 * @covers Image_Tag_Attributes::trim()
	 * @covers Image_Tag_Attributes::get_class_attribute_for_view()
	 * @covers Image_Tag_Attributes::get_style_attribute_for_view()
	 * @covers Image_Tag_Attributes::get_array_attribute_for_view()
	 *
	 * @dataProvider data_get
	 */
	function test_get( Image_Tag_Properties $instance, $get_properties, $expected = null, $context = 'edit' ) {
		parent::test_get( $instance, $get_properties, $expected, $context );
	}

}

?>
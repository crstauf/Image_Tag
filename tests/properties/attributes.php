<?php

require_once 'abstract-tests.php';

/**
 * @coversDefaultClass Image_Tag_Attributes
 * @group properties
 * @group attributes
 */
class Image_Tag_Attributes_Test extends Image_Tag_Properties_Tests {

	const DEFAULTS = array(
		'id' => null,
		'alt' => '',
		'src' => null,
		'title' => null,
		'width' => null,
		'height' => null,
		'data-src' => null,
		'data-srcset' => array(),
		'data-sizes' => array(),
		'srcset' => array(),
		'style' => array(),
		'sizes' => array( '100vw' ),
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

	/**
	 * Test Image_Tag_Attributes::ORDER constant value.
	 *
	 * @group constant
	 */
	function test_order() {
		$this->assertSame( array(
			'id',
			'class',
			'src',
			'data-src',
			'srcset',
			'data-srcset',
			'sizes',
			'data-sizes',
			'width',
			'height',
			'title',
			'alt',
			'style',
		), Image_Tag_Attributes::ORDER );
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
	 * @covers ::__toString()
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
			'alt="" ' .
			'data-preloaded="1"';

		$this->assertsame( $expected, $instance->__toString() );
	}

	/**
	 * Data provider for Image_Tag_Properties_Tests::test__set().
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
	 * Data provider for Image_TagProperties_Tests::__get().
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
	 * Data provider for Image_Tag_Attribute_Tests::test_set().
	 *
	 * Add attribute specific tests.
	 *
	 * @see self::test_set()
	 * @uses Image_Tag_Properties_Tests::data_set()
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
	 * @see Image_Tag_Properties_Tests::test_set()
	 *
	 * @covers ::trim()
	 * @covers ::explode_deep()
	 * @covers ::set_class_attribute()
	 * @covers ::set_array_attribute()
	 *
	 * @dataProvider data_set
	 */
	function test_set( Image_Tag_Properties_Abstract $instance, $set_properties, $value = null, $expected = null ) {
		parent::test_set( $instance, $set_properties, $value, $expected );
	}


	/*
	   ###    ########  ########     ########  #######
	  ## ##   ##     ## ##     ##       ##    ##     ##
	 ##   ##  ##     ## ##     ##       ##    ##     ##
	##     ## ##     ## ##     ##       ##    ##     ##
	######### ##     ## ##     ##       ##    ##     ##
	##     ## ##     ## ##     ##       ##    ##     ##
	##     ## ########  ########        ##     #######
	*/

	/**
	 * Data provider for Image_Tag_Properties_Test::test_add_to().
	 *
	 * Adjust test for use with attributes.
	 *
	 * @see Image_Tag_Properties_Test::test_add_to()
	 * @uses Image_Tag_Properties_Tests::data_add_to()
	 * @return array[]
	 */
	function data_add_to() {
		$data = parent::data_add_to();

		$data['multiple arrays'][3]['bar'] = array_values( $data['multiple arrays'][3]['bar'] );
		$data['multiple arrays'][3]['zoo'] = static::flatten( $data['multiple arrays'][3]['zoo'] );

		return $data;
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
			'class array' => array(
				$this->new_instance( array( 'class' => array(
					'  foo ;',
					array(
						'bar; ',
						'; zoo',
					),
				) ) ),
				'class',
				'foo bar zoo',
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
	 * @covers ::get()
	 * @covers ::trim()
	 * @covers ::get_class_attribute_for_view()
	 * @covers ::get_style_attribute_for_view()
	 * @covers ::get_array_attribute_for_view()
	 *
	 * @dataProvider data_get
	 */
	function test_get( Image_Tag_Properties_Abstract $instance, $get_properties, $expected = null, $context = 'edit' ) {
		parent::test_get( $instance, $get_properties, $expected, $context );
	}

}

?>
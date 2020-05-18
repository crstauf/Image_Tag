<?php

/**
 * @coversDefaultClass Image_Tag_Abstract
 */
abstract class Image_Tag_Test_Base extends WP_UnitTestCase {

	/**
	 * Get property type.
	 *
	 * @param Image_Tag_Properties_Abstract $property
	 * @return string
	 */
	static protected function property_type( Image_Tag_Properties_Abstract $property ) {
		switch ( get_class( $property ) ) {

			case 'Image_Tag_Attributes':
				return 'attributes';

			case 'Image_Tag_Settings':
				return 'settings';

		}

		trigger_error( sprintf( 'Property type <code>%s</code> is unknown.', get_class( $property ) ), E_USER_WARNING );
	}

	/**
	 * Get the class name to run tests against.
	 *
	 * @return string
	 */
	abstract protected function class_name();

	/**
	 * Create a new instance of the tested class.
	 *
	 * @param array $params
	 * @uses self::class_name()
	 * @return Image_Tag_Properties
	 */
	protected function new_instance( ...$params ) {
		$class_name = $this->class_name();
		return new $class_name( ...$params );
	}

	/**
	 * Get or create an instance.
	 *
	 * @param array $params
	 * @uses self::new_instance()
	 * @return Image_Tag_Properties
	 */
	protected function get_instance( ...$params ) {
		static $instance = null;

		if ( !empty( $params ) )
			$instance = null;

		if ( is_null( $instance ) )
			$instance = $this->new_instance( ...$params );

		return $instance;
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
	 * @covers ::__construct()
	 *
	 * @group instance
	 * @group magic
	 */
	function test__construct() {
		$instance = $this->get_instance();

		$this->assertInstanceOf( Image_Tag_Attributes::class, $instance->attributes );
		$this->assertInstanceOf( Image_Tag_Settings::class,   $instance->settings   );
	}

	/**
	 * @covers ::__set()
	 *
	 * @group instance
	 * @group magic
	 */
	function test__set() {
		$instance = $this->new_instance();

		$this->assertNull( $instance->id );

		$instance->id = __FUNCTION__;
		$this->assertSame( __FUNCTION__, $instance->id );

		$instance->attributes = array();
		$this->assertInstanceOf( Image_Tag_Attributes::class, $instance->attributes );
		$this->assertSame( __FUNCTION__, $instance->id );

		$instance->attributes = new Image_Tag_Attributes( array( 'id' => 'foobar' ) );
		$this->assertSame( 'foobar', $instance->id );
	}

	/**
	 * @covers ::__get()
	 *
	 * @group instance
	 * @group magic
	 */
	function test__get() {
		$instance = $this->get_instance( array(
			'id' => __FUNCTION__,
			'class' => array( 'foo', 'bar' ),
		) );

		$this->assertInstanceOf( Image_Tag_Attributes::class, $instance->attributes );
		$this->assertInstanceOf( Image_Tag_Settings::class,   $instance->settings );

		$this->assertSame( __FUNCTION__, $instance->id );
		$this->assertSame( array( 'foo', 'bar' ), $instance->class );
	}

	/**
	 * @covers ::__isset()
	 *
	 * @group instance
	 * @group magic
	 */
	function test__isset() {
		$instance = $this->get_instance( array() );

		$this->assertTrue( isset( $instance->attributes ) );
		$this->assertTrue( isset( $instance->settings ) );
		$this->assertFalse( isset( $instance->id ) );

		$instance->id = __FUNCTION__;
		$this->assertTrue( isset( $instance->id ) );
	}

	/**
	 * @covers ::__unset()
	 *
	 * @group instance
	 * @group magic
	 */
	function test__unset() {
		$instance = $this->get_instance( array(
			'id' => __FUNCTION__,
			'class' => 'foo bar',
		) );

		$this->assertTrue( isset( $instance->id ) );
		$this->assertTrue( isset( $instance->class ) );

		/**
		 * Test unset attribute property.
		 *
		 * Note special case of unsetting attributes.
		 * @see Image_Tag_Properties_Abstract::__unset()
		 */
		$this->assertSame( __FUNCTION__, $instance->id );
		unset( $instance->id );
		$this->assertFalse( isset( $instance->id ) );

		# Test Image_Tag_Abstract property set to null.
		$this->assertInstanceOf( Image_Tag_Attributes::class, $instance->attributes );
		unset( $instance->attributes );
		$this->assertNull( $instance->attributes );
	}

	/**
	 * Data provider for Image_Tag_Test_Base::test__toString().
	 *
	 * @see static::test__toString()
	 * @return array[]
	 */
	function data__toString() {
		return array(

			'id' => array(
				$this->new_instance( array( 'id' => __FUNCTION__ ) ),
				'warning',
			),

			'class' => array(
				$this->new_instance( array( 'class' => 'foo bar' ) ),
				'warning',
			),

			'src' => array(
				$this->new_instance( array(
					'id' => __FUNCTION__,
					'class' => 'bar foo',
					'src' => 'https://source.unsplash.com/1000x1000',
				) ),
				'<img ' .
					'id="' . esc_attr( __FUNCTION__ ) . '" ' .
					'src="' . esc_attr( 'https://source.unsplash.com/1000x1000' ) . '" ' .
					'sizes="100vw" ' .
					'class="bar foo" ' .
					'alt="" ' .
				'/>',
			),

		);
	}

	/**
	 * @param Image_Tag_Abstract $instance
	 * @param string $expected
	 *
	 * @covers ::__toString()
	 * @group instance
	 * @group magic
	 * @group output
	 *
	 * @dataProvider data__toString
	 */
	function test__toString( Image_Tag_Abstract $instance, string $expected ) {
		if ( 'warning' === $expected ) {
			$this->expectOutputRegex( '/^\sWarning: .+?\/image_tags\/types\/abstract.php.*$/s' );
			$this->assertNull( $instance->__toString() );
			return;
		}

		$this->assertSame( $expected, $instance->__toString() );
	}


	/*
	##     ##    ###    ##       #### ########     ###    ######## ####  #######  ##    ##
	##     ##   ## ##   ##        ##  ##     ##   ## ##      ##     ##  ##     ## ###   ##
	##     ##  ##   ##  ##        ##  ##     ##  ##   ##     ##     ##  ##     ## ####  ##
	##     ## ##     ## ##        ##  ##     ## ##     ##    ##     ##  ##     ## ## ## ##
	 ##   ##  ######### ##        ##  ##     ## #########    ##     ##  ##     ## ##  ####
	  ## ##   ##     ## ##        ##  ##     ## ##     ##    ##     ##  ##     ## ##   ###
	   ###    ##     ## ######## #### ########  ##     ##    ##    ####  #######  ##    ##
	*/

	/**
	 * @covers ::get_type()
	 *
	 * @group instance
	 * @group validation
	 */
	abstract function test_get_type();

	/**
	 * @covers ::add_attributes()
	 *
	 * @group instance
	 * @group validation
	 */
	abstract function test_is_type();

	/**
	 * @covers ::check_valid()
	 * @covers ::is_valid()
	 *
	 * @group instance
	 * @group validation
	 */
	abstract function test_is_valid();


	/*
	######## ########    ###    ######## ##     ## ########  ########  ######
	##       ##         ## ##      ##    ##     ## ##     ## ##       ##    ##
	##       ##        ##   ##     ##    ##     ## ##     ## ##       ##
	######   ######   ##     ##    ##    ##     ## ########  ######    ######
	##       ##       #########    ##    ##     ## ##   ##   ##             ##
	##       ##       ##     ##    ##    ##     ## ##    ##  ##       ##    ##
	##       ######## ##     ##    ##     #######  ##     ## ########  ######
	*/

	/**
	 * @covers ::http()
	 * @group instance
	 * @group feature
	 * @group external-http
	 */
	abstract function test_http();

	/**
	 * Data provider for Image_Tag_Test_Base::test_lazyload().
	 *
	 * @see static::test_lazyload()
	 * @return array[]
	 */
	function data_lazyload() {
		$data = array();

		$data['src'] = array();
		$data['src'][0] = $this->new_instance( array( 'src' => 'https://source.unsplash.com/1000x1000' ) );
		$data['src'][1] = $this->new_instance( array(
			'src' => Image_Tag::BLANK,
			'class' => array( 'lazyload', 'hide-if-no-js' ),
			'sizes' => array(),
			'data-src' => 'https://source.unsplash.com/1000x1000',
			'data-sizes' => array( 'auto' ),
		), array(
			'lazyload' => array(
				'noscript' => true,
				'noscript_priority' => -10,
				'sizes_auto' => true,
			),
		) );

		$data['srcset'] = array();
		$data['srcset'][0] = $this->new_instance( array(
			'src' => 'https://source.unsplash.com/300x300',
			'srcset' => array(
				'https://source.unsplash.com/300x300 300w',
				'https://source.unsplash.com/600x600 600w',
				'https://source.unsplash.com/1000x1000 1000w',
			),
		) );
		$data['srcset'][1] = $this->new_instance( array(
			'src' => Image_Tag::BLANK,
			'class' => array( 'lazyload', 'hide-if-no-js' ),
			'sizes' => array(),
			'data-src' => 'https://source.unsplash.com/300x300',
			'data-sizes' => array( 'auto' ),
			'data-srcset' => array(
				'https://source.unsplash.com/300x300 300w',
				'https://source.unsplash.com/600x600 600w',
				'https://source.unsplash.com/1000x1000 1000w',
			),
		), array(
			'lazyload' => array(
				'noscript' => true,
				'noscript_priority' => -10,
				'sizes_auto' => true,
			),
		) );

		return $data;
	}

	/**
	 * @param Image_Tag_Abstract $instance
	 * @param Image_Tag_Abstract $expected
	 *
	 * @covers ::lazyload()
	 * @group instance
	 * @group feature
	 *
	 * @dataProvider data_lazyload
	 *
	 * @todo add test for noscript
	 */
	function test_lazyload( Image_Tag_Abstract $instance, Image_Tag_Abstract $expected ) {
		$this->assertNotEquals( $expected->src, $instance->src );
		$this->assertEmpty( $instance->attributes->get( 'data-src' ) );
		$this->assertEmpty( $instance->attributes->get( 'data-sizes' ) );
		$this->assertEmpty( $instance->attributes->get( 'data-srcset' ) );
		$this->assertNotContains( 'lazyload', $instance->class );
		$this->assertNotContains( 'hide-if-no-js', $instance->class );
		$this->assertEmpty( $instance->settings->get( 'lazyload' ) );

		if ( !is_null( $instance->settings->get( 'after_output', 'view' ) ) )
			$this->assertStringNotContainsString( '<noscript>', $instance->settings->get( 'after_output', 'view' ) );
		else
			$this->assertNull( $instance->settings->get( 'after_output', 'view' ) );

		$lazyload = $instance->lazyload();

		$this->assertSame( Image_Tag::BLANK, $lazyload->src );
		$this->assertSame( $expected->class, $lazyload->class );
		$this->assertSame( $expected->sizes, $lazyload->sizes );
		$this->assertSame( $instance->src, $lazyload->attributes->get( 'data-src' ) );
		$this->assertSame( $expected->attributes->get( 'data-src' ), $lazyload->attributes->get( 'data-src' ) );
		$this->assertSame( $expected->attributes->get( 'data-sizes' ), $lazyload->attributes->get( 'data-sizes' ) );
		$this->assertSame( $expected->attributes->get( 'data-srcset' ), $lazyload->attributes->get( 'data-srcset' ) );
		$this->assertSame( $expected->attributes->get( 'lazyload' ), $lazyload->attributes->get( 'lazyload' ) );

		$this->assertSame( $expected->__toString(), $lazyload->__toString() );
	}

	/**
	 * @covers ::noscript()
	 * @group instance
	 * @group feature
	 */
	function test_noscript() {
		$instance = $this->new_instance( array( 'src' => 'https://source.unsplash.com/1000x1000' ) );
		$noscript = $instance->noscript();

		$expected = $this->new_instance( array(
			'src' => 'https://source.unsplash.com/1000x1000',
			'class' => 'no-js',
		), array(
			'before_output' => array(
				20 => array( '<noscript>' ),
			),
			'after_output' => array(
				0 => array( '</noscript>' ),
			),
			'noscript' => array(
				'before_position' => 20,
				 'after_position' => 0,
			),
		) );

		$this->assertEquals( $expected, $noscript );
		$this->assertSame( $expected->__toString(), $noscript->__toString() );

		$noscript->settings->add_output( 'before', 'before open' );
		$noscript->settings->add_output(  'after', 'after close' );

		$expected = $this->new_instance( array(
			'src' => 'https://source.unsplash.com/1000x1000',
			'class' => 'no-js',
		), array(
			'before_output' => array(
				20 => array( '<noscript>' ),
				10 => array( 'before open' ),
			),
			'after_output' => array(
				 0 => array( '</noscript>' ),
				10 => array( 'after close' ),
			),
			'noscript' => array(
				'before_position' => 20,
				 'after_position' => 0,
			),
		) );

		$this->assertEquals( $expected, $noscript );
		$this->assertSame( $expected->__toString(), $noscript->__toString() );

		$noscript->settings->add_output( 'before', 'after open',  25 );
		$noscript->settings->add_output(  'after', 'before close', 5 );

		$expected = $this->new_instance( array(
			'src' => 'https://source.unsplash.com/1000x1000',
			'class' => 'no-js',
		), array(
			'before_output' => array(
				20 => array( '<noscript>' ),
				10 => array( 'before open' ),
				25 => array( 'after open' ),
			),
			'after_output' => array(
				 0 => array( '</noscript>' ),
				10 => array( 'after close' ),
				 5 => array( 'before close' ),
			),
			'noscript' => array(
				'before_position' => 20,
				 'after_position' =>  0,
			),
		) );

		$this->assertEquals( $expected, $noscript );
		$this->assertSame( $expected->__toString(), $noscript->__toString() );

		$noscript = $instance->noscript( array(), array(
			'noscript' => array(
				'before_position' => 10,
				 'after_position' => 10,
			),
		) );

		$expected = $this->new_instance( array(
			'src' => 'https://source.unsplash.com/1000x1000',
			'class' => 'no-js',
		), array(
			'before_output' => array(
				10 => array( '<noscript>' ),
			),
			'after_output' => array(
				10 => array( '</noscript>' ),
			),
			'noscript' => array(
				'before_position' => 10,
				 'after_position' => 10,
			),
		) );

		$this->assertEquals( $expected, $noscript );
		$this->assertSame( $expected->__toString(), $noscript->__toString() );

		$noscript->settings->add_output( 'before', 'after open' );
		$noscript->settings->add_output(  'after', 'after close' );

		$expected = $this->new_instance( array(
			'src' => 'https://source.unsplash.com/1000x1000',
			'class' => 'no-js',
		), array(
			'before_output' => array(
				10 => array(
					'<noscript>',
					'after open',
				),
			),
			'after_output' => array(
				10 => array(
					'</noscript>',
					'after close',
				),
			),
			'noscript' => array(
				'before_position' => 10,
				 'after_position' => 10,
			),
		) );

		$this->assertEquals( $expected, $noscript );
		$this->assertSame( $expected->__toString(), $noscript->__toString() );
	}

}

?>
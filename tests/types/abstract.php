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

	/**
	 * Data provider for Image_Tag_Test_Base::test_constant_types().
	 *
	 * @see static::test_constant_types()
	 * @return array[]
	 */
	abstract function data_constant_types();

	/**
	 * @group constant
	 *
	 * @dataProvider data_constant_types
	 */
	function test_constant_types( array $expected ) {
		$constant = constant( $this->class_name() . '::TYPES' );

		$this->assertIsArray( $constant );
		$this->assertNotEmpty( $constant );

		$this->assertSame( $expected, $constant );
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
	 * Data provider for Image_Tag_Test_Base::test_create().
	 *
	 * @see Image_Tag_Test_Base::test_create()
	 * @return array[]
	 */
	abstract function data_create();

	/**
	 * @param string|Image_Tag_Abstract
	 * @param array $params
	 *
	 * @covers Image_Tag::create()
	 * @group static
	 *
	 * @dataProvider data_create
	 */
	function test_create( $expected, ...$params ) {
		$params = array_replace( array(
			null,
			null,
			array(),
		), $params );

		if ( 'warning' === $expected ) {
			$instance = @Image_Tag::create( $params[1], $params[2] );
			$this->assertEquals( new Image_Tag( $params[1], $params[2] ), $instance );
			$this->expectException( PHPUnit\Framework\Error\Error::class );
		}

		$instance = Image_Tag::create( ...$params );
		$this->assertEquals( $expected, $instance );
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
		$instance = $this->get_instance( array( 'id' => __FUNCTION__ ), array( 'foo' => __METHOD__ ) );

		$this->assertInstanceOf( Image_Tag_Attributes::class, $instance->attributes );
		$this->assertInstanceOf( Image_Tag_Settings::class,   $instance->settings   );

		$this->assertSame( __FUNCTION__, $instance->attributes->id );
		$this->assertSame( __METHOD__, $instance->settings->foo );
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
	abstract function data__toString();

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

	/**
	 * @covers ::__clone()
	 * @group instance
	 * @group magic
	 */
	function test__clone() {
		$instance = $this->new_instance( array( 'id' => __FUNCTION__ ), array( 'foo' => __FUNCTION__ ) );
		$clone = clone $instance;

		$this->assertEquals( $instance, $clone );
		$this->assertNotSame( $instance, $clone );

		$this->assertEquals( $instance->attributes, $clone->attributes );
		$this->assertNotSame( $instance->attributes, $clone->attributes );

		$this->assertEquals( $instance->settings, $clone->settings );
		$this->assertNotSame( $instance->settings, $clone->settings );

		$clone->id = 'foo';
		$this->assertSame( __FUNCTION__, $instance->id );
		$this->assertSame( 'foo', $clone->id );
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
	 * Data provider for Image_Tag_Test_Base::test_http().
	 *
	 * @see static::test_http()
	 * @return array[]
	 */
	abstract function data_http();

	/**
	 * @param Image_Tag_Abstract $instance
	 *
 	 * @covers ::http()
 	 * @group instance
	 * @group feature
 	 * @group external-http
 	 *
 	 * @dataProvider data_http
 	 */
 	function test_http( Image_Tag_Abstract $instance ) {
 		$count = ( int ) did_action( 'http_api_debug' );
 		$response = $instance->http();

 		if ( is_wp_error( $response ) )
 			$this->fail( $response->get_error_message() );

 		$this->assertEquals( ++$count, did_action( 'http_api_debug' ) );

 		# Call again to confirm pulled from cache.
 		$instance->http();
 		$this->assertEquals( $count, did_action( 'http_api_debug' ) );

 		# And call again to test flag skips cache.
 		$instance->http( true );
 		$this->assertEquals( ++$count, did_action( 'http_api_debug' ) );

 		# And call again (one more time) to confirm pulled from cache.
 		$instance->http();
 		$this->assertEquals( $count, did_action( 'http_api_debug' ) );
 	}

	/**
	 * Data provider for Image_Tag_Test_Base::test_lazyload().
	 *
	 * @see static::test_lazyload()
	 * @return array[]
	 */
	abstract function data_lazyload();

	/**
	 * @param Image_Tag_Abstract $instance
	 * @param Image_Tag_Abstract $expected
	 *
	 * @covers ::lazyload()
	 * @group instance
	 * @group feature
	 *
	 * @dataProvider data_lazyload
	 */
	function test_lazyload( Image_Tag_Abstract $instance, Image_Tag_Abstract $expected ) {
		$this->assertNotEquals( $expected->src, $instance->src );
		$this->assertEmpty( $instance->attributes->get( 'data-src' ) );
		$this->assertEmpty( $instance->attributes->get( 'data-sizes' ) );
		$this->assertEmpty( $instance->attributes->get( 'data-srcset' ) );
		$this->assertNotContains( 'lazyload', $instance->class );
		$this->assertNotContains( 'hide-if-no-js', $instance->class );

		if ( !is_null( $instance->settings->get_output( 'after' ) ) )
			$this->assertStringNotContainsString( '<noscript>', $instance->settings->get_output( 'after' ) );
		else
			$this->assertNull( $instance->settings->get_output( 'after' ) );

		$lazyload = $instance->lazyload();

		$this->assertSame( Image_Tag::BLANK, $lazyload->src );
		$this->assertSame( $expected->class, $lazyload->class );
		$this->assertSame( $expected->sizes, $lazyload->sizes );
		$this->assertSame( $instance->attributes->get( 'src', 'view' ), $lazyload->attributes->get( 'data-src', 'view' ) );
		$this->assertSame( $expected->attributes->get( 'data-src', 'view' ), $lazyload->attributes->get( 'data-src', 'view' ) );
		$this->assertSame( $expected->attributes->get( 'data-sizes', 'view' ), $lazyload->attributes->get( 'data-sizes', 'view' ) );
		$this->assertSame( $expected->attributes->get( 'data-srcset', 'view' ), $lazyload->attributes->get( 'data-srcset', 'view' ) );
		$this->assertSame( $expected->settings->get( 'lazyload' ), $lazyload->settings->get( 'lazyload' ) );
		$this->assertSame( $expected->settings->get( 'after_output' ), $lazyload->settings->get( 'after_output' ) );

		if (
			   !empty( $lazyload->settings->get( 'lazyload' ) )
			&& !empty( $lazyload->settings->get( 'lazyload' )['noscript'] )
		)
			$this->assertStringContainsString( '<noscript>', $lazyload->__toString() );

		$this->assertSame( $expected->__toString(), $lazyload->__toString() );
	}

	/**
	 * Data provider for Image_Tag_Test_Base::test_noscript().
	 *
	 * @see static::test_noscript()
	 * @return array[]
	 */
	function data_noscript() {
		$instance = $this->get_instance( array( 'src' => 'https://source.unsplash.com/1000x1000' ) );

		$data = array();

		# Test defaults.
		$data['defaults'] = array();
		$data['defaults'][0] = $instance;
		$data['defaults'][1] = array( array(), array() );
		$data['defaults'][2] = array();
		$data['defaults'][3] = $this->new_instance( array(
			'src' => 'https://source.unsplash.com/1000x1000',
			'class' => array( 1 => 'no-js' ),
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

		# Test adding output outside of noscript tags.
		$data['outside'] = array();
		$data['outside'][0] = $instance;
		$data['outside'][1] = array( array(), array() );
		$data['outside'][2] = array(
			array( 'before', 'before open' ),
			array( 'after',  'after close' ),
		);
		$data['outside'][3] =  $this->new_instance( array(
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

		# Test adding output inside of noscript tags.
		$data['inside'] = array();
		$data['inside'][0] = $instance;
		$data['inside'][1] = array( array(), array() );
		$data['inside'][2] = array(
			array( 'before', 'after open',  25 ),
			array(  'after', 'before close', 5 ),
		);
		$data['inside'][3] = $this->new_instance( array(
			'src' => 'https://source.unsplash.com/1000x1000',
			'class' => 'no-js',
		), array(
			'before_output' => array(
				20 => array( '<noscript>' ),
				25 => array( 'after open' ),
			),
			'after_output' => array(
				0 => array( '</noscript>' ),
				5 => array( 'before close' ),
			),
			'noscript' => array(
				'before_position' => 20,
				 'after_position' =>  0,
			),
		) );

		# Test adjusting position of noscript tags.
		$data['positions'] = array();
		$data['positions'][0] = $instance;
		$data['positions'][1] = array( array(), array(
			'noscript' => array(
				'before_position' => 10,
				 'after_position' => 10,
			),
		) );
		$data['positions'][2] = array();
		$data['positions'][3] = $this->new_instance( array(
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

		# Test adding output after noscript tags in adjust positions.
		$data['positions after'] = array();
		$data['positions after'][0] = $instance;
		$data['positions after'][1] = array( array(), array(
			'noscript' => array(
				'before_position' => 10,
				 'after_position' => 10,
			),
		) );
		$data['positions after'][2] = array(
			array( 'before', 'after open'  ),
			array(  'after', 'after close' ),
		);
		$data['positions after'][3] = $this->new_instance( array(
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

		return $data;
	}

	/**
	 * @param Image_Tag_Abstract $instance
	 * @param array $noscript_params
	 * @param array $add_output
	 * @param Image_Tag_Abstract $expected
	 *
	 * @covers ::noscript()
	 * @group instance
	 * @group feature
	 *
	 * @dataProvider data_noscript
	 */
	function test_noscript( Image_Tag_Abstract $instance, array $noscript_params, array $add_output, Image_Tag_Abstract $expected ) {
		$noscript = $instance->noscript( ...$noscript_params );

		if ( !empty( $add_output ) )
			foreach ( $add_output as $args )
				$noscript->settings->add_output( ...$args );

		$this->assertEquals( $expected, $noscript );
		$this->assertSame( $expected->__toString(), $noscript->__toString() );
	}

	/**
	 * Data provider for Image_Tag_Test_Base::test_into().
	 *
	 * @see static::test_into()
	 * @return array[]
	 */
	abstract function data_into();

	/**
	 * This should test the conversion from another image type into this image type.
	 * If testing Image_Tag_JoeSchmoe, should cover conversion into Image_Tag_JoeSchmoe, not from.
	 *
	 * @covers ::into()
	 * @group instance
	 * @group into
	 *
	 * @dataProvider data_into()
	 */
	function test_into( Image_Tag_Abstract $instance, string $into_type, $into_params, Image_Tag_Abstract $expected ) {

		# Test self conversion attempt.
		if ( $instance === $expected ) {
			$into = @$instance->into( $into_type, ...$into_params );
			$this->assertSame( $expected, $into );

			$this->expectException( PHPUnit\Framework\Error\Error::class );
			$into = $instance->into( $into_type, ...$into_params );

			return;
		}

		$into = $instance->into( $into_type, ...$into_params );

		$this->assertEquals( $expected, $into );

		if (
			$expected->is_valid()
			&& $into->is_valid()
		)
			$this->assertEquals(
				$expected->__toString(),
				$into->__toString()
			);
	}

}

?>
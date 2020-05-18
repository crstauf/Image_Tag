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
	 * Data provider for Image_Tag_Abstract::test__toString().
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
	##     ## ######## ######## ########
	##     ##    ##       ##    ##     ##
	##     ##    ##       ##    ##     ##
	#########    ##       ##    ########
	##     ##    ##       ##    ##
	##     ##    ##       ##    ##
	##     ##    ##       ##    ##
	*/

	/**
	 * @covers ::http()
	 * @group instance
	 * @group external-http
	 */
	abstract function test_http();

}

?>
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
		$instance = $this->get_instance( array( 'id' => __FUNCTION__ ) );

		$this->assertInstanceOf( Image_Tag_Attributes::class, $instance->attributes );
		$this->assertInstanceOf( Image_Tag_Settings::class,   $instance->settings );

		$this->assertSame( __FUNCTION__, $instance->id );
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
	 */
	abstract function test_get_type();

	/**
	 * @covers ::add_attributes()
	 */
	abstract function test_is_type();

	/**
	 * @covers ::check_valid()
	 */
	abstract function test_check_valid();

	/**
	 * @covers ::is_valid()
	 *
	 * @todo define
	 */
	function test_is_valid() {
	}

}

?>
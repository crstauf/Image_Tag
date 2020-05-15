<?php

/**
 * @coversDefaultClass Image_Tag_Abstract
 */
abstract class Image_Tag_Test_Base extends WP_UnitTestCase {

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
	 * @covers ::add_attributes()
	 * @covers ::add_attribute()
	 * @covers ::set_attributes()
	 * @covers ::set_attribute()
	 * @covers ::add_to_attributes()
	 * @covers ::add_to_attribute()
	 *
	 * @group instance
	 * @group chaining
	 */
	function test_chaining() {
		$instance = $this->new_instance();

		$this->assertSame( $instance, $instance->add_attributes( array( 'id' => __FUNCTION__ ) ) );
		$this->assertSame( $instance, $instance->add_attribute( 'title', __FUNCTION__ ) );
		$this->assertSame( $instance, $instance->set_attributes( array( 'id' => __FUNCTION__ ) ) );
		$this->assertSame( $instance, $instance->set_attribute( 'id', __FUNCTION__ ) );
		$this->assertSame( $instance, $instance->add_to_attributes( array( 'id' => __FUNCTION__ ) ) );
		$this->assertSame( $instance, $instance->add_to_attribute( 'id', __FUNCTION__ ) );
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
	   ###    ######## ######## ########  #### ########  ##     ## ######## ########  ######
	  ## ##      ##       ##    ##     ##  ##  ##     ## ##     ##    ##    ##       ##    ##
	 ##   ##     ##       ##    ##     ##  ##  ##     ## ##     ##    ##    ##       ##
	##     ##    ##       ##    ########   ##  ########  ##     ##    ##    ######    ######
	#########    ##       ##    ##   ##    ##  ##     ## ##     ##    ##    ##             ##
	##     ##    ##       ##    ##    ##   ##  ##     ## ##     ##    ##    ##       ##    ##
	##     ##    ##       ##    ##     ## #### ########   #######     ##    ########  ######
	*/

	/**
	 * @covers ::add_attributes()
	 *
	 * @group properties
	 * @group attributes
	 */
	function test_add_attributes() {
		$image = $this->new_instance();
		$attributes = new Image_Tag_Attributes( array() );

		$add_attributes = array(
			'id' => __FUNCTION__,
			'class' => 'foo bar',
			'width' => 1600,
			'height' => 900,
		);

		$image->add_attributes( $add_attributes );
		$attributes->add( $add_attributes );

		foreach ( $add_attributes as $attribute => $value )
			$this->assertSame( $attributes->$attribute, $image->attributes->$attribute );
	}

	/**
	 * @covers ::add_attribute()
	 *
	 * @group properties
	 * @group attributes
	 */
	function test_add_attribute() {
		$image = $this->new_instance();
		$attributes = new Image_Tag_Attributes( array() );

		$image->add_attribute( 'id', __FUNCTION__ );
		$attributes->add( 'id', __FUNCTION__ );

		$this->assertSame( $attributes->id, $image->attributes->id );
	}

	/**
	 * @covers ::set_attributes()
	 *
	 * @group properties
	 * @group attributes
	 */
	function test_set_attributes() {
		$image = $this->new_instance();
		$attributes = new Image_Tag_Attributes( array() );

		$set_attributes = array(
			'id' => __FUNCTION__,
			'class' => array( 'bar', 'foo' ),
		);

		foreach ( $set_attributes as $attribute => $value ) {
			$this->assertNotEquals( $value, $image->attributes->$attribute );
			$this->assertSame( $attributes->$attribute, $image->attributes->$attribute );
		}

		$image->set_attributes( $set_attributes );
		$attributes->set( $set_attributes );

		foreach ( $set_attributes as $attribute => $value ) {
			$this->assertSame( $value, $image->attributes->$attribute );
			$this->assertSame( $attributes->$attribute, $image->attributes->$attribute );
		}
	}

	/**
	 * @covers ::set_attribute()
	 *
	 * @group properties
	 * @group attributes
	 */
	function test_set_attribute() {
		$image = $this->new_instance( array( 'id' => uniqid( __FUNCTION__ ) ) );
		$attributes = new Image_Tag_Attributes( array( 'id' => uniqid( __FUNCTION__ ) ) );

		$this->assertNotEquals( __FUNCTION__, $image->attributes->id );
		$this->assertNotEquals( __FUNCTION__, $attributes->id );

		$image->set_attribute( 'id', __FUNCTION__ );
		$attributes->set( 'id', __FUNCTION__ );

		$this->assertSame( $attributes->id, $image->attributes->id );
	}

	/**
	 * @covers ::attributes_are_set()
	 *
	 * @group properties
	 * @group attributes
	 */
	function test_attributes_are_set() {
		$image = $this->new_instance( array(
			'id' => __FUNCTION__,
			'width' => 1600,
			'height' => 900,
		) );

		$attributes = new Image_Tag_Attributes( array(
			'id' => __FUNCTION__,
			'width' => 1600,
			'height' => 900,
		) );

		$this->assertTrue( $image->attributes_are_set( array( 'id', 'width', 'height' ) ) );
		$this->assertSame( $attributes->isset( array( 'id', 'width', 'height' ) ), $image->attributes_are_set( array( 'id', 'width', 'height' ) ) );

		$this->assertFalse( $image->attributes_are_set( array( 'id', 'title' ) ) );
		$this->assertSame(  $attributes->isset( array( 'id', 'title' ) ), $image->attributes_are_set( array( 'id', 'title' ) ) );
	}

	/**
	 * @covers ::attribute_isset()
	 *
	 * @group properties
	 * @group attributes
	 */
	function test_attribute_isset() {
		$image = $this->new_instance( array( 'id' => __FUNCTION__ ) );
		$attributes = new Image_Tag_Attributes( array( 'id' => __FUNCTION__ ) );

		$this->assertTrue( $image->attribute_isset( 'id' ) );
		$this->assertSame( $attributes->isset( 'id' ), $image->attribute_isset( 'id' ) );
	}

	/**
	 * @covers ::attributes_exist()
	 *
	 * @group properties
	 * @group attributes
	 */
	function test_attributes_exist() {
		$image = $this->new_instance();
		$attributes = new Image_Tag_Attributes( array() );

		$this->assertTrue( $image->attributes_exist( array( 'id', 'class', 'width' ) ) );
		$this->assertSame( $attributes->exists( array( 'id', 'class', 'width' ) ), $image->attributes_exist( array( 'id', 'class', 'width' ) ) );

		$this->assertFalse( $image->attributes_exist( array( 'id', 'foo' ) ) );
		$this->assertSame( $attributes->exists( array( 'id', 'foo' ) ), $image->attributes_exist( array( 'id', 'foo' ) ) );
	}

	/**
	 * @covers ::attribute_exists()
	 *
	 * @group properties
	 * @group attributes
	 */
	function test_attribute_exists() {
		$image = $this->new_instance();
		$attributes = new Image_Tag_Attributes( array() );

		$this->assertTrue( $image->attribute_exists( 'id' ) );
		$this->assertSame( $attributes->exists( 'id' ), $image->attribute_exists( 'id' ) );

		$this->assertFalse( $image->attribute_exists( 'foo' ) );
		$this->assertSame( $attributes->exists( 'foo' ), $image->attribute_exists( 'foo' ) );
	}

	/**
	 * @covers ::add_to_attributes()
	 *
	 * @group properties
	 * @group attributes
	 */
	function test_add_to_attributes() {
		$image = $this->new_instance( array(
			'id' => __FUNCTION__,
			'class' => 'foo',
		) );

		$attributes = new Image_Tag_Attributes( array(
			'id' => __FUNCTION__,
			'class' => 'foo',
		) );

		$this->assertSame( $attributes->id, $image->attributes->id );
		$this->assertSame( $attributes->class, $image->attributes->class );

		$image->add_to_attributes( array(
			'id' => 'foo',
			'class' => 'bar',
		) );

		$attributes->add_to( array(
			'id' => 'foo',
			'class' => 'bar',
		) );

		$this->assertSame( $attributes->id, $image->attributes->id );
		$this->assertSame( $attributes->class, $image->attributes->class );
	}

	/**
	 * @covers ::add_to_attribute()
	 *
	 * @group properties
	 * @group attributes
	 */
	function test_add_to_attribute() {
		$image = $this->new_instance( array( 'id' => __FUNCTION__ ) );
		$attributes = new Image_Tag_Attributes( array( 'id' => __FUNCTION__ ) );

		$this->assertSame( $attributes->id, $image->attributes->id );

		$image->add_to_attribute( 'id', 'bar' );
		$attributes->add_to( 'id', 'bar' );

		$this->assertSame( $attributes->id, $image->attributes->id );
	}

	/**
	 * @covers ::get_attributes()
	 *
	 * @group properties
	 * @group attributes
	 */
	function test_get_attributes() {
		$image = $this->new_instance( array(
			'id' => __FUNCTION__,
			'class' => array( 'foo', 'zoo' ),
		) );

		$attributes = new Image_Tag_Attributes( array(
			'id' => __FUNCTION__,
			'class' => array( 'foo', 'zoo' ),
		) );

		foreach ( array( 'edit', 'view' ) as $context ) {
			$this->assertSame( $attributes->get( null, $context ), $image->get_attributes( null, $context ) );
			$this->assertSame( $attributes->get( array( 'id', 'class', 'foo' ), $context ), $image->get_attributes( array( 'id', 'class', 'foo' ), $context ) );
			$this->assertSame( 2, count( $attributes->get( array( 'id', 'class' ), $context ) ) );
		}
	}

	/**
	 * @covers ::get_attribute()
	 *
	 * @group properties
	 * @group attributes
	 */
	function test_get_attribute() {
		$image = $this->new_instance( array( 'id' => __FUNCTION__ ) );
		$attributes = new Image_Tag_Attributes( array( 'id' => __FUNCTION__ ) );

		$this->assertSame( $attributes->id, $image->attributes->id );

		foreach ( array( 'edit', 'view' ) as $context )
			$this->assertSame( $attributes->get( 'id', $context ), $image->get_attribute( 'id', $context ) );
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
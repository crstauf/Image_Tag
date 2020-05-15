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
	 * @covers ::add()
	 * @covers ::set()
	 * @covers ::add_to()
	 *
	 * @group instance
	 * @group chaining
	 */
	function test_chaining() {
		$instance = $this->new_instance();

		$this->assertSame( $instance, $instance->add( 'attributes', 'add', array( 'id' => __FUNCTION__ ) ) );
		$this->assertSame( $instance, $instance->set( 'attributes', 'set', array( 'id' => __FUNCTION__ ) ) );
		$this->assertSame( $instance, $instance->add_to( 'attributes', 'add_to', array( 'id' => __FUNCTION__ ) ) );
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
	########  ########   #######  ########  ######## ########  ######## #### ########  ######
	##     ## ##     ## ##     ## ##     ## ##       ##     ##    ##     ##  ##       ##    ##
	##     ## ##     ## ##     ## ##     ## ##       ##     ##    ##     ##  ##       ##
	########  ########  ##     ## ########  ######   ########     ##     ##  ######    ######
	##        ##   ##   ##     ## ##        ##       ##   ##      ##     ##  ##             ##
	##        ##    ##  ##     ## ##        ##       ##    ##     ##     ##  ##       ##    ##
	##        ##     ##  #######  ##        ######## ##     ##    ##    #### ########  ######
	*/

	/**
	 * @covers ::add()
	 * @covers ::access_property()
	 *
	 * @group properties
	 */
	function test_add() {
		$image = $this->new_instance();
		$attributes = new Image_Tag_Attributes( array() );

		$add_attributes = array(
			'id' => __FUNCTION__,
			'class' => 'foo bar',
			'width' => 1600,
			'height' => 900,
		);

		$image->add( 'attributes', $add_attributes );
		$attributes->add( $add_attributes );

		foreach ( $add_attributes as $attribute => $value )
			$this->assertSame( $attributes->$attribute, $image->attributes->$attribute );

		$image = $this->new_instance();
		$attributes = new Image_Tag_Attributes( array() );

		$image->add( 'attribute', 'id', __FUNCTION__ );
		$attributes->add( 'id', __FUNCTION__ );

		$this->assertSame( $attributes->id, $image->attributes->id );
	}

	/**
	 * @covers ::set()
	 * @covers ::access_property()
	 *
	 * @group properties
	 */
	function test_set() {
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

		$image->set( 'attributes', $set_attributes );
		$attributes->set( $set_attributes );

		foreach ( $set_attributes as $attribute => $value ) {
			$this->assertSame( $value, $image->attributes->$attribute );
			$this->assertSame( $attributes->$attribute, $image->attributes->$attribute );
		}

		$image = $this->new_instance( array( 'id' => uniqid( __FUNCTION__ ) ) );
		$attributes = new Image_Tag_Attributes( array( 'id' => uniqid( __FUNCTION__ ) ) );

		$this->assertNotEquals( __FUNCTION__, $image->attributes->id );
		$this->assertNotEquals( __FUNCTION__, $attributes->id );

		$image->set( 'attribute', 'id', __FUNCTION__ );
		$attributes->set( 'id', __FUNCTION__ );

		$this->assertSame( $attributes->id, $image->attributes->id );
	}

	/**
	 * @covers ::isset()
	 * @covers ::access_property()
	 *
	 * @group properties
	 */
	function test_isset() {
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

		$this->assertTrue( $image->isset( 'attributes', array( 'id', 'width', 'height' ) ) );
		$this->assertSame( $attributes->isset( array( 'id', 'width', 'height' ) ), $image->isset( 'attributes', array( 'id', 'width', 'height' ) ) );

		$this->assertFalse( $image->isset( 'attributes', array( 'id', 'title' ) ) );
		$this->assertSame(  $attributes->isset( array( 'id', 'title' ) ), $image->isset( 'attributes', array( 'id', 'title' ) ) );

		$image = $this->new_instance( array( 'id' => __FUNCTION__ ) );
		$attributes = new Image_Tag_Attributes( array( 'id' => __FUNCTION__ ) );

		$this->assertTrue( $image->isset( 'attribute', 'id' ) );
		$this->assertSame( $attributes->isset( 'id' ), $image->isset( 'attribute', 'id' ) );
	}

	/**
	 * @covers ::exists()
	 * @covers ::access_property()
	 *
	 * @group properties
	 */
	function test_attributes_exist() {
		$image = $this->new_instance();
		$attributes = new Image_Tag_Attributes( array() );

		$this->assertTrue( $image->exists( 'attributes', array( 'id', 'class', 'width' ) ) );
		$this->assertSame( $attributes->exists( array( 'id', 'class', 'width' ) ), $image->exists( 'attributes', array( 'id', 'class', 'width' ) ) );

		$this->assertFalse( $image->exists( 'attributes', array( 'id', 'foo' ) ) );
		$this->assertSame( $attributes->exists( array( 'id', 'foo' ) ), $image->exists( 'attributes', array( 'id', 'foo' ) ) );

		$image = $this->new_instance();
		$attributes = new Image_Tag_Attributes( array() );

		$this->assertTrue( $image->exists( 'attribute', 'id' ) );
		$this->assertSame( $attributes->exists( 'id' ), $image->exists( 'attribute', 'id' ) );

		$this->assertFalse( $image->exists( 'attribute', 'foo' ) );
		$this->assertSame( $attributes->exists( 'foo' ), $image->exists( 'attribute', 'foo' ) );
	}

	/**
	 * @covers ::add_to()
	 * @covers ::access_property()
	 *
	 * @group properties
	 */
	function test_add_to() {
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

		$image->add_to( 'attributes', array(
			'id' => 'foo',
			'class' => 'bar',
		) );

		$attributes->add_to( array(
			'id' => 'foo',
			'class' => 'bar',
		) );

		$this->assertSame( $attributes->id, $image->attributes->id );
		$this->assertSame( $attributes->class, $image->attributes->class );

		$image = $this->new_instance( array( 'id' => __FUNCTION__ ) );
		$attributes = new Image_Tag_Attributes( array( 'id' => __FUNCTION__ ) );

		$this->assertSame( $attributes->id, $image->attributes->id );

		$image->add_to( 'attribute', 'id', 'bar' );
		$attributes->add_to( 'id', 'bar' );

		$this->assertSame( $attributes->id, $image->attributes->id );
	}

	/**
	 * @covers ::get()
	 * @covers ::access_property()
	 *
	 * @group properties
	 */
	function test_get() {
		$image = $this->new_instance( array(
			'id' => __FUNCTION__,
			'class' => array( 'foo', 'zoo' ),
		) );

		$attributes = new Image_Tag_Attributes( array(
			'id' => __FUNCTION__,
			'class' => array( 'foo', 'zoo' ),
		) );

		foreach ( array( 'edit', 'view' ) as $context ) {
			$this->assertSame( $attributes->get( null, $context ), $image->get( 'attributes', null, $context ) );
			$this->assertSame( $attributes->get( array( 'id', 'class', 'foo' ), $context ), $image->get( 'attributes', array( 'id', 'class', 'foo' ), $context ) );
			$this->assertSame( 2, count( $attributes->get( array( 'id', 'class' ), $context ) ) );
		}

		$image = $this->new_instance( array( 'id' => __FUNCTION__ ) );
		$attributes = new Image_Tag_Attributes( array( 'id' => __FUNCTION__ ) );

		$this->assertSame( $attributes->id, $image->attributes->id );

		foreach ( array( 'edit', 'view' ) as $context )
			$this->assertSame( $attributes->get( 'id', $context ), $image->get( 'attribute', 'id', $context ) );
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
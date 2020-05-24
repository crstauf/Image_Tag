<?php

require_once 'abstract-tests.php';

/**
 * @coversDefaultClass Image_Tag_Settings
 * @group settings
 */
class Image_Tag_Settings_Test extends Image_Tag_Properties_Tests {

	const DEFAULTS = array(
		'before_output' => array(),
		'after_output' => array(),
	);

	protected function class_name() {
		return Image_Tag_Settings::class;
	}

	/**
	 * Test Image_Tag_Settings::NAME constant value.
	 *
	 * @group constant
	 */
	function test_name_constant() {
		$this->assertSame( 'setting', constant( $this->class_name() . '::NAME' ) );
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
	 * Data provider for Image_Tag_Settings_Test::test__get().
	 *
	 * @see Image_Tag_Settings_Test::test__get()
	 * @uses Image_Tag_Properties_Tests::data__get()
	 * @return array[]
	 */
	function data__get() {
		$data = parent::data__get();

		# Add test for property access via alias.
		$data['settings'] = array(
			'settings',
			null,
			static::DEFAULTS,
		);

		return $data;
	}

	/**
	 * @param string $property
	 * @param mixed $value
	 * @param mixed $expected
	 *
	 * @covers ::__get()
	 * @group instance
	 * @group magic
	 * @group get
	 *
	 * @dataProvider data__get
	 */
	function test__get( string $property, $value, $expected = null ) {
		if ( 'settings' === $property ) {
			$this->assertIsArray( $this->get_instance()->$property );
			return;
		}

		parent::test__get( $property, $value, $expected = null );
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
	 * @param Image_Tag_Properties_Abstract $instance
	 * @param string|array $get_properties
	 * @param mixed $expected
	 *
	 * @covers ::get()
	 * @covers ::get_properties()
	 * @covers ::get_property()
	 * @covers ::_get()
	 * @group instance
	 * @group get
	 *
	 * @dataProvider data_get
	 */
	function test_get( Image_Tag_Properties_Abstract $instance, $get_properties, $expected ) {
		if ( is_string( $get_properties ) ) {
			$this->assertSame( $expected, $instance->get( $get_properties ) );
			return;
		}

		if ( is_null( $get_properties ) ) {
			$this->assertSame( $expected, $instance->get( null ) );
			return;
		}

		$actual = array();

		foreach ( $get_properties as $property )
			$actual[$property] = $instance->get( $property );

		$this->assertSame( $expected, $actual );
	}


	/*
	 #######  ##     ## ######## ########  ##     ## ########
	##     ## ##     ##    ##    ##     ## ##     ##    ##
	##     ## ##     ##    ##    ##     ## ##     ##    ##
	##     ## ##     ##    ##    ########  ##     ##    ##
	##     ## ##     ##    ##    ##        ##     ##    ##
	##     ## ##     ##    ##    ##        ##     ##    ##
	 #######   #######     ##    ##         #######     ##
	*/

	/**
	 * @covers ::set_before_output_setting()
	 * @covers ::set_after_output_setting()
	 * @covers ::add_to_before_output_setting()
	 * @covers ::add_to_after_output_setting()
	 * @covers ::add_output()
	 * @covers ::get_output()
	 */
	function test_output() {
		$instance = $this->new_instance( null, array(
			'before_output' => array(),
			 'after_output' => array(),
		) );

		$this->assertSame( array(), $instance->before_output );
		$this->assertSame( array(), $instance->after_output  );

		# Test ::set_{position}_output_setting() functions.
		$instance = $this->new_instance( null, array(
			'before_output' => __FUNCTION__,
			 'after_output' => __FUNCTION__,
		) );

		$this->assertSame( array( 10 => array( __FUNCTION__ ) ), $instance->before_output );
		$this->assertSame( array( 10 => array( __FUNCTION__ ) ), $instance->after_output  );

		# Test ::add_to_{position}_output_setting() functions.
		$instance->add_to( 'before_output', 'foo' );
		$instance->add_to(  'after_output', 'bar' );

		$this->assertSame( array( 10 => array( __FUNCTION__, 'foo' ) ), $instance->before_output );
		$this->assertSame( array( 10 => array( __FUNCTION__, 'bar' ) ), $instance->after_output  );

		# Test ::add_output() function.
		$instance->add_output( 'before', 'bar', 5 );
		$instance->add_output(  'after', 'foo', 5 );

		$this->assertSame( array( 5 => array( 'bar' ), 10 => array( __FUNCTION__, 'foo' ) ), $instance->before_output );
		$this->assertSame( array( 5 => array( 'foo' ), 10 => array( __FUNCTION__, 'bar' ) ), $instance->after_output  );

		# Test ::get_{position}_output_setting() functions.
		$this->assertSame( "\nbar\n" . __FUNCTION__ . "\nfoo\n", $instance->get_output( 'before' ) );
		$this->assertSame( "\nfoo\n" . __FUNCTION__ . "\nbar\n", $instance->get_output(  'after' ) );
		$this->assertNull( $this->get_instance( array() )->get_output( 'before' ) );
		$this->assertNull( $this->get_instance( array() )->get_output(  'after' ) );

		# Test adding multiple priorities.
		$instance = $this->new_instance( null, array(
			'before_output' => array(
				10 => __FUNCTION__,
				5 => 'foo',
				15 => 'bar',
				50 => 'zoo',
			),
		) );
		$this->assertSame( array(
			 5 => array( 'foo' ),
			10 => array( __FUNCTION__ ),
			15 => array( 'bar' ),
			50 => array( 'zoo' )
		), $instance->before_output );

		# Test adding negative priority.
		$instance = $this->new_instance( null, array( 'before_output' => __FUNCTION__ ) );
		$instance->add_output( 'before', 'foo', -10 );
		$this->assertSame( array( -10 => array( 'foo' ), 10 => array( __FUNCTION__ ) ), $instance->before_output );

		# Test removal of empty value.
		$instance = $this->new_instance( null, array( 'before_output' => array(
			10 => array(
				__FUNCTION__,
				'  ',
			),
			5 => array(
				array(
					' ',
					'foo',
					array( 'bar' ),
				),
			),
			'foo' => __FUNCTION__, // test priority is changed to 10
		) ) );
		$this->assertSame( array( 5 => array( 'foo', 'bar' ), 10 => array( __FUNCTION__, __FUNCTION__ ) ), $instance->before_output );

		# Test added to image tag output.
		$image = new Image_Tag( array( 'src' => 'https://source.unsplash.com/1000x1000' ), array(
			'before_output' => 'before output',
			 'after_output' =>  'after output',
		) );

		$image->settings->add_output( 'before', 'bar' . __FUNCTION__, 5 );
		$image->settings->add_output(  'after', 'foo' . __FUNCTION__, 5 );

		$this->assertSame(
			"\n" .
			'bar' . __FUNCTION__ . "\n" .
			'before output' . "\n" .
			'<img ' .
				'src="' . esc_attr( 'https://source.unsplash.com/1000x1000' ) . '" ' .
				'sizes="100vw" ' .
				'alt="" ' .
			'/>' .
			"\n" .
			'foo' . __FUNCTION__ . "\n" .
			'after output' . "\n",
		$image->__toString() );
	}

}

?>
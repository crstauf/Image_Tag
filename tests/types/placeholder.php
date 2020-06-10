<?php

require_once 'abstract.php';

/**
 * @coversDefaultClass Image_Tag_Placeholder
 */
class Image_Tag_Placeholder_Test extends Image_Tag_Test_Base {

	protected function class_name() {
		return Image_Tag_Placeholder::class;
	}

	/**
	 * Data provider for Image_Tag_Test_Base::test_constant_types().
	 *
	 * @see Image_Tag_Test_Base::test_constant_types()
	 * @return array[]
	 */
	function data_constant_types() {
		return array(
			array(
				array(
					'placeholder.com',
					'dimensions',
					'size',
					'text',
					'placeholder',
					'external',
					'remote',
				),
			),
		);
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
	 * @covers Image_Tag::create()
	 */
	function data_create() {
		return array(
			'placeholder.com' => array(
				$this->new_instance(),
				'placeholder.com',
			),
			'dimensions' => array(
				$this->new_instance(),
				'dimensions',
			),
			'size' => array(
				$this->new_instance(),
				'size',
			),
			'text' => array(
				$this->new_instance(),
				'text',
			),
		);
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
	 * Data provider for Image_Tag_Picsum_Test::test__toString().
	 *
	 * @see static::test__toString()
	 * @return array[]
	 */
	function data__toString() {
		return array(
			'warning' => array(
				$this->new_instance(),
				'warning',
			),
			'width' => array(
				$this->new_instance( array(), array( 'width' => 400 ) ),
				'<img src="https://via.placeholder.com/400" sizes="100vw" alt="" />',
			),
			'height' => array(
				$this->new_instance( array(), array( 'height' => 300 ) ),
				'<img src="https://via.placeholder.com/300" sizes="100vw" alt="" />',
			),
			'width and height' => array(
				$this->new_instance( array(), array( 'width' => 400, 'height' => 300 ) ),
				'<img src="https://via.placeholder.com/400x300" sizes="100vw" alt="" />',
			),
		);
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
	function test_get_type() {
		$this->assertSame( 'placeholder.com', $this->new_instance()->get_type() );
	}

	/**
	 * @covers ::is_type()
	 *
	 * @group instance
	 * @group validation
	 */
	function test_is_type() {
		$instance = $this->new_instance();

		$this->assertTrue( $instance->is_type( 'placeholder.com' ) );
		$this->assertTrue( $instance->is_type( 'dimensions' ) );
		$this->assertTrue( $instance->is_type( 'size' ) );
		$this->assertTrue( $instance->is_type( 'text' ) );
		$this->assertTrue( $instance->is_type( 'placeholder' ) );
		$this->assertTrue( $instance->is_type( 'remote' ) );
		$this->assertTrue( $instance->is_type( 'external' ) );

		$this->assertFalse( $instance->is_type( 'local' ) );
	}

	/**
	 * @covers ::is_valid()
	 * @covers ::check_valid()
	 * @covers Image_Tag_Abstract::is_valid()
	 * @covers Image_Tag_Abstract::check_valid()
	 *
	 * @group instance
	 * @group validation
	 */
	function test_is_valid() {
		$instance = $this->new_instance();
		$this->assertFalse( $instance->is_valid() );

		$instance = $this->new_instance( array(), array(
			'width' => 400,
		) );
		$this->assertTrue( $instance->is_valid() );

		$instance = $this->new_instance( array(), array(
			'height' => 300,
		) );
		$this->assertTrue( $instance->is_valid() );

		$this->assertTrue( $instance->is_valid( 'placeholder.com' ) );
		$this->assertFalse( $instance->is_valid( 'local' ) );
	}


	/*
	######## ########    ###    ######## ##     ## ########  ########  ######
	##       ##         ## ##      ##    ##     ## ##     ## ##       ##    ##
	##       ##        ##   ##     ##    ##     ## ##     ## ##       ##
	######   ######   ##     ##    ##    ##     ## ########  ######    ######
	##       ##       #########    ##    ##     ## ##   ##   ##             ##
	##       ##       ##     ##    ##    ##     ## ##    ##  ##       ##    ##
	##       ######## ##     ##    ##     #######  ##     ## ########  ######
	*/

	function data_http() {
		$this->markTestIncomplete();
	}

	/**
	 * Data provider for Image_Tag_Test_Base::test_noscript().
	 *
	 * @see static::test_noscript()
	 * @return array[]
	 */
	function data_noscript() {
		$instance = $this->get_instance( array( 'src' => 'https://source.unsplash.com/1000x1000' ) );

		return array(
			array(
				$this->new_instance( array(), array( 'width' => 400 ) ),
				array( array(), array() ),
				array(),
				new Image_Tag_Placeholder( array(
					'class' => array( 1 => 'no-js' ),
				), array(
					'width' => 400,
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
				) ),
			),
		);
	}

	/**
	 * Data provider for Image_Tag_Test_Base::test_lazyload().
	 *
	 * @see Image_Tag_Test_Base::test_lazyload()
	 * @return array[]
	 */
	function data_lazyload() {
		return array(
			array(
				$this->get_instance( array(), array(
					'width' => 100,
					'lazyload' => array( 'noscript' => false ),
				) ),
				new Image_Tag( array(
					'src' => Image_Tag::BLANK,
					'class' => array( 'lazyload', 'hide-if-no-js' ),
					'sizes' => array(),
					'data-src' => 'https://via.placeholder.com/100',
					'data-sizes' => array( 'auto' ),
				), array(
					'lazyload' => array(
						'noscript' => false,
						'noscript_priority' => -10,
						'sizes_auto' => true,
					),
				) ),
			),
		);
	}

	/**
	 * Data provider for Image_Tag_Placeholder_Test::test_into().
	 *
	 * @see static::test_into()
	 * @return array[]
	 */
	function data_into() {
		$data = array();

		$data['direct'] = array(
			Image_Tag::create( 'https://source.unsplash.com/1000x1000' ),
			'placeholder',
			array( null, null ),
			Image_Tag::create( 'placeholder' ),
		);

		$instance = Image_Tag::create( 'placeholder' );
		$data['self'] = array(
			$instance,
			'placeholder',
			array( null, null ),
			$instance,
		);

		return $data;
	}

}

?>
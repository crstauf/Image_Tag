<?php

require_once 'abstract.php';

/**
 * @coversDefaultClass Image_Tag_JoeSchmoe
 */
class Image_Tag_JoeSchmoe_Test extends Image_Tag_Test_Base {

	protected function class_name() {
		return Image_Tag_JoeSchmoe::class;
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
					'joeschmoe',
					'avatar',
					'person',
					'external',
					'remote',
				),
			),
		);
	}

	/**
	 * @group constant
	 */
	function test_constant_primary_url() {
		$this->assertSame( 'https://joeschmoe.io/api/v1/', constant( $this->class_name() . '::PRIMARY_URL' ) );
	}

	/**
	 * @group constant
	 */
	function test_constant_alt_url() {
		$this->assertSame( 'https://joeschmoe.crstauf.workers.dev/', constant( $this->class_name() . '::ALT_URL' ) );
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
	 * @covers Imagee_Tag::create()
	 */
	function data_create() {
		return array(
			'joeschmoe' => array(
				$this->new_instance(),
				'joeschmoe',
			),
			'avatar' => array(
				$this->new_instance(),
				'avatar',
			),
			'person' => array(
				$this->new_instance(),
				'person',
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
	 * @covers ::__construct()
	 *
	 * @group instance
	 * @group magic
	 */
	function test__construct() {
		parent::test__construct();

		$instance = $this->new_instance( array(), array( 'gender' => 'lgbtq' ) );
		$this->assertNull( $instance->settings->gender );
	}

	/**
	 * Data providerf or Image_Tag_JoeSchmoe_Test::test__toString().
	 *
	 * @see static::test__toString()
	 * @return array[]
	 */
	function data__toString() {
		return array(
			'alt source' => array(
				$this->new_instance(),
				'<img src="https://joeschmoe.crstauf.workers.dev/" sizes="100vw" alt="" />',
			),
			'male' => array(
				$this->new_instance( array(), array( 'gender' => 'male' ) ),
				'<img src="https://joeschmoe.crstauf.workers.dev/male/" sizes="100vw" alt="" />',
			),
			'female' => array(
				$this->new_instance( array(), array( 'gender' => 'female' ) ),
				'<img src="https://joeschmoe.crstauf.workers.dev/female/" sizes="100vw" alt="" />',
			),
			'male name' => array(
				$this->new_instance( array(), array( 'gender' => 'male', 'seed' => 'josh' ) ),
				'<img src="https://joeschmoe.crstauf.workers.dev/male/josh" sizes="100vw" alt="" />',
			),
			'female name' => array(
				$this->new_instance( array(), array( 'gender' => 'female', 'seed' => 'josephine' ) ),
				'<img src="https://joeschmoe.crstauf.workers.dev/female/josephine" sizes="100vw" alt="" />',
			),
			'primary source' => array(
				$this->new_instance( array(), array( 'source' => 'primary' ) ),
				'<img src="https://joeschmoe.io/api/v1/" sizes="100vw" alt="" />',
			),
			'lgbtq' => array(
				$this->new_instance( array(), array( 'gender' => uniqid(), 'seed' => 'alex' ) ),
				'<img src="https://joeschmoe.crstauf.workers.dev/alex" sizes="100vw" alt="" />',
			),
		);
	}

	/**
	 * @param Image_Tag_Abstract $instance
	 * @param string $expected
	 *
	 * @covers ::__toString()
	 * @covers ::generate_src()
	 * @covers ::get_url()
	 *
	 * @group instance
	 * @group magic
	 * @group output
	 *
	 * @dataProvider data__toString
	 */
	function test__toString( Image_Tag_Abstract $instance, string $expected ) {
		parent::test__toString( $instance, $expected );
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
		$this->assertSame( 'joeschmoe', $this->new_instance()->get_type() );
	}

	/**
	 * @covers ::is_type()
	 *
	 * @group instance
	 * @group validation
	 */
	function test_is_type() {
		$instance = $this->new_instance();

		$this->assertTrue( $instance->is_type( 'joeschmoe' ) );
		$this->assertTrue( $instance->is_type( 'avatar' ) );
		$this->assertTrue( $instance->is_type( 'person' ) );
	}

	/**
	 * @covers ::is_valid()
	 * @covers ::check_valid()
	 *
	 * @group instance
	 * @group validation
	 */
	function test_is_valid() {
		$instance = $this->new_instance();
		$this->assertTrue( $instance->is_valid() );
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

	/**
	 * @covers ::http()
	 * @group instance
	 * @group external-http
	 */
	function test_http() {
		$this->markTestIncomplete();
	}

}

?>
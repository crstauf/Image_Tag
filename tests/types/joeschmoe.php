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
	 * Data provider for Image_Tag_JoeSchmoe_Test::test__toString().
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
				'<img src="https://joeschmoe.crstauf.workers.dev/male/josh/" sizes="100vw" alt="" />',
			),
			'female name' => array(
				$this->new_instance( array(), array( 'gender' => 'female', 'seed' => 'josephine' ) ),
				'<img src="https://joeschmoe.crstauf.workers.dev/female/josephine/" sizes="100vw" alt="" />',
			),
			'primary source' => array(
				$this->new_instance( array(), array( 'source' => 'primary' ) ),
				'<img src="https://joeschmoe.io/api/v1/" sizes="100vw" alt="" />',
			),
			'lgbtq' => array(
				$this->new_instance( array(), array( 'gender' => uniqid(), 'seed' => 'alex' ) ),
				'<img src="https://joeschmoe.crstauf.workers.dev/alex/" sizes="100vw" alt="" />',
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
	 * Data provider for Image_Tag_JoeSchmoe_Test::test_http().
	 *
	 * @see static::test_http()
	 * @return array[]
	 */
	function data_http() {
		return array(
			array( $this->new_instance() ),
			array( $this->new_instance( array(), array( 'source' => 'primary', 'random' => true ) ) ),
		);
	}

	/**
	 * @covers ::http()
	 * @group instance
	 * @group external-http
	 *
	 * @dataProvider data_http
	 */
	function test_http( Image_Tag_Abstract $instance ) {
		$count = did_action( 'http_api_debug' );
		echo $instance;
		$response = $instance->http();

		$this->assertSame( ++$count, did_action( 'http_api_debug' ) );
		$this->assertSame( 'image/svg+xml; charset=utf-8', wp_remote_retrieve_header( $response, 'content-type' ) );

		$this->assertSame( $response, $instance->http() );
		$this->assertSame( $count, did_action( 'http_api_debug' ) );
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
					'lazyload' => array( 'noscript' => false ),
				) ),
				new Image_Tag( array(
					'src' => Image_Tag::BLANK,
					'class' => array( 'lazyload', 'hide-if-no-js' ),
					'sizes' => array(),
					'data-src' => 'https://joeschmoe.crstauf.workers.dev/',
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
	 * Data provider for Image_Tag_JoeSchmoe_Test::test_into().
	 *
	 * @see static::test_into()
	 * @return array[]
	 */
	function data_into() {
		$data = array();

		$data['direct'] = array(
			Image_Tag::create( 'https://source.unsplash.com/1000x1000' ),
			'joeschmoe',
			array( null, null ),
			Image_Tag::create( 'joeschmoe' ),
		);

		$gender = array_rand( array( 'male' => 1, 'female' => 1 ) );
		$data['gender'] = array(
			Image_Tag::create( 'https://source.unsplash.com/1000x1000', array(), array( 'gender' => $gender ) ),
			'joeschmoe',
			array( null, null ),
			Image_Tag::create( 'joeschmoe', array(), array( 'gender' => $gender ) ),
		);

		$params = array( array( 'id' => __FUNCTION__ ), array( 'seed' => __FUNCTION__ ) );
		$data['params'] = array(
			Image_Tag::create( 'https://source.unsplash.com/1000x1000' ),
			'joeschmoe',
			$params,
			Image_Tag::create( 'joeschmoe', ...$params ),
		);

		$instance = Image_Tag::create( 'joeschmoe' );
		$data['self'] = array(
			$instance,
			'joeschmoe',
			array( null, null ),
			$instance,
		);

		return $data;
	}

}

?>
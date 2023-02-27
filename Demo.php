<?php

declare( strict_types=1 );

namespace Image_Tag\Demo;

defined( 'WPINC' ) || die();

class Demo {

	/**
	 * Instance.
	 *
	 * @return self
	 */
	public static function instance() : self {
		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self; // @codeCoverageIgnore
		}

		return $instance;
	}

	/**
	 * Construct.
	 */
	protected function __construct() {
		$this->theme = get_stylesheet();

		add_action( 'template_redirect', array( $this, 'action__template_redirect' ), 101 );
	}


	/*
	....###.....######..########.####..#######..##....##..######.
	...##.##...##....##....##.....##..##.....##.###...##.##....##
	..##...##..##..........##.....##..##.....##.####..##.##......
	.##.....##.##..........##.....##..##.....##.##.##.##..######.
	.#########.##..........##.....##..##.....##.##..####.......##
	.##.....##.##....##....##.....##..##.....##.##...###.##....##
	.##.....##..######.....##....####..#######..##....##..######.
	*/

	/**
	 * Action: template_redirect
	 *
	 * @return void
	 */
	public function action__template_redirect() : void {
		if ( 'template_redirect' !== current_action() ) {
			return;
		}

		$slug = 'image-tag';

		if ( defined( 'IMAGE_TAG_DEMO_PAGE_SLUG' ) ) {
			$slug = constant( 'IMAGE_TAG_DEMO_PAGE_SLUG' );
		}

		$slug = apply_filters( 'image-tag-demo-page-slug', $slug );

		if ( ! is_page( $slug ) ) {
			return;
		}

		get_header();

		$this->header();
		$this->attachment();
		$this->attachment_lqip();
		$this->theme();
		$this->joeschmoe();
		$this->picsum();
		$this->unsplash();
		$this->placeholder();
		$this->url();
		$this->fallback();

		get_footer();

		exit;
	}

	protected function header() : void {
		?>

		<html>

			<head>
				<title>Image Tag demo</title>

				<style>

					html, body {
						background-color: #eee;
						margin: 0;
						padding: 0;
					}

					body,
					figure {
						display: flex;
						flex-direction: column;
						align-items: center;
					}

						body {
							margin: 100px auto;
							text-transform: uppercase;
							font-family: sans-serif;
						}

						figure + figure {
							margin-top: 100px;
						}

					figcaption {
						display: block;
						padding: 10px 20px;
						margin-top: 10px;
						background-color: rgba( 255, 255, 255, 0.8 );
						border: 1px solid #000;
						white-space: pre-wrap;
						box-sizing: border-box;
						font-family: monospace;
						letter-spacing: 1px;
						text-align: left;
						font-weight: 400;
						font-size: 12px;
					}

					img {
						width: auto;
						height: auto;
						max-width: 60vw;
						max-height: 40vh;
						padding: 40px;
						background-color: #FFF;
						box-shadow: 0 0 10px 0 rgba( 0, 0, 0, 0.3 );
						border: 10px solid #000;
						box-sizing: border-box;
					}
				</style>
			</head>

			<body>

		<?php
	}

	protected function output( $image, $code ) {
		if ( is_object( $image ) ) {
			$image->attributes->loading = 'lazy';
		}

		printf(
			'<figure>%s<figcaption>%s</figcaption></figure>',
			$image,
			trim( $code )
		);
	}

	protected function attachment() {
		$query = new \WP_Query( array(
			'post_type'      => 'attachment',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'no_found_rows'  => true,
			'fields'         => 'ids',
		) );

		$post_id = array_pop( $query->posts );
		$image   = \Image_Tag::create( $post_id );

		ob_start();
		?>

\Image_Tag::create( <?php echo $post_id ?> );

		<?php
		$this->output( $image, ob_get_clean() );
	}

	protected function attachment_lqip() {
		$query = new \WP_Query( array(
			'post_type'      => 'attachment',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'no_found_rows'  => true,
			'fields'         => 'ids',
		) );

		$post_id = array_pop( $query->posts );
		$image   = \Image_Tag::create( $post_id )->lqip();
		$img     = sprintf( '<img src="%s" />', $image );

		ob_start();
		?>

\Image_Tag::create( <?php echo $post_id ?> )->lqip();

		<?php
		$this->output( $img, ob_get_clean() );
	}

	protected function theme() : void {
		$image = \Image_Tag::create( 'assets/images/Daffodils.jpg' );

		ob_start();
		?>

\Image_Tag::create( 'assets/images/Daffodils.jpg' )

		<?php
		$this->output( $image, ob_get_clean() );
	}

	protected function joeschmoe() : void {
		$image = \Image_Tag::create( 'joeschmoe', array(
			'width'  => 1000,
			'height' => 1000,
		) );

		ob_start();
		?>

\Image_Tag::create( 'joeschmoe', array(
	'width'  => 1000,
	'height' => 1000,
) );

		<?php
		$this->output( $image, ob_get_clean() );
	}

	protected function picsum() : void {
		$image = \Image_Tag::create( 'picsum', array(
			'width'  => 1000,
			'height' => 1000,
		) );

		ob_start();
		?>

\Image_Tag::create( 'picsum', array(
	'width'  => 1000,
	'height' => 1000,
) );

		<?php
		$this->output( $image, ob_get_clean() );
	}

	protected function unsplash() : void {
		$image = \Image_Tag::create( 'unsplash', array(
			'width'  => 400,
			'height' => 300,
		), array(
			'random' => 1,
		) );

		ob_start();
		?>

\Image_Tag::create( 'unsplash', array(
	'width'  => 400,
	'height' => 300,
), array(
	'random' => 1,
) );

		<?php
		$this->output( $image, ob_get_clean() );
	}

	protected function placeholder() : void {
		$image = \Image_Tag::create( 'placeholder', array(
			'width'  => 1000,
			'height' => 350,
		), array(
			'text' => 'Placeholder.com',
		) );

		ob_start();
		?>

\Image_Tag::create( 'placeholder', array(
	'width'  => 1000,
	'height' => 350,
), array(
	'text' => 'Placeholder.com',
) );

		<?php
		$this->output( $image, ob_get_clean() );
	}

	protected function url() {
		$url = get_theme_file_uri( 'assets/images/playing-in-the-sand.jpg' );

		$image = \Image_Tag::create( $url, array(
			'width'  => 982,
			'height' => 1424,
		) );

		ob_start();
		?>

\Image_Tag::create(
	"<?php echo $url ?>",
	array(
		'width'  => 982,
		'height' => 1424,
	)
);

		<?php
		$this->output( $image, ob_get_clean() );
	}

	protected function fallback() {
		$image = \Image_Tag::create( 1, array(), array(
			'width'       => 1000,
			'height'      => 1000,
			'image-sizes' => 'full',
			'fallback'    => array(
				'joeschmoe'   => ! empty( $_GET['person'] ),
				'placeholder' => current_user_can( 'edit_post', get_queried_object_id() ),
				'picsum'      => true
			),
		) );

		ob_start();
		?>

\Image_Tag::create( 1, array(), array(
	'width'       => 1000,
	'height'      => 1000,
	'image-sizes' => 'full',
	'fallback'    => array(
		'joeschmoe'   => ! empty( $_GET['person'] ),
		'placeholder' => current_user_can( 'edit_post', get_queried_object_id() ),
		'picsum'      => true
	),
) );

		<?php
		$this->output( $image, ob_get_clean() );
	}

}

Demo::instance();
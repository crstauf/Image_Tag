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

		if ( ! is_page( 'image-tag' ) ) {
			return;
		}

		$this->header();
		$this->attachment();
		$this->attachment_lqip();
		$this->theme();
		$this->joeschmoe();
		$this->picsum();
		$this->placeholder();
		$this->url();

		echo '</body></html>';

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
						max-width: 40vw;
						padding: 10px 30px;
						margin-top: 10px;
						background-color: #fff;
						border: 1px solid #000;
						box-sizing: border-box;
						font-family: monospace;
						letter-spacing: 1px;
						font-weight: 300;
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

	protected function output( $image, $label ) {
		printf(
			'<figure>%s<figcaption>%s</figcaption></figure>',
			$image,
			$label
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

		$this->output( $image, sprintf( '\Image_Tag::create( %d )', $post_id ) );
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
		$image   = \Image_Tag::create( $post_id );
		$img     = sprintf( '<img src="%s" />', $image->lqip() );

		$this->output( $img, sprintf( '\Image_Tag::create( %d )->lqip()', $post_id ) );
	}

	protected function theme() : void {
		$image = \Image_Tag::create( 'assets/images/Daffodils.jpg' );
		$this->output( $image, '\Image_Tag::create( "assets/images/Daffodils.jpg" )' );
	}

	protected function joeschmoe() : void {
		$image = \Image_Tag::create( 'joeschmoe', array(
			'width'  => 1000,
			'height' => 1000,
		) );

		$this->output( $image, '\Image_Tag::create( "joeschmoe" )' );
	}

	protected function picsum() : void {
		$image = \Image_Tag::create( 'picsum', array(
			'width'  => 1000,
			'height' => 1000,
		) );

		$this->output( $image, '\Image_Tag::create( "picsum" )' );
	}

	protected function placeholder() : void {
		$image = \Image_Tag::create( 'placeholder', array(
			'width'  => 1000,
			'height' => 350,
		), array(
			'text' => 'Placeholder.com',
		) );

		$this->output( $image, '\Image_Tag::create( "placeholder" )' );
	}

	protected function url() {
		$url = get_theme_file_uri( 'assets/images/playing-in-the-sand.jpg' );

		$image = \Image_Tag::create( $url, array(
			'width'  => 982,
			'height' => 1424,
		) );

		$this->output( $image, sprintf( '\Image_Tag::create( "%s" )', $url ) );
	}

}

Demo::instance();
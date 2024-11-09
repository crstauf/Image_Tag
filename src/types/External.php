<?php declare(strict_types=1);

namespace Image_Tag;

class External {

	use Traits\Attributes_Helper,
		Traits\Lazysizes,
		Traits\Noscript,
		Traits\Settings_Helper;

	/** @var string */
	protected string $url;

	/** @var Stores\Attributes */
	public Stores\Attributes $attributes;

	/** @var Stores\Settings */
	public Stores\Settings $settings;

	/**
	 * Construct.
	 */
	public function __construct( string $url ) {
		$this->url        = $url;
		$this->attributes = new Stores\Attributes();
		$this->settings   = new Stores\Settings();

		$this->attributes->update( 'src', $url );
	}

	public function __toString() : string {
		$string  = '<picture><img ';
		$string .= (string) $this->attributes;
		$string .= ' /></picture>';

		return $string;
	}

}

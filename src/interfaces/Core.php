<?php declare(strict_types=1);

namespace Image_Tag\Interfaces;

interface Core {

	public function width() : int;
	public function height() : int;
	public function ratio() : string|float;
	public function is_valid() : bool;
	public function output() : string;
	public function print() : void;

}

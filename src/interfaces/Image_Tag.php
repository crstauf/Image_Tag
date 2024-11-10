<?php declare(strict_types=1);

namespace Image_Tag\Interfaces;

interface Image_Tag {

	public function is_valid() : bool;
	public function output() : string;
	public function print() : void;

}

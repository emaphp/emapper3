<?php
namespace Acme;

class RGBColor {
	public $red;
	public $green;
	public $blue;
	
	public function __construct($red, $green, $blue) {
		$this->red = $red;
		$this->green = $green;
		$this->blue = $blue;
	}
}
?>
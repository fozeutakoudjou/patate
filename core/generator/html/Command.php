<?php
namespace form\generator;
class Command extends HtmlElement{
	private $label;
	private $icon;
	private $isLink=false;
	function __construct() {
	}
	public function getValue() {
		return $this->value;
	}
	public function getLabel() {
		return $this->label;
	}
	
	public function setLabel($label) {
		$this->label=$label;
	}
	
	public function generate() {
		;
	}
	
	public function isLink() {
		return $this->isLink;
	}
	public function setIsLink($isLink) {
		$this->isLink=$isLink;
	}
	public function getIcon() {
		return $this->icon;
	}
	public function setIcon($icon) {
		$this->icon=$icon;
	}
}
<?php
namespace form\generator;
class Field extends HtmlElement{
	private $value;
	private $label;
	private $icon;
	protected $valueSetted = false;
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
	public function setValue($value){
		$this->value=$value;
		$this->valueSetted =true;
	}
	
	public function generate() {
		;
	}
	
	public function isLink() {
		return $this->link;
	}
	public function getIcon() {
		return $this->icon;
	}
	public function hasIcon() {
	    return !empty($this->icon);
	}
	public function setIcon($icon) {
		$this->icon=$icon;
	}
	public function isValueSetted() {
		return $this->valueSetted;
	}
}
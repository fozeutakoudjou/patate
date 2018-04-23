<?php
namespace core\generator\html;
abstract class Option extends Field{
	protected $options;
	
	function __construct($name, $label='', $options = array()) {
		parent::__construct($name, $label);
		$this->setName($name);
		$this->setOptions($options);
	}
	
	public function getOptions() {
		return $this->options;
	}
	public function setOptions($options) {
		$this->options=$options;
	}
	
	public function isOptionSelected($value) {
		return ($this->value==$value);
	}
}
<?php
namespace core\generator\html;
class Select extends Field{
	protected $templateFile = 'generator/select';
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
		return false;
	}
}
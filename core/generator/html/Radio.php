<?php
namespace core\generator\html;
class Radio extends Option{
	protected $templateFile = 'generator/radio';
	
	function __construct($name, $label='', $options = array(), $switch = false) {
		parent::__construct($name, $label, $options);
		if($switch){
			$this->setTemplateFile('generator/switch', false);
		}
	}
	
	public function getOptionId($value) {
		return $this->name.'_'.$value;
	}
	
	public function isOptionSelected($value) {
		return parent::isOptionSelected($value) || (empty($this->value) && empty($value));
	}
}
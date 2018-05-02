<?php
namespace core\generator\html;
class Radio extends Option{
	protected $templateFile = 'generator/radio';
	
	public function getOptionId($value) {
		return $this->name.'_'.$value;
	}
	
	public function isOptionSelected($value) {
		return parent::isOptionSelected($value) || (empty($this->value) && empty($value));
	}
}
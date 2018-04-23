<?php
namespace core\generator\html;
class Radio extends Option{
	protected $templateFile = 'generator/radio';
	
	public function getOptionId($value) {
		return $this->name.'_'.$value;
	}
}
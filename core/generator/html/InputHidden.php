<?php
namespace core\generator\html;
class InputHidden extends Field{
	protected $templateFile = 'generator/input_hidden';
	function __construct($name, $value=null) {
		parent::__construct($name);
		if($value!==null){
			$this->setValue($value);
		}
	}
}
<?php
namespace core\generator\html;
class InputHidden extends Field{
	protected $templateFile = 'generator/input_hidden';
	function __construct($name, $value=null, $class = '') {
		parent::__construct($name);
		if($value!==null){
			$this->setValue($value);
		}
		if(!empty($class)){
			$this->addClass($class);
		}
	}
}
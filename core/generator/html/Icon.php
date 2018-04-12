<?php
namespace core\generator\html;
class Icon extends Element{
	protected $value;
	protected $textIcon;
	protected $templateFile = 'generator/icon';
	
	function __construct($value, $textIcon = false) {
		$this->setValue($value);
		$this->setTextIcon($textIcon);
	}
	public function getValue() {
		return $this->value;
	}
	public function setValue($value){
		$this->value=$value;
	}
	
	public function setTextIcon($textIcon){
		$this->textIcon=$textIcon;
	}
	
	public function isTextIcon(){
		return $this->textIcon;
	}
}
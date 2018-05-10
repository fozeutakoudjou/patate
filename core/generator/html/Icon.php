<?php
namespace core\generator\html;
class Icon extends Element{
	protected $value;
	protected $textIcon;
	protected $addOnIcon;
	protected $templateFile = 'generator/icon';
	
	function __construct($value, $textIcon = false, $addOnIcon = true) {
		$this->setValue($value);
		$this->setTextIcon($textIcon);
		$this->setAddOnIcon($addOnIcon);
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
	
	public function isAddOnIcon(){
		return $this->addOnIcon;
	}
	
	public function setAddOnIcon($addOnIcon){
		$this->addOnIcon=$addOnIcon;
	}
}
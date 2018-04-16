<?php
namespace core\generator\html;
class InputText extends Field{
	protected $templateFile = 'generator/input_text';
	protected $leftIcon;
	protected $rightIcon;
	protected $placeholder;
	protected $type;
	
	function __construct($name, $label='', $type = 'text') {
		parent::__construct($name, $label);
		$this->setName($name);
		$this->setType($type);
	}
	
	public function getLeftIcon(){
		return $this->leftIcon;
	}
	public function setLeftIcon($leftIcon){
		$this->leftIcon=$leftIcon;
	}
	
	public function getType(){
		return $this->type;
	}
	public function setType($type){
		$this->type=$type;
	}
	
	public function getRightIcon(){
		return $this->rightIcon;
	}
	public function setRightIcon($rightIcon){
		$this->rightIcon=$rightIcon;
	}
	public function getPlaceholder(){
		return $this->placeholder;
	}
	public function setPlaceholder($placeholder){
		$this->placeholder=$placeholder;
	}
	
	public function hasPlaceholder(){
		return !empty($placeholder->placeholder);
	}
	
	public function hasLeftIcon(){
		return ($this->leftIcon!=null);
	}
	
	public function hasRightIcon(){
		return ($this->rightIcon!=null);
	}
}
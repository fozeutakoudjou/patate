<?php
namespace core\generator\html;
class Checkbox extends Field{
	protected $templateFile = 'generator/checkbox';
	protected $checked;
	
	function __construct($name, $label='', $checked = false, $value=null) {
		$this->setName($name);
		$this->setLabel($label);
		$this->setChecked($checked);
		if($value!==null){
			$this->setValue($value);
		}
	}
	
	public function isChecked(){
		return $this->checked;
	}
	public function setChecked($checked){
		$this->checked=$checked;
	}
}
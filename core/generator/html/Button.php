<?php
namespace core\generator\html;
class Button extends Command{
	protected $templateFile = 'generator/button';
	protected $submit = false;
	public function __construct($label, $submit = false, $icon = null, $name = '') {
		parent::__construct($label, $icon, $name);
		$this->setSubmit($submit);
	}
	
	public function isSubmit(){
		return $this->submit;
	}
	public function setSubmit($submit){
		$this->submit=$submit;
	}
}
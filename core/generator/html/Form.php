<?php
namespace core\generator\html;
class Form extends Block{
	protected $templateFile = 'generator/form';
	protected $formAction;
	protected $method;
	protected $submit;
	protected $submitAction;
	protected $cancel;
	protected $subLabel;
	protected $enctype='multipart/form-data';
	
	public function __construct($decorated = true, $label ='', $icon = null, $formAction='', $submitAction = '', $method = 'post') {
		parent::__construct($decorated, $label, $icon);
		$this->setFormAction($formAction);
		$this->setMethod($method);
		$this->setSubmitAction($submitAction);
		
	}
	public function hasCancel(){
		return ($this->cancel!=null);
	}
	public function hasSubmit(){
		return ($this->submit!=null);
	}
	
	public function hasFooter(){
		return ($this->hasCancel() || $this->hasSubmit() || !empty($this->footers));
	}
	
	public function hasSubLabel(){
		return !empty($this->subLabel);
	}
	
	public function getSubmitAction(){
		return $this->submitAction;
	}
	public function setSubmitAction($submitAction){
		$this->submitAction=$submitAction;
	}
	public function getFormAction(){
		return $this->formAction;
	}
	public function setFormAction($formAction){
		$this->formAction=$formAction;
	}
	public function getMethod(){
		return $this->method;
	}
	public function setMethod($method){
		$this->method=$method;
	}
	public function getSubLabel(){
		return $this->subLabel;
	}
	public function setSubLabel($subLabel){
		$this->subLabel=$subLabel;
	}
	
	public function setSubmit($submit){
		$this->submit=$submit;
	}
	public function setCancel($cancel){
		$this->cancel=$cancel;
	}
	
	public function getSubmit(){
		return $this->submit;
	}
	public function getCancel(){
		return $this->cancel;
	}
	
	public function getEnctype(){
		return $this->enctype;
	}
	public function setEnctype($enctype){
		$this->enctype=$enctype;
	}
}
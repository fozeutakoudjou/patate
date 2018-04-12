<?php
namespace core\generator\html;
class Form extends Block{
	protected $templateFile = 'generator/form';
	protected $formAction;
	protected $method;
	protected $submit;
	protected $cancel;
	
	protected $contentOnly = false;
	
	public function __construct($decorated = true, $label ='', $icon = null, $formAction='', $method = 'post') {
		parent::__construct($decorated, $label, $icon);
		$this->setFormAction($formAction);
		$this->setMethod($method);
		
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
	
	public function isContentOnly(){
		return $this->contentOnly;
	}
	public function setContentOnly($contentOnly){
		$this->contentOnly=$contentOnly;
	}
}
<?php
namespace core\generator\html;
class HtmlGenerator{
	protected $defaultSubmitText;
	protected $defaultCancelText;
	protected $defaultCancelIcon = 'cancel';
	protected $defaultSubmitIcon='save';
	
	public function __construct($defaultSubmitText = '', $defaultCancelText = '') {
		$this->setDefaultSubmitText($defaultSubmitText);
		$this->setDefaultCancelText($defaultCancelText);
	}
	
	public function setDefaultSubmitText($defaultSubmitText){
		$this->defaultSubmitText=$defaultSubmitText;
	}
	public function setDefaultCancelText($defaultCancelText){
		$this->defaultCancelText=$defaultCancelText;
	}
	
	public function setDefaultSubmitIcon($defaultSubmitIcon){
		$this->defaultSubmitIcon=$defaultSubmitIcon;
	}
	
	public function setDefaultCancelIcon($defaultCancelIcon){
		$this->defaultCancelIcon=$defaultCancelIcon;
	}
	
	
	public function createBlock($decorated = true, $label = '', $icon = ''){
		$icon = empty($icon) ? null : $this->createIcon($icon);
		return new Block($decorated, $label, $icon);
	}
	public function createForm($useSubmit = true, $useCancel = true, $cancelLink = '#', $decorated = true, $label = '', $icon = '', $formAction = '', $method = 'post'){
		$icon = empty($icon) ? null : $this->createIcon($icon);
		$form = new Form($decorated, $label, $icon, $formAction, $method);
		if($useSubmit){
			$form->setSubmit($this->createButton($this->defaultSubmitText, true, $this->defaultSubmitIcon));
		}
		if($useCancel){
			$form->setCancel($this->createLink($this->defaultCancelText, $cancelLink, $this->defaultCancelIcon));
		}
		return $form;
	}
	public function createIcon($value){
		return new Icon($value, false);
	}
	
	public function createTextIcon($value){
		return new Icon($value, true);
	}
	
	public function createContent($content){
		return new Content($content);
	}
	
	public function createButton($label, $isSubmit = false, $icon = ''){
		$icon = empty($icon) ? null : $this->createIcon($icon);
		return new Button($label, $isSubmit, $icon);
	}
	
	public function createLink($label, $href = '#', $icon = ''){
		$icon = empty($icon) ? null : $this->createIcon($icon);
		return new Link($label, $href, $icon);
	}
}
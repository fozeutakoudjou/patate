<?php
namespace core\generator\html;
class HtmlGenerator{
	protected $defaultSubmitText;
	protected $defaultCancelText;
	protected $defaultCancelIcon = 'cancel';
	protected $defaultSubmitIcon='save';
	protected $languages;
	protected $activeLang;
	
	public function __construct($defaultSubmitText = '', $defaultCancelText = '', $languages = array(), $activeLang = '') {
		$this->setDefaultSubmitText($defaultSubmitText);
		$this->setDefaultCancelText($defaultCancelText);
		$this->setLanguages($languages);
		$this->setActiveLang($activeLang);
		Content::setLanguages($this->languages);
		Content::setActiveLang($this->activeLang);
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
	
	public function setLanguages($languages){
		$this->languages=$languages;
	}
	
	public function setActiveLang($activeLang){
		$this->activeLang=$activeLang;
	}
	
	
	public function createBlock($decorated = true, $label = '', $icon = ''){
		$icon = empty($icon) ? null : $this->createIcon($icon);
		return new Block($decorated, $label, $icon);
	}
	public function createForm($useSubmit = true, $useCancel = true, $cancelLink = '#', $decorated = true, $label = '', $icon = '', $formAction = '', $submitAction = '', $method = 'post'){
		$icon = empty($icon) ? null : $this->createIcon($icon);
		$form = new Form($decorated, $label, $icon, $formAction, $submitAction, $method);
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
	
	public function createButton($label, $isSubmit = false, $icon = '', $name = ''){
		$icon = empty($icon) ? null : $this->createIcon($icon);
		return new Button($label, $isSubmit, $icon, $name);
	}
	
	public function createLink($label, $href = '#', $icon = '', $name = ''){
		$icon = empty($icon) ? null : $this->createIcon($icon);
		return new Link($label, $href, $icon, $name);
	}
	
	public function createTextField($name, $label = ''){
		return new InputText($name, $label, 'text');
	}
	
	public function createPasswordField($name, $label = ''){
		return new InputText($name, $label, 'password');
	}
	
	public function createCheckbox($name, $label = '', $checked = false){
		return new Checkbox($name, $label, $checked);
	}
}
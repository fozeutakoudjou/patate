<?php
namespace core\generator\html;
class Field extends Element{
	const TRANSLATABLE_CLASS='translatable_field';
	protected $value;
	protected $valueSetted = false;
	/*protected $errorsSetted = false;*/
	protected $required = false;
	protected $helpText;
	//protected $errors;
	protected $labelObject;
	protected $labelWidth;
	
	protected $translatable = false;
	protected $fieldOnly = false;
	
	protected static $langListContent;
	function __construct($name, $label='') {
		$this->setName($name);
		$this->setLabel($label);
	}
	public function getValue() {
		return $this->value;
	} 
	public function setValue($value){
		$this->value=$value;
		$this->valueSetted =true;
	}
	
	public function getLabelWidth() {
		return $this->labelWidth;
	} 
	public function setLabelWidth($labelWidth){
		$this->labelWidth=$labelWidth;
	}
	
	public function needValue() {
		return true;
	}
	
	public function needError() {
		return true;
	}
	
	public function hasLabelObject(){
		return ($this->labelObject!=null);
	}
	
	public function hasHelpText(){
		return !empty($this->helpText);
	}
	
	public function getHelpText() {
		return $this->helpText;
	} 
	public function setHelpText($helpText){
		$this->helpText=$helpText;
	}
	public function isRequired() {
		return $this->required;
	} 
	public function setRequired($required){
		$this->required=$required;
	}
	public function isTranslatable() {
		return $this->translatable;
	} 
	public function setTranslatable($translatable){
		$this->translatable=$translatable;
	}
	
	public function isFieldOnly() {
		return $this->fieldOnly;
	} 
	public function setFieldOnly($fieldOnly){
		$this->fieldOnly=$fieldOnly;
	}
	/*
	public function getErrors() {
		return $this->errors;
	}
	
	public function setErrors($errors){
		if(is_array($errors)){
			$this->errors=$errors;
			$this->errorsSetted =true;
			
		}else{
			$this->addError($errors);
		}
	}
	public function addError($error){
		$this->errors[]=$error;
	}*/
	public function drawLangList(){
		if(self::$langListContent === null){
			$label = isset(self::$languages[self::$activeLang]) ? self::$languages[self::$activeLang]->getIsoCode() : self::$activeLang;
			$block = new Block(true, $label);
			$block->setTemplateFile('generator/lang_list', false);
			$parentClass = 'li_switcher_field_lang';
			foreach(self::$languages as $key =>$lang){
				$link = new Link($lang->getName());
				$link->setShowHide(true);
				$link->setTargetToHide('.'.self::TRANSLATABLE_CLASS);
				$link->setTargetToShow('.'.self::TRANSLATABLE_CLASS.'.'.$this->getLangClass($key));
				$link->addAttribute('data-label', $lang->getIsoCode());
				$link->addAttribute('data-lang', $key);
				$block->addChild($link);
				$link->addClass('switcher_field_lang');
				$active = (self::$activeLang == $key) ? ' active' : '';
				$link->addAdditionalData('parentClass', $parentClass . ' li_'.$key . $active);
			}
			$block->addClass('lang_form_label');
			self::$langListContent = $block->generate();
		}
		return self::$langListContent;
	}
	
	public function generate(){
		if($this->translatable){
			$this->addClass(self::TRANSLATABLE_CLASS);
		}
		return parent::generate();
	}
	
	public function getFieldValue($lang = ''){
		$value = '';
		if($this->translatable && isset($this->value[$lang])){
			$value = $this->value[$lang]; 
		}elseif(!is_array($this->value)){
			$value = $this->value;
		}
		return $value;
	}
	
	public function getLangClass($lang){
		return $this->translatable ? 'lang_'.$lang : '';
	}
	
	public function getLangVisible($lang){
		return ($this->translatable && ($lang!=self::$activeLang)) ? 'display:none;' : '';
	}
	public function hasErrorLang($lang = ''){
		$hasError = $this->hasErrors();
		if($this->translatable){
			$hasError = $hasError && isset($this->errors[$lang]);
		}
		return $hasError;
	}
	public function drawErrorTextVisible($lang = ''){
		return $this->hasErrorLang($lang) ? '' : 'display:none;';
	}
	
	public function getErrorText($lang = ''){
		if($this->translatable && $this->hasErrorLang($lang)){
			$text = '('.(isset(self::$languages[$lang]) ? self::$languages[$lang]->getIsoCode() : $lang).') ' . $this->errors[$lang];
		}else{
			$text = $this->errors;
		}
		return $text;
	}
	
	public function drawWrapperErrorClass(){
		return $this->hasErrors() ? 'has-error' : '';
	}
}
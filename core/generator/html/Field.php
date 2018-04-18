<?php
namespace core\generator\html;
class Field extends Element{
	protected $value;
	protected $valueSetted = false;
	protected $errorsSetted = false;
	protected $required = false;
	protected $helpText;
	protected $errors;
	protected $labelObject;
	
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
	
	public function needValue() {
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
	}
	public function drawLangList(){
		if(self::$langListContent === null){
			$content = new Content();
			$content->setTemplateFile('generator/lang_list', false);
			self::$langListContent = $content->generate();
		}
		return self::$langListContent;
	}
	
	public function generate(){
		if($this->translatable){
			$this->addClass('translatable_field');
		}
		return parent::generate();
	}
}
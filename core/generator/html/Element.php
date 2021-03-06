<?php
namespace core\generator\html;
abstract class Element extends Content{
	protected $visible = true;
	protected $id;
	protected $name;
	protected $label;
	protected $icon;
	protected $attributes=array();
	protected $wrapperAttributes=array();
	protected $classes=array();
	protected $labelDisabled = false;
	protected $wrapperClasses=array();
	protected $wrapperWidth;
	protected $width;
	protected $value;
	protected $additionalData = array();
	protected $valueSetted = false;
	protected $errorSetted = false;
	protected $errors = array();
	
	public function hasErrors(){
		return !empty($this->errors);
	}
	public function getErrors(){
		return $this->errors;
	}
	public function setErrors($errors){
		$this->errors=$errors;
		$this->errorSetted =true;
	}
	
	public function addClass($class) {
		if (!$this->hasClass($class)) {
			$this->classes[] =$class;
		}
	}
	public function hasClass($class) {
		return in_array($class, $this->classes);
	}
	public function addWrapperClass($class) {
		if (!in_array($class, $this->wrapperClasses)) {
			$this->wrapperClasses[] =$class;
		}
	}
	public function setAdditionalData($additionalData) {
		$this->additionalData =$additionalData;
	}
	public function setClasses($classes) {
		$this->classes =$classes;
	}
	public function setWrapperClasses($wrapperClasses) {
		$this->wrapperClasses =$wrapperClasses;
	}
	public function setAttributes($attributes, $clearExisting = false) {
		if($clearExisting){
			$this->attributes=array();
		}
		foreach ($attributes as $key => $value) {
			$this->addAttribute($key, $value);
		}
	}
	public function addAttribute($name, $value = null) {
		$this->setAttribute($name, $value);
	}
	public function setAttribute($name, $value = null) {
		$this->attributes[$name] =$value;
	}
	
	public function addWrapperAttribute($name, $value = null) {
		$this->setWrapperAttribute($name, $value);
	}
	public function setWrapperAttribute($name, $value = null) {
		$this->wrapperAttributes[$name] =$value;
	}
	
	public function addAdditionalData($name, $value = null) {
		$this->additionalData[$name] =$value;
	}
	
	public function getAdditional($name) {
		return isset($this->additionalData[$name]) ? $this->additionalData[$name] : '';
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getClasses() {
		return $this->classes;
	}
	public function getAttributes() {
		return $this->attributes;
	}
	
	public function drawAttributes() {
		return $this->drawAttributesFromList($this->attributes);
	}
	public function drawWrapperAttributes() {
		return $this->drawAttributesFromList($this->wrapperAttributes);
	}
	
	protected function drawAttributesFromList($attributes) {
		$result = '';
		foreach ($attributes as $key => $value) {
			$result.=' '.$key.(($value ===null)?'':'="'.$value.'"');
		}
		return $result;
	}
	protected function drawWrapperClassesFromList($classes) {
		return implode(' ', $classes);
	}
	public function drawClasses() {
		return $this->drawWrapperClassesFromList($this->classes);
	}
	
	public function drawWrapperClasses() {
		return $this->drawWrapperClassesFromList($this->wrapperClasses);
	}
	
	public function getLabel(){
		return $this->label;
	}
	public function setLabel($label){
		$this->label=$label;
	}
	public function getIcon(){
		return $this->icon;
	}
	public function setIcon($icon){
		$this->icon=$icon;
	}
	public function isVisible(){
		return $this->visible;
	}
	public function setVisible($visible){
		$this->visible=$visible;
	}
	public function hasIcon(){
		return ($this->icon!=null);
	}
	public function hasLabel(){
		return !empty($this->label);
	}
	
	public function drawVisible() {
		return $this->visible ? '' : 'display:none;';
	}
	
	public function isLabelDisabled() {
		return $this->labelDisabled;
	} 
	public function setLabelDisabled($labelDisabled){
		$this->labelDisabled=$labelDisabled;
	}
	public function getWidth(){
		return $this->width;
	}
	public function setWidth($width){
		$this->width=$width;
	}
	public function getWrapperWidth(){
		return $this->wrapperWidth;
	}
	public function setWrapperWidth($wrapperWidth){
		$this->wrapperWidth=$wrapperWidth;
	}
	
	public function isValueSetted() {
		return $this->valueSetted;
	}
	public function isErrorSetted() {
		return $this->errorSetted;
	}
	public function getValue() {
		return $this->value;
	} 
	public function setValue($value){
		$this->value=$value;
		$this->valueSetted =true;
	}
}
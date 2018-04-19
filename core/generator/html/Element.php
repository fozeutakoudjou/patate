<?php
namespace core\generator\html;
abstract class Element extends Content{
	protected $visible = true;
	protected $id;
	protected $name;
	protected $label;
	protected $icon;
	protected $attributes=array();
	protected $classes=array();
	protected $labelDisabled = false;
	protected $wrapperClasses=array();
	protected $wrapperWidth;
	protected $width;
	protected $value = array();
	
	public function addClass($class) {
		if (!in_array($class, $this->classes)) {
			$this->classes[] =$class;
		}
	}
	public function addWrapperClasses($class) {
		if (!in_array($class, $this->wrapperClasses)) {
			$this->wrapperClasses[] =$wrapperClasses;
		}
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
	
	public function getId() {
		return $this->id;
	}
	
	public function getClasses() {
		return $this->classes;
	}
	public function getAttributes() {
		return $this->attributes;
	}
	public function generateContent() {
		
	}
	
	public function drawAttributes() {
		$result = '';
		foreach ($this->attributes as $key => $value) {
			$result.=' '.$key.(($value ===null)?'':'="'.$value.'"');
		}
		return $result;
	}
	
	public function drawClasses() {
		return implode(' ', $this->classes);
	}
	
	public function drawWrapperClasses() {
		return implode(' ', $this->wrapperClasses);
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
	
	public function getValue() {
		return $this->value;
	} 
	public function setValue($value){
		$this->value=$value;
		$this->valueSetted =true;
	}
}
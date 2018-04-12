<?php
namespace core\generator\html;
abstract class Element extends Content{
	protected $id;
	protected $name;
	protected $label;
	protected $icon;
	protected $attributes=array();
	protected $classes=array();
	
	public function addClass($name) {
		if (!in_array($name, $this->classes)) {
			$this->classes[] =$name;
		}
	}
	
	public function setClasses($classes) {
		$this->classes =$classes;
	}
	public function setAttributes($attributes, $clearExisting = false) {
		if($clearExisting){
			$this->attributes=array();
		}
		foreach ($attributes as $key => $value) {
			$this->setAttribute($key, $value);
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
	public function getName() {
		return $this->name;
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
	public function hasIcon(){
		return ($this->icon!=null);
	}
	public function hasLabel(){
		return !empty($this->label);
	}
}
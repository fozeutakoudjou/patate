<?php
namespace form\generator;
class HtmlElement extends HtmlContent{
	protected $id;
	protected $type;
	protected $name;
	protected $attributes;
	protected $classes;
	public function __construct() {
		$this->attributes=array();
		$this->classes=array();
	}
	
	public function addAttribute($name, $value = "") {
		$this->setAttribute($name, $value);
	}
	
	public function addClass($name) {
		if (!in_array($name, $this->classes)) {
			$this->classes[] =$name;
		}
	}
	
	public function setClasses($classes) {
		$this->classes =$classes;
	}
	public function setAttributes($attributes) {
		//$this->attributes =$attributes;
		$this->attributes=array();
		foreach ($attributes as $key => $value) {
			$this->addAttribute($key, $value);
		}
	}
	public function setAttribute($name, $value = "") {
		$this->attributes[$name] =$value;
	}
	
	public function getId() {
		return $this->id;
	}
	public function getName() {
		return $this->name;
	}
	public function getType() {
		return $this->type;
	}
	
	public function getClasses() {
		return $this->classes;
	}
	public function getAttributes() {
		return $this->attributes;
	}
	
	public function generate() {
		
	}
	public function generateContent() {
		
	}
}
<?php
namespace core\generator\html;
class Block extends Element{
	protected $contents = array();
	protected $value = array();
	protected $decorated;
	protected $footers = array();
	protected $headers = array();
	protected $templateFile = 'generator/block';
	protected $contentOnly = false;
	
	public function __construct($decorated = true, $label ='', $icon = null) {
		$this->setLabel($label);
		$this->setIcon($icon);
		$this->setDecorated($decorated);
	}
	
	public function setChildValue($name, $value) {
		return $this->value[$name] = $value;
	}
	
	public function hasChild($name) {
		return isset($this->contents[$name]);
	}
	
	public function addChild($child) {
		if($child->hasName()){
			$this->contents[$child->getName()] = $child;
		}else{
			$this->contents[] = $child;
		}
	}
	
	public function getChild($name) {
		return $this->hasChild($name) ? $this->contents[$name] : null;
	}
	
	public function generateContent() {
		$html='';
		$this->prepareContent();
		foreach ($this->contents as $content) {
			$html.=$content->generate();
		}
		return $html;
	}
	public function prepareContent() {
		foreach ($this->contents as $content) {
			if(isset($this->value[$content->getName()]) && $content->needValue() && !$content->isValueSetted()){
				$content->setValue($this->value[$content->getName()]);
			}
			if(isset($this->errors[$content->getName()]) && $content->needError() && !$content->isErrorSetted()){
				$content->setErrors($this->errors[$content->getName()]);
			}
		}
	}
	public function generate() {
		return parent::generate();
	}
	public function isContentOnly(){
		return $this->contentOnly;
	}
	public function setContentOnly($contentOnly){
		$this->contentOnly=$contentOnly;
	}
	public function getContents() {
		return $this->contents;
	}
	public function setContents($contents, $clearExisting = false) {
		if($clearExisting){
			$this->contents=array();
		}
		foreach ($contents as $content) {
			$this->addChild($content);
		}
	}
	
	public function needValue() {
		return true;
	}
	
	public function needError() {
		return true;
	}
	
	public function isDecorated(){
		return $this->decorated;
	}
	
	public function setDecorated($decorated){
		$this->decorated=$decorated;
	}
	
	public function hasHeader(){
		return ($this->hasIcon() || $this->hasLabel() || !empty($this->headers));
	}
	
	public function hasFooter(){
		return (!empty($this->footers));
	}
	
	public function getHeaders(){
		return $this->headers;
	}
	public function setHeaders($headers){
		$this->headers=$headers;
	}
	
	public function getFooters(){
		return $this->headers;
	}
	public function setFooters($footers){
		$this->footers=$footers;
	}
}
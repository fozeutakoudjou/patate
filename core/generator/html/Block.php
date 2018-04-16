<?php
namespace core\generator\html;
class Block extends Element{
	protected $contents;
	protected $value;
	protected $decorated;
	protected $footers = array();
	protected $headers = array();
	protected $templateFile = 'generator/block';
	
	public function __construct($decorated = true, $label ='', $icon = null) {
		$this->setLabel($label);
		$this->setIcon($icon);
		$this->setDecorated($decorated);
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
		foreach ($this->contents as $content) {
			if(isset($value[$content->getName()]) && $content->needValue() && !$content->isValueSetted()){
				$content->setValue($value[$content->getName()]);
			}
			$html.=$content->generate();
		}
		return $html;
	}
	public function generate() {
		return parent::generate();
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
	
	public function setValue($value){
		$this->value=$value;
		$this->valueSetted =true;
	}
	
	public function needValue() {
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
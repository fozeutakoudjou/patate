<?php
namespace form\generator;
class Block extends HtmlElement{
	protected $htmlContents;
	public function __construct($contents = array()) {
		$this->setHtmlContents($contents);
	}
	
	public function addChild($child) {
		$this->htmlContents[] = $child;
	}
	
	public function generateContent() {
		$html="";
		foreach ($this->htmlContents as $content) {
			$html.=$content.generate();
		}
		return $html;
	}
	public function getContents() {
		return $this->htmlContents;
	}
	public function setHtmlContents($htmlContents) {
		$this->htmlContents=array();
		foreach ($htmlContents as $value) {
			$this->addChild($value);
		}
		
	}
}
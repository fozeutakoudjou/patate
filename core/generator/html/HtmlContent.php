<?php
namespace form\generator;
class HtmlContent{
	protected $content;
	public function __construct($content = "") {
		$this->content=$content;
	}
	
	public function setHtml($content) {
		$this->content=$content;
	}
	public function gethtml() {
		return $this->content;
	}
	public function generate() {
		return $this->gethtml();
	}
	
	public function needValue() {
		return false;
	}
	public function isValueSeted() {
		return false;
	}
}
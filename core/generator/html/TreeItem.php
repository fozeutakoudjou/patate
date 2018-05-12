<?php
namespace core\generator\html;
class TreeItem extends Element{
	protected $first = false;
	protected $last = false;
	protected $subObjects;
	protected $renderingCancelled = false;
	protected $content;
	
	public function __construct($value, $subObjects = array()) {
		$this->setValue($value);
		$this->setSubObjects($subObjects);
	}
	
	public function isFirst() {
		return $this->first;
	}
	
	public function setFirst($first) {
		$this->first = $first;
	}
	public function isRenderingCancelled() {
		return $this->renderingCancelled;
	}
	
	public function setRenderingCancelled($renderingCancelled) {
		$this->renderingCancelled = $renderingCancelled;
	}
	
	public function isLast() {
		return $this->last;
	}
	
	public function setLast($last) {
		$this->last = $last;
	}
	public function getContent() {
		return $this->content;
	}
	
	public function setContent($content) {
		$this->content = $content;
	}
	public function setSubObjects($subObjects) {
		$this->subObjects = $subObjects;
	}
	
	public function hasChildren() {
		return !empty($this->subObjects);
	}
}